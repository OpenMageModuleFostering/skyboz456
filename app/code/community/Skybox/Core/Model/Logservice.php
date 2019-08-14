<?php

/**
 * Skybox Core
 *
 * @category    Skybox
 * @package     Skybox_Core
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Core_Model_Logservice extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('skyboxcore/logservice', 'service_id');
    }
}