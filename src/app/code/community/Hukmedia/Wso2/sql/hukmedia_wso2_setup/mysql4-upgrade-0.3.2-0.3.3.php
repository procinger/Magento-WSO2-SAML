<?php
    $this->startSetup();
    $installer = new Mage_Customer_Model_Entity_Setup();

    $installer->addAttribute('customer', 'wso_scim_id', array(
        'type' => 'varchar',
        'input' => 'label',
        'label' => 'WSO SCIM ID',
        'user_defined' => false,
        'required' => false,
        'visible' => true,
        'visible_on_front' => false,
    ));

    $formsArray = array(
        'adminhtml_customer',
        'customer_address',
        'customer_register',
    );

    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'wso_scim_id')
        ->setUsedInForms($formsArray)
        ->save();


    $this->endSetup();
