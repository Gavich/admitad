<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Model_Resource_Product extends Mage_Catalog_Model_Resource_Product
{
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
}
