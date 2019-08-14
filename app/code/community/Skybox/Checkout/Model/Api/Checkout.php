<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2014 SkyBOX Checkout, Inc. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_Model_Api_Checkout extends Skybox_Core_Model_Standard
{

    /**
     * Model instance
     * @var Mage_Core_Catalog_Model_Product
     */
    protected $_product = null;

    protected $_currentProduct = null;

    protected $_typeProduct = "catalog/product";

    public function getProduct()
    {
        if (null === $this->_product)
            $this->_product = Mage::getModel($this->_typeProduct);

        return $this->_product;
    }

    public function InitializeBarSkybox()
    {
        //if(!$this->getErrorAuthenticate())
        //{
        //$this->_config = Mage::getModel('skyboxcore/config');
        Mage::log("InitializeBarSkybox ", null, 'checkout.log', true);

        $params = array(
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_MERCHANT => $this->getMerchant(),
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_TOKEN => $this->getAuthorizedToken(),
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_GUID => $this->getGuidApi(),
            'customeriplocal' => $this->_getConfig()->getHost(),
            'customeripremote' => $this->_getConfig()->getRemoteAddr(),
            'customeripproxy' => $this->_getConfig()->getProxy(),
            'customerbrowser' => $this->_getConfig()->getUserAgent(),
            'customerlanguages' => $this->_getConfig()->getLanguage()
        );

        //$response = $this->CallApiRest(Skybox_Core_Model_Config::SKYBOX_ACTION_INITIALICE, $params)->getResponse();
        /**
         * only one time for call to service start
         */
        $session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
        $callToSkyBox = $session->getData("callToSkyBox");

        Mage::log($callToSkyBox, null, 'gary.log', true);
        $initialize = $this->_getConfig()->getSession()->getCartSkybox();
        if (!empty($initialize) && (!$callToSkyBox)) {
            $response = $initialize;
        } else {
            $response = $this->CallApiRest(Skybox_Core_Model_Config::SKYBOX_ACTION_INITIALICE, $params)->getResponse();
            $session->setData("callToSkyBox", false);
        }
        /**
         * only one time for call to service end
         */
        // Set SkyboxUser session
        $this->_getConfig()->getSession()->setSkyboxUser($response);

        //if ($this->getLocationAllow()) { // Rogged, la primera vez q usuario ingresa con un pais disabled, lanza error porq no se envia datos a url de popup
        $this->_getConfig()->getSession()->setCartSkybox($response);
        //}
        //}
    }

    public function setCurrentProduct($product)
    {
        $this->_currentProduct = $product;
    }
    public function getCurrentProduct()
    {
        return $this->_currentProduct;
    }

    public function AddProductOfCart($data, $quantity)
    {
        $detailProduct = Mage::helper('checkout')->concatNameDetailProduct($this->getCurrentProduct(), $data['sku']);

        //Mage::log("entro AddProductOfCart", null, 'tracer.log', true);
        if ($this->getErrorAuthenticate() && !$this->getLocationAllow()) {
            return $this;
        }

        Mage::log("AddProductOfCart: " . print_r($data, true), null, 'skyboxcheckout.log', true);

        $storeProductName = $data['name'].' '.$detailProduct;

        $params = array(
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_MERCHANT => $this->getMerchant(),
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_TOKEN => $this->getAuthorizedToken(),
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_GUID => $this->getGuidApi(),
            'productid' => "0",
            'quantity' => $quantity,
            'storeproductcode' => $data['sku'],
            'storeproductname' => $storeProductName,
            'storeproductcategory' => $data['category_id'],
            'storeproductprice' => $data['final_price'],
            'weight' => ($data['weight'])?($data['weight']):1,
            'weightunit' => $this->getWeightUnit(),
            'storeproductimgurl' => $data['image_url'],
            'VolWeight' => $data['VolWeight'],
            'merchantproductid' => $data['merchantproductid']
        );
        $this->CallApiRest(Skybox_Core_Model_Config::SKYBOX_ACTION_ADD_PRODUCT_CART, $params);

        return $this;
    }

    public function GetTotalShoppingCart()
    {
        if ($this->getErrorAuthenticate() && !$this->getLocationAllow()) {
            return $this;
        }

        $params = array(
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_MERCHANT => $this->getMerchant(),
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_TOKEN => $this->getAuthorizedToken(),
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_GUID => $this->getGuidApi()
        );

        $this->CallApiRest(Skybox_Core_Model_Config::SKYBOX_ACTION_GET_TOTAL_SHOPINGCART, $params);

        return $this;
    }

    public function UpdateProductOfCart($productId, $quantity)
    {
        if (!$this->getErrorAuthenticate() && $this->getLocationAllow()) {
            $this->getProduct()->load($productId);
            $params = array(
                Skybox_Core_Model_Config::SKYBOX_PARAMETER_MERCHANT => $this->getMerchant(),
                Skybox_Core_Model_Config::SKYBOX_PARAMETER_TOKEN => $this->getAuthorizedToken(),
                Skybox_Core_Model_Config::SKYBOX_PARAMETER_GUID => $this->getGuidApi(),
                'productid' => $productId,
                'quantity' => $quantity
            );

            $this->CallApiRest(Skybox_Core_Model_Config::SKYBOX_ACTION_UPDATE_PRODUCT_CART, $params);
        }

        return $this;
    }

    public function DeleteProductOfCart($productId)
    {

        #if (!$this->getErrorAuthenticate() && $this->getLocationAllow()) { // Rogged
        if (!$this->getErrorAuthenticate()) { // Rogged
            $params = array(
                Skybox_Core_Model_Config::SKYBOX_PARAMETER_MERCHANT => $this->getMerchant(),
                Skybox_Core_Model_Config::SKYBOX_PARAMETER_TOKEN => $this->getAuthorizedToken(),
                Skybox_Core_Model_Config::SKYBOX_PARAMETER_GUID => $this->getGuidApi(),
                'productid' => $productId
            );

            $this->CallApiRest(Skybox_Core_Model_Config::SKYBOX_ACTION_DELETE_PRODUCT_CART, $params);
        }

        return $this;
    }

    public function getCartSkybox()
    {
        if (!$this->getErrorAuthenticate() && $this->getLocationAllow()) {
            //$this->_config = Mage::getModel('skyboxcore/config');
            return $this->_getConfig()->getSession()->getCartSkybox();
        }
        return "";
    }

    /*
     * Get Categories
     *
     * @return $data json
     */

    public function GetCategories()
    {
        $params = array(
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_MERCHANT => $this->getMerchant(),
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_TOKEN => $this->getAuthorizedToken()
        );

        /* var $response Skybox_Core_Model_Standard */
        $response = $this->CallApiRest(Skybox_Core_Model_Config::SKYBOX_ACTION_CATEGORIES, $params);
        $jsonData = $response->getResponse()->{'Categories'};
        return $jsonData;
    }

    public function getValueAccessService()
    {
        $params = array(
            "merchantCode" => $this->getMerchant(),
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_TOKEN => $this->getAuthorizedToken(),
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_GUID => $this->getGuidApi(),

            Skybox_Core_Model_Config::SKYBOX_PARAMETER_MERCHANT => $this->getMerchant(),
            'customeriplocal' => $this->_getConfig()->getHost(),
            'customeripremote' => $this->_getConfig()->getRemoteAddr(),
            'customeripproxy' => $this->_getConfig()->getProxy(),
            'customerbrowser' => $this->_getConfig()->getUserAgent(),
            'customerlanguages' => $this->_getConfig()->getLanguage()


        );


        return $params;

    }
}