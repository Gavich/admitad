<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Model_Resource_Category extends Mage_Catalog_Model_Resource_Category
{
    const BATCH_SIZE = 100;

    /**
     * Returns categories id map array(ORIGIN_ID => CATEGORY_ID)
     *
     * @return array
     */
    public function getCategoriesIdMap()
    {
        $_originAttributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode(
            Mage_Catalog_Model_Category::ENTITY, TC_AdmitadImport_Processor_Categories::ORIGIN_ID_ATTRIBUTE_CODE
        );
        /* @var $_originAttribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $_originAttribute = Mage::getModel('catalog/resource_eav_attribute')->load($_originAttributeId);

        /* @var $coreResource Mage_Core_Model_Resource */
        $coreConnection = $this->_getReadAdapter();

        $select = $coreConnection->select();
        $select->from(array('cc' => $this->getTable('catalog/category')), array('cc.entity_id'));
        $select->join(
            array('a' => $_originAttribute->getBackendTable()), 'a.entity_id=cc.entity_id', array('a.value')
        );
        $select->where('a.attribute_id =?', $_originAttribute->getId());
        $select->where('a.value IS NOT NULL');

        $result = $coreConnection->fetchPairs($select);

        return array_flip($result);
    }

    /**
     * Update IsActive attribute value for categories batch
     *
     * @param array $categories
     * @param bool  $value
     *
     * @throws Exception
     */
    public function updateVisibilityAttributeValue(array $categories, $value)
    {
        $object = new Varien_Object();
        $object->setIdFieldName('entity_id');

        $this->_getWriteAdapter()->beginTransaction();
        try {
            $attribute = $this->getAttribute($this->_getIsActiveAttributeId());

            $i = 0;
            foreach ($categories as $category) {
                $i++;
                $object->setId($category);
                // collect data for save
                $this->_saveAttributeValue($object, $attribute, $value);
                // save collected data every 1000 rows
                if ($i % self::BATCH_SIZE == 0) {
                    $this->_processAttributeValues();
                }
            }
            $this->_processAttributeValues();
            $this->_getWriteAdapter()->commit();
        } catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        }
    }
}
