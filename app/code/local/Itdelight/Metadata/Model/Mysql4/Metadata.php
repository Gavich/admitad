<?php

class Itdelight_Metadata_Model_Mysql4_Metadata extends Mage_Core_Model_Mysql4_Abstract {
    public function _construct(){
        
        $this->_init('metadata/metadata','metadata_id');
    }
}
