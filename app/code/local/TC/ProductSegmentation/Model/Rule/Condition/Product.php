<?php

/**
 * @category   TC
 * @package    TC_ProductSegmentation
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_ProductSegmentation_Model_Rule_Condition_Product extends Mage_CatalogRule_Model_Rule_Condition_Product
{
    /**
     * Load attribute options
     *
     * @return Mage_CatalogRule_Model_Rule_Condition_Product
     */
    public function loadAttributeOptions()
    {
        $productAttributes = Mage::getResourceSingleton('catalog/product')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();
        $helper     = Mage::helper('catalog');
        foreach ($productAttributes as $attribute) {
            $label = $attribute->getFrontendLabel();

            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if (empty($label)) {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $helper->__($label);
        }

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }
}
