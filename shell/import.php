<?php

require_once 'abstract.php';

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_Shell_Import extends Mage_Shell_Abstract
{
    /**
     * Run script
     */
    public function run()
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        Mage::setIsDeveloperMode(true);

        if ($this->getArg('run')) {
            Mage::getModel('tc_admitadimport/observer')->import();
        } elseif ($this->getArg('image')) {
            Mage::getModel('tc_admitadimport/observer')->importImages($this->getArg('filename'));
        } elseif ($this->getArg('pool')) {
            Mage::getModel('tc_admitadimport/observer')->runImagesProcessesPool();
        } else {
            echo $this->usageHelp() . PHP_EOL;
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     * @return string
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f import.php [options]
        php -f import.php -- run
        php -f import.php -- pool
        php -f import.php -- image --filename FILENAME

  run               Run whole import process
  help              This help
USAGE;
    }
}

$shell = new TC_Shell_Import();
$shell->run();
