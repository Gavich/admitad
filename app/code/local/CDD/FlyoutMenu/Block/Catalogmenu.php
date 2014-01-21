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
 * @author     �mer Bildirici jun.
 * @package    medusa_default
 * @copyright  Copyright 2012 �mer Bildirici jun. (http://www.creative-d2.de)
 * @license    All rights reserved.
 * @version    1.1
 */

class CDD_FlyoutMenu_Block_Catalogmenu
    extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
    public function getCatalogmenu()
    {
        if (!$this->hasData('catalogmenu')){
            $this->setData('catalogmenu', Mage::registry('catalogmenu'));
        }
        return $this->getData('catalogmenu');
    }*/
    
    public function getBlockContent()
    {
        $ulClass = $this->getData('ul_class');
        $levelClass = $this->getData('level_class') ? $this->getData('level_class') : "0";
        $type = $this->getData('set_layout_menu');

        $ulId = $this->getData('ul_id') ? "id='$this->getData('ul_id')'" : '';
        $categoryId = $this->getData('category') ? $this->getData('category') : Mage::app()->getStore()->getRootCategoryId();
        $noneUlRoot	= $this->getData('none_ul_root') ? $this->getData('none_ul_root') : "0";
        $noneFirstLi = $this->getData('none_li_first_class') ? $this->getData('none_li_first_class')  : "0";

        $noneLastLi = $this->getData('none_li_last_class') ? $this->getData('none_li_last_class') : "0";

        Mage::register('category_id_widget', $categoryId);
        Mage::register('none_li_first_class',$noneFirstLi);
        Mage::register('none_li_last_class',$noneLastLi);
        Mage::register('level_class', $levelClass);
        Mage::register('set_layout_menu', $type);

        //Add Categories to Menu
        $navigation = new CDD_FlyoutMenu_Block_Catalognavigation();

        if($noneUlRoot == "0"){
            $first = "<ul class='$ulClass' $ulId >";
            $last = "</ul>";
        }
        else {
            $first = "";
            $last = "";
        }

        $html = trim($navigation->renderCategoriesMenuHtml($levelClass,"level-top"));
        $arr=explode("<k>",$html);
        if($type==1){
            $my_html="";
            for($i=0;$i<(count($arr)-1);$i++){
                if($i%10==0)
                    $my_html.='<div class="col_1"><ul>';
                $my_html.=$arr[$i+1];
                if(($i+1)%10==0 || ($i+1)==(count($arr)-1))
                 $my_html.='</ul></div>';
            }
            $html=$my_html;

        }

        $arrmain=array();
        if($type ==4)
        {
            $arrmain=explode("<k>",$html);
            $my_html="";
            for($i=0;$i<(count($arrmain)-1);$i++){
                $countlisub = count(explode("<n>",$arrmain[$i]));
                $arrsub = explode("<n>",$arrmain[$i]);
                $countsub = count(explode("<f>",$arrsub[0]));
                $arr=explode("<f>",$arrsub[0]);
                $divfirst="";

                if(($countsub/10)>3){
                    $divfirst='<div class="dropdown_3columns">';
                }
                else {
                    $divfirst='<div class="dropdown_'.ceil($countsub/10).'columns">';
                }

                $my_html.=$arrsub[$countlisub-1];
                if($countsub>1){
                    $my_html.=$divfirst;
                    for($j=0;$j<$countsub;$j++){
                        if($j%10==0){
                            $my_html.='<div class="col_1"><ul>';
                        }
                        $my_html.=$arr[$j+1];
                        if(($j+1)%10==0 ||$j==(count($arr)-1)){
                            $my_html.='</ul></div>';
                        }
                    }
                    $my_html.='</div>';
                }
            }
            $html=$my_html;
        }
        $ulfirst="";
        $ullast="";
        if($type==3)
        {
            $ulfirst='<div class="col_1"><ul class="levels">';
            $ullast='</ul></div>';
        }
        Mage::unregister('category_id_widget');
        Mage::unregister('none_li_first_class');
        Mage::unregister('none_li_last_class');
        Mage::unregister('level_class');
        Mage::unregister('set_layout_menu');

        return $first.$ulfirst.$html.$ullast.$last;
    }
}
