<?php

class Itdelight_Metadata_Block_Adminhtml_Metadata_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('product_form');
        $this->setTitle(Mage::helper('metadata')->__('Product Metadata Information'));
       
    }
    protected function get_categories(){

    $category = Mage::getModel('catalog/category'); 
    $tree = $category->getTreeModel(); 
    $tree->load();
    $ids = $tree->getCollection()->getAllIds(); 
    $arr = array();
    if ($ids){ 
    foreach ($ids as $id){ 
    $cat = Mage::getModel('catalog/category');
    $cat->load($id);
 
    $arr[$id] = $cat->getName();
    
   }
    }

    return $arr;

}

    protected function _prepareForm()
    {
 
        
 
        $form = new Varien_Data_Form();
        $this->setForm($form);
 
        $fieldset = $form->addFieldset('product_form', array(
             'legend' =>Mage::helper('metadata')->__('Product Metadata Information')
        ));
 
 $fieldset->addField('is_enabled', 'checkbox', array(
    'label'     => Mage::helper('metadata')->__('Set to all categories'),
    'onclick'   => 'this.value = this.checked ? 1 : 0;',
    'name'      => 'is_enabled',
     'note' => 'Set to all categories?'
));
        $fieldset->addField('product_title', 'text', array(
             'label'     => Mage::helper('metadata')->__('Title'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'product_title',
             'note'     => Mage::helper('metadata')->__('The title'),
        ));
 
        $fieldset->addField('product_description', 'textarea', array(
             'label'     => Mage::helper('metadata')->__('Description'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'product_description',
        ));
 
        $fieldset->addField('product_keywords', 'textarea', array(
             'label'     => Mage::helper('metadata')->__('Keywords'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'product_keywords',
        ));
       
     $fieldset->addField('cat_select', 'select', array(
      'label'     => 'Category',
      'class'     => 'required-entry',
      'required'  => true,
      'name'      => 'products_category',
      'values' => $this->get_categories(),
      'disabled' => false,
      'readonly' => false,
      'tabindex' => 1
    ));
     
        $fieldset->addField('products', 'text', array(
             'label'     => Mage::helper('metadata')->__('Products'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'products',
             'note'     => Mage::helper('metadata')->__('Enter products id here'),
        ));
        
        $form->setForm($form);
 
        return parent::_prepareForm();
    }



}
