<?php
/**
 * @category TC
 * @package TC_Import
 * @author Aleksandr Smaga <smagaan@gmail.com>
 */

class CDD_CustomWidget_Block_Menu extends Mage_Cms_Block_Block{
	const CACHE_TAG = 'main_menu_cms_block';

	protected function _construct(){
		$this->addData(array(
			'cache_lifetime' => 86000,
			'cache_tags' => array(self::CACHE_TAG),
		));
	}
}
