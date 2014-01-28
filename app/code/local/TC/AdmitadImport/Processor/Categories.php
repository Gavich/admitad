<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Processor_Categories extends TC_AdmitadImport_Processor_AbstractProcessor
{
    const ROOT_CATEGORY_ORIGIN_ID  = 'start';
    const ORIGIN_ID_ATTRIBUTE_CODE = 'origin_id';

    /** @var array */
    private $_categories;

    /* @var Mage_Catalog_Model_Resource_Eav_Attribute */
    private $_originAttribute;

    /** @var array */
    private $_processed = array();

    /** @var array */
    private $_idsMap = array();

    /**
     * Performs import
     *
     * @param TC_AdmitadImport_Reader_DataInterface $data
     */
    public function process(TC_AdmitadImport_Reader_DataInterface $data)
    {
        $this->_getLogger()->log('Categories import started');

        $originAttributeId      = Mage::getResourceModel('eav/entity_attribute')->getIdByCode(
            Mage_Catalog_Model_Category::ENTITY, self::ORIGIN_ID_ATTRIBUTE_CODE
        );
        $this->_originAttribute = Mage::getModel('catalog/resource_eav_attribute')->load($originAttributeId);

        $this->_categories = $data->getCategories();
        $this->_idsMap     = $this->_getIdsMap();
        $defaultStore      = Mage::app()->getWebsite(true)->getDefaultStore();

        /* @var $indexer Mage_Index_Model_Indexer */
        $indexer   = Mage::getSingleton('index/indexer');
        $processes = $indexer->getProcessesCollection();
        $processes->walk('setMode', array(Mage_Index_Model_Process::MODE_MANUAL));
        $processes->walk('save');

        /* @var $rootCategory Mage_Catalog_Model_Category */
        $rootCategory = Mage::getModel('catalog/category');
        $rootCategory->load($defaultStore->getRootCategoryId());
        $rootCategory->setData(self::ORIGIN_ID_ATTRIBUTE_CODE, self::ROOT_CATEGORY_ORIGIN_ID);
        $rootCategory->save();

        try {
            $this->processChildren($rootCategory, $defaultStore);
        } catch (Exception $e) {
            $this->_getLogger()->log($e->getMessage(), Zend_Log::CRIT);

            return;
        }

        $this->_getLogger()->log('Creating and updating categories finished. Starting reindex...');
        $processes->walk('setMode', array(Mage_Index_Model_Process::MODE_REAL_TIME));
        $processes->walk('save');
        $processes->walk('reindexAll');

        $this->_getLogger()->log('Reindex finished. SUCCESS!');
    }

    /**
     * Recursive function to process all categories tree
     *
     * @param Mage_Catalog_Model_Category $parentCategory
     * @param Mage_Core_Model_Store       $store
     */
    public function processChildren(Mage_Catalog_Model_Category $parentCategory, $store)
    {
        $originId = $parentCategory->getData(self::ORIGIN_ID_ATTRIBUTE_CODE);
        $children = $this->_getChildren($originId);

        if (count($children) == 0) {
            $this->_getLogger()->log(sprintf('Children not found for category: %s', $originId));

            return;
        }

        foreach ($children as $category) {
            // get magento ID from attribute
            $idInMagento = isset($this->_idsMap[$category['id']]) ? $this->_idsMap[$category['id']] : false;
            if (false === $idInMagento) {
                //trying to create
                $categoryModel = $this->_createCategory($parentCategory, $category['name'], $category['id'], $store);
            } else {
                /* @var $rootCategory Mage_Catalog_Model_Category */
                $categoryModel = $this->_updateCategory($idInMagento, $parentCategory, $category['name']);
            }
            $this->_processed[] = $categoryModel->getId();

            // processing children
            $this->processChildren($categoryModel, $store);
        }
    }

    /**
     * Creating new category
     *
     * @param Mage_Catalog_Model_Category|int $parentCategory
     * @param string                          $name
     * @param string                          $originId
     * @param Mage_Core_Model_Store           $store
     *
     * @return Mage_Catalog_Model_Category
     */
    private function _createCategory($parentCategory, $name, $originId, $store)
    {
        $this->_getLogger()->log(sprintf('Creating category: %s', $name));
        $category = Mage::getModel('catalog/category');

        if (!$parentCategory instanceOf Mage_Catalog_Model_Category) {
            $parentCategory = Mage::getModel('catalog/category')->load($parentCategory);
        }
        $parentCategoryId = $parentCategory->getId();
        $category
            ->setData($this->_getDefaultCatData($name, $originId, $parentCategoryId))
            ->setAttributeSetId($category->getDefaultAttributeSetId())
            ->setStoreId($store->getId())
            ->setPath($parentCategory->getPath())
            ->save();

        $this->_getLogger()->log(sprintf('Category created, ID: %d', $category->getId()));

        return $category;
    }

    /**
     * Checks if changes occurred in external DB and if they exist then updates category
     *
     * @param int                         $id             Magento category ID
     * @param Mage_Catalog_Model_Category $parentCategory Magento parent category ID
     * @param string                      $name
     *
     * @return Mage_Catalog_Model_Category
     */
    private function _updateCategory($id, Mage_Catalog_Model_Category $parentCategory, $name)
    {
        $this->_getLogger()->log(sprintf('Updating category: %s', $name));
        $category = Mage::getModel('catalog/category')->load($id);

        if ($category->getName() != $name) {
            $category->setName($name);
        }

        $category->setParentId($parentCategory->getId());
        $category->setPath($parentCategory->getPath() . '/');
        //save will not send query to DB if changes not occurred
        $category->save();

        $this->_getLogger()->log(sprintf('Category has been updated: %s', $name));

        return $category;
    }

    /**
     * Find child categories in current import data
     *
     * @param mixed $originId
     *
     * @return array
     */
    private function _getChildren($originId)
    {
        $children = array();

        foreach ((array)$this->_categories as $category) {
            if (isset($category['parentId']) && $category['parentId'] === $originId) {
                $children[] = $category;
            } elseif (empty($category['parentId']) && $originId === self::ROOT_CATEGORY_ORIGIN_ID) {
                $children[] = $category;
            }
        }

        return $children;
    }

    /**
     * Returns magento category IDs
     *
     * @return int|false
     */
    private function _getIdsMap()
    {
        /* @var $coreResource Mage_Core_Model_Resource */
        $coreResource   = Mage::getModel('core/resource');
        $coreConnection = $coreResource->getConnection('core_read');

        $select = $coreConnection->select();
        $select->from(array('cc' => $coreResource->getTableName('catalog/category')), array('cc.entity_id'));
        $select->join(
            array('a' => $this->_originAttribute->getBackendTable()), 'a.entity_id=cc.entity_id', array('a.value')
        );
        $select->where('a.attribute_id =?', $this->_originAttribute->getId());

        $result = $coreConnection->fetchPairs($select);

        return array_flip($result);
    }

    /**
     * Returns array with required category data
     *
     * @param string $name
     * @param string $originId
     * @param int    $parentId
     *
     * @return array
     */
    private function _getDefaultCatData($name, $originId, $parentId)
    {
        return array(
            'name'                         => trim($name),
            'is_active'                    => 1,
            'include_in_menu'              => 1,
            'is_anchor'                    => 1,
            'url_key'                      => '',
            'description'                  => '',
            'parent_id'                    => $parentId,
            self::ORIGIN_ID_ATTRIBUTE_CODE => $originId
        );
    }
}
