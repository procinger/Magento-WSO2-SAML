<?php

class Hukmedia_Wso2_Helper_Saml extends Mage_Core_Helper_Abstract {

    /**
     * Set cURL options for the WSO2 AuthNRequest
     *
     * @param null $username
     * @param null $password
     * @param $samlRequest
     * @return array
     */
    private function getCurlOptions($username = null, $password = null, $samlRequest, $relayState = false) {
        $options = array(
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT_MS => 1000,
            CURLOPT_URL => Mage::helper('hukmedia_wso2/config')->getSamlSsoUrl(),
            CURLOPT_POST => true,
            CURLOPT_FOLLOWLOCATION => false,
        );

        if($username && $password) {
            $secToken = base64_encode("$username:$password");
            $options[CURLOPT_USERPWD] = $secToken;
            $options[CURLOPT_POSTFIELDS] = "sectoken=" . urlencode($secToken) . "&SAMLRequest=" . urlencode($samlRequest);
        } else {
            $options[CURLOPT_POSTFIELDS] = "SAMLRequest=" . urlencode($samlRequest);
        }

        if($relayState){
            $options[CURLOPT_POSTFIELDS] .= "&RelayState=" . urlencode($relayState);
        }

        return $options;
    }

    /**
     * Send the AuthNRequest to WSO2 Identity Server
     *
     * @param $username
     * @param $password
     * @param bool|false $forceAuthn
     * @param bool|false $isPassive
     */
    public function sendAuthnRequest($username, $password, $forceAuthn = false, $isPassive = false, $relayState = false) {
        $SamlSettings = new OneLogin_Saml2_Settings(Mage::helper('hukmedia_wso2/config')->getWso2SamlConfig());
        $AuthnRequest = new OneLogin_Saml2_AuthnRequest($SamlSettings, $forceAuthn, $isPassive);

        $samlRequest = $AuthnRequest->getRequest(false);
        $curlOptions = $this->getCurlOptions($username, $password, $samlRequest, $relayState);

        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, $curlOptions);
        curl_exec($curlHandle);
        $curlInfo = curl_getinfo($curlHandle);
        curl_close($curlHandle);

        if(!empty($curlInfo['redirect_url'])) {
            Mage::app()->getResponse()->setRedirect($curlInfo['redirect_url']);
            return;
        }
    }
}
