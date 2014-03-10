<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_CleanUp_Block_Adminhtml_Catalog extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->setTemplate('widget/view/container.phtml');
    }

    /**
     * Adds child blocks
     */
    protected function _prepareLayout()
    {
        $this->_headerText = Mage::helper('tc_cleanup')->__('Catalog Clean Up');
        $this->setChild('data', $this->getLayout()->createBlock('tc_cleanup_adminhtml/catalog_data'));
    }

    /**
     * Returns view HTML
     *
     * @return string
     */
    public function getViewHTML()
    {
        return $this->getChildHtml();
    }
}
