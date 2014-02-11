<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Processor_Products extends TC_AdmitadImport_Processor_AbstractProcessor
{
    /** @var array */
    private $_processedSKUs = array();

    /** @var array */
    private $_existSKUs = array();

    /** @var array */
    private $_defaultProductData = array();

    /** @var int */
    private $_attributeSetId;

    /**
     * Performs import
     *
     * @param TC_AdmitadImport_Reader_DataInterface $data
     *
     * @return void
     */
    public function process(TC_AdmitadImport_Reader_DataInterface $data)
    {
        $this->_beforeProcess();
        $this->_getLogger()->log('Products import started');

        // @TODO fetching store from settings if needed
        $defaultStore = Mage::app()->getWebsite(true)->getDefaultStore();
        // @TODO fetching attribute set id if needed
        $this->_attributeSetId = $this->_getResourceUtilityModel()->getEntityType()->getDefaultAttributeSetId();

        $products     = $data->getProducts();

        // mark existed products as processed
        $this->_processedSKUs = array_keys(array_intersect_key($products, $this->_existSKUs));

        // process product import
        $this->_processProducts($products, $defaultStore);

        $this->_afterProcess();
        $this->_getLogger()->log('Products import ended');
    }

    /**
     * Process products
     *
     * @param array                 $products
     * @param Mage_Core_Model_Store $store
     *
     * @throws Exception
     */
    private function _processProducts(array $products, Mage_Core_Model_Store $store)
    {
        while ($products) {
            try {
                $product = array_pop($products);


            } catch (TC_AdmitadImport_Exception_InvalidItemException $e) {
                $this->_getLogger()->log($e->getMessage(), Zend_Log::ERR);
                continue;
            } catch (Exception $e) {
                $this->_getLogger()->log($e->getMessage(), Zend_Log::CRIT);
                throw $e;
            }
        }
    }

    protected function _getDefaultProductData()
    {
        if (is_null($this->_defaultProductData)) {
            /** @var $websiteCollection Mage_Core_Model_Resource_Website_Collection */
            $websiteCollection = Mage::getResourceModel('core/website_collection');
            $this->_defaultProductData = array(
                "website_ids"      => $websiteCollection->getColumnValues("website_id"),
                "attribute_set_id" => ,
                "qty"              => 0,
                "is_in_stock"      => Mage_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK,
                "weight"           => 0,
                "price"            => 0,
                "tax_class_id"     => 0,
                "status"           => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
                "visibility"       => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            );
        }
        return $this->_defaultProductData;
    }

    /**
     * Before import process
     */
    protected function _beforeProcess()
    {
        $this->_existSKUs = $this->_getResourceUtilityModel()->getSKUs();
    }

    /**
     * After process steps
     */
    protected function _afterProcess()
    {
    }

    /**
     * Returns utility model
     *
     * @return TC_AdmitadImport_Model_Resource_Product
     */
    private function _getResourceUtilityModel()
    {
        return Mage::getResourceModel('tc_admitadimport/product');
    }
}
