<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_Helper_Data extends Mage_Checkout_Helper_Data
{
    protected $_api = null;
    protected function _getApi()
    {
        if (null === $this->_api) {
            $this->_api = Mage::getModel('skyboxcatalog/api_product');
        }
        return $this->_api;
    }
    public function formatPrice($price)
    {
        if(!$this->isRewriteCheckoutLinksEnabled()){
            return parent::formatPrice($price);
        }
        /* @var $config Skybox_Core_Model_Config */
        /*$config = Mage::getModel("skyboxcore/config");
        $skybox_user = $config->getSession()->getSkyboxUser();
        $currency = $skybox_user->CartCurrencySymbol;
        //return '<span class="price">' . $currency . ' ' . number_format($price, 2) . '</span>';
        return '<span class="price">' . $currency . ' ' . $price . '</span>';*/
        /* @var $config Skybox_Core_Model_Config */


        $config = Mage::getModel("skyboxcore/config");
        $skybox_user = $config->getSession()->getSkyboxUser();
        /*LocationAllow3*/
        $typeIntegration = Mage::getStoreConfig('settings/typeIntegration');
        if($this->_getApi()->getLocationAllow() && ($typeIntegration!=3)){ // Rogged
            $currency = $skybox_user->CartCurrencySymbol; // Rogged
        }else{ // Rogged
            $currency = Mage::app()->getLocale()->currency( $currency_code )->getSymbol(); // Rogged
        } // Rogged

        //Mage::log("currency".$currency, null, 'cartlabels.log', true);
        $price=str_replace(',', '', $price);
        //Mage::log("precio".$price, null, 'cartlabels.log', true);
        $price=Mage::getModel('directory/currency')->setData("currency_code",
            Mage::app()->getStore(null)->getCurrentCurrency()->getCode())->format(
            $price, array('display' =>Zend_Currency::NO_SYMBOL), false);

        if ($price < 0) return '<span class="price">('.$currency." ".-1*$price.')</span>';
        return '<span class="price">'.$currency." ".$price.'</span>';

    }

    /**
     * @deprecated
     */
    public function getLayoutCheckoutSkybox(){ // Rogged
        $page_layout = Mage::getStoreConfig('customize_your_own');

        /*LocationAllow3*/
        $active = 0;
        if(isset($_GET['v'])) {
            $active = 1;
        }
        if($this->_getApi()->getLocationAllow()){
            $page_layout = 'skybox/checkout/onepage.phtml';
        }else{
            $page_layout = 'checkout/onepage.phtml';
        }
        if($active) {
            $page_layout = 'checkout/onepage.phtml';
        }
        return $page_layout;
    }

    public function isRewriteCheckoutLinksEnabled()
    {
//        return (bool)Mage::getStoreConfig('skyboxinternational/skyboxsettings/skyboxactive', Mage::app()->getStore());
        return Mage::getModel('skyboxcore/api_restful')->isModuleEnable();
    }

    public function concatNameDetailProduct($product, $sku = null)
    {
        Mage::log('concatNameDetailProduct', null, 'tracer.log');
        $attributesConcat = '';
        if($product->getData('type_id') == 'configurable'){
            $options = $this->getAllAttributesVariantConfigurableProducts($product);
            $attributesData = $options[$product->getId()][$sku];
            $attributesConcat = implode(', ', $attributesData);
        }
        return $attributesConcat;
    }

    public function getAllAttributesVariantConfigurableProducts($configurableProduct)
    {
        $attributes = array();
        $productAttributesOptions = $configurableProduct->getTypeInstance(true)->getConfigurableOptions($configurableProduct);
        foreach ($productAttributesOptions as $productAttributeOption) {
            $attributes[$configurableProduct->getId()] = array();
            foreach ($productAttributeOption as $optionValues) {
                $val = ($optionValues['option_title']);
                $attributes[$configurableProduct->getId()][$optionValues['sku']][] = $optionValues['attribute_code'].'-'.$val;
            }
        }
        Mage::log(print_r($attributes, true), null, 'tracer.log');
        return $attributes;
    }
}