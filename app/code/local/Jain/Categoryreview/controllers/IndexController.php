<?php
class Jain_Categoryreview_IndexController extends Mage_Core_Controller_Front_Action
{

	public function catpostAction()
    {


            $data   = $this->getRequest()->getPost();

           $session    = Mage::getSingleton('core/session');
            /* @var $session Mage_Core_Model_Session */
            $review     = Mage::getModel('categoryreview/categoryreview')->setData($data);
            /* @var $review Mage_Review_Model_Review */


                try {
                    $date = Mage::app()->getLocale()->storeDate(Mage::app()->getStore(), null, true);
                    $review ->setCreatedAt($date->toString('YYYY-MM-dd HH:mm:ss'));
                    $review ->save();
                    $session->addSuccess('Ваш отзыв был принят на модерацию.');
                }
                catch (Exception $e) {
                    $session->setFormData($data);
                    $session->addError('Невозможно разместить отзыв.');
                }


        if ($redirectUrl = Mage::getSingleton('review/session')->getRedirectUrl(true)) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        $this->_redirectReferer();

    }

}