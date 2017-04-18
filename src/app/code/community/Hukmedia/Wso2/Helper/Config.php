<?php

class Hukmedia_Wso2_Helper_Config extends Mage_Core_Helper_Abstract {

    /**
     * Get WSO2 Identity Server config
     *
     * @return array
     */
    public function getWso2SamlConfig() {
        return array_merge(
            $this->getSamlSpConfig(),
            $this->getSamlIdpConfig(),
            $this->getSecurityConfig()
        );
    }

    /**
     * Get WSO2 Identity Server Service Provider settings
     *
     * @return array
     */
    public function getSamlSpConfig() {
        return array(
            'sp' => array(
                'entityId' => $this->getSamlSpEntityId(),
                'assertionConsumerService' => array(
                    'url' => Mage::getBaseUrl() . 'wso2/saml2/acs',
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
                ),
                'singleLogoutService' => array(
                    'url' => Mage::getBaseUrl() . 'wso2/saml2/sls',
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
                ),
                'NameIDFormat' => $this->getNameIdFormat(),
                'privateKey' => $this->getSamlSpPrivateKey(),
                'x509cert' => $this->getSamlSpCertificate(),
            )
        );
    }

    /**
     * Get SPs entity id
     *
     * @return string
     */
    public function getSamlSpEntityId() {
        return Mage::getStoreConfig('hukmedia_wso2_saml/sp/entityid', Mage::app()->getStore());
    }

    /**
     * Get WSO2 Identity Server specified NameIDFormat
     *
     * @return string
     */
    public function getNameIdFormat() {
        return Mage::getStoreConfig('hukmedia_wso2_saml/sp/nameidformat', Mage::app()->getStore());
    }

    /**
     * Get SPs private key
     *
     * @return string
     */
    public function getSamlSpPrivateKey() {
        return Mage::getStoreConfig('hukmedia_wso2_saml/sp/privatekey', Mage::app()->getStore());
    }

    /**
     * Get SPs private certificate
     *
     * @return mstring
     */
    public function getSamlSpCertificate() {
        return Mage::getStoreConfig('hukmedia_wso2_saml/sp/x509', Mage::app()->getStore());
    }

    public function getRedirectMessage() {
        return Mage::getStoreConfig('hukmedia_wso2_saml/sp/redirect_message', Mage::app()->getStore());
    }
    /**
     * Get WSO2 Identity Server Identity Provider settings
     *
     * @return array
     */
    public function getSamlIdpConfig() {
        return array(
            'idp' => array(
                'entityId'              => $this->getSamlEntityId(),
                'singleSignOnService'   => array(
                    'url' => $this->getSamlSsoUrl(),
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
                ),
                'singleLogoutService'   => array(
                    'url' => $this->getSamlSloUrl(),
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
                ),
                'x509cert' => $this->getSamlX509Cert(),
            )
        );
    }

    /**
     * Get WSO2 Identity Server Entity ID
     *
     * @return string
     */
    public function getSamlEntityId() {
        return Mage::getStoreConfig('hukmedia_wso2_saml/idp/entityid', Mage::app()->getStore());
    }

    /**
     * Get WSO2 Identity Server SSO Url
     *
     * @return string
     */
    public function getSamlSsoUrl() {
        return Mage::getStoreConfig('hukmedia_wso2_saml/idp/sso_url', Mage::app()->getStore());
    }

    /**
     * Get WSO2 Identity Server SLO Url
     *
     * @return string
     */
    public function getSamlSloUrl() {
        return Mage::getStoreConfig('hukmedia_wso2_saml/idp/slo_url', Mage::app()->getStore());
    }

    /**
     * Get WSO2 Identity Server x509 certificate
     *
     * @return string
     */
    public function getSamlX509Cert() {
        return Mage::getStoreConfig('hukmedia_wso2_saml/idp/x509', Mage::app()->getStore());
    }

    /**
     * Get WSO2 Security Config
     *
     * @return array
     */
    public function getSecurityConfig () {
        return array(
            'security' => array(
                'signMetadata' => false,
                'nameIdEncrypted' => false,
                'authnRequestsSigned' => true,
                'logoutRequestSigned' => false,
                'logoutResponseSigned' => false,
                'wantMessagesSigned' => false,
                'wantAssertionsSigned' => false,
                'wantAssertionsEncrypted' => false
            )
        );
    }
}