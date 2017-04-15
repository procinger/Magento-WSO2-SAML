<?php

class Hukmedia_Wso2_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * @param string $message
     * @param int $level
     */
    public function log($message = '', $level = Zend_Log::INFO) {
        Mage::log($message, $level, 'wso2.log');
    }
}