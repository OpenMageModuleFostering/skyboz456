<?php
class Skybox_Checkout_Block_Checkout_Onepage_Link extends Mage_Checkout_Block_Onepage_Link
{
    public function getCheckoutUrl()
    {
        // TODO: Refactoring

//        $isEnabled = (bool)Mage::getStoreConfig('skyboxinternational/skyboxsettings/skyboxactive', Mage::app()->getStore());
        $isEnabled = Mage::getModel('skyboxcore/api_restful')->isModuleEnable();

        /**
         * Integration 3 start
         * Give to button a url by default in case is type 3
         */
        $api = Mage::getModel('skyboxcatalog/api_product');
        //$typeIntegration = Mage::getStoreConfig('settings/typeIntegration');
        $typeIntegration = Mage::helper('skyboxinternational/data')->getSkyboxIntegration();
        if (($api->getLocationAllow()==3) && $typeIntegration==3) {
            return parent::getCheckoutUrl();
        }
        /**
         * Integration 3 end
         */

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
