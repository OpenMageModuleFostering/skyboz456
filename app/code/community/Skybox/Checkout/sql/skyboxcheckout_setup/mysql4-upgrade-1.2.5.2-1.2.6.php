<?php

echo 'Running This Upgrade: '.get_class($this)."\n <br /> \n";


// @note: Force Table entities created!

$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$installer->startSetup();

// Table: sales_flat_quote_address

$installer->addAttribute('quote_address', 'subtotal_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('quote_address', 'base_subtotal_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('quote_address', 'grand_total_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('quote_address', 'base_grand_total_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));

$installer->addAttribute('quote_address', 'customs_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'customs_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'taxes_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'taxes_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'handling_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'handling_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'shipping_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'shipping_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'insurance_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'insurance_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'clearence_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'clearence_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'duties_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'duties_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'others_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'others_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'adjust_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'adjust_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('quote_address', 'concepts_skybox', array('type' => 'text', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_address', 'rmt_skybox', array('type' => 'text', 'visible' => true, 'required' => false));

// Table: sales_flat_quote_item

$installer->addAttribute('quote_item', 'id_product_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('quote_item', 'price_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('quote_item', 'customs_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_item', 'shipping_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_item', 'insurance_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_item', 'total_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));

$installer->addAttribute('quote_item', 'price_usd_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('quote_item', 'customs_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_item', 'shipping_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_item', 'insurance_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_item', 'total_usd_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('quote_item', 'guid_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('quote_item', 'row_total_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));

$installer->addAttribute('quote_item', 'base_price_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('quote_item', 'base_price_usd_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('quote_item', 'adjust_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_item', 'adjust_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_item', 'adjust_label_skybox', array('type' => 'text', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_item', 'concepts_skybox', array('type' => 'text', 'visible' => true, 'required' => false));
$installer->addAttribute('quote_item', 'rmt_skybox', array('type' => 'text', 'visible' => true, 'required' => false));


// Table: sales_flat_order_item

$installer->addAttribute('order_item', 'id_product_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('order_item', 'price_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('order_item', 'customs_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order_item', 'shipping_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order_item', 'insurance_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order_item', 'total_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));

$installer->addAttribute('order_item', 'price_usd_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('order_item', 'customs_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order_item', 'shipping_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order_item', 'insurance_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order_item', 'total_usd_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('order_item', 'guid_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('order_item', 'row_total_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));

$installer->addAttribute('order_item', 'base_price_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('order_item', 'base_price_usd_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
$installer->addAttribute('order_item', 'adjust_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order_item', 'adjust_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order_item', 'adjust_label_skybox', array('type' => 'text', 'visible' => true, 'required' => false));
$installer->addAttribute('order_item', 'concepts_skybox', array('type' => 'text', 'visible' => true, 'required' => false));
$installer->addAttribute('order_item', 'rmt_skybox', array('type' => 'text', 'visible' => true, 'required' => false));


// Table: sales_flat_order

$installer->addAttribute('order', 'subtotal_skybox', array('type' => 'varchar', 'visible' => false, 'required' => false));
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

$installer->addAttribute('order', 'others_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order', 'others_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('order', 'adjust_total_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));
$installer->addAttribute('order', 'adjust_total_usd_skybox', array('type' => 'varchar', 'visible' => true, 'required' => false));

$installer->addAttribute('order', 'concepts_skybox', array('type' => 'text', 'visible' => true, 'required' => false));
$installer->addAttribute('order', 'rmt_skybox', array('type' => 'text', 'visible' => true, 'required' => false));


$installer->endSetup();
