<?php
/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Catalog
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

/**
 * New products widget
 *
 * @category   Skybox
 * @package    Skybox_Catalog
 */
class Skybox_Catalog_Block_Product_Widget_New extends Mage_Catalog_Block_Product_Widget_New
{

    /*
    * @var string $_cache_code
    */
    public $_cache_code = null;

    public function getCacheCode()
    {
        if ($this->_cache_code == null) {
            /* @var $config Skybox_Core_Model_Config */
            $config = Mage::getModel("skyboxcore/config");
            $skyboxUser = $config->getSession()->getSkyboxUser();
            $cache_code = $skyboxUser->CartCountryISOCode . $skyboxUser->CartCityId . $skyboxUser->CartCurrencyISOCode;
            $this->_cache_code = $cache_code;
        }
        Mage::log("[widget/new] Cache Code: " . $cache_code, null, 'cache.log', true);
        return $this->_cache_code;
    }

    /**
     * Initialize block's cache and template settings
     */
    protected function _construct()
    {
        parent::_construct();
        $cache_key = Mage_Catalog_Model_Product::CACHE_TAG . "_" . $this->getCacheCode();
        $this->setAttribute("cache_key", $cache_key);
        if (Mage::getVersion() == '1.9.1'){
            $this->addData(array('cache_lifetime' => null)); // Skip cache generation
        }
    }
}