<?php

class Itdelight_Metadata_Model_Observer {
    
    public function _construct()
    {     
    }
    
    public function getCategory1($model){
        $string=$model->getCategoryIds();
        $arr=explode(',',$string);
        $var=$arr[1];
        return $var;
    }
    public function patternCatalogFunction($text_field,$category){
         
        $categories=$category->getParentIds();
        $parent=$category->getParentId();
        $i=0;
        $cat_arr=Mage::getModel('catalog/category');
        foreach($categories as $value)
        {       
           $cat_arr->load($value);
            $text_field=str_replace("<cat".$i.">", $cat_arr->getName(), $text_field);  
            $i++;
            
        }
        $parent_it=$cat_arr->load($parent);
        $text_field=str_replace("<category>", $category->getName(), $text_field);
        $text_field=str_replace("<parent>", $parent_it->getName(), $text_field);
        
        return  $text_field;
      }
    
    public function patternProductFunction($text_field,$product){
        
        $categories=$product->getCategoryIds();
        Mage::Log($categories,null,'my.log');
       foreach($categories as $category){
           $object_category=Mage::getModel('catalog/category')->load($category);
            $text_field=str_replace("<category>", $object_category->getName(), $text_field);
       }
        $text_field=str_replace("<product>", $product->getName(), $text_field);
        $text_field=str_replace("<price>", $product->getPrice(), $text_field);
        $text_field=str_replace("<brand>",$product->getBrandLogo(),$text_field);
        return  $text_field;
      }
      
     public function applyToCategoriesOfField($category,$model){
            
            if($model->getCatForm()){
                $categories=$model->getCategories();
                
                $arr=explode(',',$categories);
                foreach($arr as $item){
                    $item=trim($item);
                     
                    if($item == $category->getId()){
                         
                        $this->setCategoryMetadata($category,$model);
                       
                    }
                }
            }
        }
    public function applyToCategoryAndChild($category,$model){
            if($model->getCatChild()){
                $parents=$category->getParentIds();
               
                foreach($parents as $parent){
                if($model->getCategoryId()==$parent){                    
                    $this->setCategoryMetadata($category,$model); 
                }      
            }
              if($model->getCategoryId()==$category->getId()){
                    $this->setCategoryMetadata($category,$model);
                }
            }
        }
    
        public function applyToCategory($category,$model){
            if($model->getCat()){
                  if($model->getCategoryId()==$category->getId()){
                    $this->setCategoryMetadata($category,$model);
                }
            }
            
        }
        public function applyToProductsOfField($product,$model){
            
            if($model->getProdForm()){
                $products=$model->getProducts();
                
                $arr=explode(',',$products);
                foreach($arr as $item){
                    $item=trim($item);
                     
                    if($item == $product->getId()){
                         
                        $this->setProductMetadata($product,$model);
                       
                    }
                }
            }
        }
        public function generateForProductsOfCatandChild($product,$model){
         
        if($model->getProdChildcat()){
            $categories=$product->getCategoryIds();
            
            foreach($categories as $item){
                $category=Mage::getModel('catalog/category')->load($item);
                $parents=$category->getParentIds();
               foreach($parents as $parent){
               if($model->getCategoryId()==$parent){
                $this->setProductMetadata($product,$model); 
            }
               }
               if($model->getCategoryId()==$item){
                   $this->setProductMetadata($product,$model);
               }
            }
            
        }
    }
    public function generateForProductsOfCat($product,$model){
         
        if($model->getProdCat()){
            
            $categories=$product->getCategoryIds();
            foreach ($categories as $category){
                if($category==$model->getCategoryId())
                {
                    $this->setProductMetadata($product,$model);
                }
            }
            
        }
    }
       public function generateNewMetadataCat($product,$custom){
         $page=Mage::getSingleton('core/session')->getPage($page);
        $array=explode("<e>",$custom);
        $count=substr_count($custom,"<e>");
        if(($page>$count)&($count>0)){
            $new_index=$page % $count;
            $new_data=$array[$new_index];
        }else{
            $new_data=$array[$page-1];
       
            
        }
        
        return $new_data;
    }
    public function generateNewMetadata($product,$custom){
         $page=Mage::getSingleton('core/session')->getPage($page);
        $array=explode("<e>",$custom);
        $count=count($array);
        if(($page>$count)&($count>0)){
            $new_index=$page % $count;
            $new_data=$array[$new_index-1];
        }else{
            $new_data=$array[$page-1];           
        }
        $new_data=$new_data;
        return $new_data;
    }
    
    public function setProductMetadata($product,$model){
        
        $custom_keywords=$this->patternProductFunction($model->getKeywords(),$product);
        $custom_description=$this->patternProductFunction($model->getDescription(),$product);
        $custom_title=$this->patternProductFunction($model->getTitle(),$product);
        $keywords=$product->getMetaKeyword();
        $description=$product->getMetaDescription();
        $title=$product->getMetaTitle();
        $newKeywords=$this->generateNewMetadata($keywords,$custom_keywords);
        $newDescription=$this->generateNewMetadata($description,$custom_description);
        $newTitle=$this->generateNewMetadata($title,$custom_title);
        $product->setMetaKeyword($newKeywords);
        $product->setMetaDescription($newDescription);
        $product->setMetaTitle($newTitle);
        
    }
    
     public function setCategoryMetadata($category,$model){
        
        $custom_keywords=$this->patternCatalogFunction($model->getKeywords(),$category);
        $custom_description=$this->patternCatalogFunction($model->getDescription(),$category);
        $custom_title=$this->patternCatalogFunction($model->getTitle(),$category);
        $keywords=$category->getMetaKeywords();
        $description=$category->getMetaDescription();
        $title=$category->getMetaTitle();
        $newKeywords=$this->generateNewMetadata($keywords,$custom_keywords);
        $newDescription=$this->generateNewMetadata($description,$custom_description);
        $newTitle=$this->generateNewMetadata($title,$custom_title);
        $category->setMetaKeywords($newKeywords);
        $category->setMetaDescription($newDescription);
        $category->setMetaTitle($newTitle);        
    }
    
   public function add_custom_metadata($observer)
   {
       
        $event=$observer->getEvent();
        $product=$event->getProduct();
        $customModel=Mage::getModel('metadata/metadata');
        $customCollection=$customModel->getCollection();
        $page=Mage::getSingleton('core/session')->getPage($page);
        foreach ($customCollection as $custom)
        {        
            $this->generateForProductsOfCat($product,$custom);
            $this->generateForProductsOfCatandChild($product,$custom);
            $this->applyToProductsOfField($product,$custom);             
        }
 
  
  }
   public function add_custom_category_metadata($observer){
       //
       
       //для категорий
     $page=Mage::getBlockSingleton('page/html_pager')->getCurrentPage();
     Mage::getSingleton('core/session')->setPage($page);
     $event=$observer->getEvent();
     $category=$event->getCategory();
     if(empty($page)){
         $page=1;
     }
     $custom_model=Mage::getModel('metadata/metadata');//get custom model
     $custom_collection=$custom_model->getCollection();     
        foreach($custom_collection as $item){
        $var=$this->getCategory1($item);
        $item->setCategoryId($var);
        $item->save();
         $this->applyToCategory($category,$item);
         $this->applyToCategoryAndChild($category,$item);
         $this->applyToCategoriesOfField($category,$item);
    }
       
            
        }
     
   
}
