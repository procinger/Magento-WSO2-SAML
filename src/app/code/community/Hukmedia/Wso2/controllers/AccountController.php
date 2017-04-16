<?php

require_once 'Mage/Customer/controllers/AccountController.php';

class Hukmedia_Wso2_AccountController extends Mage_Customer_AccountController {
    /**
     * Overwrite Magento loginAction method,
     * to check if a customer is already logged in
     */
    public function loginAction() {
        /* Something went wrong, force login form */
        if($this->getRequest()->getParam('forceWsoLogin') != true) {
            $samlHelper = Mage::helper('hukmedia_wso2/saml');
            $samlHelper->sendAuthnRequest(null, null, false, true);
        }

        /* No WSO2 session established, start login procedure */
        parent::loginAction();
    }

    /**
     * Overwrite Magento loginPostAction to send the
     * WSO2 Identity Server login request
     */
    public function loginPostAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/');
            return;
        }

        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session = $this->_getSession();

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                $samlHelper = Mage::helper('hukmedia_wso2/saml');
                $samlHelper->sendAuthnRequest($login['username'], $login['password'], true, false, Mage::helper('core/http')->getHttpReferer());
                return;
            } else {
                $session->addError($this->__('Login and password are required.'));
            }
        }

        $this->_loginPostRedirect();
    }


    public function logoutAction() {
        $session = $this->_getSession();
        $customer = $session->getCustomer();

        try {
            /* delete wso2 <> magento session relation */
            $sessionIndexModel = Mage::getModel('hukmedia_wso2/sessionindex');
            $sessionIndexModel->loadByEmail($customer->getEmail());
            $sessionIndexModel->delete();

            /* logout from magento */
            $this->_getSession()->logout();
            Mage::helper('hukmedia_wso2')->log('sign out user: ' . $customer->getEmail(), Zend_log::INFO);

            /* logout from wso2 */
            $oneLogin = new OneLogin_Saml2_Auth(Mage::helper('hukmedia_wso2/config')->getWso2SamlConfig());
            $oneLogin->logout(Mage::getBaseUrl() . '/customer/account/logoutSuccess/', array(), $customer->getEmail(), $session->getWsoSessionIndex());
        } catch (Exception $e) {
            Mage::helper('hukmedia_wso2')->log('failed to sign out user: ' . $customer->getEmail() . '. Reason: ' . $e->getMessage(), Zend_Log::ERR);
        }
    }

}