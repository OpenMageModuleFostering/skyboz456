<?php
/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Catalog
 * @copyright   Copyright (c) 2017 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

/**
 * Product list
 */
class Skybox_Catalog_Block_Product_List extends Mage_Catalog_Block_Product_List
{
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
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
        $result = parent::_beforeToHtml();

        if (!$this->isEnable()) {
            return $result;
        }

        $api_checkout = Mage::getModel('skyboxcheckout/api_checkout');
        $api_checkout->InitializeBarSkybox();

        // $session = Mage::getSingleton("core/session", array("name" => "frontend"));
        // $sky = $session->getData("skyBox");

        $data = $api_checkout->getValueAccessService();

        $skyBoxUrlClient = Mage::helper('skyboxinternational/data')->getSkyBoxUrlAPI();
        $skyBoxUrlClient = $skyBoxUrlClient . ("multiplecalculate");

        $dataJson = null;
        $multiCalculate = 1;

        try {
            // $products = $this->_getProductCollection();
            $products = $this->getToolbarBlock()->getCollection();

            foreach ($products as $prod) {
                $product = Mage::getModel('catalog/product')->load($prod->getId());
                $data['listproducts'][] = $this->getUrlService($product);
                $dataJson = json_encode($data);
            }

            $response = $this->multiCalculatePrice($skyBoxUrlClient, $dataJson);
            // Mage::log(print_r($response, true), null, 'multicalculate.log', true);

        } catch (\Exception $e) {
            $multiCalculate = 0;
            // Mage::log(print_r($dataJson, true), null, 'multicalculate.log', true);
            // Mage::log("[multicalculate] " . $e->getMessage(), null, 'multicalculate.log', true);
        }

        Mage::unregister('skybox_multicalculate');
        Mage::register('skybox_multicalculate', $multiCalculate);
        // Mage::registry('skybox_multicalculate');

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
        return $template;
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

    /**
     * is Enable?
     *
     * @return bool
     */
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
