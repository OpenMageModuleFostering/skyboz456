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
 * Skybox transaction session namespace
 *
 * @author  César Tapia M. <ctapia@skyworldint.com>
 */
class Skybox_Core_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        //$this->init('skybox');
        //Mage::dispatchEvent('myapp_session_init', array('myapp_session' => $this));

        $namespace = 'skyboxcore';
        $namespace .= '_' . (Mage::app()->getStore()->getWebsite()->getCode());

        $this->init($namespace);
        Mage::dispatchEvent('skyboxcore_session_init', array('skyboxcore_session' => $this));
    }
}