<?php

error_reporting(E_ALL | E_STRICT);

require_once './app/Mage.php';

Mage::setIsDeveloperMode(true);
Mage::app('admin');

Mage::getModel('tc_admitadimport/observer')->import();
