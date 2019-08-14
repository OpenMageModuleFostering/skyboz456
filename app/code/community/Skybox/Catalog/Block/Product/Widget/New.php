<?php
/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Catalog
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

/**
 * New products widget
 *
 * @category   Skybox
 * @package    Skybox_Catalog
 */
class Skybox_Catalog_Block_Product_Widget_New extends Mage_Catalog_Block_Product_Widget_New
{

    /*
    * @var string $_cache_code
    */
    public $_cache_code = null;

    public $_sky_cache_code = null;
//    protected $_api;

    /**
     * Retrieve API Product
     *
     * @return Skybox_Catalog_Model_Api_Product
     */
    protected function _getApi()
    {
        if (null === $this->_api) {
            $this->_api = Mage::getModel('skyboxcatalog/api_product');
        }
        return $this->_api;
    }


    /**
     * Initialize block's cache and template settings
     */
    protected function _construct()
    {
        parent::_construct();
        $cache_key = Mage_Catalog_Model_Product::CACHE_TAG . "_" . $this->getCacheCode();
        $this->setAttribute("cache_key", $cache_key);
        if (Mage::getVersion() == '1.9.1') {
            $this->addData(array('cache_lifetime' => null)); // Skip cache generation
        }
    }

    public function getCacheCode()
    {
        if ($this->_cache_code == null) {
            /* @var $config Skybox_Core_Model_Config */
            $config = Mage::getModel("skyboxcore/config");
            $skyboxUser = $config->getSession()->getSkyboxUser();
            $cache_code = $skyboxUser->CartCountryISOCode . $skyboxUser->CartCityId . $skyboxUser->CartCurrencyISOCode;
            $this->_cache_code = $cache_code;
        }
        Mage::log("Cache Code::: ", null, 'test2.log', true);
        Mage::log("[widget/new] Cache Code: " . $cache_code, null, 'cache.log', true);
        return $this->_cache_code;
    }

    protected function _getProductCollection()
    {

        return parent::_getProductCollection();
    }

    protected function _beforeToHtml()
    {
        $result = parent::_beforeToHtml();
        if (!$this->isEnable()) {
            return $result;
        }
        $api_checkout = Mage::getModel('skyboxcheckout/api_checkout');
        $api_checkout->InitializeBarSkybox();

        $data = $api_checkout->getValueAccessService();

        $skyBoxUrlClient = Mage::helper('skyboxinternational/data')->getSkyBoxUrlAPI();
        $skyBoxUrlClient = $skyBoxUrlClient . "multiplecalculate";

        $dataJson = null;
        $multiCalculate = 1;

        try {
            $products = $this->_getProductCollection();
            foreach ($products as $prod) {
                $product = Mage::getModel('catalog/product')->load($prod->getId());
                $data['listproducts'][] = $this->getUrlService($product);
                $dataJson = json_encode($data);
            }
            $response = $this->multiCalculatePrice($skyBoxUrlClient, $dataJson);
        } catch (\Exception $e) {
            $multiCalculate = 0;
        }

        Mage::unregister('skybox_multicalculate');
        Mage::register('skybox_multicalculate', $multiCalculate);
        return $result;
    }

    /**
     * @param $product
     * @return array|string
     */
    public function getUrlService($product)
    {
        $type = $product->getTypeId();
        $template = '';

        switch ($type) {
            case 'simple':
                $template = $this->_getApi()->getUrl($product->getId(), null, $product->getFinalPrice(),
                    $product->getTypeId());
                break;
            case 'configurable':
                $template = $this->_getApi()->getUrl($product->getId(), null, $product->getFinalPrice(),
                    $product->getTypeId());
                break;
            case 'bundle':
                $template = $this->_getApi()->getUrl($product, null, $product->getFinalPrice(), 'simple');
                break;
        }

        if (is_array($template)) {
            $productId = $template['htmlobjectid'];
            $cache_code = $this->getSkyboxCacheCode($productId);
            $template['htmlobjectid'] = $cache_code;
        }
        return $template;
    }

    /**
     * Return the Skybox Cache code
     *
     * @param $productId
     * @return mixed
     */
    public function getSkyboxCacheCode($productId)
    {
        if ($this->_sky_cache_code == null) {
            /* @var $config Skybox_Core_Model_Config */
            $config = Mage::getModel("skyboxcore/config");
            $skyboxUser = $config->getSession()->getSkyboxUser();
            $country_iso_code = strtoupper($skyboxUser->CartCountryISOCode);
            $cache_code = $country_iso_code . "[REPLACE]" . $skyboxUser->CartCurrencyISOCode;
            $this->_sky_cache_code = $cache_code;
        }

        $result = str_replace('[REPLACE]', $productId, $this->_sky_cache_code);
        return $result;
    }

    /**
     * Return Multi CalculatePrice
     *
     * @param $url
     * @param $data
     * @return mixed
     * @throws Exception
     */
    function multiCalculatePrice($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);

        $response = json_decode($response, true);

        if ($response['StatusCode'] != 'Success') {
            throw new \Exception('Missing or invalid data send to MultiCalculate');
        }
        return $response;
    }

    private function isEnable()
    {
        $isModuleEnable = Mage::getModel('skyboxcore/api_restful')->isModuleEnable();
        if (!$isModuleEnable) {
            return false;
        }

        /** @var Skybox_Core_Helper_Allow $allowHelper */
        $allowHelper = Mage::helper('skyboxcore/allow');

        if (!$allowHelper->isPriceEnabled()) {
            return false;
        }

        return true;
    }


}