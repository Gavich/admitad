<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Model_System_Config_Backend_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH          = 'crontab/jobs/tc_admitadimport_import_job/schedule/cron_expr';
    const CRON_MODEL_PATH_TIME      = 'tc_admitadimport/schedule/time';
    const CRON_MODEL_PATH_FREQUENCY = 'tc_admitadimport/schedule/frequency';
    const CRON_MODEL_PATH_ENABLED   = 'tc_admitadimport/schedule/enabled';

    /**
     * Cron settings after save
     *
     * @return \Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        $cronExprString = '';
        $configModel    = Mage::getModel('core/config_data');

        $isEnabled = (bool)$configModel->load(self::CRON_MODEL_PATH_ENABLED, 'path')->getValue();
        if ($isEnabled) {
            $time      = explode(',', $configModel->load(self::CRON_MODEL_PATH_TIME, 'path')->getValue());
            $frequency = $configModel->load(self::CRON_MODEL_PATH_FREQUENCY, 'path')->getValue();

            if ($frequency == Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY) {
                $frequencyInt = 7;
            } elseif ($frequency == Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY) {
                $frequencyInt = 30;
            } else {
                $frequencyInt = 1; // assume daily
            }

            $cronExprString = sprintf('%d %d */%d * *', $time[1], $time[0], $frequencyInt);
        }

        Mage::getModel('core/config_data')
            ->load(self::CRON_STRING_PATH, 'path')
            ->setValue($cronExprString)
            ->setPath(self::CRON_STRING_PATH)
            ->save();

        return parent::_afterSave();
    }
}
