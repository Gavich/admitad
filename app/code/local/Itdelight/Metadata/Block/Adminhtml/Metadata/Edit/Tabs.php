<?php

class Itdelight_Metadata_Block_Adminhtml_Metadata_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
     public function __construct()
    {
        parent::__construct();
        $this->setId('tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('metadata')->__('Metadata Information'));
    }
     protected function _beforeToHtml()
  {
         
      $this->addTab('category_section', array(
          'label'     => Mage::helper('metadata')->__('Category Information'),
          'title'     => Mage::helper('metadata')->__('Category  Information'),
          'content'   => $this->getLayout()->createBlock('metadata/adminhtml_metadata_edit_tab_category')->toHtml(),
      ));
      
      $this->addTab('categories', array(
                'label'     => Mage::helper('metadata')->__('Categories'),
                'url'       => $this->getUrl('*/*/categories', array('_current' => true)),
                'class'     => 'ajax',
            ));

      return parent::_beforeToHtml();
  }
}

