<?php

/* @var $installer Skybox_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

// Product
$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'skybox_category_id', array(
    'group'             => 'Prices',
    'type'              => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'backend'           => 'eav/entity_attribute_backend_array',
    'frontend'          => '',
    'label'             => 'SkyboxCheckout Category',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'skyboxcatalog/product_attribute_source_categories',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => true,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => true,
    'comparable'        => false,
    'visible_on_front'  => true,
    'unique'            => false,
    'apply_to'          => 'simple,configurable,virtual',
    'is_configurable'   => false
));

$setup->addAttributeToGroup(Mage_Catalog_Model_Product::ENTITY, 'Default', 'Prices', 'skybox_category_id', 100);

// Category
$setup->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'skybox_category_id', array(
//$setup->updateAttribute('catalog_category', 'skybox_category_id', array(
    'group'         => 'SkyboxCheckout',
    'type'          => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'backend'       => 'eav/entity_attribute_backend_array',
    'frontend'      => '',
    'label'         => 'Default Commodity',
    'input'         => 'select',
    'source'        => 'skyboxcatalog/product_attribute_source_categories',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => true,
    'default'       => ''
));

// Category
$setup->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'skybox_category_id_select', array(
//$setup->updateAttribute('catalog_category', 'skybox_category_id', array(
    'group'         => 'SkyboxCheckout',
    'type'          => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'backend'       => 'eav/entity_attribute_backend_array',
    'frontend'      => '',
    'label'         => 'Select Commodity',
    'input'         => 'select',
    'source'        => 'skyboxcatalog/product_attribute_source_categories',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => true,
    'default'       => ''
));

$setup->addAttributeToGroup(Mage_Catalog_Model_Category::ENTITY, 'Default', 'SkyboxCheckout', 'skybox_category_id', 100);

$installer->endSetup();