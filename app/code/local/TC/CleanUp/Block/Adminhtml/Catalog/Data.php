<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_CleanUp_Block_Adminhtml_Catalog_Data extends Mage_Adminhtml_Block_Template
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->setTemplate('tc/cleanup/catalog/data.phtml');
    }

    /**
     * Returns disabled products count
     *
     * @return int
     */
    public function getDisabledProductsCount()
    {
        return $this->_getModel()->getDisabledProductsCount();
    }

    /**
     * Returns disabled categories count
     *
     * @return int
     */
    public function getDisabledCategoriesCount()
    {
        return $this->_getModel()->getDisabledCategoriesCount();
    }

    /**
     * Returns URL to see disabled products grid
     *
     * @return string
     */
    public function getDisabledProductsGridUrl()
    {
        return $this->getUrl(
            'adminhtml/catalog_product/index', array('product_filter' => base64_encode(
                sprintf('status=%d', Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
            ))
        );
    }

    /**
     * Returns URL to see disabled categories
     *
     * @return string
     */
    public function getCategoriesViewUrl()
    {
        return $this->getUrl('adminhtml/catalog_category/index');
    }

    /**
     * Returns URL to clean up disabled products
     *
     * @return string
     */
    public function getRemoveDisabledProductsUrl()
    {
        return $this->getUrl('*/*/removeProducts');
    }

    /**
     * Returns URL to clean up disabled categories
     *
     * @return string
     */
    public function getRemoveDisabledCategoriesUrl()
    {
        return $this->getUrl('*/*/removeCategories');
    }

    /**
     * Returns model instance
     *
     * @return TC_CleanUp_Model_Resource_Cleanup
     */
    private function _getModel()
    {
        return Mage::getResourceModel('tc_cleanup/cleanup');
    }
}
