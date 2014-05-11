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

    /**
     * Prepare sql where by condition
     *
     * @return string
     */
    public function prepareConditionSql()
    {
        $attribute = $this->getAttribute();
        $value     = $this->getValue();
        $operator  = $this->correctOperator($this->getOperator(), $this->getInputType());
        if ($attribute == 'category_ids') {
            $attribute = 'category_id';
            $value     = $this->bindArrayOfIds($value);
        }

        if (in_array($this->getOperator(), array('()', '!()')) && !is_array($value)) {
            $value = explode(',', $value);
            $value = array_map('trim', $value);
        }

        $alias = Mage::helper('tc_productsegmentation/eav')->getAttributeAlias($attribute);
        /** @var $ruleResource Mage_Rule_Model_Resource_Rule_Condition_SqlBuilder */
        $ruleResource = $this->getRuleResourceHelper();

        return $ruleResource->getOperatorCondition($alias, $operator, $value);
    }
}
