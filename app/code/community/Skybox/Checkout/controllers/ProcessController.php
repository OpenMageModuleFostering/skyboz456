<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2014 - 2017 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_ProcessController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        /**
         * only one time for call to service start - Active
         * This always run when you do change country
         */
        $session = Mage::getSingleton("core/session", array("name" => "frontend"));
        $session->setData("callToSkyBox", true);
        //Mage::log("Call true: Skybox_Checkout_ProcessController", null, 'local.log', true);
        /**
         * only one time for call to service end - Active
         */


        /**
         * Integration 3 start, show price shop in cart*
         */
        $isLocationAllow = false;
        //$typeIntegration = Mage::getStoreConfig('settings/typeIntegration');
        $typeIntegration = Mage::helper('skyboxinternational/data')->getSkyboxIntegration();
        if ($typeIntegration == 3) {
            $isLocationAllow = true;
        }
        /**
         * Integration 3 end, show price shop in cart*
         */
        //Mage::log(__FILE__.' # '.__LINE__.' ~ '. __METHOD__.' => enter index', null, 'tracer.log', true);
        $_checkout = Mage::getModel('skyboxcheckout/api_checkout');
        $_checkout->InitializeBarSkybox();

        $return_url = ($this->getRequest()->getParam('return_url')) ?
            $this->getRequest()->getParam('return_url') :
            Mage::helper('core/url')->getHomeUrl();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $product_api = Mage::getSingleton('skyboxcatalog/api_product');
        Mage::log("process", null, 'processcontroller.log', true);

        /*---------------- Rogged ------------------*/
        $_config = Mage::getModel('skyboxcore/config');
        $cart = $_config->getSession()->getCartSkybox();
        $CartItemCount = 0;
        if (!empty($cart)) {
            $CartItemCount = intval($cart->{'CartItemCount'});
        }
        # check if local skybox shopping cart and skybox-checkout match
        $differentqty = 0;
        $api_mproduct = Mage::getModel('skyboxcatalog/api_product');
