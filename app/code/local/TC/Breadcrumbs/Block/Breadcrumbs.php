<?php

class TC_Breadcrumbs_Block_Breadcrumbs extends Mage_Page_Block_Html_Breadcrumbs
{
    /**
     * Is now rendering catalog page
     *
     * @return bool
     */
    public function isCatalogPage()
    {
        /* @var $helper TC_Catalog_Helper_Data */
        $helper = Mage::helper('catalog');

        return $helper->getProduct() || $helper->getCategory();
    }

    /**
     * Returns collection with siblings categories
     *
     * @param int $level
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection|null
     */
    public function getSiblingCategories($level)
    {
        /* @var $helper TC_Catalog_Helper_Data */
        $helper = Mage::helper('catalog');

        $currentCategory = $helper->getCategory();

        $pathIds = $currentCategory->getPathIds();
        if (!$helper->getProduct()) {
            // remove current id from path
            array_pop($pathIds);
        }

        $filter = array_slice($pathIds, 0, $level);

        /* @var $collection Mage_Catalog_Model_Resource_Category_Collection */
        $collection = $currentCategory->getCollection();
        $collection->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('is_active', 1)
            ->addPathsFilter(join('/', $filter))
            ->setOrder('position', Varien_Db_Select::SQL_ASC)
            ->addAttributeToFilter('level', array('eq' => $level))
            ->joinUrlRewrite();

        return $collection;
    }

    /**
     * Added additional condition to do not filter needed fields
     *
     * @@inheritdoc
     */
    public function addCrumb($crumbName, $crumbInfo, $after = false)
    {
        $availableFields = array('label', 'title', 'link', 'first', 'last', 'readonly');
        if ($this->isCatalogPage()) {
            $availableFields = array_merge($availableFields, array('level', 'id'));
        }

        $this->_prepareArray($crumbInfo, $availableFields);
        if ((!isset($this->_crumbs[$crumbName])) || (!$this->_crumbs[$crumbName]['readonly'])) {
            $this->_crumbs[$crumbName] = $crumbInfo;
        }

        return $this;
    }
}
