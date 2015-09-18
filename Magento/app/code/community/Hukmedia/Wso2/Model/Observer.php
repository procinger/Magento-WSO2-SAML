<?php

class Hukmedia_Wso2_Model_Observer {

    /*
     * @param Varien_Event_Observer $observer
     */
    public function useNativeMagentoLoginForm(Varien_Event_Observer $observer) {
/*
        if (!Mage::helper('hukmedia_wso2/config')->useNativeMagentoLogin()) {
            return;
        }

        $block = $observer->getBlock();
        if ($block instanceof Mage_Checkout_Block_Onepage_Login) {
            $this->insertWso2HtmlAttributes($observer);
        }

        if ($block instanceof Mage_Customer_Block_Form_Login) {
            $this->insertWso2HtmlAttributes($observer);
        }
*/
    }

    private function insertWso2HtmlAttributes(Varien_Event_Observer $observer) {
        $loginActionUrl = Mage::helper('hukmedia_wso2/config')->getBaseUrl() . '/commonauth';
        $block = $observer->getBlock();

        if (Mage::helper('hukmedia_wso2/config')->showWso2HiddenFields()) {
            $visibility = 'block';
        } else {
            $visibility = 'none';
        }

        // Onepage checkout login page
        if ($block instanceof Mage_Checkout_Block_Onepage_Login) {
            $loginActionXpath = "//body/div[@class='col2-set']/div[@class='col-2']/form";
            $loginFormXpath = "//body/div[@class='col2-set']/div[@class='col-2']/form/div[@class='fieldset']/ul[@class='form-list']";
            $emailInputXpath = "li/label[@for='login-email']/parent::li/div/input";
            $passwordInputXpath = "li/label[@for='login-password']/parent::li/div/input";
        }

        // Customer account login page
        if ($block instanceof Mage_Customer_Block_Form_Login) {
            $loginActionXpath = "//form";
            $loginFormXpath = "//form/div[@class='col2-set']/div[@class='col-2 registered-users']/div[@class='content fieldset']/ul[@class='form-list']";
            $emailInputXpath = "li/label[@for='email']/parent::li/div/input";
            $passwordInputXpath = "li/label[@for='pass']/parent::li/div/input";
        }

        $html = $observer->getTransport()->getHtml();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xml = simplexml_import_dom($dom);

        $loginAction = current($xml->xpath($loginActionXpath));
        $loginAction->attributes()->action = $loginActionUrl;

        $loginForm = current($xml->xpath($loginFormXpath));

        $emailInput = current($loginForm->xpath($emailInputXpath));
        $emailInput->attributes()->name = 'username';
        $emailInput->attributes()->type = 'text';

        $passwordInput = current($loginForm->xpath($passwordInputXpath));
        $passwordInput->attributes()->name = 'password';

        $li = $loginForm->addChild('li');
        $li->addAttribute('style', 'display:' . $visibility);

        $div = $li->addChild('div');
        $div->addAttribute('class', 'input-box');

        $sessionIndexInput = $div->addChild('input');
        $sessionIndexInput->addAttribute('type', 'text');
        $sessionIndexInput->addAttribute('name', 'sessionDataKey');
        $sessionIndexInput->addAttribute('id', 'sessionDataKey');
        $sessionIndexInput->addAttribute('style', 'display:' . $visibility);
        $sessionIndexInput->addAttribute('value', Mage::app()->getRequest()->getParam('sessionDataKey'));
        $sessionIndexInput->addAttribute('readonly', 'true');

        $observer->getTransport()->setHtml($xml->asXML());
    }
}