<?php

class Itdelight_Metadata_Model_Mysql4_Metadata_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    public function _construct() {
        $this->_init('metadata/metadata');
        }
}
