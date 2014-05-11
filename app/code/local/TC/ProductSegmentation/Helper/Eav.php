<?php

/**
 * @category   TC
 * @package    TC_ProductSegmentation
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_ProductSegmentation_Helper_Eav extends Mage_Core_Helper_Abstract
{
    const CATEGORY_TABLE_ALIAS = 'cpp';

    /** @var array */
    protected $_attributeTableAliasMap = array();

    /** @var Mage_Eav_Model_Entity_Abstract */
    protected $_entityModelInstance;

    /** @var bool */
    protected $_joinCategories = false;

    /**
     * Reset helper state
     */
    public function reset()
    {
        $this->_attributeTableAliasMap = array();
        $this->_joinCategories         = false;
    }

    /**
     * Returns unique attribute alias
     *
     * @param string $attributeCode
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function getAttributeAlias($attributeCode)
    {
        if ($attributeCode === 'category_id') {
            $this->_joinCategories = true;
            return sprintf('%s.%s', self::CATEGORY_TABLE_ALIAS, $attributeCode);
        }

        $entity    = $this->_getEntityModel();
        $attribute = $entity->getAttribute($attributeCode);
        if (!$attribute) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid attribute type'));
        }

        if ($attribute->getBackend()->isStatic()) {
            return sprintf('e.%s', $attributeCode);
        }

        if (!isset($this->_attributeTableAliasMap[$attributeCode])) {
            $alias = preg_replace('#[^a-z0-9]+#i', '', $attributeCode) . mt_rand(1000, 10000);
            $this->_attributeTableAliasMap[$attributeCode] = $alias;
        }

        return sprintf('%s.value', $this->_attributeTableAliasMap[$attributeCode]);
    }

    /**
     * Join attributes to collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     */
    public function ensureAttributesJoined(Mage_Catalog_Model_Resource_Product_Collection $collection)
    {
        $entity    = $this->_getEntityModel();
        foreach ($this->_attributeTableAliasMap as $attributeCode => $alias) {
            $attribute = $entity->getAttribute($attributeCode);

            if (!$attribute->getBackend()->isStatic()) {
                $condition = array(
                    sprintf('e.`%s` = `%s`.`%s`', $entity->getEntityIdField(), $alias, $attribute->getEntityIdField()),
                    sprintf('`%s`.`attribute_id` = %d', $alias, $attribute->getId())
                );
                $collection->getSelect()->joinLeft(
                    array($alias => $attribute->getBackend()->getTable()),
                    '(' . implode(') AND (', $condition) . ')',
                    array()
                );
            }
        }

        if ($this->_joinCategories === true) {
            $tableName = $collection->getTable('catalog/category_product');
            $collection->getSelect()->joinLeft(
                array(self::CATEGORY_TABLE_ALIAS => $tableName),
                sprintf('`e`.`%s` = `%s`.`product_id`', $entity->getEntityIdField(), self::CATEGORY_TABLE_ALIAS),
                array()
            );
        }

        $this->reset();
    }

    /**
     * Returns entity model instance
     *
     * @return Mage_Eav_Model_Entity_Abstract
     */
    protected function _getEntityModel()
    {
        if ($this->_entityModelInstance === null) {
            $this->_entityModelInstance = Mage::getModel('eav/entity')->setType('catalog_product');
        }

        return $this->_entityModelInstance;
    }
}
