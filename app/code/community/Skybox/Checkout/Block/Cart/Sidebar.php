<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_Block_Cart_Sidebar extends Mage_Checkout_Block_Cart_Sidebar
{

    /**
     * Get shopping cart subtotal.
     *
     * It will include tax, if required by config settings.
     *
     * @param   bool $skipTax flag for getting price with tax or not. Ignored in case when we display just subtotal incl.tax
     * @return  decimal
     */
    public function getSubtotal($skipTax = true)
    {
        $quote = $this->getQuote();

        /* $address Mage_Sales_Model_Quote_Address */
        $address = $quote->getShippingAddress();

        //$subtotal = $address->getSubtotalSkybox();
        $subtotal = $address->getSubtotal();
        return $subtotal;
    }
}
