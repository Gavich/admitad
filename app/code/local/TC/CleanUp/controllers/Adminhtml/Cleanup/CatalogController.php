<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_CleanUp_Adminhtml_Cleanup_CatalogController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Add title and breadcrumbs
     */
    protected function _init()
    {
        $helper = Mage::helper('tc_cleanup');

        $this->_title(Mage::helper('core')->__('System'));
        $this->_title($helper->__('Tools'));
        $this->_title($helper->__('Catalog Clean Up'));

        $this->_setActiveMenu('system/tools');
        $this->_addBreadcrumb($helper->__('Catalog Clean Up'), $helper->__('Catalog Clean Up'));
    }

    /**
     * Show statistics
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_init();

        $this->_addContent($this->getLayout()->createBlock('tc_cleanup_adminhtml/catalog'));
        $this->renderLayout();
    }

    /**
     * Removes disabled products
     */
    public function removeProductsAction()
    {
        try {
            Mage::getResourceModel('tc_cleanup/cleanup')->cleanUpProducts();
            $this->_getSession()->addSuccess($this->__('Catalog products were cleaned up.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    /**
     * Removes disabled categories
     */
    public function removeCategoriesAction()
    {
        try {
            Mage::getResourceModel('tc_cleanup/cleanup')->cleanUpCategories();
            $this->_getSession()->addSuccess($this->__('Catalog categories were cleaned up.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }
}
