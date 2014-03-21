<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

Mage::app()->getConfig()->saveConfig('custom_menu/general/enabled', true);
Mage::app()->getConfig()->saveConfig('custom_menu/general/ajax_load_content', true);
Mage::app()->getConfig()->saveConfig('custom_menu/general/max_level', 3);
Mage::app()->getConfig()->saveConfig('custom_menu/general/show_home_link', false);
Mage::app()->getConfig()->saveConfig('custom_menu/general/non_breaking_space', false);
Mage::app()->getConfig()->saveConfig('custom_menu/general/ie6_ignore', false);
Mage::app()->getConfig()->saveConfig('custom_menu/general/rtl', false);
Mage::app()->getConfig()->saveConfig('custom_menu/columns/count', 4);
Mage::app()->getConfig()->saveConfig('custom_menu/columns/divided_horizontally', true);
Mage::app()->getConfig()->saveConfig('custom_menu/columns/integrate', false);
Mage::app()->getConfig()->saveConfig('custom_menu/popup/width', 0);
Mage::app()->getConfig()->saveConfig('custom_menu/popup/top_offset', 0);
Mage::app()->getConfig()->saveConfig('custom_menu/popup/delay_displaying', 0);
Mage::app()->getConfig()->saveConfig('custom_menu/popup/delay_hiding', 0);
Mage::app()->getConfig()->saveConfig('custom_menu/general/move_code_to_bottom', 1);

$installer->endSetup();
