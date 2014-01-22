<?php

/**
 * Class TC_Catalog_Block_Robots
 */
class TC_Catalog_Block_Robots extends Mage_Core_Block_Template
{
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
     * Check whenever page is pagination page
     *
     * @return bool
     */
    protected function _isPaginationPage()
    {
        $page = $this->getRequest()->getParam('p');

        return null !== $page;
    }

    /**
     * Check whenever robots should be applied for current page
     *
     * @return bool
     */
    protected function _isApplicable()
    {
        // should be applied to product pages and category paginated pages
        return (Mage::registry('current_category') && $this->_isPaginationPage())
            || Mage::registry('current_product');
    }
}
