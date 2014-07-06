<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Model_Observer
{
    /**
     * Runs import process
     *
     * @return void
     */
    public function import()
    {
        /*
         * Possible scenario of extendability for multiple imports
         * Is to define job chains n config.xml and also pass reader and logger configuration there
         */
        /** @var TC_AdmitadImport_Helper_Data $helper */
        $helper = Mage::helper('tc_admitadimport');

        $defaultLogger    = $helper->getDefaultLogger();
        $configuredSource = $helper->getSource();
        $reader           = $helper->getDefaultReader();

        try {
            $importChain = $helper->getImportProcessorChain();
            $importChain->setLogger($defaultLogger);

            $importChain->process($reader->read($configuredSource));
        } catch (Exception $e) {
            $defaultLogger->log($e->getMessage(), Zend_Log::CRIT);
        }
    }

    /**
     * Process image import async task
     *
     * @param string $filename
     */
    public function importImages($filename)
    {
        /** @var TC_AdmitadImport_Helper_Data $helper */
        $helper        = Mage::helper('tc_admitadimport');
        $defaultLogger = $helper->getDefaultLogger();
        /** @var TC_AdmitadImport_Helper_Images $helperImages */
        $helperImages = Mage::helper('tc_admitadimport/images');
        $helperImages->setLogger($defaultLogger);

        $filename = $helperImages->initFromFile($filename);
        $helperImages->setAsync(false);
        $helperImages->processImages();

        if (is_file($filename)) {
            unlink($filename);
        }
    }

    /**
     * Run process management tool
     */
    public function runImagesProcessesPool()
    {
        /** @var TC_AdmitadImport_Helper_Data $importHelper */
        $importHelper  = Mage::helper('tc_admitadimport');
        $defaultLogger = $importHelper->getDefaultLogger();
        /* @var $helper TC_AdmitadImport_Helper_ProcessPool */
        $helper = Mage::helper('tc_admitadimport/processPool');
        if ($helper instanceof TC_AdmitadImport_Logger_LoggerAwareInterface) {
            $helper->setLogger($defaultLogger);
        }
        $helper->run();
    }
} 
