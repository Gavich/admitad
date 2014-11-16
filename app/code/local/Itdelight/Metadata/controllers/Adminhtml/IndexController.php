<?php

class Itdelight_Metadata_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action 
{
   public function indexAction(){
     
        $this->loadLayout();
        // $this->_addContent($this->getLayout()->createBlock('metadata/adminhtml_metadata'));
        $this->renderLayout();
        
   }
   
     public function editAction()
    {
        
         
        $id = $this->getRequest()->getParam('id', null);
        
        $model = Mage::getModel('metadata/metadata');
        if ($id) {
            $model->load((int) $id);
            Mage::register('current_metadata', $model);
            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                   $this->_redirect('*/*/');
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('metadata')->__('Metadata does not exist'));
                $this->_redirect('*/*/');
            }
        }
      
 
       $this->loadLayout();

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
     $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
      if ($block) {
          $block->setStoreId($model->getStoreId());
       }
        $this->renderLayout();
        
    }
       public function categoriesAction()
    {
      
        $this->getResponse()->setBody($this->getLayout()->createBlock('metadata/adminhtml_tree')->toHtml());
         $this->loadLayout();

    }
    public function newAction()
     {
        
        $this->loadLayout();
       
         $model = Mage::getModel('metadata/metadata');
         Mage::register('current_metadata', $model);
         $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
                $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($model->getStoreId());
        }
        $this->renderLayout();
     }
     public function categoriesJsonAction(){
         
     $this->getResponse()->setBody($this->getLayout()->createBlock('metadata/adminhtml_tree')
        ->getCategoryChildrenJson($this->getRequest()->getParam('category')));
     }
     
     public function saveAction()
     {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $metadataId      = $this->getRequest()->getParam('category_id');
        $isEdit         = (int)($this->getRequest()->getParam('id'));
        $metadata=Mage::getModel('metadata/metadata');        
          
         $data=$this->getRequest()->getPost();
         try {
             if($isEdit){
                 $metadata->load($isEdit);
                 $metadata->setKeywords($data['keywords']);
                 $metadata->setTitle($data['title']);
                 $metadata->setDescription($data['description']);
                 $metadata->setProducts($data['products']);
                 $metadata->setCategoryId($data['category_id']);
                 $metadata->setCategories($data['categories']);
                 $metadata->setCatChild($data['cat_child']);
                 $metadata->setProdCat($data['prod_cat']);
                 $metadata->setProdChildcat($data['prod_childcat']);
                 $metadata->setProdForm($data['prod_form']);
                 $metadata->setCat($data['cat']);
                 $metadata->setCatForm($data['cat_form']);
                 $metadata->setCategoryIds($data['category_ids']);
                                $metadata->save();
                
             }else{
              
                $metadata->setData($data);
                $metadata->save();
                $metadataId = $metadata->getId();
             }
               
             /**
                 * Do copying data to stores
                 */
                if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo=>$storeFrom) {
                        $newMetadata = Mage::getModel('catalog/product')
                            ->setStoreId($storeFrom)
                            ->load($metadataId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }

                $this->_getSession()->addSuccess($this->__('Metadata has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setProductData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
            if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $metadataId,
                '_current'=>true
            ));
        }else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }                        
     }
     public function deleteAction(){
  
        if($id=$this->getRequest()->getParam('id')){
 
            $metadata=Mage::getModel('metadata/metadata')->load($id);
            try{
                $metadata->delete();
                $this->_getSession()->addSuccess($this->__('The metadata has been deleted.'));
            }catch(Exception $e){
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()
            ->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
     }
     public function massDeleteAction()
    {
        $productIds = $this->getRequest()->getParam('metadata');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select metadata.'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                        $product = Mage::getSingleton('metadata/metadata')->load($productId);
                        
                        $product->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($productIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }
    public function gridOnlyAction()
    {

        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('metadata/adminhtml_metadata_grid')
                ->toHtml()
        );
    }

}
