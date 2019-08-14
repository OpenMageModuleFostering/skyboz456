<?php
class Skybox_Checkout_Block_Checkout_Cart_Sidebar extends Mage_Checkout_Block_Cart_Sidebar
{
    /**
     * Get one page checkout page url
     *
     * @return bool
     */
    public function getCheckoutUrl()
    {
        if (!$this->helper('checkout')->isRewriteCheckoutLinksEnabled()){
            return parent::getCheckoutUrl();
        }
        return $this->getUrl('skbcheckout/international', array('_secure'=>true));
    }

    public function getSubtotal()
    {
        /** @var Skybox_Core_Helper_Allow $allowHelper */
        $allowHelper = Mage::helper('skyboxcore/allow');
        if ($allowHelper->isPriceEnabled()) {
            $quote = $this->getQuote();
            /** @var Mage_Sales_Model_Quote_Address $address */
            $address = $quote->getShippingAddress();
            $subtotal = $address->getSubtotal();
            return $subtotal;
        }
        return parent::getSubtotal();
    }
}
