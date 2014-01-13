<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

Mage::app()->getConfig()->saveConfig('design/package/name', 'exdress');

$installer->endSetup();
