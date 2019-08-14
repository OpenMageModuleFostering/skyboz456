<?php

class Skybox_Checkout_Model_Invoice_Total extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect($invoice)
    {

        /*Mage::log("==============================ini", null, 'invoice.log', true); 
        $order = $invoice->getOrder();
        $amount = 40;
        if ($amount) {
            $invoice->setGrandTotal($invoice->getGrandTotal() + $amount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $amount);
        }*/
 
        return $this;
    }
}
