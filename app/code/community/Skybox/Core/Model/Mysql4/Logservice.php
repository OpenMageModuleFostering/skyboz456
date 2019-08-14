<?php

/**
 * Skybox Core
 *
 * @category    Skybox
 * @package     Skybox_Core
 * @copyright   Copyright (c) 2014 SkyBOX Checkout, Inc. (http://www.skyboxcheckout.com)
 */

/**
 *
 * Skybox Log Service Model
 *
 */
class Skybox_Core_Model_Mysql4_Logservice extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        //parent::_construct();
        $this->_init('skyboxcore/logservice', 'service_id');
    }
}