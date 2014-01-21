<?php
/**
 * Medusa for Magento 1.7.0.0
 * Design and Development by creative-d2 design&development (http://www.creative-d2.de)
 * Distributed by ThemeForest (http://themeforest.net)
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @author     Ömer Bildirici jun.
 * @package    medusa_default
 * @copyright  Copyright 2012 Ömer Bildirici jun. (http://www.creative-d2.de)
 * @license    All rights reserved.
 * @version    1.1
 */
class CDD_FlyoutMenu_Admin_ChooserController extends Mage_Adminhtml_Controller_Action
{
    public function chooserAction()
    {
        $this->getResponse()->setBody(
            $this->_getCategoryTreeBlock()->toHtml()
        );
    }

   
	public function categoriesJsonAction()
    {
        if ($categoryId = (int) $this->getRequest()->getPost('id')) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            if ($category->getId()) {
                Mage::register('category', $category);
                Mage::register('current_category', $category);
            }
            $this->getResponse()->setBody(
                $this->_getCategoryTreeBlock()->getTreeJson($category)
            );
        }
    }
    
    protected function _getCategoryTreeBlock()
    {
        return $this->getLayout()->createBlock('FlyoutMenu/chooser', '', array(
            'id' => $this->getRequest()->getParam('uniq_id'),
            'use_massaction' => false,//$this->getRequest()->getParam('use_massaction', false)
        ));
    }
}