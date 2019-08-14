<?php

/**
 * Skybox Services
 *
 * @category    Skybox
 * @package     Skybox_Services
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

/**
 *
 * Skybox Services API
 *
 * @author  Luis Alex Cordova Leon <lcordova@skyworldint.com>
 */

class Skybox_Services_Model_Api extends Mage_Api_Model_Resource_Abstract
{
    const SKYBOX_SHIPPING = 'skyboxcheckout_shipping_skyboxcheckout';
    const SKYBOX_PAYMENT = 'skyboxcheckout_pay';

    public function __construct()
    {
        Mage::log("API Construct started " . date('m/d/Y h:i:s a', time()));
    }


    /**
     * Field name in session for saving store id
     * @var int $orderIncrementId
     * @var string $status
     * @return boolean
     */
    public function setStatusOrder($orderIncrementId, $status)
    {
        try {
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
            $order->setData('state', $status); //complete
            $order->setStatus($status);
            if(Mage_Sales_Model_Order::STATE_PROCESSING == $status){
                $order->setTotalPaid($order->getGrandTotal());
            }elseif(Mage_Sales_Model_Order::STATE_COMPLETE == $status){
                $history = $order->addStatusHistoryComment('La orden se estableció como completa', false);
                $history->setIsCustomerNotified(false);
            }
            $order->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public function setQuoteDesactive($quoteId)
    {
        try {
            Mage::log("API setQuoteDesactive started " . date('m/d/Y h:i:s a', time()));
            $quote = Mage::getModel("sales/quote");
            $quote->loadByIdWithoutStore($quoteId);
            $quote->setIsActive(false);
            $quote->save();
            Mage::log("API setQuoteDesactive finished " . date('m/d/Y h:i:s a', time()));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /*
     * Return a order generated
     *
     * @params many
     * @return integer Quote ID
     */
    public function generateOrder($IdCart, $IdStore, $CustomerName, $CustomerLasName, $CustomerEmail, $CustomerAdresss, $CustomerPhone, $CustomerZipCode, $CityName, $CountryId, $RegionId, $regionName = '')
    {

        Mage::log("API generateOrder started " . date('m/d/Y h:i:s a', time()));

        $quoteId = $IdCart; // should be!

        /* @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel('sales/quote')->loadByIdWithoutStore($quoteId);

        $address = $quote->getShippingAddress();

        foreach ($quote->getAllItems() as $item) {
            Mage::log(print_r($item->debug(), true), null, 'tracer.log', true);
            $item->setCustomPrice($item->getPriceUsdSkybox());
            $item->setOriginalCustomPrice($item->getPriceUsdSkybox());
//            getOrderCurrencyCode
//            $item->getProduct()->setIsSuperMode(true);
//            $item->save();
        }

        /*
        // Bug: When a customer is already registered as guest,
        // it doesn't work with Customer validation

        /* @var $customer Mage_Customer_Model_Customer
        $customer = Mage::getModel('customer/customer');
        $customer
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())// or 1
            ->loadByEmail($CustomerEmail);

        if ($customer->getId()) {
            $quote->assignCustomer($customer); // for customer orders
        } else {
            // ...
        }
        */

        // for guest orders only
        $quote->setIsMultiShipping(false);
        $quote->setCheckoutMethod('guest');
        $quote->setCustomerId(null);
        $quote->setCustomerEmail($CustomerEmail);
        $quote->setCustomerIsGuest(true);
        $quote->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);

        $grandTotal = $address->getBaseGrandTotalSkybox();
//        $quote->setBaseGrandTotal($grandTotal);
//        $quote->setGrandTotal($grandTotal);
//        setBaseCurrencyCode(string $value)
//        setGlobalCurrencyCode(string $value)
//        setQuoteCurrencyCode(string $value)
//        setStoreCurrencyCode(string $value)

        // Just for Guest User
        $CustomerLasName = ($CustomerLasName != null) ? $CustomerLasName : $CustomerName;

        $addressData = array(
            'firstname' => $CustomerName,
            'lastname' => $CustomerLasName,
            'street' => $CustomerAdresss,
            'city' => $CityName,
            'postcode' => $CustomerZipCode,
            'telephone' => $CustomerPhone,
            'country_id' => $CountryId,
            'region_id' => $RegionId,
            'region' => $regionName,
            'is_default_billing' => '1',
        );

        $billingAddress = $quote->getBillingAddress()->addData($addressData);
        $shippingAddress = $quote->getShippingAddress()->addData($addressData);

//        if(!Mage::getStoreConfig('payment/checkmo/active')){
//            Mage::log('Desactivado checkmo', null, 'tracer.log', true);
//            Mage::app()->getStore()->setConfig('payment/checkmo/active', true);
//        }

        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod(self::SKYBOX_SHIPPING)//flatrate_flatrate
            ->setPaymentMethod(self::SKYBOX_PAYMENT);

        $quote->getPayment()->importData(array('method' => self::SKYBOX_PAYMENT));
//        $quote->getPayment()->importData(array('method' => 'checkmo'));

        $quote->collectTotals()->save();

        $address->setGrandTotal($grandTotal);
        $address->setBaseGrandTotal($grandTotal);

        $service = Mage::getModel('sales/service_quote', $quote);
        $service->submitAll();

        try {
            $order = $service->getOrder();

            // Comment
            $comment = "This order was generated by Skybox Checkout";
            $order->addStatusHistoryComment($comment);
            $order->save();

            // setQuoteDesactive
            $this->setQuoteDesactive($quoteId);
            //$quote->setIsActive(false);

            /*Mage::log("Order " . $order->getIncrementId() . " was created");
            Mage::log("API generateOrder finished " . date('m/d/Y h:i:s a', time()));
            Mage::log("API Construct finished " . date('m/d/Y h:i:s a', time()));
            Mage::log("getShippingMethod: " . $order->getShippingMethod());*/
            return $order->getIncrementId();
        } catch (Exception $e) {
            Mage::log("exception", null, 'servicios.log', true);
            Mage::log($e->getMessage());
        }
    }

    public function generateOrderFull($DataProducts, $IdStore, $CustomerName, $CustomerLasName, $CustomerEmail, $CustomerAdresss, $CustomerPhone, $CustomerZipCode, $CityName, $CountryId, $RegionId, $regionName = '')
    {
        Mage::log("API generateOrderFull started " . date('m/d/Y h:i:s a', time()));

        $IdStore = !empty($IdStore)?$IdStore:Mage::app()->getStore('default')->getId();
        $quote = Mage::getModel('sales/quote')->setStoreId($IdStore);
        $productList = array();
        $objData = json_decode($DataProducts);
        Mage::log('########### $objData ###########', null, 'tracer.log', true);
        Mage::log(print_r($objData, true), null, 'tracer.log', true);
        if(count($objData->Products)>0){
            foreach ($objData->Products as $prod){
                $product = Mage::getModel('catalog/product')->load($prod->ProductId);
                $buyInfo = array(
                    'qty' => $prod->Quantity,
                    // custom option id => value id
                    // or
                    // configurable attribute id => value id
                );
                $productList[$prod->ProductId] = $prod;
                $quote->addProduct($product, new Varien_Object($buyInfo));
            }
        }
        Mage::log('########### $productList ###########', null, 'tracer.log', true);
        Mage::log(print_r($productList, true), null, 'tracer.log', true);

        $quoteId = $quote->getId();

        $address = $quote->getShippingAddress();

        foreach ($quote->getAllItems() as $item) {
            Mage::log('########### $item ###########', null, 'tracer.log', true);
//            Mage::log(print_r($item->debug(), true), null, 'tracer.log', true);
            Mage::log(print_r($item->getProductId(), true), null, 'tracer.log', true);
            $prod = $productList[$item->getProductId()];
            Mage::log('########### PROD ###########', null, 'tracer.log', true);
            Mage::log(print_r($prod, true), null, 'tracer.log', true);
            $item->setCustomPrice($prod->ProductPriceUSD);
            $item->setOriginalCustomPrice($prod->ProductPriceUSD);
//            getOrderCurrencyCode
//            $item->getProduct()->setIsSuperMode(true);
//            $item->save();
        }

        // for guest orders only
        $quote->setIsMultiShipping(false);
        $quote->setCheckoutMethod('guest');
        $quote->setCustomerId(null);
        $quote->setCustomerEmail($CustomerEmail);
        $quote->setCustomerIsGuest(true);
        $quote->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);

//        $grandTotal = $address->getBaseGrandTotalSkybox();
        $grandTotal = $objData->TotalShoppingCart->TotalPriceUSD;
        Mage::log('########### $grandTotal ###########', null, 'tracer.log', true);
        Mage::log(print_r($grandTotal, true), null, 'tracer.log', true);
//        $quote->setBaseGrandTotal($grandTotal);
//        $quote->setGrandTotal($grandTotal);
//        setBaseCurrencyCode(string $value)
//        setGlobalCurrencyCode(string $value)
//        setQuoteCurrencyCode(string $value)
//        setStoreCurrencyCode(string $value)

        // Just for Guest User
        $CustomerLasName = ($CustomerLasName != null) ? $CustomerLasName : $CustomerName;

        $addressData = array(
            'firstname' => $CustomerName,
            'lastname' => $CustomerLasName,
            'street' => $CustomerAdresss,
            'city' => $CityName,
            'postcode' => $CustomerZipCode,
            'telephone' => $CustomerPhone,
            'country_id' => $CountryId,
            'region_id' => $RegionId,
            'region' => $regionName,
            'is_default_billing' => '1',
        );

        $billingAddress = $quote->getBillingAddress()->addData($addressData);
        $shippingAddress = $quote->getShippingAddress()->addData($addressData);

        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod(self::SKYBOX_SHIPPING)//flatrate_flatrate
            ->setPaymentMethod(self::SKYBOX_PAYMENT);

        $quote->getPayment()->importData(array('method' => self::SKYBOX_PAYMENT));

        $quote->collectTotals()->save();

        $address->setConceptsSkybox(json_encode($objData->TotalShoppingCart->ListDetailConcepts));
        $address->setGrandTotal($grandTotal);
        $address->setBaseGrandTotal($grandTotal);

        $service = Mage::getModel('sales/service_quote', $quote);
        $service->submitAll();

        try {
            $order = $service->getOrder();

            // Comment
            $comment = "This order was generated by Skybox Checkout";
            $order->addStatusHistoryComment($comment);
            $order->setTotalPaid($order->getGrandTotal());
            $order->save();

            // setQuoteDesactive
            $this->setQuoteDesactive($quoteId);
            //$quote->setIsActive(false);

            return $order->getIncrementId();
        } catch (Exception $e) {
            Mage::log("exception", null, 'servicios.log', true);
            Mage::log($e->getMessage());
        }
    }
}