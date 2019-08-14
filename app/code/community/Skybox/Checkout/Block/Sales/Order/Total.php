<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_Block_Sales_Order_Total extends Mage_Sales_Block_Order_Totals
{
    public function initTotals()
    {
        parent::_initTotals();

        if ($this->isEnable()) {
            return $this;
        }

        $order = $this->getOrder();

        // Mage::log('$order', null, 'tracer.log', true);
        // Mage::log(print_r($order->debug(), true), null, 'tracer.log', true);

        $objData = json_decode($order->getConceptsSkybox());

        // Mage::log('$ConceptsSkyboxjson', null, 'tracer.log', true);
        // Mage::log(print_r($ConceptsSkyboxjson, true), null, 'tracer.log', true);

        if (count($objData) > 0) {

            if (isset($objData->TotalFeeUSD)) {
                $this->getParentBlock()->addTotal(new Varien_Object(array(
                    'code'       => 'checkout_total_skybox_fee',
                    'value'      => $objData->TotalFeeUSD,
                    'base_value' => $objData->TotalFeeUSD,
                    'label'      => 'SkyBoxCheckout',
                )), 'subtotal', 'tax');

                if ($order->getShippingAmount() == 0) {
                    $this->getParentBlock()->removeTotal('shipping');
                }

            } else {

                // @note: Only for compatibility, should be removed.
                $i = 0;

                $ConceptsSkyboxjson = json_decode($order->getConceptsSkybox());

                foreach ($ConceptsSkyboxjson as $item) {
                    if ($item->Visible != 0) {
                        $i += 1;
                        $this->getParentBlock()->addTotal(new Varien_Object(array(
                            'code'       => 'checkout_total' . $i,
                            'value'      => $item->ValueUSD,//$item->Value
                            'base_value' => $item->ValueUSD,//$item->Value
                            'label'      => $item->Concept,
                        )), 'subtotal', 'tax');
                    }
                }
                if ($order->getShippingAmount() == 0) {
                    $this->getParentBlock()->removeTotal('shipping');
                }
            }
        }

        return $this;
    }

    private function isEnable()
    {
        /** @var Skybox_Core_Model_Api_Restful $api_restful */
        $api_restful = Mage::getModel('skyboxcore/api_restful');
        $activation  = $api_restful->isModuleEnable();

        if ( ! $activation) {
            return false;
        }

        /** @var Skybox_Core_Helper_Allow $allowHelper */
        $allowHelper = Mage::helper('skyboxcore/allow');

        $locationAllow = $allowHelper->getLocationAllow();

        if ( ! $locationAllow) {
            return false;
        }

        return true;
    }
}
