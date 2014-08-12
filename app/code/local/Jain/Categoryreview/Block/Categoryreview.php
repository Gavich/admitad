<?php
class Jain_Categoryreview_Block_Categoryreview extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCategoryreview()     
     { 
        if (!$this->hasData('categoryreview')) {
            $this->setData('categoryreview', Mage::registry('categoryreview'));
        }
        return $this->getData('categoryreview');
        
    }
}