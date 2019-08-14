<?php
/*
$installer = $this;
$installer->startSetup();

$installer->run("
    --DROP TABLE IF EXISTS {$this->getTable('skybox_log_service')};

    CREATE TABLE {$this->getTable('skybox_log_service')} (
		`service_id` int(11) NOT NULL auto_increment,
		`action` text NOT NULl,
		`request` text NOT NULl,
		`response` text NULL,
		`timestamp_request` timestamp NOT NULL default CURRENT_TIMESTAMP,
		`timestamp_response` timestamp NULL
      PRIMARY KEY (`service_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8
  ");

$installer->endSetup();

echo 'Running This Upgrade: '.get_class($this)."\n <br /> \n";
die("Exit for now");
//exit();
*/

$installer = $this;
$installer->startSetup();

//$table = $installer->getConnection()
//    //->newTable($installer->getTable('skyboxcore/skybox_log_service'))
//    ->newTable($installer->getTable('skybox_log_service'))
//    ->addColumn('service_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
//        'identity' => true,
//        'unsigned' => true,
//        'nullable' => false,
//        'primary' => true,
//    ), 'Service Id')
//    ->addColumn('action', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
//        'nullable' => false,
//    ), 'Action')
//    ->addColumn('request', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
//        'request' => false,
//    ), 'Request')
//    ->addColumn('response', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
//        'request' => false,
//    ), 'Response')
//    ->addColumn('timestamp_request', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
//        'request' => false,
//    ), 'Timestamp Request')
//    ->addColumn('timestamp_response', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
//        'request' => false,
//    ), 'Timestamp Response');
//$installer->getConnection()->createTable($table);

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('skybox_log_service')}`;
CREATE TABLE `skybox_log_service` (
	`service_id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Service Id' , 
	`action` TEXT NOT NULL COMMENT 'Action' , 
	`request` TEXT NULL COMMENT 'Request' , 
	`response` TEXT NULL COMMENT 'Response' , 
	`timestamp_request` TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp Request' , 
	`timestamp_response` TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp Response' , 
	PRIMARY KEY (`service_id`) 
)
COMMENT='skybox_log_service' ENGINE=INNODB CHARSET=utf8 COLLATE=utf8_general_ci;
");
$installer->endSetup();
