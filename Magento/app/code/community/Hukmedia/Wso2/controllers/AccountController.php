<?php

require_once 'Mage/Customer/controllers/AccountController.php';

class Hukmedia_Wso2_AccountController extends Mage_Customer_AccountController {

    protected $_oneLogin = null;

    public function preDispatch() {
        parent::preDispatch();

        require_once(dirname(dirname(__FILE__)) . '/_onelogin_lib_loader.php');
        $this->_oneLogin = new OneLogin_Saml2_Auth(Mage::helper('hukmedia_wso2/config')->getWso2SamlConfig());
    }

    public function getOneLogin() {
        return $this->_oneLogin;
    }

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
                $samlHelper->sendAuthnRequest($login['username'], $login['password'], false, false);
            } else {
                $session->addError($this->__('Login and password are required.'));
            }
        }

        $this->_loginPostRedirect();
    }


    public function logoutAction() {
        /* delete wso2 <> magento session relation */
        $session = $this->_getSession();
        $customer = $session->getCustomer();
        $sessionIndexModel = Mage::getModel('hukmedia_wso2/sessionindex');
        $sessionIndexModel->loadByEmail($customer->getEmail());
        $sessionIndexModel->delete();

        /* logout from magento */
        $this->_getSession()->logout();

        /* logout from wso2 */
        $this->getOneLogin()->logout(Mage::getBaseUrl() . '/customer/account/logoutSuccess/' , array(), $customer->getEmail(), $session->getWsoSessionIndex());
    }

}