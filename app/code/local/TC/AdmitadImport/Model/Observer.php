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
        /** @var TC_AdmitadImport_Helper_Data $helper */
        $helper = Mage::helper('tc_admitadimport');

        $defaultLogger    = $helper->getDefaultLogger();
        $importChain      = $helper->getImportProcessorChain();
        $configuredSource = $helper->getConfiguredSource();

        $importChain->setLogger($defaultLogger);
        $importChain->process($configuredSource);
    }
} 
