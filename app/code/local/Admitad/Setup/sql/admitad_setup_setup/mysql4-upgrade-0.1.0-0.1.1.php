<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

Mage::app()->getConfig()->saveConfig('design/header/logo_src', 'images/cdd_btnhome.png');
Mage::app()->getConfig()->saveConfig('design/header/logo_alt', 'Интернет магазин одежды и обуви.');

$installer->endSetup();
