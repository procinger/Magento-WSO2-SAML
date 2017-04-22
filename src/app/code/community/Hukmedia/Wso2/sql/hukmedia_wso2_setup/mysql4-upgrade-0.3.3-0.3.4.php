<?php
    $this->startSetup();
    $installer = new Mage_Customer_Model_Entity_Setup();

    $formsArray = array(
        'adminhtml_customer',
        'customer_account_create'
    );

    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'wso_scim_id')
        ->setUsedInForms($formsArray)
        ->save();


    $this->endSetup();
