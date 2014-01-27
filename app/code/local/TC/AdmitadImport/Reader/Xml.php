<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Reader_Xml implements TC_AdmitadImport_Reader_ReaderInterface
{
    /** @var array */
    protected static $_productAttributes
        = array(
            'name',
            'picture_orig',
            'local_delivery_cost',
            'delivery',
            'vendorCode',
            'typePrefix',
            'model',
            'param',
            'categoryId',
            'vendor',
            'price',
            'advcampaign_id',
            'advcampaign_name',
            'modified_time',
            'picture',
            'thumbnail',
            'url',
            'currencyId'
        );

    /** @var array */
    protected static $_categoryAttributes = array('id', 'parentId');

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

        if (empty ($source)) {
            throw new LogicException('Source does not configured properly');
        }

        $content = file_get_contents($source);
        if (false === $content) {
            throw new LogicException('Unable to read data from given source');
        }

        $xml = new SimpleXMLElement($content);
        unset($content);

        $_categoriesToProcess = isset($xml->shop, $xml->shop->categories)
            ? $xml->shop->categories->children() : array();
        foreach ($_categoriesToProcess as $key => $_categoryToProcess) {
            $_category = array('name' => (string)$_categoryToProcess);

            $_attributes = $_categoriesToProcess->attributes();
            foreach (self::$_categoryAttributes as $attrName) {
                $_category[$attrName] = (string)$_attributes->{$attrName};
            }

            $_categories[] = $_category;
            unset($_categoriesToProcess[$key]);
        }
        unset($_categoriesToProcess);

        $_productsToProcess = isset($xml->shop, $xml->shop->offers)
            ? $xml->shop->offers->children() : array();
        foreach ($_productsToProcess as $key => $_productToProcess) {
            $_product = array();

            $_attributes = $_productToProcess->attributes();
            foreach (self::$_productAttributes as $attrName) {
                $_category[$attrName] = (string)$_attributes->{$attrName};
            }

            $_products[] = $_product;
            unset($_productsToProcess[$key]);
        }

        unset($_productsToProcess, $xml);

        return new TC_AdmitadImport_Reader_DataBag($_categories, $_products);
    }
}
