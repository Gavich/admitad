<?php

class Itdelight_Metadata_Block_Adminhtml_Metadata_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

  public function __construct(){
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'metadata';
        $this->_controller = 'adminhtml_metadata';
        $this->_mode = 'edit';
       
        $this->_addButton('save_and_continue', array(
                  'label' => Mage::helper('metadata')->__('Save And Continue Edit'),
                  'onclick' => 'saveAndContinueEdit()',
                  'class' => 'save',
        ), -100);
      
        
        $this->_updateButton('save', 'label', Mage::helper('metadata')->__('Save Metadata'));
 
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }
 
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
  
    }
public function getMetadata()
    {
        return Mage::registry('current_metadata');
    }

     
    public function getHeaderText()
    {   
        if (Mage::registry('current_metadata')->getId()) {
            return Mage::helper('metadata')->__('Edit metadata');
        }   
        else {
            return Mage::helper('metadata')->__('Add new metadata'); 
           
        }   
    }
    
 public function getSelectedTabId()
    {
        return addslashes(htmlspecialchars($this->getRequest()->getParam('tab')));
    }
}