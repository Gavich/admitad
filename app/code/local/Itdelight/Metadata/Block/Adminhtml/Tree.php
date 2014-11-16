<?php

class Itdelight_Metadata_Block_Adminhtml_Tree extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories {
   
        public function _construct(){
           
        }
        public function isReadonly()
        {
            return false;
        }
 
        protected function getCategoryIds()
        {
        
            $categories_ids_array = array();
//             $categories=Mage::getModel('catalog/category');
//            $categories_ids_array=$categories->getParentIds();
    $category = Mage::getModel('catalog/category'); 
    $categories_ids_array = $category->getParentId(); 
    
            Mage::Log($categories_ids_array,null,'categories.log');
            return $categories_ids_array;
        }
 
        public function getIdsString()
        {
            return implode(',', $this->getCategoryIds());
        }
}
