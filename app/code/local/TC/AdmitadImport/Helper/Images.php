<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Helper_Images extends Mage_Core_Helper_Abstract
    implements TC_AdmitadImport_Logger_LoggerAwareInterface
{
    /**
     * @var array [PRODUCT_ID => URL]
     */
    private $_collectedData = array();

    /** @var Zend_Http_Client */
    private $_httpClient;

    /** @var TC_AdmitadImport_Logger_LoggerInterface */
    private $_logger;

    /**
     * Inject the logger
     *
     * @param TC_AdmitadImport_Logger_LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(TC_AdmitadImport_Logger_LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

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

        foreach ($this->_collectedData as $productId => $imageUrls) {
            $this->_logger->log(sprintf('Process images for product ID: %d', $productId));
            $product = clone $productModel;
            $product->setId($productId);

            if (!is_array($imageUrls)) {
                $imageUrls = array($imageUrls);
            }

            $massAdd = array();
            foreach ($imageUrls as $imageUrl) {
                $relativePath = md5($imageUrl) . '.' . pathinfo($imageUrl, PATHINFO_EXTENSION);
                $path         = $importDir . $relativePath;

                try {
                    $response = $this->_getHttpClient()->setUri($imageUrl)->request();
                    if (200 === $response->getStatus()) {
                        file_put_contents($path, $response->getBody());

                        if (empty($massAdd)) {
                            // first image is main
                            $massAdd[] = array(
                                'file'           => $relativePath,
                                'mediaAttribute' => 'thumbnail',
                            );
                            $massAdd[] = array(
                                'file'           => $relativePath,
                                'mediaAttribute' => 'image',
                            );
                            $massAdd[] = array(
                                'file'           => $relativePath,
                                'mediaAttribute' => 'small_image',
                            );
                        } else {
                            $massAdd[] = array(
                                'file'           => $relativePath,
                                'mediaAttribute' => null
                            );
                        }
                    } else {
                        $this->_logger->log(
                            sprintf(
                                'Failed to download image, response code: %d', $response->getStatus(), Zend_Log::ERR
                            )
                        );
                    }
                } catch (Exception $e) {
                    $this->_logger->log($e->getMessage(), Zend_Log::ERR);
                }
            }

            if (!empty($massAdd)) {
                $backendModel->addImagesWithDifferentMediaAttributes(
                    $product, $massAdd, $importDir, true, false
                );
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