//        Mage::log('entro getAllItems $CartItemCount',null,'tracer.log',true);
//        Mage::log((int)$quote->getItemsQty() .'-'.$CartItemCount,null,'tracer.log',true);
        if (!$isLocationAllow) {
            if ((int)$quote->getItemsQty() != $CartItemCount && $api_mproduct->getLocationAllow()) {
                $differentqty = 1;
            }
        }

        /*-----------------------------------------*/
        foreach ($quote->getAllItems() as $item) {
            $parentItem = $item->getParentItem();
            $parentItemId = $item->getParentItemId();
            /*---------------- Rogged ------------------*/
            if ($differentqty == 1) {
                $cart = Mage::getModel('skyboxcheckout/cart');

                if ($item->getProductType() === 'simple' && !$parentItem) {
                    $productModel = Mage::getModel('catalog/product')->load($item->getProductId());
                    $paramsRequest = array();
                    $paramsRequest['product'] = $item->getProductId();
                    $paramsRequest['qty'] = $item->getQty();
                    $cart->removeItem($item->getItemId());
                    $cart->addProduct($productModel, $paramsRequest);
                }
                /* end  */

                if ($item->getProductType() === 'simple' && $parentItem) {
                    $paramsRequest = array();
                    $productParentId = Mage::getModel('catalog/product_type_configurable')
                        ->getParentIdsByChild($item->getProductId());
                    $productParentId = $productParentId[0];
                    $productModel = Mage::getModel('catalog/product')->load($productParentId);
                    $_typeInstance = $productModel->getTypeInstance(true);
                    $_children = $_typeInstance->getUsedProducts(null, $productModel);
                    $_attributes = $_typeInstance->getUsedProductAttributes($productModel);
                    foreach ($_children as $_child) {
                        if ($_child->entity_id == $item->getProductId()) {
                            $_superAttributes = array();
                            foreach ($_attributes as $_attribute) {
                                $_superAttributes[$_attribute->getAttributeId()] = $_child->getData($_attribute->getAttributeCode());
                            }
                        }
                    }
                    $paramsRequest['product'] = $item->getProductId();
                    $paramsRequest['qty'] = $item->getQty();
                    $paramsRequest['super_attribute'] = $_superAttributes;
                    $cart->removeItem($parentItemId);
                    $cart->addProduct($productModel, $paramsRequest);
                }
//                if ($item->getProductType() === 'simple' && !$parentItem && $item->getPriceSkybox()) {
//                    $productModel = Mage::getModel('catalog/product')->load($item->getProductId());
//                    $paramsRequest=array();
//                    $paramsRequest['product']=$item->getProductId();
//                    $paramsRequest['qty']=$item->getQty();
//                    $cart->removeItem($item->getItemId());
//                    $cart->addProduct($productModel,$paramsRequest);
//                }
            }
            /*----------------------------------------*/


            Mage::log("process", null, 'process.log', true);
            //Mage::log("ITEM Class: " . get_class($item), null, 'cart.log', true);
            //Mage::log("ITEM Id: " . $item->getProductId(), null, 'cart.log', true);


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

            /* -- Rogged --*/
            /* Cuando se cambia de country no allow a otro country allow, no hay precio en skybox registrado, por eso tomamos el precio de tienda  */
            /** @var Mage_Catalog_Model_Resource_Product $productModel */
            // $productModel = Mage::getModel('catalog/product')->load($item->getProductId());

            /** @var Mage_Catalog_Model_Product $productModel */
            $productModel = Mage::getModel('catalog/product')->load($item->getProductId());

            if (!$isLocationAllow) {
                if ($api_mproduct->getLocationAllow() && !$item->getPriceSkybox()) {
                    $item->setPriceSkybox($productModel->getPrice());
                }
            }

            /* -- ----- --*/

            #echo $item->getPrice()." - ".$item->getPriceSkybox()."<br>";


//            Mage::log(print_r($productModel->getFinalPrice(), true), null, 'processcontroller.log', true);

            if ($item->getProductType() === 'configurable') {

                $product_api->CalculatePrice($item->getProductId(), null, $productModel->getPriceUsdSkybox(),
                    $item->getProductType(), null);
                Mage::log("configurable", null, 'processcontroller.log', true);
            }
            if ($item->getProductType() === 'simple' && $parentItem && !$item->getPriceSkybox()) {

                $product_api->CalculatePrice($item->getProductId(), null, $productModel->getPriceUsdSkybox(),
                    $item->getProductType(), null);
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
                // $product_api->CalculatePrice($item->getProductId(), null, $productModel->getFinalPrice());

                $_price = $productModel->getFinalPrice();
                $_price = isset($_price) ? $productModel->getFinalPrice() : $productModel->getPrice();
                $product_api->CalculatePrice($item->getProductId(), null, $_price);
                Mage::log("simple3", null, 'processcontroller.log', true);
            }

            // Bundle Product
            if ($item->getProductType() === 'bundle') {

                $product_api->CalculatePrice($item->getProductId(), null, $productModel->getPriceUsdSkybox());
                Mage::log("bundle", null, 'processcontroller.log', true);
            };

            if ($isLocationAllow) {
                //$total = str_replace(",", "", $product_api->getPriceUSD());
                $total = str_replace(",", "", $productModel->getTotalPriceUSD());

                /**
                 * Currency amounts in the default currency of that customer
                 */
                $item->setCustomsSkybox($productModel->getCustoms());
                $item->setShippingSkybox($productModel->getShipping());
                $item->setInsuranceSkybox($productModel->getInsurance());
                $item->setPriceSkybox($productModel->getPrice());
                $item->setTotalSkybox($productModel->getTotalPrice());
                $item->setRowTotal($total);

                /*
                 * Currency amounts in the USD Currency
                 */
                $item->setCustomsUsdSkybox($productModel->getCustomsUSD());
                $item->setShippingUsdSkybox($productModel->getShippingUSD());
                $item->setInsuranceUsdSkybox($productModel->getInsuranceUSD());
                $item->setPriceUsdSkybox($productModel->getPriceUSD());
                $item->setTotalUsdSkybox($productModel->getTotalPriceUSD());

                // Set GUID
                $item->setGuidSkybox($productModel->getGuidSkybox());

                $item->setBasePriceSkybox($productModel->getBasePrice());
                $item->setBasePriceUsdSkybox($productModel->getBasePriceUSD());
                $item->setAdjustTotalSkybox($productModel->getAdjustPrice());
                $item->setAdjustTotalUsdSkybox($productModel->getAdjustPriceUSD());
                $item->setAdjustLabelSkybox($productModel->getAdjustLabel());

                /**
                 * Registramos el monto total en USD
                 */
                //$item->setOriginalCustomPrice($product_api->getTotalPriceUSD());
                //$item->setOriginalCustomPrice($total);
                $prices = str_replace(',', '', $productModel->getPrice());
                $prices = number_format((float)($prices), 2, ',', '.');
                $item->setOriginalCustomPrice($prices);
                //$item->setOriginalCustomPrice($product_api->getPrice());
                //$skybox_total = str_replace(",", "", $product_api->getTotalPrice());
                $skybox_total = str_replace(",", "", $productModel->getPrice());
                $row_total = floatval($skybox_total) * $item->getQty();
                $item->setRowTotalSkybox($row_total);
            } else {
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

                // $prices = str_replace(',', '', $product_api->getPrice());
                // $prices = number_format((float)($prices), 2, ',', '.');

                $prices = $this->getFormatPrice($product_api->getPrice());

                $item->setOriginalCustomPrice($prices);

                // @todo: Rewrite custom Quote and LocationAllow logic.
                if ($product_api->getLocationAllow() == 0) {
                    // Mage::log("The country is not allow", null, 'bueyada.log', true);
                    $item->setOriginalCustomPrice($productModel->getPrice());
                }
                //$item->setOriginalCustomPrice($product_api->getPrice());
                //$skybox_total = str_replace(",", "", $product_api->getTotalPrice());
                $skybox_total = str_replace(",", "", $product_api->getPrice());
                $row_total = floatval($skybox_total) * $item->getQty();
                $item->setRowTotalSkybox($row_total);
            }

        }

        $quote->save();

        $_getSession = Mage::getSingleton('checkout/session');
        $_getSession->setCartWasUpdated(true);

        // @note: Force to load Home Page due cache issue. (minicart)
        if (strpos($return_url, 'force_reload')) {
            $return_url = str_replace('force_reload', '', $return_url);

            /** @var Skybox_Core_Model_Config $config */
            $config = Mage::getModel("skyboxcore/config");
            $forceReload = 1;
            $config->getSession()->setChangeCountryHomePage($forceReload);
        }

        //$this->getResponse()->setRedirect($return_url);
        Mage::app()->getResponse()->setRedirect($return_url);
    }

    /**
     * Skybox Format Price
     * @param $price
     * @return mixed|string
     */
    function getFormatPrice($price)
    {
        $value = str_replace(',', '', $price);
        $value = number_format((float)($value), 2, ',', '.');
        return $value;
    }
}
