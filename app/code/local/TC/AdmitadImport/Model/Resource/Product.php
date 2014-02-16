<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Model_Resource_Product extends Mage_Catalog_Model_Resource_Product
{
    const BATCH_SIZE = 1000;

    /**
     * Get SKUs for all existed products
     *
     * @return array
     */
    public function getSKUs()
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/product'), array('sku', 'entity_id'));

        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Update status for given SKUs
     *
     * @param array $skus
     * @param int   $value
     *
     * @throws Exception
     */
    public function updateStatusAttributeValue(array $skus, $value)
    {
        /* @var $action Mage_Catalog_Model_Product_Action */
        $object = new Varien_Object();
        $object->setIdFieldName('entity_id');

        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getEntityTable(), 'entity_id')
            ->where('sku IN (?)', $skus);
        $ids     = $adapter->fetchCol($select);

        $adapter = $this->_getWriteAdapter();
        $adapter->beginTransaction();
        try {
            $attribute = $this->getAttribute('status');

            $i = 0;
            foreach ($ids as $id) {
                $i++;
                $object->setId($id);
                // collect data for save
                $this->_saveAttributeValue($object, $attribute, $value);
                // save collected data every 1000 rows
                if ($i % self::BATCH_SIZE == 0) {
                    $this->_processAttributeValues();
                }
            }
            $this->_processAttributeValues();
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }
    }
}
