<?php
/**
 * @category TC
 * @package TC_Catalog
 * @author Aleksandr Nezdoiminoga <alex.n.2k7@gmail.com>
 */

class TC_Catalog_Helper_Data
    extends Mage_Catalog_Helper_Data
{
    public $_colorAttributeCode = 'dim1';
    public $_colorAttribute;
    public $_colorImages;

    /**
     * Get all available color-image(file) pairs
     */
    public function getAvailableColors()
    {
        if (!$this->_colorImages && !$this->_colorAttribute) {
            /** @var $model Mage_Catalog_Model_Resource_Eav_Attribute */
            $model = Mage::getModel('catalog/resource_eav_attribute');
            $model->loadByCode(Mage_Catalog_Model_Product::ENTITY, $this->_colorAttributeCode);

            $this->_colorAttribute = $model;

            $colorsCollection = $model->getSource()->getAllOptions();
            $_colors = array();
            foreach ($colorsCollection as $color) {
                if (trim($color['value']) != '') {
                    $file = Mage::getBaseDir('media').DS.'icon'.DS.'colors'.DS.$color['label'].'.jpg';
                    if (is_file($file)){
                        $_colors[trim($color['value'])] = array(
                            'label' => $color['label'],
                            'file' => $file,
                            'url' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'icon'.DS.'colors'.DS.$color['label'].'.jpg'
                        );
                    }
                }
            }
            $this->_colorImages = $_colors;
        }
    }

    /**
     * Get all available color-image(file) pairs per configurable
     */
    public function getSwatches($_product)
    {
        if (!Mage::getStoreConfigFlag('catalog/frontend/color_is_enabled')) return false;

        $this->_colorAttributeCode = Mage::getStoreConfig('catalog/frontend/color_attribute_code');
        $this->getAvailableColors();

        $swatches = array();
        $usedProducts = $_product->getTypeInstance(true)->getUsedProducts($this->_colorAttribute->getId(), $_product);
        foreach ($usedProducts as $innerProduct) {
            if (!in_array($innerProduct->getData($this->_colorAttributeCode), array_keys($this->_colorImages))) continue;
            $swatches[$innerProduct->getData($this->_colorAttributeCode)] = $this->_colorImages[$innerProduct->getData($this->_colorAttributeCode)];
        }

        return $swatches;
    }

    /**
     * Return current category path or get it from current category
     * and creating array of categories|product paths for breadcrumbs
     */
    public function getBreadcrumbPath()
    {
        if (!$this->_categoryPath){
            $path = array();
            if ($category = $this->getCategory()){
                $pathInStore = $category->getPathInStore();
                $pathIds = array_reverse(explode(',', $pathInStore));
                $categories = $category->getParentCategories();

                // add category path breadcrumb
                foreach ($pathIds as $categoryId){
                    if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()){
                        $path['category' . $categoryId] = array(
                            'label' => $categories[$categoryId]->getName(),
                            'link'  => $categories[$categoryId]->getUrl(),
                            'level' => $categories[$categoryId]->getLevel(),
                            'id'    => $categories[$categoryId]->getId()
                        );
                    }
                }
            }

            if ($this->getProduct()){
                $path['product'] = array('label'=>$this->getProduct()->getName());
            }

            $this->_categoryPath = $path;
        }
        return $this->_categoryPath;
    }
}
