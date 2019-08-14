<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_ProcessController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $_checkout = Mage::getModel('skyboxcheckout/api_checkout');
        $_checkout->InitializeBarSkybox();

        $return_url = ($this->getRequest()->getParam('return_url')) ?
            $this->getRequest()->getParam('return_url') :
            Mage::helper('core/url')->getHomeUrl();

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $product_api = Mage::getSingleton('skyboxcatalog/api_product');
        Mage::log("process", null, 'processcontroller.log', true);

        foreach ($quote->getAllItems() as $item) {

             Mage::log("process", null, 'process.log', true);
            //Mage::log("ITEM Class: " . get_class($item), null, 'cart.log', true);
            //Mage::log("ITEM Id: " . $item->getProductId(), null, 'cart.log', true);

            $parentItem = $item->getParentItem();

            /*if ($item->getProductType() === 'configurable') continue;
            if ($item->getProductType() === 'simple' && $parentItem && !$item->getPriceSkybox()) continue;

            // Simple Product
            if ($item->getProductType() === 'simple' && !$parentItem && $item->getPriceSkybox()) {
                $product_api->CalculatePrice($item->getProductId(), null, null);
            }

            // Configurable Product
            if ($item->getProductType() === 'simple' && $parentItem) {
                $item = $parentItem;
                $product_api->CalculatePrice($item->getProductId(), null, $item->getPriceUsdSkybox());
            }

            // Bundle Product
            if ($item->getProductType() === 'bundle') {
                $product_api->CalculatePrice($item->getProductId(), null, $item->getPriceUsdSkybox());
            };

            $total = str_replace(",", "", $product_api->getTotalPriceUSD());*/

            if ($item->getProductType() === 'configurable'){
                $product_api->CalculatePrice($item->getProductId(),NULL,$item->getPriceUsdSkybox(),$item->getProductType(),NULL);
                Mage::log("configurable", null, 'processcontroller.log', true);
            }
            if ($item->getProductType() === 'simple' && $parentItem && !$item->getPriceSkybox()) {
                $product_api->CalculatePrice($item->getProductId(),NULL,$item->getPriceUsdSkybox(),$item->getProductType(),NULL);
                Mage::log("simple1", null, 'processcontroller.log', true);
            }

            // Simple Product
            if ($item->getProductType() === 'simple' && !$parentItem && $item->getPriceSkybox()) {
                $product_api->CalculatePrice($item->getProductId(), null, null);
                Mage::log("simple2", null, 'processcontroller.log', true);
            }

            // Configurable Product
            if ($item->getProductType() === 'simple' && $parentItem) {
                $item = $parentItem;
                $product_api->CalculatePrice($item->getProductId(), null, $item->getPriceUsdSkybox());
                Mage::log("simple3", null, 'processcontroller.log', true);
            }

            // Bundle Product
            if ($item->getProductType() === 'bundle') {
                $product_api->CalculatePrice($item->getProductId(), null, $item->getPriceUsdSkybox());
                Mage::log("bundle", null, 'processcontroller.log', true);
            };

            //$total = str_replace(",", "", $product_api->getPriceUSD());
            $total = str_replace(",", "", $product_api->getTotalPriceUSD());

            /**
             * Currency amounts in the default currency of that customer
             */
            $item->setCustomsSkybox($product_api->getCustoms());
            $item->setShippingSkybox($product_api->getShipping());
            $item->setInsuranceSkybox($product_api->getInsurance());
            $item->setPriceSkybox($product_api->getPrice());
            $item->setTotalSkybox($product_api->getTotalPrice());
            $item->setRowTotal($total);

            /*
             * Currency amounts in the USD Currency
             */
            $item->setCustomsUsdSkybox($product_api->getCustomsUSD());
            $item->setShippingUsdSkybox($product_api->getShippingUSD());
            $item->setInsuranceUsdSkybox($product_api->getInsuranceUSD());
            $item->setPriceUsdSkybox($product_api->getPriceUSD());
            $item->setTotalUsdSkybox($product_api->getTotalPriceUSD());

            // Set GUID
            $item->setGuidSkybox($product_api->getGuidSkybox());

            $item->setBasePriceSkybox($product_api->getBasePrice());
            $item->setBasePriceUsdSkybox($product_api->getBasePriceUSD());
            $item->setAdjustTotalSkybox($product_api->getAdjustPrice());
            $item->setAdjustTotalUsdSkybox($product_api->getAdjustPriceUSD());
            $item->setAdjustLabelSkybox($product_api->getAdjustLabel());

            /**
             * Registramos el monto total en USD
             */
            //$item->setOriginalCustomPrice($product_api->getTotalPriceUSD());
            //$item->setOriginalCustomPrice($total);
            $item->setOriginalCustomPrice($product_api->getPrice());
            //$item->setOriginalCustomPrice($product_api->getPrice());
            //$skybox_total = str_replace(",", "", $product_api->getTotalPrice());
            $skybox_total = str_replace(",", "", $product_api->getPrice());
            $row_total = floatval($skybox_total) * $item->getQty();
            $item->setRowTotalSkybox($row_total);
        }

        $quote->save();

        //$this->getResponse()->setRedirect($return_url);
        Mage::app()->getResponse()->setRedirect($return_url);
    }
}