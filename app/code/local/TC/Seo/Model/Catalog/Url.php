<?php

/**
 * @category   TC
 * @package    TC_Seo
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_Seo_Model_Catalog_Url extends Mage_Catalog_Model_Url
{
    // @codingStandardsIgnoreStart
    /**
     * Override original method to skip parent category path and use id instead of it. e.g. 23-category-url-path.html
     *
     * @param Varien_Object $category
     * @param string        $parentPath
     *
     * @return string
     */
    public function getCategoryRequestPath($category, $parentPath)
    {
        $storeId = $category->getStoreId();
        $idPath  = $this->generatePath('id', null, $category);
        $suffix  = $this->getCategoryUrlSuffix($storeId);

        if (isset($this->_rewrites[$idPath])) {
            $this->_rewrite = $this->_rewrites[$idPath];
            $existingRequestPath = $this->_rewrites[$idPath]->getRequestPath();
        }

        if ($category->getUrlKey() == '') {
            $urlKey = $this->getCategoryModel()->formatUrlKey($category->getName());
        } else {
            $urlKey = $this->getCategoryModel()->formatUrlKey($category->getUrlKey());
        }

        $categoryUrlSuffix = $this->getCategoryUrlSuffix($category->getStoreId());

        $requestPath = $category->getId() . '-' . $urlKey . $categoryUrlSuffix;
        if (isset($existingRequestPath) && $existingRequestPath == $requestPath . $suffix) {
            return $existingRequestPath;
        }

        if ($this->_deleteOldTargetPath($requestPath, $idPath, $storeId)) {
            return $requestPath;
        }

        return $this->getUnusedPath($category->getStoreId(), $requestPath,
            $this->generatePath('id', null, $category)
        );
    }
    // @codingStandardsIgnoreEnd
}
