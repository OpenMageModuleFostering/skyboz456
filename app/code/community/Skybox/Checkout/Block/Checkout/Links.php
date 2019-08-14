<?php
class Skybox_Checkout_Block_Checkout_Links extends Mage_Checkout_Block_Links
{
    /**
     * Add link on checkout page to parent block
     *
     * @return Mage_Checkout_Block_Links
     */
    public function addCheckoutLink()
    {
        //Mage::log('get addCheckoutLink skybox', null, 'tracer.log', true);

        if (!$this->helper('checkout')->isRewriteCheckoutLinksEnabled()){
            return parent::addCheckoutLink();
        }

        if (!$this->helper('checkout')->canOnepageCheckout()) {
            return $this;
        }
        if ($parentBlock = $this->getParentBlock()) {
            $text = $this->__('Checkout');
            $parentBlock->addLink($text, 'skbcheckout/international', $text, true, array('_secure'=>true), 60, null, 'class="top-link-skybox"');
        }
        return $this;
    }
}
