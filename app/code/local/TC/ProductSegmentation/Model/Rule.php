<?php

/**
 * @category   TC
 * @package    TC_ProductSegmentation
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_ProductSegmentation_Model_Rule extends Mage_Rule_Model_Abstract
{
    /**
     * Getter for rule combine conditions instance
     *
     * @return Mage_Rule_Model_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('tc_productsegmentation/rule_condition_combine');
    }

    /**
     * Getter for rule actions collection instance
     *
     * @throws LogicException
     */
    public function getActionsInstance()
    {
        throw new LogicException('Actions are not supported');
    }
}
