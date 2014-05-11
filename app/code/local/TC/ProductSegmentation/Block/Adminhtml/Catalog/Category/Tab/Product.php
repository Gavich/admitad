<?php

/**
 * @category   TC
 * @package    TC_ProductSegmentation
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_ProductSegmentation_Block_Adminhtml_Catalog_Category_Tab_Product
    extends Mage_Adminhtml_Block_Catalog_Category_Tab_Product
{
    /** @var Varien_Data_Form */
    protected $_form;

    /**
     * Override prepare layout to add "segmentation" button
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                     'label'   => Mage::helper('adminhtml')->__('Configure segment'),
                     'onclick' => $this->_getBuilderJsObjectName() . '.build()',
                     'class'   => 'go segmentation-btn'
                )
            );

        $this->setChild('segmentation_button', $button);

        $helper = $this->_getHelper();
        $select = new Varien_Data_Form_Element_Select();
        $select
            ->setForm(new Varien_Data_Form())
            ->setName('use_segment')
            ->setValues(array(
                 TC_ProductSegmentation_Helper_Data::ACTION_NONE         => $helper->__('Nothing'),
                 TC_ProductSegmentation_Helper_Data::ACTION_INTERSECTION => $helper->__('Intersection with segment'),
                 TC_ProductSegmentation_Helper_Data::ACTION_DIFFERENCE   => $helper->__('Difference with segment'),
            ));
        $this->setData('use_segment_selector', $select);

        // override buttons onclick in order to handle grid actions inside segmentation object
        $resetButton  = $this->getChild('reset_filter_button');
        $resetButton->setData(
            'onclick', sprintf('%s.resetFilter(%s)', $this->_getBuilderJsObjectName(), $this->getJsObjectName())
        );
        $filterButton = $this->getChild('search_button');
        $filterButton->setData(
            'onclick', sprintf('%s.doFilter(%s)', $this->_getBuilderJsObjectName(), $this->getJsObjectName())
        );

        $this->_prepareForm();

        return $this;
    }

    /**
     * Override to do additional work with filters
     *
     * @param array $data
     *
     * @return void
     */
    protected function _setFilterValues($data)
    {
        parent::_setFilterValues($data);

        $useSegmentValue = isset($data['use_segment'])
            ? $data['use_segment']
            : TC_ProductSegmentation_Helper_Data::ACTION_NONE;

        // populate selector value
        $this->getData('use_segment_selector')->setValue($useSegmentValue);
        if (isset($data['general'], $data['general']['segment_data'])
            && $useSegmentValue !== TC_ProductSegmentation_Helper_Data::ACTION_NONE
        ) {
            /** @var $model TC_ProductSegmentation_Model_Rule */
            $model = Mage::getModel('tc_productsegmentation/rule');
            /** @var Mage_Catalog_Model_Category $tmpCategory */
            $tmpCategory = Mage::getModel('catalog/category')->setData(
                TC_ProductSegmentation_Helper_Data::SEGMENT_DATA_ATTRIBUTE_CODE,
                $data['general']['segment_data']
            );
            $model->loadPost($this->_getHelper()->getCategorySegmentData($tmpCategory));
            $this->_getHelper()->applyConditions($this->getCollection(), $model, $useSegmentValue);
        }
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
        $model->loadPost($this->_getHelper()->getCategorySegmentData($this->getCategory()));
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $model->setJsFormObject('rule_conditions_fieldset');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');
        $form->setBaseUrl(Mage::getBaseUrl());

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('tc/productsegmentation/renderer/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/promo_catalog/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldSet = $form->addFieldset(
            'conditions_fieldset', array(
            'legend' => Mage::helper('catalogrule')->__(
                'Conditions (leave blank for all products)'
            ))
        )->setRenderer($renderer);

        $fieldSet->addField('conditions', 'text', array(
               'name'     => 'conditions',
               'label'    => Mage::helper('catalogrule')->__('Conditions'),
               'title'    => Mage::helper('catalogrule')->__('Conditions'),
               'required' => true,
          ))
            ->setData('rule', $model)
            ->setRenderer(Mage::getBlockSingleton('rule/conditions'));

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
        $html = $this->_getSegmentFilterHtml();
        $html .= $this->geSegmentationButtonHtml();
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

    /**
     * Returns segment filter HTML
     *
     * @return string
     */
    protected function _getSegmentFilterHtml()
    {
        $html = sprintf('<label>%s:&nbsp</label>', Mage::helper('tc_productsegmentation')->__('Filter using segment'));
        $html .= $this->getData('use_segment_selector')->getElementHtml();

        return $html;
    }

    /**
     * Getter for helper object
     *
     * @return TC_ProductSegmentation_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('tc_productsegmentation');
    }
}
