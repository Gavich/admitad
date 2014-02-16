<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_SOURCE_PATH = 'tc_admitadimport/settings/source';

    /**
     * Returns default logger
     *
     * @return TC_AdmitadImport_Logger_LoggerInterface
     */
    public function getDefaultLogger()
    {
        return $this->getLogger('tc_admitadimport/file');
    }

    /**
     * Returns default reader object
     *
     * @return TC_AdmitadImport_Reader_ReaderInterface
     */
    public function getDefaultReader()
    {
        return $this->getReader('tc_admitadimport/xml');
    }

    /**
     * Returns configured chain processor prototype
     *
     * @return TC_AdmitadImport_Processor_Chain
     */
    public function getImportProcessorChain()
    {
        /** @var TC_AdmitadImport_Processor_Chain $processor */
        $processor = $this->getProcessorPrototype('tc_admitadimport/chain');

        // if needed possibility to configure multiple job's chains
        // load configuration could be processed here
        $processor->addProcessor($this->getProcessorPrototype('tc_admitadimport/categories'));
        $processor->addProcessor($this->getProcessorPrototype('tc_admitadimport/products'));

        return $processor;
    }

    /**
     * Get source file from configuration
     *
     * @return Mage_Core_Model_Config_Element
     */
    public function getSource()
    {
        return Mage::getStoreConfig(self::CONFIG_SOURCE_PATH);
    }

    /**
     * Get logger instance by short name
     *
     * @param string $name
     *
     * @return TC_AdmitadImport_Logger_LoggerInterface
     */
    public function getLogger($name)
    {
        $registryKey = '_logger/' . $name;
        if (!Mage::registry($registryKey)) {
            $loggerClass = Mage::getConfig()->getGroupedClassName('logger', $name);
            Mage::register($registryKey, new $loggerClass);
        }

        return Mage::registry($registryKey);
    }

    /**
     * Get reader instance by short name
     *
     * @param string $name
     *
     * @return TC_AdmitadImport_Logger_LoggerInterface
     */
    public function getReader($name)
    {
        $registryKey = '_reader/' . $name;
        if (!Mage::registry($registryKey)) {
            $readerClass = Mage::getConfig()->getGroupedClassName('reader', $name);
            Mage::register($registryKey, new $readerClass);
        }

        return Mage::registry($registryKey);
    }

    /**
     * Returns processor by short name, it's always new instance
     *
     * @param string $name
     *
     * @return TC_AdmitadImport_Processor_ProcessorInterface
     */
    public function getProcessorPrototype($name)
    {
        $processorClass = Mage::getConfig()->getGroupedClassName('processor', $name);

        return new $processorClass;
    }
}
