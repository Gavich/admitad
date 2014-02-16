<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Helper_Attributes extends Mage_Core_Helper_Abstract
{
    /**
     * @var array(ATTRIBUTE_CODE => DATA_KEY)
     */
    private $_map;
    /**
     * @var array(ATTRIBUTE_CODE => [type: CUSTOM_OPTION_TYPE, name:CUSTOM_OPTION_NAME])
     */
    private $_customOptions;

    /** @var bool */
    private $_initialized = false;

    /** @var array Attributes object pool */
    private $_attributes;

    /** @var array Attribute option collection object pool */
    private $_attributeOptionCollection;

    /** @var array Attribute options runtime cache */
    private $_attributeOptionsRuntimeCache;

    /**
     * Maps given data to correct attributes
     *
     * @param array $data
     *
     * @return array
     */
    public function getMappedValues(array $data)
    {
        $this->_init();

        $attributes           = array();
        $attributesSourceData = array_merge($data, $data['param']);
        foreach ($this->_map as $attributeCode => $sourceCode) {
            if (!empty($attributesSourceData[$sourceCode])) {
                $attributes[$attributeCode] = $this->_prepareAttributeValue(
                    $attributeCode, $attributesSourceData[$sourceCode]
                );
            }
        }

        return $attributes;
    }

    /**
     * Process custom options save depends on configuration
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @throws LogicException
     *
     * @return bool
     */
    public function processCustomOptions(Mage_Catalog_Model_Product $product)
    {
        /* @var $optionModel Mage_Catalog_Model_Product_Option */
        $optionModel = clone $product->getOptionInstance();
        $optionModel->setProduct($product);

        foreach ((array)$this->_customOptions as $attributeCode => $config) {
            $optionTitle = isset($config['title']) ? $config['title'] : $attributeCode;

            if ($product->hasData($attributeCode)) {
                $data = explode(',', $product->getData($attributeCode));

                if (Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN === $config['type']) {
                    $options = $optionModel->getOptions();

                    $optionConfig   = array_merge(array('values' => array(), 'is_require' => true), $config);
                    $existOptionKey = null;
                    foreach ((array)$options as $k => $option) {
                        if ($option['title'] === $optionTitle) {
                            $existOptionKey = $k;
                            $optionConfig   = array_merge($optionConfig, $option);
                        }
                    }

                    foreach ($data as $value) {
                        $value = trim($value);

                        $isExisted = array_filter(
                            $optionConfig['values'],
                            function ($valueConfig) use ($value) {
                                return $valueConfig['title'] === $value;
                            }
                        );

                        if (!$isExisted) {
                            $optionConfig['values'][] = array(
                                'title'      => $value,
                                'price_type' => 'fixed'
                            );
                        }
                    }

                    if (null == $existOptionKey) {
                        $options[] = $optionConfig;
                    } else {
                        $options[$existOptionKey] = $optionConfig;
                    }

                    $optionModel->setOptions($options);
                } else {
                    throw new LogicException('Only drop down custom option supported');
                }

                $product->unsetData($attributeCode);
            }
        }

        if ($optionModel->getOptions()) {
            $resource = $product->getResource();

            $resource->getWriteConnection()->update(
                $resource->getEntityTable(), array('has_options' => true), sprintf('entity_id = %d', $product->getId())
            );

            $optionModel->saveOptions();
        }
        unset($optionModel);
    }

    /**
     * Initialize helper
     *
     * @throws RuntimeException
     */
    private function _init()
    {
        if (!$this->_initialized) {
            $file    = Mage::getBaseDir('var') . DS . 'data' . DS . 'attributes.json';
            $content = file_get_contents($file);

            if (false === $content) {
                throw new RuntimeException('Could not read attributes map from data folder');
            }

            $data = Zend_Json::decode($content);
            if (!is_array($data) || empty($data['map']) || empty($data['custom_options'])) {
                throw new RuntimeException('Malformed attributes data');
            }

            $this->_map           = $data['map'];
            $this->_customOptions = $data['custom_options'];
            $this->_initialized   = true;
        }
    }

    /**
     * Prepare attribute value based on it's type
     *
     * @param string $attributeCode
     * @param mixed  $value
     *
     * @return array|float|int|string
     */
    private function _prepareAttributeValue($attributeCode, $value)
    {
        $attribute = $this->_getAttribute($attributeCode);

        if (is_object($attribute)) {
            switch ($attribute->getData('frontend_input')) {
                case 'multiselect':
                case 'select':
                    $this->_prepareAttributeOptions($attribute, is_array($value) ? $value : explode(',', $value));
                    $newValue = array();
                    break;
                case 'decimal':
                    $newValue = floatval($value);
                    break;
                case 'int':
                    $newValue = (int)$value;
                    break;
                default:
                    $newValue = (string)$value;
                    break;
            }
            if ($attribute->usesSource()) {
                $optionCollection = $this->_getAttributeOptionCollection($attribute);
                $options          = $optionCollection->toOptionArray();

                if ('multiselect' === $attribute->getData('frontend_input')) {
                    if (!is_array($value)) {
                        $value = explode(',', $value);
                    }
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $newValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = null;
                    foreach ($options as $item) {
                        if ($item['label'] == $value) {
                            $newValue = $item['value'];
                            break;
                        }
                    }
                }
            }
        } else {
            $newValue = $value;
        }

        return $newValue;
    }

    /**
     * Prepare attribute options and adds if not exist
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param array|string                    $value
     */
    private function _prepareAttributeOptions(Mage_Eav_Model_Entity_Attribute $attribute, $value)
    {
        if ($attribute->getSource() instanceof Mage_Eav_Model_Entity_Attribute_Source_Table) {
            if (!is_array($value)) {
                $value = array($value);
            }
            sort($value);
            $cacheKey = md5($attribute->getId() . implode('', $value));
            if (in_array($cacheKey, $this->_attributeOptionsRuntimeCache)) {
                return;
            }
            $this->_attributeOptionsRuntimeCache[] = $cacheKey;
            $collection                            = $this->_getAttributeOptionCollection($attribute);
            $optionsData                           = $collection->toOptionArray();
            $labels                                = array();
            foreach ($optionsData as $option) {
                $labels[] = $option['label'];
            }

            $value = array_diff($value, $labels);

            if (!empty($value)) {
                $_option = array();
                foreach ($optionsData as $option) {
                    $_option['value'][$option['value']][0] = $option['label'];
                }
                $i = 0;
                foreach ($value as $label) {
                    $_option['value']['options_' . $i][0] = $label;
                    $_option['order']['options_' . $i]    = 0;
                    $i++;
                }
                $attribute->setData('option', $_option);
                $attribute->save();
                $collection->clear();
                $collection->load();
            }
        }
    }

    /**
     * Returns options collection
     *
     * @param Mage_Core_Model_Abstract $attribute
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
     */
    private function _getAttributeOptionCollection(Mage_Core_Model_Abstract $attribute)
    {
        $attributeId      = $attribute->getId();
        $attributeStoreId = $attribute->getData('store_id');
        if (!isset($this->_attributeOptionCollection[$attributeId])) {
            /** @var $collection Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection */
            $collection = Mage::getResourceModel('eav/entity_attribute_option_collection');
            $collection->setAttributeFilter($attributeId);
            $collection->setStoreFilter($attributeStoreId);
            $collection->load();
            $this->_attributeOptionCollection[$attributeId] = $collection;
        }

        return $this->_attributeOptionCollection[$attributeId];
    }

    /**
     * Retrieve eav entity attribute model
     *
     * @param string $code
     *
     * @return Mage_Eav_Model_Entity_Attribute
     */
    private function _getAttribute($code)
    {
        if (!isset($this->_attributes[$code])) {
            /* @var $resource Mage_Catalog_Model_Resource_Product */
            $resource                 = Mage::getResourceModel('catalog/product');
            $this->_attributes[$code] = $resource->getAttribute($code);
        }

        return $this->_attributes[$code];
    }
}
