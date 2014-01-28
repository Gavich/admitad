<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
interface TC_AdmitadImport_Processor_ProcessorInterface
{
    /**
     * Performs import
     *
     * @param TC_AdmitadImport_Reader_DataInterface $data
     *
     * @return void
     */
    public function process(TC_AdmitadImport_Reader_DataInterface $data);
}
