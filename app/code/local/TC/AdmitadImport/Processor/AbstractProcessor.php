<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
abstract class TC_AdmitadImport_Processor_AbstractProcessor
    implements TC_AdmitadImport_Processor_ProcessorInterface, TC_AdmitadImport_Logger_LoggerAwareInterface
{
    /** @var TC_AdmitadImport_Logger_LoggerInterface */
    private $_logger;

    /**
     * Inject the logger
     *
     * @param TC_AdmitadImport_Logger_LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(TC_AdmitadImport_Logger_LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * Getter for logger
     *
     * @return TC_AdmitadImport_Logger_LoggerInterface
     */
    protected function _getLogger()
    {
        return $this->_logger;
    }

    /**
     * Before process preparing
     */
    protected function _beforeProcess()
    {
        /* @var $indexer Mage_Index_Model_Indexer */
        $indexer   = Mage::getSingleton('index/indexer');
        $processes = $indexer->getProcessesCollection();
        $processes->walk('setMode', array(Mage_Index_Model_Process::MODE_MANUAL));
        $processes->walk('save');
    }

    /**
     * After process steps
     */
    protected function _afterProcess()
    {
        /* @var $indexer Mage_Index_Model_Indexer */
        $indexer   = Mage::getSingleton('index/indexer');
        $processes = $indexer->getProcessesCollection();

        $this->_getLogger()->log('Import finished. Starting reindex...');
        $processes->walk('setMode', array(Mage_Index_Model_Process::MODE_REAL_TIME));
        $processes->walk('save');
        $processes->walk('reindexAll');
    }
}
