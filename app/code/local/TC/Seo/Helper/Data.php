<?php

/**
 * @category   TC
 * @package    TC_Seo
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_Seo_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XPATH_SEO_NOFOLLOW_ATTRIBUTE = 'catalog/seo/nofollow_attribute';

    /**
     * Is current page equals to product page
     *
     * @return bool
     */
    public function isProductPage()
    {
        return Mage::app()->getFrontController()->getAction() instanceof Mage_Catalog_ProductController;
    }

    /**
     * Is current page equals to category page
     *
     * @return bool
     */
    public function isCategoryPage()
    {
        return Mage::app()->getFrontController()->getAction() instanceof Mage_Catalog_CategoryController;
    }

    /**
     * Is current page equals to search result page
     *
     * @return bool
     */
    public function isSearchResultsPage()
    {
        return Mage::app()->getFrontController()->getAction() instanceof Mage_CatalogSearch_ResultController;
    }

    /**
     * Check whenever page is pagination page
     *
     * @return bool
     */
    public function isPaginationPage()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        $page    = $request->getParam('p');

        return ($this->isCategoryPage() || $this->isSearchResultsPage()) && null !== $page;
    }

    /**
     * Check whenever category do not have description attribute filled, if not category page return FALSE
     *
     * @return bool
     */
    public function isCategoryWithoutDescription()
    {
        $description = Mage::registry('current_category') ?
            trim(Mage::registry('current_category')->getDescription()) : true;

        return empty($description);
    }

    /**
     * Should links to products have nofollow attribute
     *
     * @return mixed
     */
    public function isProductLinksNoFollowed()
    {
        return Mage::getStoreConfig(self::XPATH_SEO_NOFOLLOW_ATTRIBUTE);
    }
} 
