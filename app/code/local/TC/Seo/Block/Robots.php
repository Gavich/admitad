<?php

/**
 * @category   TC
 * @package    TC_Seo
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_Seo_Block_Robots extends Mage_Core_Block_Template
{
    const XPATH_CLOSE_CATEGORY_PAGES            = 'catalog/seo/close_category_pages';
    const XPATH_CLOSE_CATEGORY_WO_DESC_PAGES    = 'catalog/seo/close_category_wo_desc_pages';
    const XPATH_CLOSE_CATEGORY_PAGINATION_PAGES = 'catalog/seo/category_pagination_pages';
    const XPATH_CLOSE_PRODUCT_PAGES             = 'catalog/seo/close_product_pages';

    /**
     * Set robots to NOINDEX, NOFOLLOW on pages where this block will be added through layout xml configuration
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            if ($this->_isApplicable()) {
                $headBlock->setRobots('NOINDEX, FOLLOW');
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Check whenever robots should be applied for current page
     *
     * @return bool
     */
    protected function _isApplicable()
    {
        // @codingStandardsIgnoreStart
        /* @var $helper TC_Seo_Helper_Data */
        $helper = Mage::helper('tc_seo');
        $result = false;
        switch (true) {
            case $helper->isCategoryPage() || $helper->isSearchResultsPage():
                $result |= Mage::getStoreConfig(self::XPATH_CLOSE_CATEGORY_PAGES);
                $result |= $helper->isPaginationPage() && Mage::getStoreConfig(self::XPATH_CLOSE_CATEGORY_PAGINATION_PAGES);
                $result |= $helper->isCategoryWithoutDescription() && Mage::getStoreConfig(self::XPATH_CLOSE_CATEGORY_WO_DESC_PAGES);
                break;
            case $helper->isProductPage():
                $result |= Mage::getStoreConfig(self::XPATH_CLOSE_PRODUCT_PAGES);
                break;
        }

        return $result;
        // @codingStandardsIgnoreEnd
    }
}
