<?php

/**
 * @category   TC
 * @package    TC_Seo
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_ProductSegmentation_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function test()
    {
        $this->setChild(
            'reset_filter_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                         'label'   => Mage::helper('adminhtml')->__('Reset Filter'),
                         'onclick' => $this->getJsObjectName() . '.resetFilter()',
                    )
                )
        );
    }
}
