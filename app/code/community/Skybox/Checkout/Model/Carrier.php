<?php
class Skybox_Checkout_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'skyboxcheckout_shipping';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigData('active') /*|| !Mage::helper('skyboxinternational')->getActive()*/) {
            return false;
        }
        $result = Mage::getModel('shipping/rate_result');
        $result->append($this->_getDefaultRate());

        return $result;
    }

    public function getAllowedMethods()
    {
        return array(
            'skyboxcheckout' => 'skybox delivery'//$this->getConfigData('name'),
        );
    }

    protected function _getDefaultRate()
    {
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('skyboxcheckout');//$this->_code
        $rate->setMethodTitle($this->getConfigData('title'));
        $rate->setPrice($this->getConfigData('price'));
        $rate->setCost(0);

        return $rate;
    }
}