<?php
require_once rtrim(Mage::getModuleDir('controllers', 'Mage_Wishlist'), DS) . DS . 'IndexController.php';

class Admitad_Setup_IndexController extends Mage_Wishlist_IndexController
{
    /**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function cartAction()
    {
        $itemId = (int)$this->getRequest()->getParam('item');
        /* @var $item Mage_Wishlist_Model_Item */
        $item = Mage::getModel('wishlist/item')->load($itemId);
        if (!$item->getId()) {
            return $this->_redirect('*/*');
        }

        return $this->_redirectUrl(
            Mage::getResourceModel('catalog/product')->getAttributeRawValue(
                $item->getProductId(), Admitad_Setup_Helper_Data::URL_ATTRIBUTE_CODE, Mage::app()->getStore()
            )
        );
    }
}
