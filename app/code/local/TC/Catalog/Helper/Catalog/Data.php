<?php

class TC_Catalog_Helper_Catalog_Data extends Mage_Catalog_Helper_Data
{
    /**
     * Return current category path or get it from current category
     * and creating array of categories|product paths for breadcrumbs
     */
    public function getBreadcrumbPath()
    {
        if (!$this->_categoryPath) {
            $path = array();
            if ($category = $this->getCategory()) {
                $pathInStore = $category->getPathInStore();
                $pathIds     = array_reverse(explode(',', $pathInStore));
                $categories  = $category->getParentCategories();

                // add category path breadcrumb
                foreach ($pathIds as $categoryId) {
                    if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                        $path['category' . $categoryId] = array(
                            'label' => $categories[$categoryId]->getName(),
                            'link'  => $categories[$categoryId]->getUrl(),
                            'level' => $categories[$categoryId]->getLevel(),
                            'id'    => $categories[$categoryId]->getId()
                        );
                    }
                }
            }

            if ($this->getProduct()) {
                $path['product'] = array('label' => $this->getProduct()->getName());
            }

            $this->_categoryPath = $path;
        }

        return $this->_categoryPath;
    }
}
