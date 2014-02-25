<?php

class Admitad_Setup_Helper_Checkout_Cart extends Mage_Checkout_Helper_Cart
{
// @codingStandardsIgnoreStart
    /**
     * Retrieve url for add product to cart
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array                      $additional
     *
     * @return string
     */
    public function getAddUrl($product, $additional = array())
    {
        return Mage::getResourceModel('catalog/product')->getAttributeRawValue(
            $product->getId(), Admitad_Setup_Helper_Data::URL_ATTRIBUTE_CODE, Mage::app()->getStore()
        );
    }
// @codingStandardsIgnoreEnd
}
