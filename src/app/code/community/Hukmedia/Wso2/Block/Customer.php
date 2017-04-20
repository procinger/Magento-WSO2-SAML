<?php

class Hukmedia_Wso2_Block_Customer extends Mage_Core_Block_Template {

    public function getRedirectForm($login) {
        $samlHelper = Mage::helper('hukmedia_wso2/saml');

        if(!is_array($login)) {
            $username = null;
            $password = null;
            $forceAuthn = false;
            $isPassiv = true;
        } else {
            $username = $login['username'];
            $password = $login['password'];
            $forceAuthn = true;
            $isPassiv = false;
        }

        return $samlHelper->buildAuthnRequest($username, $password, $forceAuthn, $isPassiv, Mage::helper('core/http')->getHttpReferer());
    }
}