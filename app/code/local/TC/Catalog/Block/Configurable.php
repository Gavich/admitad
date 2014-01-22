<?php
/**
 * @category TC
 * @package TC_Catalog
 * @author Aleksandr Nezdoiminoga <alex.n.2k7@gmail.com>
 */

class TC_Catalog_Block_Configurable
    extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    /**
     * Get all attributes products by value
     *
     * @return array
     */
    public function getAllAttributeProducts()
    {
        if(!$this->hasData('attributes_products')){
            $products = array();
            foreach($this->getAllowProducts() as $product){
                $productId = $product->getId();

                foreach($this->getAllowAttributes() as $attribute){
                    $productAttribute = $attribute->getProductAttribute();
                    $attributeId = $productAttribute->getAttributeId();
                    $attributeValue = $product->getData($productAttribute->getAttributeCode());

                    if(!isset($products[$attributeId])){
                        $products[$attributeId] = array();
                    }

                    if(!isset($products[$attributeId][$attributeValue])){
                        $products[$attributeId][$attributeValue] = array();
                    }

                    $products[$attributeId][$attributeValue][] = $productId;
                }
            }
            $this->setData('attributes_products', $products);
        }

        return $this->getData('attributes_products');
    }

    /**
     * Get all products for attribute value
     *
     * @param int $attributeId
     * @param mixed $value
     * @return array
     */
    public function getAttributeOptionProducts($attributeId, $value)
    {
        $products = (array) $this->getAllAttributeProducts();
        if(isset($products[$attributeId])){
            if(isset($products[$attributeId][$value])){
                return $products[$attributeId][$value];
            }
        }
        return array();
    }

    /**
     * Get all attributes options
     *
     * @return array
     */
    public function getAllAttributesOptions()
    {
        if(!$this->hasData('attributes_options')){
            $options = array();
            foreach($this->getAllowAttributes() as $attribute){
                $attributeId = $attribute->getAttributeId();
                $attributeCode = $attribute->getProductAttribute()->getAttributeCode();
                $prices = $attribute->getPrices();
                $options[$attributeId] = array();

                foreach((array) $prices as $option){
                    $rel = $option['label'];

                    /*if('color' == $attributeCode) {
                        $rel = $this->_getColorImage($option['label']);
                        if(!$rel) {
                            $rel = Mage::getBaseDir('skin').'frontend/enterprise/hackett/images/cms/blank.gif';
                        }
                    }*/

                    $options[$attributeId][] = array(
                        'id' => $option['value_index'],
                        'label' => $option['label'],
                        'rel' => $rel,
                        'products' => $this->getAttributeOptionProducts($attributeId, $option['value_index'])
                    );
                }
            }
            $this->setData('attributes_options', $options);
        }

        return $this->getData('attributes_options');
    }

    /**
     * Get attribute options
     *
     * @param Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute
     * @return array
     */
    public function getAttributeOptions(Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute)
    {
        $options = (array) $this->getAllAttributesOptions();
        if(isset($options[$attribute->getAttributeId()])) {
            return $options[$attribute->getAttributeId()];
        }
        return array();
    }

    /**
     * Get JSON encoded attribute options
     *
     * @param Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute
     * @return string
     */
    public function getJsonAttributeOptions(Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute)
    {
        return $this->helper('core')->jsonEncode($this->getAttributeOptions($attribute));
    }

    /**
     * Get selected attribute option value
     * 
     * @param Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribtue
     * @return mixed
     */
    protected function getSelectedOption(Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute)
    {
        $options  = (array) $this->getAttributeOptions($attribute);
        $value    = $this->getRequest()->getParam($attribute->getProductAttribute()->getAttributeCode());
        if($value){
            foreach($options as $option){
                if(isset($option['label']) && $option['label'] == $value){
                    return $option['id'];
                }
            }
        }
        return '';
    }

    /**
     * Check attribute options has products
     *
     * @param Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute
     * @param array $option
     * @return boolean
     */
    public function useOption(Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute, $option)
    {
        $products = $this->getAttributeOptionProducts($attribute->getAttributeId(), $option['value_index']);
        return !empty($products);
    }

    /**
     * Get color image from media
     *
     * @param string $search
     * @return mixed
     */
    /*protected function _getColorImage($search)
    {
        $mediaImage = sprintf('icon/colors/%s.jpg', $this->_strDelimiter($search,''));

        // Check for file in manageable media dir
        if(is_file(Mage::getBaseDir('media').DS.$mediaImage)){
            return Mage::getBaseUrl('media').$mediaImage;
        }

        return false;
    }*/

    /**
     * Remove spaces, camel casing etc.
     * from string and insert a delimiter.
     * Used for creating class names etc.
     * from titles/labels
     *
     * @param string $str
     * @param string $delimiter
     * @return string
     */
    protected function _strDelimiter($str, $delimiter = '-')
    {
        return strtolower(preg_replace('/[^A-Z^a-z^0-9]+/', $delimiter,
            preg_replace('/([a-zd])([A-Z])/', '\1'.$delimiter.'\2',
                preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1'.$delimiter.'\2', $str))));
    }

    public function getJsonConfig()
    {
        $attributes = array();
        $options = array();
        $store = Mage::app()->getStore();
        foreach ($this->getAllowProducts() as $product) {
            $productId  = $product->getId();
            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttribute->getId()])) {
                    $options[$productAttribute->getId()] = array();
                }
                if (!isset($options[$productAttribute->getId()][$attributeValue])) {
                    $options[$productAttribute->getId()][$attributeValue] = array();
                }
                $options[$productAttribute->getId()][$attributeValue][] = $productId;
            }
        }

        $this->_resPrices = array(
            $this->_preparePrice($this->getProduct()->getFinalPrice())
        );

        $optionsLabel = array();
        $configurablePrice = $this->getProduct()->getFinalPrice();
        $configurableOldPrice = $this->getProduct()->getPrice();
        foreach ($this->getAllowAttributes() as $attribute) {
            array_push($optionsLabel, $attribute->getLabel()) ;
        }
        $numberOfOptions =  count($optionsLabel);
        foreach ($this->getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = array(
                'id'        => $productAttribute->getId(),
                'code'      => $productAttribute->getAttributeCode(),
                'label'     => $attribute->getLabel(),
                'options'   => array()
            );
            $optionPrices = array();
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if(!$this->_validateAttributeValue($attributeId, $value, $options)){
                        continue;
                    }
                    if($attribute->getLabel() == $optionsLabel[$numberOfOptions - 1]){
                        $products= $options[$attributeId][$value['value_index']];
                        $numItems= count($products);
                        for($i=0;$i<$numItems;$i++){
                            $backoder_date=null;
                            $a = array(0 => $products[$i]);
                            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($products[$i]);
                            $currentItem = Mage::getModel('catalog/product')->load($products[$i]);
                            $__manStock = $currentItem->getStockItem()->getManageStock();
                            if($__manStock > 0){
                                $outStockLabel = "(out of stock)";
                            } else {
                                $outStockLabel = "";
                            }
                            /*
                            if ($currentItem->getData('special_price')){
                                $simplePrice = $currentItem->getData('special_price');
                            } else {
                                if($currentItem->getTierPrice($currentItem)){
                                    $simplePrice = $currentItem->getTierPrice($currentItem);
                                } else {
                                    $simplePrice = $currentItem->getData('price');
                                }
                            }*/

                            /**
                             * Image getter
                             */
                            $simpleImage = '';
                            $simpleThumb = '';
                            if (($image = $currentItem->getImage()) && (is_file(Mage::getBaseDir('media').DS.'catalog'.DS.'product'.$currentItem->getImage()))){
                                $simpleImage = (string)Mage::helper('catalog/image')->init($currentItem, 'image', $image)->resize(476, 720);
                                $simpleThumb = (string)Mage::helper('catalog/image')->init($currentItem, 'image', $image)->resize(238, 360);
                            }
                            elseif($gallery = $currentItem->getMediaGallery()){
                                if(is_array($gallery['images'])){
                                    foreach ($gallery['images'] as $image){
                                        if (!$image['disabled']){
                                            $simpleImage = (string)Mage::helper('catalog/image')->init($currentItem, 'image', $image['file'])->resize(476, 720);
                                            $simpleThumb = (string)Mage::helper('catalog/image')->init($currentItem, 'image', $image['file'])->resize(238, 360);
                                        }
                                    }
                                }
                            }

                            $simplePrice = $currentItem->getPrice();
                            $finalPrice = $currentItem->getFinalPrice();
                            $info['options'][] = array(
                                'id'    => $value['value_index'],
                                //'oldPrice'  => $simplePrice - $configurableOldPrice,
                                'oldPrice'  => $simplePrice,
                                'label' => ($stockItem->getQty() <= 0) ? $value['label'] . $outStockLabel : $value['label'],
                                'price' =>  $this->_registerJsPrice($this->_convertPrice($finalPrice)) - $this->_registerJsPrice($this->_convertPrice($configurablePrice)),
                                'products'   => isset($options[$attributeId][$value['value_index']]) ? $a : array(),
                                'image' => $simpleImage,
                                'thumb' => $simpleThumb
                            );
                        }
                    } else {
                        $info['options'][] = array(
                            'id'    => $value['value_index'],
                            'label' => $value['label'],
                            'price' => $this->_preparePrice($value['pricing_value'], $value['is_percent']),
                            'products'   => isset($options[$attributeId][$value['value_index']]) ? $options[$attributeId][$value['value_index']] : array(),
                        );
                    }
                    $optionPrices[] = $this->_preparePrice($value['pricing_value'], $value['is_percent']);
                }
            }

            /**
             * Prepare formated values for options choose
             */
            foreach ($optionPrices as $optionPrice) {
                foreach ($optionPrices as $additional) {
                    $this->_preparePrice(abs($additional-$optionPrice));
                }
            }
            if($this->_validateAttributeInfo($info)) {
                $attributes[$attributeId] = $info;
            }
        }

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $taxConfig = array(
            'includeTax'        => Mage::helper('tax')->priceIncludesTax(),
            'showIncludeTax'    => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices'    => Mage::helper('tax')->displayBothPrices(),
            'defaultTax'        => $defaultTax,
            'currentTax'        => $currentTax,
            'inclTaxTitle'      => Mage::helper('catalog')->__('Incl. Tax'),
        );
        $config = array(
            'attributes'        => $attributes,
            'template'          => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
            'basePrice'         => $this->_registerJsPrice($this->_convertPrice($this->getProduct()->getFinalPrice())),
            'oldPrice'          => $this->_registerJsPrice($this->_convertPrice($this->getProduct()->getPrice())),
            'productId'         => $this->getProduct()->getId(),
            'chooseText'        => Mage::helper('catalog')->__('Choose an Option...'),
            'taxConfig'         => $taxConfig,
        );

        return Mage::helper('core')->jsonEncode($config);
    }

    public function getSKUJsonConfig($json=TRUE)
    {
        $product = $this->getProduct();
        $skus = array();
        foreach ($product->getTypeInstance()->getUsedProducts() as $childProduct){
            $skus[$childProduct->getId()] = $childProduct->getSku();
        }

        return ($json) ? Mage::helper('core')->jsonEncode($skus) : $skus;
    }


}
