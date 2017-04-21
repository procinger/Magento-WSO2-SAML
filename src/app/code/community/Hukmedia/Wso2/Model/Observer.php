<?php
class Hukmedia_Wso2_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function login(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $fullActionName = $event->getAction()->getFullActionName();

        if($fullActionName !== 'customer_account_login') {
            return;
        }

        if(Mage::app()->getRequest()->getParam('forceLogin') == true) {
            return;
        }

        $event->getLayout()->getUpdate()->addHandle('hukmedia_wso2_login_check');
    }
}