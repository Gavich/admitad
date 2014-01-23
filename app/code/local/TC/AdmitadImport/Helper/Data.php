<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Helper_Data extends Mage_Core_Helper_Abstract
{
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
     * Returns configured chain processor prototype
     *
     * @return TC_AdmitadImport_Processor_ProcessorInterface
     */
    public function getImportProcessorChain()
    {
        return $this->getProcessorPrototype('tc_admitadimport/chain');
    }

    public function getConfiguredSource()
    {
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
            $loggerClass = Mage::getConfig()->getGroupedClassName('helper', $name);
            Mage::register($registryKey, new $loggerClass);
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
