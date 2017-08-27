<?php

class Hukmedia_Wso2_Saml2Controller extends Mage_Core_Controller_Front_Action {

    protected $_oneLogin = null;

    public function _construct()
    {
        $this->_oneLogin = new OneLogin_Saml2_Auth(Mage::helper('hukmedia_wso2/config')->getWso2SamlConfig());
    }

    public function getOneLogin() {
        return $this->_oneLogin;
    }

    /**
     * SAML Service Provider Metadata
     */
    public function metadataAction() {
        $oneLoginSettings = $this->getOneLogin()->getSettings();
        $oneLoginMetadata = $oneLoginSettings->getSPMetadata();
        $oneLoginMetadataErrors = $oneLoginSettings->validateMetadata($oneLoginMetadata);

        if (empty($oneLoginMetadataErrors)) {
            $this->getResponse()->setHeader('Content-type', 'text/xml');
            $this->getResponse()->setBody($oneLoginMetadata);
        } else {
            throw new OneLogin_Saml2_Error(
                'Invalid SP metadata: '.implode(', ', $oneLoginMetadataErrors),
                OneLogin_Saml2_Error::METADATA_SP_INVALID
            );
        }
    }

    /**
     * Assertion Consumer Service
     */
    public function acsAction() {
        $wsoHelper = Mage::helper('hukmedia_wso2');
        $session = Mage::getSingleton('customer/session');
        $this->getOneLogin()->processResponse();

        /* Something went wrong, check the WSO2 log. Redirecting back to login form */
        if(!$this->getOneLogin()->isAuthenticated()) {
            $wsoHelper->log(print_r($this->getOneLogin()->getErrors(), true));
            $this->_redirect('customer/account/login/', array('forceLogin' => true));
            return;
        }

        $wso2ClaimAttributes = $this->getOneLogin()->getAttributes();
        $claimHelper = Mage::helper('hukmedia_wso2/claim');

        if(!$claimHelper->hasRequiredClaims($wso2ClaimAttributes)) {
            $session->addError($this->__('Login failed. There is a technical issue.'));
            $this->_redirect('customer/account/login/', array('forceLogin' => true));
            return;
        }

        $claimMappingConfigCollection = $claimHelper->getClaimMappingConfigCollection();
        foreach($claimMappingConfigCollection as $claimMappingConfig) {
            if($claimMappingConfig->getWsoScimId()) {
                $scimId = current($wso2ClaimAttributes[$claimMappingConfig->getWsoScimId()]);
                break;
            }
        }

        $customer = Mage::getModel('hukmedia_wso2/acs_customer')
            ->setClaimAttributes($wso2ClaimAttributes)
            ->load($scimId);

        /* Magento login */
        $session->loginById($customer->getId());
        $session->setCustomerAsLoggedIn($customer);
        $session->setWsoSessionIndex($this->getOneLogin()->getSessionIndex());

        Mage::helper('hukmedia_wso2')->log('sign in user: ' . $this->getOneLogin()->getNameId());

        /* Save WSO2 session for remote SLO */
        Mage::getModel('hukmedia_wso2/sessionindex')
            ->saveSessionData($session, $customer, $this->getOneLogin()->getSessionIndex());

        /* Redirect to customer dashboard */
        $this->_redirect('customer/account/');
    }

    /**
     * Single Logout Service
     */
    public function slsAction() {
        if($this->getRequest()->getPost('RelayState')) {
            $this->_redirectUrl($this->getRequest()->getPost('RelayState'));
            return;
        }

        $samlRequest = $this->getRequest()->getPost('SAMLRequest');
        $oneLoginSettings = new OneLogin_Saml2_Settings(Mage::helper('hukmedia_wso2/config')->getWso2SamlConfig());
        $logoutRequest = new OneLogin_Saml2_LogoutRequest($oneLoginSettings, $samlRequest);

        if(!$logoutRequest->isValid()) {
            Mage::helper('hukmedia_wso2')->log('invalid logout request received: ' . print_r($logoutRequest, true), Zend_Log::ERR);
            return;
        }
        try {
            $logoutRequestXml = $logoutRequest->getXml();

            $sessionIndex = current($logoutRequest->getSessionIndexes($logoutRequestXml));

            $sessionIndexModel = Mage::getModel('hukmedia_wso2/sessionindex');
            $sessionIndexModel->loadBySessionIndex($sessionIndex);
            /* destroy the session from incomming wso2 logout request */
            session_destroy();

            /* load the magento customer session and destroy */
            /* this is a ugly solution, how can a session be loaded by id or somtheing else? */
            /* someting like ...
            /* $session = Mage::getSingleton('core/session')->loadByAnyId($sessionIndexModel->getMagentoSessionId()) */
            /* $session->logout()->renew() */

            /* i'm not happy with this solution :'-( */
            session_id($sessionIndexModel->getMagentoSessionId());
            session_start();
            session_destroy();
            $sessionIndexModel->delete();

            Mage::helper('hukmedia_wso2')->log('succesfully signed out ' . $sessionIndexModel->getMagentoUserName() . ' WSO Session Index ' . $sessionIndexModel->getWsoSessionIndex());
        } catch (Exception $e) {
            Mage::helper('hukmedia_wso2')->log('failed to sign out ' . $sessionIndexModel->getMagentoUserName() . '. WSO Session Index ' . $sessionIndexModel->getWsoSessionIndex(), Zend_Log::ERR);
        }
    }
}