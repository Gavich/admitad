<?php

class Rganzhuyev_MetaTitle_Block_MetaTitle extends Mage_Page_Block_Html_Head
{
    /**
     * Retrieve title from header meta
     *
     * @return string
     */
    public function getTitle()
    {
        if (empty($this->_data['title'])) {
            $this->_data['title'] = $this->getDefaultTitle();
        }
        if (!Mage::registry('my_title')) {
            Mage::register('my_title', $this->_data['title']);
        } else {
            $this->_data['title'] = Mage::registry('my_title');
        }
        return htmlspecialchars(html_entity_decode(trim($this->_data['title']), ENT_QUOTES, 'UTF-8'));
    }
}

