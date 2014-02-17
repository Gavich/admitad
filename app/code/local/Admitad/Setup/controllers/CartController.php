<?php
require_once rtrim(Mage::getModuleDir('controllers', 'Mage_Checkout'), DS) . DS . 'CartController.php';

class Admitad_Setup_CartController extends Mage_Checkout_CartController
{
    /**
     * Disallow cart, just redirect to homepage
     */
    public function indexAction()
    {
        $this->_redirect('/');
    }
}
