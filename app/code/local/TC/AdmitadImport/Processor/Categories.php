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
    private $_categories = array();

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

        $error = false;
        // fetching store from settings if needed could be done here
        $defaultStore      = Mage::app()->getWebsite(true)->getDefaultStore();
        $this->_categories = $data->getCategories();
        $this->_idsMap     = $this->_getResourceUtilityModel()->getCategoriesIdMap();

        /* @var $rootCategory Mage_Catalog_Model_Category */
        $rootCategory = Mage::getModel('catalog/category');
        $rootCategory->load($defaultStore->getRootCategoryId());
        $rootCategory->setData(self::ORIGIN_ID_ATTRIBUTE_CODE, self::ROOT_CATEGORY_ORIGIN_ID);
        $rootCategory->save();

        $this->_processed[] = $rootCategory->getId();

        try {
            $this->_processChildren($rootCategory, $defaultStore);
        } catch (Exception $e) {
            $this->_getLogger()->log($e->getMessage(), Zend_Log::CRIT);
            $error = true;
        }
        if (!$error) {
            $this->_updateVisibility();
            $this->_getLogger()->log('Categories successfully imported. SUCCESS!');
        }
        $this->_getLogger()->log('Categories import ended');
    }

    /**
     * Recursive function to process all categories tree
     *
     * @param Mage_Catalog_Model_Category $parentCategory
     * @param Mage_Core_Model_Store       $store
     */
    protected function _processChildren(Mage_Catalog_Model_Category $parentCategory, $store)
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
            $this->_processChildren($categoryModel, $store);
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
    protected function _createCategory($parentCategory, $name, $originId, $store)
    {
        $this->_getLogger()->log(sprintf('Creating category: %s', $name));
        /* @var $category Mage_Catalog_Model_Category */
        $category = Mage::getModel('catalog/category');

        if (!$parentCategory instanceOf Mage_Catalog_Model_Category) {
            /** @var Mage_Catalog_Model_Category $parentCategory */
            $parentCategory = Mage::getModel('catalog/category')->load((int)$parentCategory);
        }
        $parentCategoryId = $parentCategory->getId();
        $category->setData($this->_getDefaultCategoryData($name, $originId, $parentCategoryId));
        $category->setData('attribute_set_id', $category->getDefaultAttributeSetId());
        $category->setStoreId($store->getId());
        $category->setData('path', $parentCategory->getData('path'));
        $category->save();

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
    protected function _updateCategory($id, Mage_Catalog_Model_Category $parentCategory, $name)
    {
        $this->_getLogger()->log(sprintf('Updating category: %s', $name));
        /* @var $category Mage_Catalog_Model_Category */
        $category = Mage::getModel('catalog/category')->load($id);

        if ($category->getName() != $name) {
            $category->setData('name', $name);
        }

        $category->setData('parent_id', $parentCategory->getId());
        $category->setData('level', $parentCategory->getLevel() + 1);
        $category->setData('path', $parentCategory->getData('path') . '/');
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
     * Disable categories that not in imported data
     */
    private function _updateVisibility()
    {
        $categoriesToDisable = array_diff(array_values($this->_idsMap), $this->_processed);

        $this->_getResourceUtilityModel()->updateVisibilityAttributeValue($categoriesToDisable, false);
        $this->_getResourceUtilityModel()->updateVisibilityAttributeValue($this->_processed, true);
    }

    /**
     * Returns utility model
     *
     * @return TC_AdmitadImport_Model_Resource_Category
     */
    private function _getResourceUtilityModel()
    {
        return Mage::getResourceModel('tc_admitadimport/category');
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
    private function _getDefaultCategoryData($name, $originId, $parentId)
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
