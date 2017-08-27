<?php

class Hukmedia_Wso2_Helper_Claim extends Mage_Core_Helper_Abstract {

    /**
     * Get claim config mapping
     *
     * @return bool|Varien_Data_Collection
     */
    public function getClaimMappingConfigCollection() {
        $claimMappingConfig = Mage::getStoreConfig('hukmedia_wso2_saml/claim/mapping', Mage::app()->getStore());
        if(empty($claimMappingConfig)) {
            return false;
        }

        $claimMappingConfig = unserialize($claimMappingConfig);
        if(!is_array($claimMappingConfig)) {
            return false;
        }


        $claimConfigCollection = new Varien_Data_Collection();
        foreach($claimMappingConfig as $key => $claimMappingRow) {
            $claimConfig = new Varien_Object();
            $claimConfig->setData($claimMappingRow['local_customer_attribute'], $claimMappingRow['wso2_customer_attribute']);
            $claimConfig->setData('local_attribute', $claimMappingRow['local_customer_attribute']);
            $claimConfig->setData('name', $claimMappingRow['wso2_customer_attribute']);
            $claimConfig->setData('is_required', boolval($claimMappingRow['wso2_attribute_is_required']));
            $claimConfig->setData('attribute_name_format', $claimMappingRow['wso2_attribute_name_format']);

            $claimConfigCollection->addItem($claimConfig);
        }

        return $claimConfigCollection;
    }

    public function hasRequiredClaims($repsonseClaim) {
        $wsoHelper = Mage::helper('hukmedia_wso2');
        $claimMappingConfigCollection = $this->getClaimMappingConfigCollection();
        $hasRequiredClaims = true;
        $hasLocalScimIdMapping = false;

        foreach($claimMappingConfigCollection as $claimMappingConfig) {
            if ($claimMappingConfig->getWsoScimId() && !$hasLocalScimIdMapping) {
                $hasLocalScimIdMapping = true;

                if(!array_key_exists($claimMappingConfig->getWsoScimId(), $repsonseClaim) || empty (current($repsonseClaim[$claimMappingConfig->getWsoScimId()]))) {
                    $wsoHelper->log(sprintf('Missing "%s" claim attribute. Check Service Provider configuration in WSO2 Server.', $claimMappingConfig->getWsoScimId()), Zend_Log::ERR);
                }
            }

            if($claimMappingConfig->getIsRequired() &&
                (!array_key_exists($claimMappingConfig->getName(), $repsonseClaim) || empty (current($repsonseClaim[$claimMappingConfig->getName()])))) {
                $wsoHelper->log(sprintf('Missing "%s" claim attribute in SAML Response. Check Service Provider configuration in WSO2 Identity Server.', $claimMappingConfig->getName()), Zend_Log::ERR);
                $hasRequiredClaims = false;
            }
        }

        if(!$hasLocalScimIdMapping) {
            $wsoHelper->log('Missing "WSO SCIM ID" mapping. Check claim mapping config in Magento System > Configuration > Saml Settings > Claim Mapping.', Zend_Log::ERR);
            $hasRequiredClaims = false;
        }

        return $hasRequiredClaims;
    }
}