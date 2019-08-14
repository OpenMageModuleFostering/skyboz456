<?php
/**
 * Skybox Checkout
 *
 * @category    Mage
 * @package     Skybox_Catalog
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

/**
 *
 * Product price Block
 *
 * @author      César Tapia M. <ctapia@skyworldint.com>
 */
class Skybox_Catalog_Block_Product_Price extends Mage_Catalog_Block_Product_Price
{
    /*
    * @var string $_cache_code
    */
    public $_cache_code = null;

    public function getCacheCode()
    {
        Mage::log("cache ", null, 'orden.log', true);
        if ($this->_cache_code == null) {
            /* @var $config Skybox_Core_Model_Config */
            $config = Mage::getModel("skyboxcore/config");
            $skyboxUser = $config->getSession()->getSkyboxUser();
            $cache_code = $skyboxUser->CartCountryISOCode . $skyboxUser->CartCityId . $skyboxUser->CartCurrencyISOCode;
            $this->_cache_code = $cache_code;
        }
        Mage::log("[product/price] Cache Code: " . $cache_code, null, 'cache.log', true);
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
        $cache_key = "PRODUCT_" . $this->getProduct()->getId() . "_" . $this->getCacheCode();
        return $cache_key;
    }

    public function getCacheTags()
    {
        $cache_tag = $this->getProduct()->getId() . "_" . $this->getCacheCode();
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
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if($this->_getApi()->getLocationAllow()){ // Rogged
            if ($this->_getApi()->getErrorAuthenticate() && !$this->_getApi()->getLocationAllow() && $this->_getApi()->HasError()) {
                return '';
            } elseif ($this->_getApi()->HasError()) {
                //$error_code = $this->_getApi()->getStatusCode();
                $message = $this->_getApi()->getStatusMessage();
                return '<div style="color:#FF0000;">' . $message . '</div>';
            }

            /* @var $product Mage_Catalog_Model_Product */
            $product = $this->getProduct();
            $type = $product->getTypeId();
            $route_name = Mage::app()->getRequest()->getRouteName();

            // Simple Product
            if ($type == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE && $this->getTemplate() == 'catalog/product/price.phtml') {
                return $this->calculatePrice($product);
            }

            if ($type == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE && $this->getTemplate() == 'catalog/product/view/price_clone.phtml') {
                return '';
            }

            // Configurable Product
            if ($type == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && $this->getTemplate() == 'catalog/product/price.phtml') {
                if ($route_name == 'catalog') {
                    return '';
                }
                return $this->calculatePrice($product);
            }

            if ($type == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && $this->getTemplate() == 'catalog/product/view/tierprices.phtml') {
                if ($route_name == 'catalog') {
                    return $this->calculatePrice($product);
                }
                return '';
            }
        }
        return parent::_toHtml();
    }

    /**
     * Calculate Price HTML output
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function calculatePrice($product)
    {
        $stockItem = $product->getStockItem();
        if (!$stockItem->getIsInStock()) {
            return ''; // Out of Stock
        }

        $type = $product->getTypeId();
        switch ($type) {
            case 'simple':
                $template = $this->_getApi()->CalculatePrice($product->getId(), null, $product->getFinalPrice(), $product->getTypeId())
                    ->GetTemplateProduct();
                break;
            case 'configurable':
                $template = $this->_getApi()->CalculatePrice($product->getId(), null, $product->getFinalPrice(), $product->getTypeId())
                    ->GetTemplateProduct();
                break;
            case 'bundle':
                $template = $this->_getApi()->CalculatePrice($product, null, $product->getFinalPrice(), 'simple')
                    ->GetTemplateProduct();
                break;
        }

        $extraHtml = ''
            . '<div class="price-box">'
            . '<p class="label" id="skybox-configurable-price-from-'
            . $product->getId()
            . $this->getIdSuffix()
            . '">'
            . $template
            . '</p>'
            . '</div>'
            . '<div style="clear:both"></div>';
        //$priceHtml = parent::_toHtml();
        #manually insert extra html needed by the extension into the normal price html

        //substr_replace($priceHtml, $extraHtml, strpos($priceHtml, $htmlToInsertAfter)+strlen($htmlToInsertAfter),0);
        return $extraHtml;
    }
}