<?php

/**
 * @category   TC
 * @package    TC_ProductSegmentation
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_ProductSegmentation_Block_Adminhtml_Catalog_Category_Tab_Product
    extends Mage_Adminhtml_Block_Catalog_Category_Tab_Product
{
    const SEGMENT_DATA_ATTRIBUTE_CODE = 'segment_data';

    /** @var Varien_Data_Form */
    protected $_form;

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

        $this->_prepareForm();

        return parent::_prepareLayout();
    }

    /**
     * Append form html to the end of output
     *
     * @param string $html
     *
     * @return string
     */
    protected function _afterToHtml($html)
    {
        $formHtml = $this->_form ? $this->_form->toHtml() : '';

        return $html . $formHtml;
    }

    /**
     * Prepare form for segmentation
     */
    protected function _prepareForm()
    {
        $head = $this->getLayout()->getBlock('head');
        if (!$head) {
            // no head block then loaded via ajax
            return;
        }

        $head->setCanLoadRulesJs(true);
        /** @var $model TC_ProductSegmentation_Model_Rule */
        $model = Mage::getModel('tc_productsegmentation/rule');
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');

        $data = $this->getCategory()->getData(self::SEGMENT_DATA_ATTRIBUTE_CODE);
        $rule = array();
        parse_str($data, $rule);
        $model->loadPost($rule && isset($rule['rule']) ?$rule['rule'] : array());
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('tc/productsegmentation/renderer/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/promo_catalog/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldSet = $form->addFieldset(
            'conditions_fieldset', array(
                'legend' => Mage::helper('catalogrule')->__(
                    'Conditions (leave blank for all products)'
                ))
        )->setRenderer($renderer);

        $fieldSet->addField(
            'conditions', 'text', array(
               'name'     => 'conditions',
               'label'    => Mage::helper('catalogrule')->__('Conditions'),
               'title'    => Mage::helper('catalogrule')->__('Conditions'),
               'required' => true,
          )
        )
            ->setData('rule', $model)
            ->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setBaseUrl(Mage::getBaseUrl());

        $this->_form = $form;
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
        return sprintf('var %s = new SegmentationBuilder()', $this->_getBuilderJsObjectName());
    }

    /**
     * Returns unique js object name for segmentation builder
     *
     * @return string
     */
    protected function _getBuilderJsObjectName()
    {
        return $this->getId() . '_segmentation';
    }
}
