<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Reader_Xml implements TC_AdmitadImport_Reader_ReaderInterface
{
    /**
     * Read data from source
     *
     * @param mixed $source
     *
     * @throws LogicException
     * @return TC_AdmitadImport_Reader_DataInterface
     */
    public function read($source)
    {
        $_products = $_categories = array();

        if (empty($source)) {
            throw new LogicException('Source does not configured properly');
        }

        $xml = new XMLReader();
        $xml->open($source);

        while ($xml->read()) {
            if ($xml->nodeType == XMLReader::ELEMENT) {
                if ($xml->name === 'category') {
                    $_category = array('name' => (string)$xml->readString());
                    foreach (array('id', 'parentId') as $attrName) {
                        $_category[$attrName] = (string)$xml->getAttribute($attrName);
                    }

                    $_categories[(string)$xml->getAttribute('id')] = $_category;
                } elseif ($xml->name === 'offer') {
                    $productXml = new SimpleXMLElement($xml->readOuterXML());
                    $_product   = get_object_vars($productXml);
                    if (isset($_product['@attributes'])) {
                        unset($_product['@attributes']);
                    }

                    $_product['param'] = array();
                    $_params           = $productXml->xpath('/offer/param');
                    if (!empty($_params)) {
                        foreach ($_params as $_param) {
                            $_attributes = $_param->attributes();
                            if (isset($_attributes['name'])) {
                                $_product['param'][(string)$_attributes['name']] = (string)$_param;
                            }
                        }
                    }

                    $_attributes = $productXml->attributes();
                    foreach ($_attributes as $key => $value) {
                        $_product[$key] = (string)$value;
                    }

                    $_products[$this->_getProductSKU($_product)] = $_product;
                }
            }
        }
        $xml->close();
        unset($xml);

        return new TC_AdmitadImport_Reader_DataBag($_categories, $_products);
    }

    /**
     * Retrieve product SKU from given data
     *
     * @param array $product
     *
     * @return string
     * @throws Exception
     */
    private function _getProductSKU(array $product)
    {
        $skuParts = array_intersect_key($product, array_flip(array('id', 'vendorCode')));

        if (empty($skuParts)) {
            throw new Exception(sprintf(
                'Could not retrieve SKU for product: %s %s', PHP_EOL, var_export($product, true)
            ));
        }

        return preg_replace('#[^a-z0-9_-]{1}#', '-', strtolower(implode('-', $skuParts)));
    }
}
