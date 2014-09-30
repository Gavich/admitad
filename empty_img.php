<?php
/**
 * Created by PhpStorm.
 * User: User Home
 * Date: 27.09.14
 * Time: 11:49
 */
include './app/Mage.php';
$mage = Mage::app();
Mage::getSingleton('core/session', array('name'=>'adminhtml'));


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

$productsEmptyImage = array_diff($productsAll,$productsIssetImage);

echo count($productsEmptyImage);

/*$storeId = Mage::app()->getStore()->getId();

Mage::getSingleton('catalog/product_action')
    ->updateAttributes($productsEmptyImage, array('status' => 2), $storeId);

Mage::getResourceModel('tc_cleanup/cleanup')->cleanUpProducts();*/
