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
class CDD_FlyoutMenu_Block_Catalognavigation
    extends Mage_Catalog_Block_Navigation
{
    public function getStoreCategories()
    {
        $helper = Mage::helper('FlyoutMenu');
        return $helper->getStoreCategories();
    }

    protected function _renderCategoryMenuItemHtml
    (
        $category,
        $level = 0,
        $isLast = false,
        $isFirst = false,
        $isOutermost = false,
        $outermostItemClass = '',
        $childrenWrapClass = '',
        $noEventAttributes = false
    ){
        $type = Mage::registry('set_layout_menu');
        if (!$category->getIsActive()) {
            return '';
        }
        $html = array();
        $html1 = array();
        // get all children
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array)$category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = ($children && $childrenCount);

        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
            if ($child->getIsActive() && $child->getLevel() < 5) {
                $activeChildren[] = $child;
            }
        }

        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);

        // prepare list item html classes
        $classes = array();
        $classes[] = 'level' . $level;
        $classes[] = 'nav-' . $this->_getItemPosition($level);
        $linkClass = '';
        $classa="";

        if($level==0){
            $classa="drop";
        }

        if ($isOutermost && $outermostItemClass) {
            $classes[] = $outermostItemClass;
            $linkClass = ' class="'.$outermostItemClass." ".$classa.'"';
        }

        if ($this->isCategoryActive($category)){
            $classes[] = 'active';
        }

        if ($isFirst) {
            if($level == (int)Mage::registry('level_class')){
                if(Mage::registry('none_li_first_class') == "0")
                    $classes[] = 'first';
            }
            else
                $classes[] = 'first';
        }
        if ($isLast) {
            if($level == (int)Mage::registry('level_class')){
                if(Mage::registry('none_li_last_class') == "0")
                    $classes[] = 'last';
            }
            else
                $classes[] = 'last';
        }
        if ($hasActiveChildren) {
            $classes[] = 'parent';
        }

        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }
        if ($hasActiveChildren && !$noEventAttributes) {
             $attributes['onmouseover'] = 'toggleMenu(this,1)';
             $attributes['onmouseout'] = 'toggleMenu(this,0)';
        }

        // assemble list item with attributes
        //type =1,2,3
        switch($type)
        {
            case 1:
                $html[] = '<k><li><a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
                $html[] =  $this->escapeHtml($category->getName());
                $html[] = '</a></li>';

                // render children
                $htmlChildren = '';
                $j = 0;
                foreach ($activeChildren as $child) {
                    $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                        $child,
                        ($level + 1),
                        ($j == $activeChildrenCount - 1),
                        ($j == 0),
                        false,
                        $outermostItemClass,
                        $childrenWrapClass,
                        $noEventAttributes
                    );
                    $j++;
                }
                if (!empty($htmlChildren)) {
                    $html[] = $htmlChildren;
                }
            break;
            case 2:
                if($level==0){
                    $htmlLi='<div class="col_1 ">';
                }
                else {
                    $htmlLi = '<li';
                    foreach ($attributes as $attrName => $attrValue) {
                        $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
                    }
                    $htmlLi .= '>';
                }

                $html[] = $htmlLi;
                if ($level==0){
                    $html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
                    $html[] = '<span>'. $this->escapeHtml($category->getName()).'</span>';
                    $html[] = '</a>';
                }
                else {
                    $html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
                    $html[] =  $this->escapeHtml($category->getName());
                    $html[] = '</a>';
                }

                // render children
                $htmlChildren = '';
                $j = 0;

                foreach ($activeChildren as $child){
                    $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                        $child,
                        ($level + 1),
                        ($j == $activeChildrenCount - 1),
                        ($j == 0),
                        false,
                        $outermostItemClass,
                        $childrenWrapClass,
                        $noEventAttributes
                    );
                    $j++;
                }
                if (!empty($htmlChildren)) {
                    if ($childrenWrapClass) {
                        $html[] = '<div class="' . $childrenWrapClass . '">';
                    }
                    $html[] = '<ul class="level' . $level . '">';
                    $html[] = $htmlChildren;
                    $html[] = '</ul>';
                    if ($childrenWrapClass) {
                        $html[] = '</div>';
                    }
                }
                if($level==0){
                    $html[]='</div>';
                }
                else {
                    $html[] = '</li>';
                }

            break;
			case 3:
			{
				$htmlLi = '<li';
				foreach ($attributes as $attrName => $attrValue) {
					$htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
				}
				$htmlLi .= '>';
					
				$html[] = $htmlLi;
				
				$html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
				$html[] =  $this->escapeHtml($category->getName());
				$html[] = '</a>';
					
					// render children
					$htmlChildren = '';
					$j = 0;
					foreach ($activeChildren as $child) {
						$htmlChildren .= $this->_renderCategoryMenuItemHtml(
							$child,
							($level + 1),
							($j == $activeChildrenCount - 1),
							($j == 0),
							false,
							$outermostItemClass,
							$childrenWrapClass,
							$noEventAttributes
						);
						$j++;
					}
					
					if (!empty($htmlChildren)) {
						if ($childrenWrapClass) {
							$html[] = '<div class="' . $childrenWrapClass . '">';
						}
						$html[] = '<ul class="level' . $level. '">';
						$html[] = $htmlChildren;
						$html[] = '</ul>';
						if ($childrenWrapClass) {
							$html[] = '</div>';
						}
					}
						$html[] = '</li>';
				break;
			}
			case 4:
			{
					if($level !=0)
					{
						$f="<f>";
					}
					if($level==0)
					{
						$n="<n>";
					}
					$htmlLi = $n.$f.'<li';
					foreach ($attributes as $attrName => $attrValue) {
					$htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
					}
					$htmlLi .= '>';	
					$html[] = $htmlLi;
					$html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
					$html[] =  $this->escapeHtml($category->getName());
					$html[] = '</a>';
					if($level==0)
					{
						$html[]="<k>";
					}

					// render children
					$htmlChildren = '';
					$j = 0;
					foreach ($activeChildren as $child) {
						$htmlChildren .= $this->_renderCategoryMenuItemHtml(
							$child,
							($level + 1),
							($j == $activeChildrenCount - 1),
							($j == 0),
							false,
							$outermostItemClass,
							$childrenWrapClass,
							$noEventAttributes
						);
						$j++;
					}
					if (!empty($htmlChildren)) {
						if ($childrenWrapClass) {
							$html[] = '<div class="' . $childrenWrapClass . '">';
						}
						$ulfirst="";
						$ullast="";
						
						//$ulfirst='<ul class="level' . $level . '">';
						//$ullast='</ul>';
						
						$html[] =$ulfirst.$htmlChildren.$ullast;
					
						if ($childrenWrapClass) {
							$html[] = '</div>';
						}
					}
					$html[] = '</li>';
					break;
			}
			case 5:
			{
				 if($level==1 )
					{
						$htmlLi='<div class="col_1">';
					}
					else
					{
							$htmlLi = '<li';
							foreach ($attributes as $attrName => $attrValue) {
								$htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
							}
							$htmlLi .= '>';
					
					}
					$html[] = $htmlLi;
							if ($level==1)
							{
								$html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
								$html[] = '<span>'. $this->escapeHtml($category->getName()).'</span>';
								$html[] = '</a>';
							}
							else
							{
								$html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
								$html[] =  $this->escapeHtml($category->getName());
								$html[] = '</a>';
							}
					
					// render children
					$htmlChildren = '';
					$j = 0;
					foreach ($activeChildren as $child) {
						$htmlChildren .= $this->_renderCategoryMenuItemHtml(
							$child,
							($level + 1),
							($j == $activeChildrenCount - 1),
							($j == 0),
							false,
							$outermostItemClass,
							$childrenWrapClass,
							$noEventAttributes
						);
						$j++;
					}
					if (!empty($htmlChildren)) {
						if ($childrenWrapClass) {
							$html[] = '<div class="' . $childrenWrapClass . '">';
						}
						$ulfirst="";
						$ullast="";
						if($level==0)
						{
							$count=0;
							if( $childrenCount >5 )
							{
								$count=5;
							}
							else
							{
								$count = $childrenCount;
							}
							$ulfirst='<div class="dropdown_'.$count.'columns">';
							$ullast='</div>';
						}
						else
						{
							$ulfirst='<ul class="level' . $level . '">';
							$ullast='</ul>';
						}
					
						$html[] =$ulfirst.$htmlChildren.$ullast;
					
						if ($childrenWrapClass) {
							$html[] = '</div>';
						}
					}

				   if($level==1)
					{
						$html[]='</div>';
					}
					else
					{
						
					$html[] = '</li>';
						
					}
					break;
			}
			case 6:
			{
					$htmlLi = '<li';
					foreach ($attributes as $attrName => $attrValue) {
						$htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
					}
					$htmlLi .= '>';
					$html[] = $htmlLi;
					$html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
					$html[] =  $this->escapeHtml($category->getName());
					$html[] = '</a>';
				
					// render children
					$htmlChildren = '';
					$j = 0;
					foreach ($activeChildren as $child) {
						$htmlChildren .= $this->_renderCategoryMenuItemHtml(
							$child,
							($level + 1),
							($j == $activeChildrenCount - 1),
							($j == 0),
							false,
							$outermostItemClass,
							$childrenWrapClass,
							$noEventAttributes
						);
						$j++;
					}
					if (!empty($htmlChildren)) {
						if ($childrenWrapClass) {
							$html[] = '<div class="' . $childrenWrapClass . '">';
						}
						$ulfirst="";
						$ullast="";
						if($level==0)
						{
							
							$ulfirst='<div class="dropdown_1columns"><div class="col_1"><ul class="levels">';
							$ullast='</ul></div></div>';
						}
						else
						{
							$ulfirst='<ul class="level' . $level. '">';
							$ullast='</ul>';
						}
					
						$html[] =$ulfirst.$htmlChildren.$ullast;
					
						if ($childrenWrapClass) {
							$html[] = '</div>';
						}
					}
					$html[] = '</li>';

					break;
			}
			default:
			{
				   $htmlLi = '<li';
					foreach ($attributes as $attrName => $attrValue) {
						$htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
					}
					$htmlLi .= '>';
					$html[] = $htmlLi;

					$html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
					$html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
					$html[] = '</a>';

					// render children
					$htmlChildren = '';
					$j = 0;
					foreach ($activeChildren as $child) {
						$htmlChildren .= $this->_renderCategoryMenuItemHtml(
							$child,
							($level + 1),
							($j == $activeChildrenCount - 1),
							($j == 0),
							false,
							$outermostItemClass,
							$childrenWrapClass,
							$noEventAttributes
						);
						$j++;
					}
					if (!empty($htmlChildren)) {
						if ($childrenWrapClass) {
							$html[] = '<div class="' . $childrenWrapClass . '">';
						}
						$html[] = '<ul class="level' . $level . '">';
						$html[] = $htmlChildren;
						$html[] = '</ul>';
						if ($childrenWrapClass) {
							$html[] = '</div>';
						}
					}

					$html[] = '</li>';
			}
			
		}
        $html = implode("\n", $html);
        return $html;
    }
	 
}

 