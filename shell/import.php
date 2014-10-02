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
        } elseif ($this->getArg('delete')) {
            $productsIssetImage = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect(array('image'))
                ->addAttributeToFilter('status', array('eq' => 1))
                ->addAttributeToFilter('image', array('notnull' => true))
                ->load()
                ->getLoadedIds();

            $productsAll = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToFilter('status', array('eq' => 1))
                ->load()
                ->getLoadedIds();

            $productsEmptyImage = array_diff($productsAll, $productsIssetImage);

            $storeId = Mage::app()->getStore()->getId();

            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($productsEmptyImage, array('status' => 2), $storeId);

            Mage::getResourceModel('tc_cleanup/cleanup')->cleanUpProducts();

        } elseif($this->getArg('disable') || $this->getArg('enable')){
            $storeId = Mage::app()->getStore()->getId();
            $status = ($this->getArg('disable'))? 2 : 1;
            $search = ($status == 1)? 2 : 1;
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToFilter('status', array('eq' => (int)$search))
                ->load()
                ->getLoadedIds();

            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($products, array('status' => $status), $storeId);
        }else {
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

