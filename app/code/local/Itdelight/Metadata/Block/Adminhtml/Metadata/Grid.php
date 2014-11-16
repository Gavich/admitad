<?php

class Itdelight_Metadata_Block_Adminhtml_Metadata_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
     public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('id');
        $this->setId('metadata');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('metadata_filter');
    }
     
    protected function _prepareCollection()
    {
       
        $collection=Mage::getModel('metadata/metadata')->getCollection();
        $this->setCollection($collection);
         
        return parent::_prepareCollection();
    }
     
    protected function _prepareColumns()
    {
         $this->addColumn('metadata_id',
            array(
                'header'=> Mage::helper('metadata')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'metadata_id',
        ));
        $this->addColumn('title',
            array(
                'header'=> $this->__('Title'),
                'index' => 'title'
            )
        );
        $this->addColumn('description',
            array(
                'header'=> $this->__('Description'),
                'index' => 'description'
            )
        );
          $this->addColumn('keywords',
            array(
                'header'=> $this->__('Keywords'),
                'index' => 'keywords'
            )
        );
          $this->addColumn('category_id',
            array(
                'header'=> $this->__('Category'),
                'index' => 'category_id'
            )
        );
          $this->addColumn('products',
            array(
                'header'=> $this->__('Products'),
                'index' => 'products'
            )
        );
          $this->addColumn('categories',
            array(
                'header'=> $this->__('Categories'),
                'index' => 'categories'
            )
        );
           
          
           $this->addColumn('action',
            array(
                'header'    => Mage::helper('metadata')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('metadata')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));
        return parent::_prepareColumns();
    }
     
     protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('metadata');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('metadata')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('metadata')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('metadata')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('metadata')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
      
        return $this;
    }
        public function getGridUrl()
    {
        return $this->getUrl('*/*/gridonly', array('_current'=>true));
    }
    
    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
