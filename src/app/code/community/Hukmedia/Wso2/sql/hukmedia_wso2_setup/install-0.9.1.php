<?php

$installer = $this;
$installer->startSetup();

$sessionTable = $installer->getConnection()
    ->newTable($installer->getTable('hukmedia_wso2/sessionindex'))
    ->addColumn('session_index_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'auto_increment' => true,
        'identitiy' => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID')
    ->addColumn('magento_session_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => false,
    ), 'Magneto Session ID')
    ->addColumn('magento_user_name',Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'Email Username')
    ->addColumn('magento_customer_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Magento Customer Entity Id')
    ->addColumn('wso_session_index', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'WSO2 IS SessionIndex');

$installer->getConnection()->createTable($sessionTable);
$installer->endSetup();