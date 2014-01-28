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

        if (empty ($source)) {
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
                    $_attributes = $productXml->attributes();
                    foreach ($_attributes as $key => $value) {
                        $_product[$key] = (string)$value;
                    }

                    $_products[] = $_product;
                }
            }
        }
        $xml->close();
        unset($xml);

        return new TC_AdmitadImport_Reader_DataBag($_categories, $_products);
    }
}
