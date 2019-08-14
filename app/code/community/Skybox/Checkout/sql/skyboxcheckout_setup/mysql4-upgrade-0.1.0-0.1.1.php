<?php

echo 'Running This Upgrade: ' . get_class($this) . "\n <br /> \n";


$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$installer->startSetup();
/*
$installer->addAttribute('quote_address', 'customs_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'customs_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'Taxes_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'Taxes_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'Handling_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'Handling_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'Shipping_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'Shipping_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'Insurance_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'Insurance_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'Clearence_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'Clearence_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'Duties_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'Duties_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'Adjust_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'Adjust_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));*/

/*$installer->addAttribute('quote_address', 'concepts_skybox', array('type' => 'text', 'visible' => true, 'required' => false));*/


/*$installer->addAttribute('order', 'subtotal_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('order', 'base_subtotal_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('order', 'grand_total_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('order', 'base_grand_total_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));

$installer->addAttribute('order', 'customs_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order', 'customs_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('order', 'taxes_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order', 'taxes_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('order', 'handling_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order', 'handling_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('order', 'shipping_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order', 'shipping_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('order', 'insurance_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order', 'insurance_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('order', 'clearence_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order', 'clearence_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('order', 'duties_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order', 'duties_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('order', 'adjust_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order', 'adjust_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('order', 'concepts_skybox', array('type' => 'text', 'visible' => true, 'required' => false));
*/

$installer->endSetup();
