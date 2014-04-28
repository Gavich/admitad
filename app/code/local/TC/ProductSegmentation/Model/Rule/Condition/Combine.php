<?php

/**
 * @category   TC
 * @package    TC_ProductSegmentation
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_ProductSegmentation_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('tc_productsegmentation/rule_condition_combine');
    }

    /**
     * Returns options needed to build "New child" in connditions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productCondition  = Mage::getModel('tc_productsegmentation/rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $attributes        = array();
        foreach ($productAttributes as $code => $label) {
            $attributes[] = array(
                'value' => 'tc_productsegmentation/rule_condition_product|' . $code,
                'label' => $label
            );
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
                  array(
                      'value' => 'tc_productsegmentation/rule_condition_combine',
                      'label' => Mage::helper('catalogrule')->__('Conditions Combination')
                  ),
                  array(
                      'value' => $attributes,
                      'label' => Mage::helper('catalogrule')->__('Product Attribute')
                  ),
             )
        );

        return $conditions;
    }
}
