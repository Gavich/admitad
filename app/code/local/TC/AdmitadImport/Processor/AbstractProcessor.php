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
    protected $_logger;

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
}
