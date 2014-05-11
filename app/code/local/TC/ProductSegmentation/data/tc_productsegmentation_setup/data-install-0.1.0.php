<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
/* @var $this Mage_Eav_Model_Entity_Setup */
$this->startSetup();

$this->addAttribute(
    Mage_Catalog_Model_Category::ENTITY,
    TC_ProductSegmentation_Helper_Data::SEGMENT_DATA_ATTRIBUTE_CODE,
    array(
         'type'       => 'text',
         'label'      => 'Segment data',
         'input'      => 'hidden',
         'required'   => false,
         'sort_order' => 1,
         'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
         'group'      => 'Special Attributes',
    )
);

$this->endSetup();
