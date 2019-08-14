<?php
class Skybox_Checkout_Block_Checkout_Onepage_Link extends Mage_Checkout_Block_Onepage_Link
{
    public function getCheckoutUrl()
    {
        // TODO: Refactoring

//        $isEnabled = (bool)Mage::getStoreConfig('skyboxinternational/skyboxsettings/skyboxactive', Mage::app()->getStore());
        $isEnabled = Mage::getModel('skyboxcore/api_restful')->isModuleEnable();

        if($isEnabled) {
            return $this->getUrl('skbcheckout/international', array('_secure'=>true));
        }
        /*
        if (Mage::helper('onestepcheckout')->isRewriteCheckoutLinksEnabled()){
            return $this->getUrl('onestepcheckout', array('_secure'=>true));
        }
        */
        /*
        Mage::log('get getCheckoutUrl skybox', null, 'tracer.log', true);
        if (!$this->helper('checkout')->isRewriteCheckoutLinksEnabled()){
            return parent::getCheckoutUrl();
        }
        */

        return parent::getCheckoutUrl();

    }
}
