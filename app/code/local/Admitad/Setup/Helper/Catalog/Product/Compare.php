<?php

class Admitad_Setup_Helper_Catalog_Product_Compare extends Mage_Catalog_Helper_Product_Compare
{
    /**
     * Retrieve add to cart url
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public function getAddToCartUrl($product)
    {
        return Mage::getResourceModel('catalog/product')->getAttributeRawValue(
            $product->getId(), Admitad_Setup_Helper_Data::URL_ATTRIBUTE_CODE, Mage::app()->getStore()
        );
    }
}
