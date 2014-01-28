<?php
/**
 * Medusa for Magento 1.7.0.0
 * Design and Development by creative-d2 design&development (http://www.creative-d2.de)
 * Distributed by ThemeForest (http://themeforest.net)
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @author     Ömer Bildirici jun.
 * @package    medusa_default
 * @copyright  Copyright 2012 Ömer Bildirici jun. (http://www.creative-d2.de)
 * @license    All rights reserved.
 * @version    1.1
 */
class CDD_CategorySearch_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_SHOW_SUBCATEGORIES                = 'catalog/catalog_category_search/show_subcategories';
    const XML_PATH_INDENTATION_TEXT                  = 'catalog/catalog_category_search/indentation_text';
    const XML_PATH_SELECT_CATEGORY_ON_CATEGORY_PAGES = 'catalog/catalog_category_search/select_category_on_category_pages';

    public function showSubCategories() {
        return Mage::getStoreConfig(self::XML_PATH_SHOW_SUBCATEGORIES);
    }

    public function getIndentationText() {
        return Mage::getStoreConfig(self::XML_PATH_INDENTATION_TEXT);
    }

    public function selectCategoryOnCategoryPages() {
        return Mage::getStoreConfig(self::XML_PATH_SELECT_CATEGORY_ON_CATEGORY_PAGES);
    }

    public function getCategoryParamName() {
        return Mage::getModel('catalog/layer_filter_category')->getRequestVar();
    }

    public function getMaximumCategoryLevel() {
        return $this->showSubCategories() ? 3 : 2;
    }

    public function isCategoryPage() {
        return Mage::app()->getFrontController()->getAction() instanceof Mage_Catalog_CategoryController;
    }

    public function isSearchResultsPage() {
        return Mage::app()->getFrontController()->getAction() instanceof Mage_CatalogSearch_ResultController;
    }

}
