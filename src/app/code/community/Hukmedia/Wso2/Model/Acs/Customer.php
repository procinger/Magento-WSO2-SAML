<?php

class Hukmedia_Wso2_Model_Acs_Customer {

    protected $claimAttributes = null;

    /**
     * Set the claim attributes from the WSO2 response
     *
     * @param $claimAttributes
     * @return $this
     */
    public function setClaimAttributes($claimAttributes) {
        $this->claimAttributes = $claimAttributes;

        return $this;
    }

    /**
     * Get the claim attributes from the WSO2 response
     *
     * @return array
     */
    public function getClaimAttributes() {
        return $this->claimAttributes;
    }

    /**
     * Get data helper
     *
     * @return Hukmedia_Wso2_Helper_Data
     */
    public function getWsoHelper() {
        return Mage::helper('hukmedia_wso2');
    }


    /**
     * Get claim helper
     *
     * @return Hukmedia_Wso2_Helper_Claim
     */
    public function getClaimHelper() {
        return Mage::helper('hukmedia_wso2/claim');
    }

    /**
     * Get customer model by scim id
     *
     * @param string $scimId
     * @return Mage_Customer_Model_Customer
     */
    public function load($scimId = null) {
        $customer = Mage::getModel('customer/customer')->getCollection()
            ->addFieldToFilter('wso_scim_id', $scimId)
            ->getFirstItem();

        if(!$customer->getId()) {
            $customer = $this->_createCustomer($customer);
        }

        $customer = $this->_updateCustomer($customer);

        return $customer;
    }

    /**
     * Create a new customer model
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_Model_Customer
     */
    private function _createCustomer(Mage_Customer_Model_Customer $customer) {
        $customer->setStore(Mage::app()->getStore())
            ->setPassword(md5(time() . uniqid()));

        return $customer;
    }

    /**
     * Update an existing customer model
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_Model_Customer
     */
    private function _updateCustomer(Mage_Customer_Model_Customer $customer) {
        $claimMappingConfigCollection = $this->getClaimHelper()->getClaimMappingConfigCollection();
        $claimAttributes = $this->getClaimAttributes();

        foreach($claimMappingConfigCollection as $claimMappingConfig) {
            $customer->setData($claimMappingConfig->getLocalAttribute(), current($claimAttributes[$claimMappingConfig->getName()]));
        }

        $customer->save();

        return $customer;
    }
}