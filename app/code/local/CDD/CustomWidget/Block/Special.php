<?php
/**
 * Medusa for Magento 1.7.0.0
 * Design and Development by creative-d2 design&development (http://www.creative-d2.de)
 * Distributed by ThemeForest (http://themeforest.net)
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @author     Ömer Bildirici jun.
 * @package    medusa_default
 * @copyright  Copyright 2012 Ömer Bildirici jun. (http://www.creative-d2.de)
 * @license    All rights reserved.
 * @version    1.1
 */
class CDD_CustomWidget_Block_Special extends Mage_Catalog_Block_Product
{
    public function getProductSpecialPrice()
    {
        $collections = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter(array(
                                array(
                                
                                        'attribute' => 'special_price',
                                
                                        'gt'        => array(0),
                                
                                        ),
                                    )
                                );
                                echo count($collections);
        foreach($collections as $c)
        {
            print_r($c->getData());
        }
                                //print_r($collections);
                                exit('abc');
    }
}

?>