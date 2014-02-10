<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Processor_Products extends TC_AdmitadImport_Processor_AbstractProcessor
{
    /** @var array */
    private $_processedSKUs = [];

    /** @var array */
    private $_existSKUs = [];

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

        $error = false;
        // @TODO fetching store from settings if needed
        $defaultStore = Mage::app()->getWebsite(true)->getDefaultStore();
        $products     = $data->getProducts();

        // mark existed products as processed
        $this->_processedSKUs = array_merge()

        var_dump(array_keys($products));
        die;
    }

    /**
     * Before import process
     */
    protected function _beforeProcess()
    {
        $this->_existSKUs = $this->_getResourceUtilityModel()->getSKUs();
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
