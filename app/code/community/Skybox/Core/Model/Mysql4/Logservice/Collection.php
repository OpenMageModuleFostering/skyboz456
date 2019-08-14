<?php

/**
 * Skybox Core
 *
 * @category    Skybox
 * @package     Skybox_Core
 * @copyright   Copyright (c) 2014 SkyBOX Checkout, Inc. (http://www.skyboxcheckout.com)
 */
class Skybox_Core_Model_Mysql4_Logservice_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('skyboxcore/logservice', 'service_id');
    }
}