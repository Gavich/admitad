<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_CleanUp_Model_Resource_Cleanup extends Mage_Core_Model_Resource
{
    /**
     * Returns count of disabled products
     *
     * @return int
     */
    public function getDisabledProductsCount()
    {
        $collection = $this->_getDisabledProductsCollection();

        return $collection->getSize();
    }

    /**
     * Returns count of disabled categories
     *
     * @return int
     */
    public function getDisabledCategoriesCount()
    {
        $collection = $this->_getDisabledCategoriesCollection();

        return $collection->getSize();
    }

    /**
     * Removes disabled products and do reindex
     */
    public function cleanUpProducts()
    {
        $collection = $this->_getDisabledProductsCollection();
        $query      = $collection->getConnection()->deleteFromSelect($collection->getSelect(), 'e');
        $collection->getConnection()->query($query);

        $this->_cleanUpImages();

        /* @var $indexer Mage_Index_Model_Indexer */
        $indexer   = Mage::getSingleton('index/indexer');
        $processes = $indexer->getProcessesCollection();
        $processes->walk('reindexEverything');
    }

    /**
     * Removes disabled categories and do reindex
     */
    public function cleanUpCategories()
    {
        $collection = $this->_getDisabledCategoriesCollection();
        $query      = $collection->getConnection()->deleteFromSelect($collection->getSelect(), 'e');
        $collection->getConnection()->query($query);

        /* @var $indexer Mage_Index_Model_Indexer */
        $indexer   = Mage::getSingleton('index/indexer');
        $processes = $indexer->getProcessesCollection();
        $processes->walk('reindexEverything');
    }

    /**
     * Executes few bash command that cleans up unused images
     */
    private function _cleanUpImages()
    {
        $output             = array();
        $usedImagesFileName = rtrim(sys_get_temp_dir(), DS) . DS . uniqid('used');
        $allImagesFileName  = rtrim(sys_get_temp_dir(), DS) . DS . uniqid('all');
        $diffFilename       = rtrim(sys_get_temp_dir(), DS) . DS . uniqid('diff');

        $io = new Varien_Io_File();
        $io->open(array('path' => sys_get_temp_dir()));

        $connection = $this->getConnection('core_read');
        $select     = $connection->select()
            ->from(
                $this->getTableName(Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media::GALLERY_TABLE),
                array('value')
            );

        $result = $connection->fetchCol($select);
        $io->filePutContent($usedImagesFileName, implode(PHP_EOL, $result));

        /* @var $config Mage_Catalog_Model_Product_Media_Config */
        $config    = Mage::getModel('catalog/product_media_config');
        $mediaPath = $config->getBaseMediaPath();

        // collect all files in media folder
        $command = sprintf("cd %s; find ./ -type f | sed -e 's/^\\.\\+//' > %s", $mediaPath, $allImagesFileName);
        exec($command, $output);
        $this->_checkExecution($output);

        exec(sprintf('sort %s -o %s', $allImagesFileName, $allImagesFileName), $output);
        $this->_checkExecution($output);
        exec(sprintf('sort %s -o %s', $usedImagesFileName, $usedImagesFileName), $output);
        $this->_checkExecution($output);


        exec(sprintf('comm -23 %s %s > %s', $allImagesFileName, $usedImagesFileName, $diffFilename), $output);
        $this->_checkExecution($output);

        // remove files from PHP to ensure that permissions are grunted
        $toDelete = file($diffFilename);
        $io->cd($mediaPath);
        foreach ((array)$toDelete as $image) {
            $io->rm(sprintf('.%s', trim($image)));
        }

        $io->rm($usedImagesFileName);
        $io->rm($allImagesFileName);
        $io->rm($diffFilename);
    }

    /**
     * Check bash command execution
     *
     * @param array $output
     *
     * @throws LogicException
     */
    private function _checkExecution($output)
    {
        if (!empty($output)) {
            throw new LogicException('Errors during command execution. Output:' . implode(' ', $output));
        }
    }

    /**
     * Returns collection of disabled products
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    private function _getDisabledProductsCollection()
    {
        return Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_DISABLED));
    }

    /**
     * Returns collection of disabled categories
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    private function _getDisabledCategoriesCollection()
    {
        return Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToFilter('is_active', array('eq' => 0));
    }
}
