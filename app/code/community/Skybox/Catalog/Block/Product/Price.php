<?php
/**
 * Skybox Checkout
 *
 * @category    Mage
 * @package     Skybox_Catalog
 * @copyright   Copyright (c) 2014 - 2017 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

/**
 *
 * Product Price Block
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
        $activation = Mage::getModel('skyboxcore/api_restful')->isModuleEnable();

        if (!$activation) {
            return '';
        }
        //$typeIntegration = Mage::getStoreConfig('settings/typeIntegration');
        $typeIntegration = Mage::helper('skyboxinternational/data')->getSkyboxIntegration();
        //Mage::log(print_r('product\Price::_toHtml', true), null, 'tracer.log', true);
        if ($this->_getApi()->getLocationAllow() && ($typeIntegration != 3)) {
            if ($this->_getApi()->getErrorAuthenticate() && !$this->_getApi()->getLocationAllow() && $this->_getApi()->HasError()) {
                return '';
            } elseif ($this->_getApi()->HasError()) {
                //$error_code = $this->_getApi()->getStatusCode();
                $message = $this->_getApi()->getStatusMessage();

                if ($this->_getApi()->_getApi()->ErrorRatesNotFound()) {
                    $message = $this->_getApi()->_getApi()->getErrorRatesNotFoundMessage($this->getLanguageId());
                    return '<div style="color:#FF0000;">' . $message . '</div>';
                }

                // return '<div style="color:#FF0000;">' . $message . '</div>';
                return '';
            }

            /* @var $product Mage_Catalog_Model_Product */
            $product = $this->getProduct();
            $type = $product->getTypeId();
            $route_name = Mage::app()->getRequest()->getRouteName();

//            Mage::log(print_r('$route_name: '. $route_name, true), null, 'tracer.log', true);

            // Simple Product
            if ($type == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE && $this->getTemplate() == 'catalog/product/price.phtml') {
                return $this->calculatePrice($product);
            }


            if ($type == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE && $this->getTemplate() == 'catalog/product/view/price_clone.phtml') {
                return '';
            }

            // Configurable Product
            if ($type == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && $this->getTemplate() == 'catalog/product/price.phtml') {
                /*if ($route_name == 'catalog') {
                    return '';
                }*/

                if (Mage::registry('current_product')) {
                    return "";
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
        //Mage::log('Entro calcular producto', null, 'tracer.log', true);
        $stockItem = $product->getStockItem();
//        if (!$stockItem->getIsInStock()) {
//            Mage::log('sin stock', null, 'tracer.log', true);
//            return ''; // Out of Stock
//        }

        $type = $product->getTypeId();
        //Mage::log(print_r('product\Price: '.$type, true), null, 'tracer.log', true);

        if (Mage::registry('current_category') and (!(Mage::registry('current_product')))) {
            /**
             * Apply multiple calculate start
             * When: is different to product detail and you are on catalog category
             */
            $template = '<div class="skybox-price-set" product-id="' . $product->getId() . '" id="product-' . $product->getId() . '"></div>';
            /*
                if($product->getTypeId() == "simple"){
                    $parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
                    if(!$parentIds)
                        $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
                    if(isset($parentIds[0])){
                        $parent = Mage::getModel('catalog/product')->load($parentIds[0]);
                        // do stuff here
                    }
                }*/

            /**
             * Apply multiple calculate end
             */
        } else {
            switch ($type) {
                case 'simple':
                    $template = $this->_getApi()->CalculatePrice($product->getId(), null, $product->getFinalPrice(),
                        $product->getTypeId())
                        ->GetTemplateProduct();
                    break;
                case 'configurable':
                    $template = $this->_getApi()->CalculatePrice($product->getId(), null, $product->getFinalPrice(),
                        $product->getTypeId())
                        ->GetTemplateProduct();
                    break;
                case 'bundle':
                    $template = $this->_getApi()->CalculatePrice($product, null, $product->getFinalPrice(), 'simple')
                        ->GetTemplateProduct();
                    break;
            }
        }

//        Mage::log(print_r('##Product template##', true), null, 'tracer.log', true);
//        Mage::log(print_r($template, true), null, 'tracer.log', true);
        /*
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

        */
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


    /**
     * Return the Language Id
     * @return int
     */
    private function getLanguageId()
    {
        $_config = Mage::getModel('skyboxcore/config');
        $cart = $_config->getSession()->getCartSkybox();
        $id = $cart->{'LanguageId'};
        return intval($id);
    }
}
