<?php

class Itdelight_Metadata_Block_Adminhtml_Metadata extends Mage_Adminhtml_Block_Widget_Container{
 
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('metadata/seo.phtml');
    }
    public function _prepareLayout() {
        $this->_addButton('create_new', array(
            'label' => Mage::helper('metadata')->__('Add Metadata'),
            'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
            'class' => 'add',
        ));
            
        $this->setChild('grid', $this->getLayout()->createBlock('metadata/adminhtml_metadata_grid', 'metadata.grid'));

        parent::_prepareLayout();
    }
     public function getGridHtml(){
        return $this->getChildHtml('grid');
    }
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
               return false;
        }
        return true;
    }
}
