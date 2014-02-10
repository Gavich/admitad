<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
/* @var $this Mage_Eav_Model_Entity_Setup */
$this->startSetup();

$disableRequiredOption = ['weight', 'short_description'];
foreach ($disableRequiredOption as $attributeCode) {
    $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode(
        Mage_Catalog_Model_Product::ENTITY, $attributeCode
    );
    $attribute   = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
    $attribute->setIsRequired(false)->save();
}

$textAttributes = [
    'model', 'type_prefix', 'brand', 'material', 'collection', 'color', 'season', 'sex', 'age',
    'clasp_type', 'heel', 'external_material', 'url', 'platform_height', 'sole_material', 'bootleg_height',
    'bootleg_width'
];

$floatAttributes = ['local_delivery_cost'];

$data = [
    'type'         => 'varchar',
    'label'        => '',
    'input'        => 'text',
    'required'     => false,
    'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'group'        => 'Parameters',
    'user_defined' => 1
];

$entityTypeId = Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId();
$object       = new Varien_Object();
$object->setData($data);

$attributes = array_merge($textAttributes, $floatAttributes);
foreach ($attributes as $attributeCode) {
    $current = clone $object;
    $current->setLabel(
        ucfirst(trim(strtolower(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $attributeCode))))
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
    $attributeModel->setApplyTo([])->save();
}
