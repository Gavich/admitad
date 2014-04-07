<?php

/**
 * @category   TC
 * @package    TC_Seo
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_ProductSegmentation_Block_Adminhtml_Catalog_Category_Tab_Product
    extends Mage_Adminhtml_Block_Catalog_Category_Tab_Product
{
    /**
     * Override prepare layout to add "segmentation" button
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                     'label'   => Mage::helper('adminhtml')->__('Segmentation'),
                     'onclick' => $this->_getBuilderJsObjectName() . '.build()',
                     'class'   => 'go segmentation-btn'
                )
            );

        $this->setChild('segmentation_button', $button);

        return parent::_prepareLayout();
    }

    /**
     * Returns button HTML
     *
     * @return string
     */
    public function geSegmentationButtonHtml()
    {
        return $this->getChildHtml('segmentation_button');
    }

    /**
     * Override to add output of "segmentation" button
     *
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = $this->geSegmentationButtonHtml();
        $html .= parent::getMainButtonsHtml();

        return $html;
    }

    /**
     * Returns JS for segmentation builder
     *
     * @return string
     */
    public function getAdditionalJavaScript()
    {
        $options = array();

        return sprintf('var %s = new SegmentationBuilder(%s)', $this->_getBuilderJsObjectName(), json_encode($options));
    }

    /**
     * Returns unique js object name for segmentation builder
     *
     * @return string
     */
    protected function _getBuilderJsObjectName()
    {
        return $this->getId() . 'segmentation';
    }
}
