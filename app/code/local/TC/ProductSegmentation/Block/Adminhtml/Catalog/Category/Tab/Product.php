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
                     'onclick' => $this->getJsObjectName() . '.doSegment()',
                     'class'   => 'go'
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
}
