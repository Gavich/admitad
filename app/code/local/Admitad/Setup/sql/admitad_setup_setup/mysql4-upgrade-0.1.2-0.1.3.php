<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

Mage::app()->getConfig()->saveConfig('catalog/seo/product_use_categories', false);

$installer->endSetup();
