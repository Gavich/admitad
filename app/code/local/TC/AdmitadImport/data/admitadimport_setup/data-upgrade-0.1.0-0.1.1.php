<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
/* @var $this Mage_Eav_Model_Entity_Setup */
$this->startSetup();

$disableRequiredOption = array('weight', 'short_description');
foreach ($disableRequiredOption as $attributeCode) {
    $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode(
        Mage_Catalog_Model_Product::ENTITY, $attributeCode
    );
    $attribute   = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
    $attribute->setIsRequired(false)->save();
}

$toRemove = array('manufacturer', 'country_of_manufacture');
foreach ($toRemove as $attributeCode) {
    $attribute = $this->removeAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
}

$textAttributes = array(
    'model', 'type_prefix', 'brand', 'material', 'fashion_collection', 'color', 'season', 'sex', 'age',
    'clasp_type', 'heel', 'external_material', 'ad_redirect_url', 'platform_height', 'sole_material', 'bootleg_height',
    'bootleg_width', 'manufacturer', 'country_of_manufacture'
);

$floatAttributes = array('local_delivery_cost');

$data = array(
    'type'         => 'varchar',
    'label'        => '',
    'input'        => 'text',
    'required'     => false,
    'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'        => 'Parameters',
    'user_defined' => 1
);

$defaultAttributeSetId = Mage::getModel('catalog/product')->getDefaultAttributeSetId();
$entityTypeId          = Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId();
$object                = new Varien_Object();
$object->setData($data);

$attributes = array_merge($textAttributes, $floatAttributes);
foreach ($attributes as $attributeCode) {
    $current = clone $object;
    $current->setLabel(
        ucfirst(trim(strtolower(preg_replace(array('/([A-Z])/', '/[_\s]+/'), array('_$1', ' '), $attributeCode))))
    );

    if (in_array($attributeCode, $floatAttributes)) {
        $current->setType('decimal')
            ->setInput('price')
            ->setBackendModel('catalog/product_attribute_backend_price');
    }
    $this->addAttribute($entityTypeId, $attributeCode, $current->getData());
    $attributeModel = Mage::getModel('catalog/resource_eav_attribute')->loadByCode(
        Mage_Catalog_Model_Product::ENTITY, $attributeCode
    );
    $attributeModel->setApplyTo(array())->save();
}

$this->endSetup();
