<?php

/**
 * @category   TC
 * @package    TC_ProductSegmentation
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_ProductSegmentation_Helper_Data extends Mage_Core_Helper_Abstract
{
    const ACTION_NONE         = 'none';
    const ACTION_DIFFERENCE   = 'difference';
    const ACTION_INTERSECTION = 'intersection';

    const SEGMENT_DATA_ATTRIBUTE_CODE = 'segment_data';

    /**
     * Retrieve segment definition from category object
     *
     * @param Mage_Catalog_Model_Category $category
     *
     * @return array
     */
    public function getCategorySegmentData(Mage_Catalog_Model_Category $category)
    {
        $data = $category->getData(self::SEGMENT_DATA_ATTRIBUTE_CODE);
        $rule = array();
        parse_str($data, $rule);

        return ($rule && isset($rule['rule'])) ? $rule['rule'] : array();
    }

    /**
     * Apply conditions to collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @param TC_ProductSegmentation_Model_Rule              $rule
     * @param string                                         $condition
     */
    public function applyConditions(
        Mage_Catalog_Model_Resource_Product_Collection $collection,
        TC_ProductSegmentation_Model_Rule $rule,
        $condition
    ) {
        $select = $collection->getSelect();

        $conditionSelect = $this->_buildConditionSelect($rule);
        if ($condition === self::ACTION_DIFFERENCE) {
            $select->where('entity_id NOT IN (?)', new Zend_Db_Expr($conditionSelect->assemble()));
        } else {
            $select->where('entity_id IN (?)', new Zend_Db_Expr($conditionSelect->assemble()));
        }
    }

    /**
     * Build select statement based on rule model
     *
     * @param TC_ProductSegmentation_Model_Rule $rule
     *
     * @return \Varien_Db_Select
     */
    private function _buildConditionSelect(TC_ProductSegmentation_Model_Rule $rule)
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        $select     = $collection->getSelect();
        $select->reset(Varien_Db_Select::COLUMNS);
        $select->columns(array($collection->getEntity()->getEntityIdField()));

        $where = $rule->getConditions()->prepareConditionSql();
        if (!empty($where)) {
            $select->where($where);
        }

        Mage::helper('tc_productsegmentation/eav')->ensureAttributesJoined($collection);

        return $select;
    }
}
