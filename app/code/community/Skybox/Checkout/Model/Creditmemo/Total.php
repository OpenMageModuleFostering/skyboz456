<?php

class Skybox_Checkout_Model_Creditmemo_Total extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract{

    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {

        Mage::log("==============================ini", null, 'invoice.log', true); 
        $order = $creditmemo->getOrder();
        $amount = 20;
        if ($amount) {
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $amount);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $amount);
        }
 
        return $this;
    }
}