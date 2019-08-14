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
            $history = $order->addStatusHistoryComment('La ordern se establecio como completa', false);
            $history->setIsCustomerNotified(false);
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
    public function generateOrder($IdCart, $IdStore, $CustomerName, $CustomerLasName, $CustomerEmail, $CustomerAdresss, $CustomerPhone, $CustomerZipCode, $CityName, $CountryId, $RegionId)
    {

        Mage::log("API generateOrder started " . date('m/d/Y h:i:s a', time()));

        $quoteId = $IdCart; // should be!

        /* @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel('sales/quote')->loadByIdWithoutStore($quoteId);
        Mage::log("despues de quote", null, 'servicios.log', true);

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
            'is_default_billing' => '1',
        );

        $billingAddress = $quote->getBillingAddress()->addData($addressData);
        $shippingAddress = $quote->getShippingAddress()->addData($addressData);

        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('flatrate_flatrate')
            ->setPaymentMethod('checkmo');

        $quote->getPayment()->importData(array('method' => 'checkmo'));
        $quote->getPayment()->importData(array('method' => 'checkmo'));

        $quote->collectTotals()->save();

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
}