<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Logger_File implements TC_AdmitadImport_Logger_LoggerInterface
{
    const LOG_FILENAME = 'tc_admitadimport';

    /**
     * Add message to log file
     *
     * @param string $message
     * @param int    $priority
     */
    public function log($message, $priority)
    {
        Mage::log($message, $priority, self::LOG_FILENAME . '.log');
    }
}
