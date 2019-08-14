<?php

echo 'Running This Upgrade: '.get_class($this)."\n <br /> \n";


$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$installer->startSetup();
/*
$installer->addAttribute('quote_address', 'others_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'others_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('order', 'others_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order', 'others_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
*/
$installer->endSetup();

die("Exit for now");

    