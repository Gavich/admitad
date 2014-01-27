<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Processor_Chain extends TC_AdmitadImport_Processor_AbstractProcessor
{
    /** @var array|TC_AdmitadImport_Processor_ProcessorInterface[] */
    private $_processors = array();

    /**
     * Add processor to chain
     *
     * @param TC_AdmitadImport_Processor_ProcessorInterface $processor
     */
    public function addProcessor(TC_AdmitadImport_Processor_ProcessorInterface $processor)
    {
        $this->_processors[] = $processor;
    }

    /**
     * Performs import
     *
     * @param TC_AdmitadImport_Reader_DataInterface $data
     *
     * @return void
     */
    public function process(TC_AdmitadImport_Reader_DataInterface $data)
    {
        foreach ($this->_processors as $processor) {
            if ($processor instanceof TC_AdmitadImport_Logger_LoggerAwareInterface) {
                $processor->setLogger($this->_getLogger());
            }

            $processor->process($data);
        }
    }
}
