<?php

class Hukmedia_Wso2_Helper_Saml extends Mage_Core_Helper_Abstract {
    /**
     * Send the AuthNRequest to WSO2 Identity Server
     *
     * @param $username
     * @param $password
     * @param bool|false $forceAuthn
     * @param bool|false $isPassive
     */
    public function buildAuthnRequest($username, $password, $forceAuthn = false, $isPassive = false, $relayState = false) {
        $SamlSettings = new OneLogin_Saml2_Settings(Mage::helper('hukmedia_wso2/config')->getWso2SamlConfig());
        $AuthnRequest = new OneLogin_Saml2_AuthnRequest($SamlSettings, $forceAuthn, $isPassive);

        $samlRequest = $AuthnRequest->getRequest(false);
        $secToken = '';
        if($username && $password) {
            $secToken = urlencode(base64_encode("$username:$password"));
            $secToken = sprintf('sectoken=%s&', $secToken);
        }

        $samlRequest = urlencode($samlRequest);
        $relayState = urlencode($relayState);

        $urlParameters = sprintf('%sSAMLRequest=%s&RelayState=%s', $secToken, $samlRequest, $relayState);
        $ssoEndpoint = Mage::helper('hukmedia_wso2/config')->getSamlSsoUrl();

        return sprintf('%s?%s', $ssoEndpoint, $urlParameters);
    }
}
