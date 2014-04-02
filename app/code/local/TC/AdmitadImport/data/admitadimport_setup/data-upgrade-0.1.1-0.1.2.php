<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
/* @var $this Mage_Eav_Model_Entity_Setup */
$this->startSetup();

$attributes = array(
    'brand', 'country_of_manufacture', 'material', 'fashion_collection', 'color',
    'season', 'sex', 'age', 'clasp_type', 'external_material', 'heel',
    'bootleg_height', 'bootleg_width', 'sole_material', 'platform_height'
);
foreach ($attributes as $attributeCode) {
    $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode(
        Mage_Catalog_Model_Product::ENTITY, $attributeCode
    );
    $attribute   = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
    $attribute->setIsVisibleOnFront(true)->save();
}
$this->endSetup();
