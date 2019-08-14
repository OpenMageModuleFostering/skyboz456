<?php

/**
 * Skybox Catalog
 *
 * @category    Skybox
 * @package     Skybox_Catalog
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Catalog_Model_Product_Attribute_Source_Categories extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public $_options = null;

    public function getAllOptions()
    {
        if ($this->_options === null) {
            /* var @api_checkout Skybox_Checkout_Model_Api_Checkout */
            $api_checkout = Mage::getModel('skyboxcheckout/api_checkout');
            $categories = $api_checkout->GetCategories();

            $item = array(
                'value' => '',
                'label' => '',
            );

            $data[] = $item;

            foreach ($categories as $obj) {
                $item = array(
                    'value' => $obj->IdCommoditie,
                    'label' => $obj->Description,
                );
                $data[] = $item;
            }
            $this->_options = $data;
        }
        return $this->_options;
    }
}