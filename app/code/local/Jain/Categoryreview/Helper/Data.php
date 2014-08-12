<?php

class Jain_Categoryreview_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getCatreview($catid='')
    {
        $collection = Mage::getModel('categoryreview/categoryreview')
            ->getCollection()
            ->addFilter('catid',$catid)
            ->addFilter('status', 2)
            ->toArray();
        $catreviews = $collection['items'];
        return $catreviews;
    }

    public function loadTemplate($obj)
    {
        $layer = Mage::getSingleton('catalog/layer');
        $_category = $layer->getCurrentCategory();
        $cou = 3;
        $_helper    = Mage::helper('catalog/output');
        $_category_name = $_category->getName();
        $catId	=	$_category->getId();
        $catUrl = $_category->getUrl();
        $reviews = Mage::helper('categoryreview')->getCatreview($catId);
        $reviews_count = count($reviews);
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customer_data['fullname'] = $customer->getName();
            $customer_data['email'] = $customer->getEmail();
            $customer_data['id'] = $customer->getId();
    }
        require Mage::getBaseDir('design').DS.$obj->setTemplate('categoryreview'.DS.'categoryreview.phtml')->getTemplateFile();
    }

}