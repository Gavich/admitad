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
    TC_AdmitadImport_Processor_Categories::ORIGIN_ID_ATTRIBUTE_CODE,
    array(
         'type'       => 'varchar',
         'label'      => 'Origin ID',
         'input'      => 'text',
         'required'   => false,
         'sort_order' => 1,
         'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
         'group'      => 'Special Attributes',
    )
);

$this->endSetup();
