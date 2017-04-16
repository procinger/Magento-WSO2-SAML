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

        if (empty($errors)) {
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
        $websiteId = Mage::app()->getWebsite()->getId();
        $session = Mage::getSingleton('customer/session');
        $this->getOneLogin()->processResponse();
        /* Something went wrong, check the WSO2 log. Redirecting back to login form */
        if(!$this->getOneLogin()->isAuthenticated()) {
            Mage::helper('hukmedia_wso2')->log(print_r($this->getOneLogin()->getErrors(), true));
            $this->_redirect('customer/account/login/', array('forceWsoLogin' => true));
            return;
        }

        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($this->getOneLogin()->getNameId());

        /* Check if customer exist, otherwise create a new customer object */
        if (!$customer->getId()) {
            $wso2ClaimAttributes = $this->getOneLogin()->getAttributes();
            $customer->setStore(Mage::app()->getStore())
                ->setFirstname($wso2ClaimAttributes['firstname'][0])
                ->setLastname($wso2ClaimAttributes['lastname'][0])
                ->setEmail($this->getOneLogin()->getNameId())
                ->setPassword(md5(time() . uniqid()))
                ->save();

            Mage::helper('hukmedia_wso2')->log('created new user: ' . $this->getOneLogin()->getNameId());

            $customer->loadByEmail($this->getOneLogin()->getNameId());
        }

        /* Magento login */
        $session->loginById($customer->getId());
        $session->setCustomerAsLoggedIn($customer);
        $session->setWsoSessionIndex($this->getOneLogin()->getSessionIndex());

        Mage::helper('hukmedia_wso2')->log('sign in user: ' . $this->getOneLogin()->getNameId());

        /* Save WSO2 session for remote SLO */
        $sessionIndexModel = Mage::getModel('hukmedia_wso2/sessionindex');
        $sessionIndexModel->loadByCustomerId($customer->getId());
        if(!$sessionIndexModel->getId()) {
            $sessionIndexModel->setMagentoSessionId($session->getEncryptedSessionId());
            $sessionIndexModel->setMagentoUserName($customer->getEmail());
            $sessionIndexModel->setMagentoCustomerId($customer->getId());
            $sessionIndexModel->setWsoSessionIndex($this->getOneLogin()->getSessionIndex());
        } else {
            $sessionIndexModel->setMagentoSessionId($session->getEncryptedSessionId());
            $sessionIndexModel->setWsoSessionIndex($this->getOneLogin()->getSessionIndex());
        }
        $sessionIndexModel->save();

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