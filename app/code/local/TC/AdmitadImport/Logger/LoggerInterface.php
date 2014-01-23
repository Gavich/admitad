<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
interface TC_AdmitadImport_Logger_LoggerInterface
{
    /**
     * Log a message at a priority
     *
     * @param string  $message  Message to log
     * @param integer $priority Priority of message
     *
     * @return void
     */
    public function log($message, $priority);
}
