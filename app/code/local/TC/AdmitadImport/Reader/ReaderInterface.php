<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
interface TC_AdmitadImport_Reader_ReaderInterface
{
    /**
     * Read data from source
     *
     * @param mixed $source
     *
     * @return TC_AdmitadImport_Reader_DataInterface
     */
    public function read($source);
} 
