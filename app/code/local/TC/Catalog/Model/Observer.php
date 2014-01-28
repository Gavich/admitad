<?php

class TC_Catalog_Model_Observer
{
    /**
     * Process redirect if p=1 found in request, event catalog_controller_category_init_after
     *
     * @param Varien_Event_Observer $event
     */
    public function categoryInitAfter(Varien_Event_Observer $event)
    {
        /** @var  Mage_Core_Controller_Request_Http $request */
        $request = $event->getControllerAction()->getRequest();
        if ($request->getParam('p') == 1) {
            /** @var Mage_Core_Controller_Response_Http $response */
            $response = $event->getControllerAction()->getResponse();

            $urlParams                 = array();
            $urlParams['_escape']      = false;
            $urlParams['_use_rewrite'] = true;
            $urlParams['_query']       = array_diff_key($request->getParams(), array_flip(array('p', 'id')));

            $response->setRedirect(Mage::getUrl('*/*/*', $urlParams));
        }
    }
} 
