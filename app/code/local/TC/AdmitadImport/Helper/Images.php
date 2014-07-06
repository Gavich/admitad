<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Helper_Images extends Mage_Core_Helper_Abstract
    implements TC_AdmitadImport_Logger_LoggerAwareInterface
{
    const STATUS_PENDING = 1;
    const STATUS_WORKING = 2;

    /**
     * @var array [PRODUCT_ID => URL]
     */
    private $_collectedData = array();

    /** @var Zend_Http_Client */
    private $_httpClient;

    /** @var TC_AdmitadImport_Logger_LoggerInterface */
    private $_logger;

    /** @var bool */
    private $_isAsyncMode = true;

    /** @var null|\Symfony\Component\Process\Process */
    private $_poolProcess;

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
     * Set async mode to enabled/disabled
     *
     * @param bool $value
     */
    public function setAsync($value)
    {
        $this->_isAsyncMode = $value;
    }

    /**
     * Initialize helper, if async mode then run process pool
     */
    public function init()
    {
        if ($this->_isAsyncMode()) {
            /* @var $helper TC_AdmitadImport_Helper_ProcessPool */
            $helper = Mage::helper('tc_admitadimport/processPool');
            if ($helper instanceof TC_AdmitadImport_Logger_LoggerAwareInterface && null !== $this->_logger) {
                $helper->setLogger($this->_logger);
            }

            $this->_poolProcess = $helper->startProcessPool();
        }
    }

    /**
     * Do  needed work when all imports done
     */
    public function terminate()
    {
        $this->_wait();
    }

    /**
     * Init helper state from state file
     *
     * @param string $filename
     *
     * @return string Filename changed to working
     */
    public function initFromFile($filename)
    {
        if (!is_readable($filename)) {
            throw new LogicException(sprintf('Unable to read %s', $filename));
        }

        $this->_collectedData = require $filename;

        $newFilename = substr($filename, 0, -2) . '.' . self::STATUS_WORKING;
        rename($filename, $newFilename);

        return $newFilename;
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
     *
     * @throws Mage_Core_Exception
     */
    public function processImages()
    {
        if ($this->_isAsyncMode()) {
            $this->_prepareAsyncData($this->_collectedData);
            $this->_collectedData = array();

            return;
        }

        /* @var $productModel Mage_Catalog_Model_Product */
        $productModel = Mage::getModel('catalog/product');
        $productModel->getResource()->loadAllAttributes();
        /** @var Mage_Catalog_Model_Product_Attribute_Backend_Media $backendModel */
        $backendModel = $productModel->getResource()->getAttribute('media_gallery')->getBackend();
        $importDir    = Mage::getBaseDir('media') . DS . 'import' . DS;
        if (!is_writable($importDir)) {
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
                                'Failed to download image %s, response code: %d', $imageUrl, $response->getStatus(),
                                Zend_Log::ERR
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
    }

    /**
     * Prepare data file for async task
     *
     * @param array $data
     */
    private function _prepareAsyncData($data)
    {
        $filename = uniqid('imagesData') . time() . '.' . self::STATUS_PENDING;
        $content  = <<<PHP
<?php
return %s;
PHP;
        $content   = sprintf($content, var_export($data, true));
        $importDir = Mage::getBaseDir('media') . DS . 'import' . DS;
        if (!is_dir($importDir)) {
            mkdir($importDir);
        }
        $filename = $importDir . $filename;

        file_put_contents($filename, $content);
    }

    /**
     * Wait until all jobs done
     *
     * @return $this
     */
    private function _wait()
    {
        if (!($this->_isAsyncMode() && null !== $this->_poolProcess)) {
            return $this;
        }

        while ($this->_poolProcess->isRunning()) {
            sleep(10);
        }

        return $this;
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

    /**
     * Is async mode enabled
     *
     * @return bool
     */
    private function _isAsyncMode()
    {
        return $this->_isAsyncMode;
    }
}
