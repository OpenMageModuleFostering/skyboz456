<?php
/**
 * Skybox Checkout
 *
 * @category    Mage
 * @package     Skybox_Catalog
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

/**
 * Bundle product price block
 *
 * @category   Skybox
 * @package    Skybox_Catalog
 */
class Skybox_Catalog_Block_Bundle_Price extends Mage_Bundle_Block_Catalog_Product_Price
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
        Mage::log("[bundle/price] Cache Code: " . $cache_code, null, 'cache.log', true);
        return $this->_cache_code;
    }

    /*
    protected function _construct()
    {
        //parent::__construct();
        $this->addData(array(
            'cache_lifetime' => 120 //seconds
        ));
    }
    */

    public function getCacheKey()
    {
        $cache_key = "B_PRODUCT_" . $this->getProduct()->getId() . "_" . $this->getCacheCode();
        return $cache_key;
    }

    public function getCacheTags()
    {
        $cache_tag = $this->getProduct()->getId() . "_B_" . $this->getCacheCode();
        return array(Mage_Catalog_Model_Product::CACHE_TAG . $cache_tag);
    }

    /**
     * Retrieve API Product
     *
     * @return Skybox_Catalog_Model_Api_Product
     */
    protected function _getApi()
    {
        if (null === $this->_api) {
            $this->_api = Mage::getModel('skyboxcatalog/api_product');
        }
        return $this->_api;
    }

    /**
     * Convert block to html string
     *
     * @return string
     */
    public function _toHtml()
    {
        //return parent::_toHtml();
        Mage::log("CatalogBlockPrice__toHtmlbundle ", null, 'orden.log', true);
        if ($this->_getApi()->getErrorAuthenticate() && !$this->_getApi()->getLocationAllow() && $this->_getApi()->HasError()) {
            return '';
        } elseif ($this->_getApi()->HasError()) {
            //$error_code = $this->_getApi()->getStatusCode();
            $message = $this->_getApi()->getStatusMessage();
            //return '<div style="color:#FF0000;">' . $message . '</div>';
            Mage::log("[bundle/price] Some parameter doesn't given to Calculate Price: " . $this->_getApi()->getStatusMessage(), null, 'skyboxcheckout.log', true);
        }

        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->getProduct();
        $type = $product->getTypeId();
        $route_name = Mage::app()->getRequest()->getRouteName();
        //Mage::log("ROUTER; ". $route_name, null, 'cart.log', true);

        if ($type == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE && $this->getTemplate() == 'bundle/catalog/product/price.phtml') {
            if ($route_name == 'catalog') {
                return $this->calculatePrice($product);
            }

            // Note: we can't get the "Final Price".
            // Moreover, we show "From XXX to XXX" price Block.
            //return parent::_toHtml();

            // Note: We make a product type: 'bundle_fixed' to calculate Price and Weight dynamically
            return $this->calculatePrice($product, 'bundle_fixed');
        }
    }

    /**
     * Calculate Price HTML output
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function calculatePrice($product, $bundleType = 'bundle')
    {
        //Mage::log(__FILE__.' # '.__LINE__.' ~ '. __METHOD__.' => enter calculatePrice', null, 'tracer.log', true);
        $stockItem = $product->getStockItem();
        if (!$stockItem->getIsInStock()) {
            return ''; // Out of Stock
        }

        $finalPrice = $product->getFinalPrice();
        $finalPrice = ($finalPrice > 0) ? $finalPrice : null;

        /*
        //$weight = $product->getWeight();
        $weight = $product->getTypeInstance(true)->getWeight($product);
        Mage::log("[Bundle] " . $product->getName() . " : " . $finalPrice . " - [weight] " . $weight, null, 'cart.log', true);
        */

        //$template = $this->_getApi()->CalculatePrice($product, null, null, 'bundle')
        //$template = $this->_getApi()->CalculatePrice($product, null, $finalPrice, 'bundle')
        $template = $this->_getApi()->CalculatePrice($product, null, $finalPrice, $bundleType)
            ->GetTemplateProduct();

        $extraHtml = '<p class="label" id="skybox-configurable-price-from-'
            . $product->getId()
            . $this->getIdSuffix()
            . '">'
            . $template
            . '</p>'
            . '<div style="clear:both"></div>';
        //$priceHtml = parent::_toHtml();
        #manually insert extra html needed by the extension into the normal price html

        //substr_replace($priceHtml, $extraHtml, strpos($priceHtml, $htmlToInsertAfter)+strlen($htmlToInsertAfter),0);
        return $extraHtml;
    }
}