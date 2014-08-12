<?php

class Jain_Categoryreview_Model_Categoryreview extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('categoryreview/categoryreview');
    }
}