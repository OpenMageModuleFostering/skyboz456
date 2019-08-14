<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2014 - 2017 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_Model_Observer
{
    protected $_typeApi = 'skyboxcheckout/api_checkout';
    /**
     * Model instance
     *
     * @var Skybox_Checkout_Model_Api_Checkout
     */
    protected $_api = null;

    protected function _getApi()
    {
        if (null === $this->_api) {
            $this->_api = Mage::getModel($this->_typeApi);
        }

        return $this->_api;
    }

    protected $_typeProduct = 'skyboxcatalog/api_product';
    protected $_product = null;
    protected $_enable = null;

    protected function _getProduct()
    {
        if (null === $this->_product) {
            $this->_product = Mage::getModel($this->_typeProduct);
        }

        return $this->_product;
    }

    private function isEnable()
    {
        if ($this->_enable === null) {
//            $value = (bool)Mage::getStoreConfig('skyboxinternational/skyboxsettings/skyboxactive', Mage::app()->getStore());
            $value = Mage::getModel('skyboxcore/api_restful')->isModuleEnable();
            $this->_enable = $value;
        }
        return $this->_enable;
    }

    /**
     * Metodo inicializador de Skybox Checkout
     */
    public function InitializeSkybox($observer)
    {
        $event = $observer->getEvent();
        Mage::log($event->getName());
        $this->_getApi()->InitializeBarSkybox();
    }

    public function RemoveTax(Varien_Event_Observer $observer)
    {
        if (!$this->isEnable()) {
            return;
        }

        Mage::log('RemoveTax->ini', null, 'SkyObserver.log', true);
        $customer_id = Mage::getSingleton('customer/session')->getId();
        $customer = Mage::getModel("customer/customer")->load($customer_id);

        if ($customer->getIsTaxExempt() == 1) {
            $items = $observer->getEvent()->getQuote()->getAllVisibleItems();
            foreach ($items as $item) {
                $item->getProduct()->setTaxClassId(0);
            }
            Mage::log('RemoveTax->Tax', null, 'SkyObserver.log', true);
        }
        Mage::log('RemoveTax->fin', null, 'SkyObserver.log', true);
    }

    public function CalculatePriceQuoteItem(Varien_Event_Observer $observer)
    {
        if (!$this->isEnable()) {
            return $this;
        }
        Mage::log('Observer->CalculatePriceQuoteItem->ini', null, 'cart.log', true);

        $event = $observer->getEvent();
        $quote_item = $event->getQuoteItem();

        /*if (count($quote_item)) {
            foreach ($quote_item as $item) {

                $item->setTaxPercent(0);
                $item->setTaxAmount(0);
                $item->setBaseTaxAmount(0);

                $item->setPriceInclTax($item->getPrice());
                $item->setBasePriceInclTax($item->getBasePrice());
                $item->setBaseRowTotalInclTax($item->setBaseRowTotal());

            }

            //$quote_item->save();
            Mage::log('|UpdateParametersQuoteItem|fin', null, 'cart.log', true);
        }*/

        return $this;

        //$product = $quote_item->getProduct();

        /*$quote = Mage::getSingleton('checkout/session');
        foreach ($quote->getQuote()->getAllItems() as $item) {
           if($item->getProductId() == $quote_item->getProductId())
           {
               Mage::throwException(
               Mage::helper('sales')->__('Error al agregar producto al carrito.' . $item->getQty())
               );
               break;
           }
       }
       Mage::throwException(
               Mage::helper('sales')->__('Error al agregar producto al carrito.-')
           );*/
        /*$this->_getProduct()->CalculatePrice($quote_item->getProductId(), $product->getFInalPrice());

        if($this->_getProduct()->HasError())
        {
            Mage::throwException(
                Mage::helper('sales')->__('Error al agregar producto al carrito.')
            );
            //return $this;
        }

        Mage::log(json_encode($this->_getProduct()->getResponse()));

        $this->_getApi()->AddProductOfCart($quote_item->getProductId(), 1);



        if($this->_getApi()->HasError())
        {
            Mage::throwException(
                Mage::helper('sales')->__('Error al agregar producto al carrito')
            );
            //return $this;
        }

        $idProductSkybox = $this->_getApi()->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_ID, "0");
        $customsSkybox   = $this->_getProduct()->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_CUSTOMS, "0");
        $shippingSkybox  = $this->_getProduct()->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_SHIPPING, "0");
        $insuranceSkybox = $this->_getProduct()->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_INSURANCE, "0");
        $totalPrice 	 = $this->_getProduct()->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_TOTAL, "0");

        $quote_item->setIdProductSkybox($idProductSkybox);
        $quote_item->setCustomsSkybox($customsSkybox);
        $quote_item->setShippingSkybox($shippingSkybox);
        $quote_item->setInsureSkybox($insuranceSkybox);
        $quote_item->setOriginalCustomPrice($totalPrice);

        $quote_item->save();
        */
        //Mage::log('|UpdateParametersQuoteItem| id->' . $quote_item->getId() . " quty->" . $quote_item->getQty(), null, 'cart.log', true);
    }

    public function changeQuoteAddressSkybox(Varien_Event_Observer $observer)
    {
        /**
         * only one time for call to service start - Active
         * This always run when you do alter to product as change/delete product
         */
        //$session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
        //$session->setData("callToSkyBox", true);

        /**
         * only one time for call to service end - Active
         */

        if (!$this->isEnable()) {
            return $this;
        }

        /**
         * Integration 3 start, show price shop in cart*
         */
        $allowRunByIntegration3 = true;
        //$typeIntegration = Mage::getStoreConfig('settings/typeIntegration');
        $typeIntegration = Mage::helper('skyboxinternational/data')->getSkyboxIntegration();
        if ($typeIntegration == 3) {
            $allowRunByIntegration3 = false;
        }
        //Mage::log("Call true22: changeQuoteAddressSkybox", null, 'local.log', true);
        /**
         * Integration 3 end, show price shop in cart*
         */
        if ($allowRunByIntegration3) {
            if ($this->_getApi()->getLocationAllow()) {
                Mage::log('Observer->changeQuoteAddressSkybox : ini', null, 'TotalSales.log', true);
                /* $quote Mage_Sales_Model_Quote */
                $quote = $observer->getEvent()->getQuote();

                /* $address Mage_Sales_Model_Quote_Address */
                $address = $quote->getShippingAddress();

                $totals = 0;
                $baseTotals = 0;
                $totalTax = 0;

                foreach ($quote->getAllItems() as $item) {

                    //Estas lineas se agregaron para elimnar el Tax del Carrito
                    /*$item->setTaxPercent(0);
                    $item->setTaxAmount(0);
                    $item->setBaseTaxAmount(0);

                    $item->setPriceInclTax($item->getPrice());
                    $item->setBasePriceInclTax($item->getBasePrice());
                    $item->setBaseRowTotalInclTax($item->setBaseRowTotal());*/
                    //------------------------------------------------------------>

                    Mage::log('Observer->changeQuoteAddressSkybox->Item->getRowTotalSkybox->' . $item->getRowTotalSkybox(),
                        null, 'TotalSales.log', true);

                    $totals += $item->getRowTotalSkybox();
                }

                $quote->save();

                $baseTotals = $totals;

                //$totalTax1= $address->getTaxAmount();

                //Estas lineas se agregaron para elimnar el Tax del Carrito
                $address->setTaxAmount(0);
                $address->setBaseTaxAmount(0);

                $applied_taxes = array();

                $address->setAppliedTaxes($applied_taxes);
                //------------------------------------------------------------>

                //$address->save();

                //$totales = $address->getTotalAmount("checkout_total1");

                //$tamanio = count($totales);
                //for ($x=0;$x<$tamanio; $x++) Mage::log('Observer->changeQuoteAddressSkybox->total->' . $totales[$x], null, 'TotalSales.log', true);

                //Mage::log('Observer->changeQuoteAddressSkybox->totales->' . var_dump($totales["subtotal"]), null, 'TotalSales.log', true);
                //Mage::log('Observer->changeQuoteAddressSkybox->totales->' . $totales, null, 'TotalSales.log', true);

                $totalTax = 0; //$address->getTaxAmount(); //Comentado

                //Recorremos los conceptos agrupados (esto debe modificarse no es lo optimo)
                /*$conceptsSkybox = json_decode($address->getConceptsSkybox());
                foreach ($conceptsSkybox as $concept) {
                    $totalTax = $totalTax + $concept->Value;
                }*/

                $totalRmt = 0;
                //Recorremos los conceptos agrupados (esto debe modificarse no es lo optimo)
                /*$rmtSkybox = json_decode($address->getRmtSkybox());
                foreach ($rmtSkybox as $rmt) {
                    $totalRmt = $totalRmt + $rmt->Value;
                }*/

                //Mage::log('Observer->changeQuoteAddressSkybox->totalTax1->' . $totalTax1 . ' &totalbase->' . $baseTotals, null, 'TotalSales.log', true);
                //Mage::log('Observer->changeQuoteAddressSkybox->totalTax->' . $totalTax . ' &totalbase->' . $baseTotals, null, 'TotalSales.log', true);

                /*$address->setSubtotalSkybox($totals);
                $address->setBaseSubtotalSkybox($baseTotals);
                $address->setGrandTotalSkybox($totals+$totalTax);
                $address->setBaseGrandTotalSkybox($baseTotals+$totalTax);*/

                $baseTotals = $address->getBaseGrandTotalSkybox();
                $totals = $address->getGrandTotalSkybox();

                $address->setPriceInclTax($totals);
                $address->setBasePriceInclTax($baseTotals);

                $totals = floatval(preg_replace("/[^-0-9\.]/", "", $totals));
                //$address->setGrandTotal($totals+$totalTax);
                $address->setGrandTotal($totals + $totalTax);
                $address->setBaseGrandTotal($baseTotals + $totalTax);

                //$address->save();

                Mage::log('Observer->changeQuoteAddressSkybox : fin', null, 'TotalSales.log', true);

                return $this;
            }
            return $this;
        }

    }

    /**
     * Hide Skybox Shipping Method
     * @param Varien_Event_Observer $observer
     */
    public function hideShippingMethod(Varien_Event_Observer $observer)
    {
        if (Mage::getDesign()->getArea() === Mage_Core_Model_App_Area::AREA_FRONTEND) {
            $quote = $observer->getEvent()->getQuote();
            $store = Mage::app()->getStore($quote->getStoreId());
            $carriers = Mage::getStoreConfig('carriers', $store);

            $hiddenMethodCode = 'skyboxcheckout_shipping';

            foreach ($carriers as $carrierCode => $carrierConfig) {
                if ($carrierCode == $hiddenMethodCode) {
                    $store->setConfig("carriers/{$carrierCode}/active", '0');
                }
            }
        }
    }
}
