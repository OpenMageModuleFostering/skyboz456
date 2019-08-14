<?php

/**
 * Skybox Core
 *
 * @category    Skybox
 * @package     Skybox_Core
 * @copyright   Copyright (c) 2014 - 2017 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Core_Helper_Allow extends Mage_Core_Helper_Abstract
{
    const TYPE_LOCATION_ALLOW_STORE = 1;
    const TYPE_LOCATION_ALLOW_CART_DISABLE = 0;
    const TYPE_LOCATION_ALLOW_CART_SHOW = 1;
    const TYPE_LOCATION_ALLOW_CART_HIDE = 3;
    const FULL_ACTION_NAME_IFRAME = 'skbcheckout_international_index';

    protected $_integrationType;
    protected $_locationAllow;
    protected $_actionName;

    public function isPriceEnabled()
    {
        if ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_SHOW && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_SHOW && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_HIDE && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_HIDE && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        }

        return false;
    }

    public function isCartBarEnabled()
    {
        $fullActionName = $this->getFullActionName();

        if ($this->getTypeLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_SHOW
            && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE && $fullActionName != self::FULL_ACTION_NAME_IFRAME
        ) {
            return true;
        } elseif ($this->getTypeLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_SHOW
            && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE && $fullActionName == self::FULL_ACTION_NAME_IFRAME
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_SHOW
            && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_HIDE
            && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE && $fullActionName != self::FULL_ACTION_NAME_IFRAME
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_HIDE
            && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE && $fullActionName == self::FULL_ACTION_NAME_IFRAME
        ) {
            return true;
        } elseif ($this->getTypeLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_HIDE
            && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        }

        return false;
    }

    public function isCartButtonEnabled()
    {
        if ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_SHOW && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_SHOW && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_HIDE && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_HIDE && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        }

        return false;
    }

    public function isCartButtonSkyboxEnabled()
    {
        if ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_SHOW && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_SHOW && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_HIDE && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_HIDE && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        }

        return false;
    }

    public function isChangeCountryEnabled()
    {
        if ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_SHOW && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_SHOW && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_HIDE && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_HIDE && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        }

        return false;
    }

    public function isVisible()
    {
        return $this->getLocationAllow() != 0 ? true : false;
    }

    public function isOperationCartEnabled()
    {
        if ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_SHOW && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_SHOW && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_HIDE && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_HIDE && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        }

        return false;
    }

    public function showSubtotal()
    {
        if ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_SHOW && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_SHOW && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_HIDE && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getTypeLocationAllow()
            == self::TYPE_LOCATION_ALLOW_CART_HIDE && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        }

        return true;
    }

    /*
     * Get Store Type Location
     */
    private function getTypeLocationAllow()
    {
        if ($this->_integrationType == null) {
            $integrationType = Mage::helper('skyboxinternational/data')->getSkyboxIntegration();
            $this->_integrationType = $integrationType;
        }
        return $this->_integrationType;
    }

    /*
     * Get LocationAllow
     */
    private function getLocationAllow()
    {
        if ($this->_locationAllow == null) {
            $_checkout = Mage::getModel('skyboxcheckout/api_checkout');
            $value = $_checkout->getLocationAllow();
            $value = isset($value) ? $value : 0;
            $this->_locationAllow = $value;
        }
        return $this->_locationAllow;
    }

    /*
     * Get FullActionName router name
     */
    private function getFullActionName()
    {
        $moduleName = Mage::app()->getRequest()->getModuleName();
        $controllerName = Mage::app()->getRequest()->getControllerName();
        $actionName = Mage::app()->getRequest()->getActionName();

        $result = $moduleName . "_" . $controllerName . "_" . $actionName;
        return $result;
    }
}
