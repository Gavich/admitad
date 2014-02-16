<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Helper_Images extends Mage_Core_Helper_Abstract
{
    /**
     * @var array [PRODUCT_ID => URL]
     */
    private $_collectedData = array();

    /** @var Zend_Http_Client */
    private $_httpClient;

    /**
     * Collect information for further processing
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array                      $data
     */
    public function collectData(Mage_Catalog_Model_Product $product, $data)
    {
        if (!empty($data['picture_orig'])) {
            $this->_collectedData[$product->getId()] = $data['picture_orig'];
        }
    }

    /**
     * Process save images from remote CDN
     */
    public function processImages()
    {
        /* @var $productModel Mage_Catalog_Model_Product */
        $productModel = Mage::getModel('catalog/product');
        $productModel->getResource()->loadAllAttributes();
        /** @var Mage_Catalog_Model_Product_Attribute_Backend_Media $backendModel */
        $backendModel = $productModel->getResource()->getAttribute('media_gallery')->getBackend();
        $importDir    = Mage::getBaseDir('media') . DS . 'import' . DS;
        if (!is_dir($importDir)) {
            mkdir($importDir, 0777);
        }

        foreach ($this->_collectedData as $productId => $imageUrl) {
            $product = clone $productModel;
            $product->setId($productId);

            $relativePath = md5($imageUrl) . '.' . pathinfo($imageUrl, PATHINFO_EXTENSION);
            $path         = $importDir . $relativePath;
            $response     = $this->_getHttpClient()->setUri($imageUrl)->request();
            if (200 === $response->getStatus()) {
                file_put_contents($path, $response->getBody());

                $massAdd = array(
                    array(
                        'file'           => $relativePath,
                        'mediaAttribute' => 'thumbnail',
                    ),
                    array(
                        'file'           => $relativePath,
                        'mediaAttribute' => 'image',
                    ),
                    array(
                        'file'           => $relativePath,
                        'mediaAttribute' => 'small_image',
                    ),
                );
                $backendModel->addImagesWithDifferentMediaAttributes($product, $massAdd, $importDir, true, false);
                $product->getResource()->save($product);
            }
        }

        $this->_cleanUp($importDir);
    }

    /**
     * Cleans dir, removes all content recursively
     *
     * @param string $dir
     */
    private function _cleanUp($dir)
    {
        $it    = new RecursiveDirectoryIterator($dir);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        /** @var $file SplFileInfo */
        foreach ($files as $file) {
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

    /**
     * Returns http client
     *
     * @return Zend_Http_Client
     */
    private function _getHttpClient()
    {
        if (null === $this->_httpClient) {
            $this->_httpClient = new Zend_Http_Client();
        }

        return $this->_httpClient;
    }
}
