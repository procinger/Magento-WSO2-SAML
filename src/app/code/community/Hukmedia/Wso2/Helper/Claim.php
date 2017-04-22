<?php

class Hukmedia_Wso2_Helper_Claim extends Mage_Core_Helper_Abstract {

    /**
     * Get claim config mapping
     *
     * @return bool|Varien_Object
     */
    public function getClaimMappingConfig() {
        $claimMappingConfig = Mage::getStoreConfig('hukmedia_wso2_saml/claim/mapping', Mage::app()->getStore());
        if(empty($claimMappingConfig)) {
            return false;
        }

        $claimMappingConfig = unserialize($claimMappingConfig);
        if(!is_array($claimMappingConfig)) {
            return false;
        }


        $claimConfig = new Varien_Object();
        foreach($claimMappingConfig as $key => $claimMappingRow) {
            $claimConfig->setData($claimMappingRow['local_customer_attribute'], $claimMappingRow['wso2_customer_attribute']);
        }

        return $claimConfig;
    }

    public function hasRequiredClaims($repsonseClaim) {
        $wsoHelper = Mage::helper('hukmedia_wso2');
        $claimMappingConfig = $this->getClaimMappingConfig();
        $hasRequiredClaims = true;

        if(!$claimMappingConfig->getWsoScimId()) {
            $wsoHelper->log('Missing "WSO SCIM ID" mapping. Check claim mapping config in Magento.', Zend_Log::ERR);
            $hasRequiredClaims = false;
        }

        if(!array_key_exists($claimMappingConfig->getWsoScimId(), $repsonseClaim) || empty (current($repsonseClaim[$claimMappingConfig->getWsoScimId()]))) {
            $wsoHelper->log(sprintf('Missing "%s" claim attribute. Check Service Provider config in WSO2 Server.', $claimMappingConfig->getWsoScimId()), Zend_Log::ERR);
            $hasRequiredClaims = false;
        }

        return $hasRequiredClaims;
    }
}