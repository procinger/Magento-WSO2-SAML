<?php

class Hukmedia_Wso2_Model_Resource_SessionIndex extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('hukmedia_wso2/sessionindex', 'session_index_id');
    }

}