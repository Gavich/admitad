<?php

class Jain_Categoryreview_Block_Adminhtml_Categoryreview_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);	  
	  $reviewdetail	=	Mage::registry('categoryreview_data')->getData();
	  $custid	=	(isset($reviewdetail['custid']))? $reviewdetail['custid'] : null;
	  //for category name and url
	  $catid	=	$reviewdetail['catid'];
	  $customer = Mage::getModel('customer/customer')->load($custid);  
	  $category = Mage::getModel('catalog/category')->load($catid); 
	  $caturl	= $category->getUrl();
	  $catname	= $category->getName();
	  $cattext	= Mage::helper('categoryreview')->__('<a href="%1$s" target="_blank">%2$s</a>',$caturl,$catname);
	  
      $fieldset = $form->addFieldset('categoryreview_form', array('legend'=>Mage::helper('categoryreview')->__('Review information')));

      $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
      $fieldset->addField('created_at', 'date', array(
          'name'   => 'created_at',
          'class'     => 'required-entry',
          'required'  => true,
          'label'  => Mage::helper('catalogrule')->__('Date'),
          'title'  => Mage::helper('catalogrule')->__('Date'),
          'image'  => $this->getSkinUrl('images/grid-cal.gif'),
          'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
          'format'       => 'yyyy-MM-dd ',
          'value' => 'created_at',
      ));

      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('categoryreview')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
		if ($customer->getId()) {
            $customerText = Mage::helper('categoryreview')->__('%1$s', $this->htmlEscape($customer->getName()));
            $email = $this->htmlEscape($customer->getEmail());
            $nickname = $this->htmlEscape($customer->getName());
        } else {
            if (is_null($form->getCustid())) {
                $customerText = Mage::helper('categoryreview')->__('Guest');
            } elseif ($form->getCustid() == 0) {
                $customerText = Mage::helper('categoryreview')->__('Administrator');
            }
            $email = Mage::helper('categoryreview')->__('<a href="mailto:%1$s">%1$s</a>', $reviewdetail['email']);
            $nickname = $reviewdetail['nickname'];
        }
	 $fieldset->addField('custid', 'note', array(
            'label'     => Mage::helper('categoryreview')->__('Posted By'),
            'text'      => $customerText,
        ));
      if(isset($email)){
          $fieldset->addField('email', 'note', array(
              'label'     => Mage::helper('categoryreview')->__('E-mail'),
              'text'      => $email,
          ));
      }
      $fieldset->addField('catid', 'note', array(
            'label'     => Mage::helper('categoryreview')->__('Category Name'),
            'text'      => $cattext,
        ));
      $fieldset->addField('nickname', 'text', array(
          'label'     => Mage::helper('categoryreview')->__('Nickname'),
          'class'     => 'required-entry',
          'required'  => true,
          'text'      => $nickname,
          'name'      => 'nickname',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('categoryreview')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('categoryreview')->__('Not Approved'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('categoryreview')->__('Approved'),
              ),
          ),
      ));

      $fieldset->addField('rating_value', 'select', array(
          'label'     => Mage::helper('categoryreview')->__('Rating'),
          'name'      => 'rating_value',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('categoryreview')->__('1 Звезда'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('categoryreview')->__('2 Звезды'),
              ),
              array(
                  'value'     => 3,
                  'label'     => Mage::helper('categoryreview')->__('3 Звезды'),
              ),
              array(
                  'value'     => 4,
                  'label'     => Mage::helper('categoryreview')->__('4 Звезды'),
              ),
              array(
                  'value'     => 5,
                  'label'     => Mage::helper('categoryreview')->__('5 Звезд'),
              ),
          ),
      ));
    

      $fieldset->addField('detail', 'editor', array(
          'name'      => 'detail',
          'label'     => Mage::helper('categoryreview')->__('Detail'),
          'title'     => Mage::helper('categoryreview')->__('detail'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getCategoryreviewData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCategoryreviewData());
          Mage::getSingleton('adminhtml/session')->setCategoryreviewData(null);
      } elseif ( Mage::registry('categoryreview_data') ) {
          $form->setValues(Mage::registry('categoryreview_data')->getData());
      }
      return parent::_prepareForm();
  }
}