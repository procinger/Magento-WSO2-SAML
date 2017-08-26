<?php

    $installer = new Mage_Customer_Model_Entity_Setup();
    $installer->startSetup();
    $installer->updateAttribute('customer', 'wso_scim_id', 'frontend_input', 'text');
    $installer->endSetup();