<?php

/**
 * Skybox International
 *
 * @category    Skybox
 * @package     Skybox_International
 * @copyright   Copyright (c) 2014 SkyBOX Checkout, Inc. (http://www.skyboxcheckout.com)
 */
class Skybox_International_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getMerchantCode()
    {
        return Mage::getStoreConfig('skyboxinternational/skyboxsettings/merchantcode', Mage::app()->getStore());
    }

    public function getMerchantKey()
    {
        return Mage::getStoreConfig('skyboxinternational/skyboxsettings/merchantkey', Mage::app()->getStore());
    }

    public function getWeightUnit()
    {
        return Mage::getStoreConfig('skyboxinternational/skyboxsettings/weigthunitproduct', Mage::app()->getStore());
    }

    public function getActive()
    {
//        return Mage::getStoreConfig('skyboxinternational/skyboxsettings/skyboxactive', Mage::app()->getStore());
        return Mage::getModel('skyboxcore/api_restful')->isModuleEnable();
    }

    public function getSkyboxLog()
    {
        return Mage::getStoreConfig('skyboxinternational/skyboxsettings/skyboxlog', Mage::app()->getStore());
    }

    public function getSkyboxEmail()
    {
        return Mage::getStoreConfig('skyboxinternational/skyboxsettings/skyboxemail', Mage::app()->getStore());
    }

    public function getSkyboxIntegration()
    {
        return Mage::getStoreConfig('skyboxinternational/skyboxsettings/skyboxintegration', Mage::app()->getStore());
    }

    public function getSkyboxUrlAPI()
    {
        return Mage::getStoreConfig('skyboxinternational/skyboxsettings/skyboxurlapi', Mage::app()->getStore());
    }

    public function getSkyboxUrlMain()
    {
        return Mage::getStoreConfig('skyboxinternational/skyboxsettings/skyboxurlmain', Mage::app()->getStore());
    }

    public function getEnabledAddSumTaxToPrice()
    {
        return Mage::getStoreConfig('skyboxinternational/skyboxproduct/skyboxaddtaxtopriceenable', Mage::app()->getStore());
    }

    public function getStoreId()
    {
        //return $this->helper('core')->getStoreId();
        return Mage::app()->getStore()->getId();
    }

    public function getCartId()
    {
        return Mage::getSingleton('checkout/session')->getQuoteId();
    }

    public function getCssVersion()
    {
        return Mage::getModel('skyboxcatalog/api_product')->getCssVersion();
    }
}