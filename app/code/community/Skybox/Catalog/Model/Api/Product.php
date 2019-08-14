<?php

/**
 * Skybox Catalog
 *
 * @category    Skybox
 * @package     Skybox_Catalog
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Catalog_Model_Api_Product extends Skybox_Core_Model_Standard
{

    /**
     * Model instance
     * @var Skybox_Core_Model_Config
     */
    /*protected $_config = null;

    protected $_typeConfig = "skyboxcore/config";

    protected function getConfig()
    {
        if(null === $this->_config)
            $this->_config = Mage::getModel($this->_typeConfig);
    }*/

    /**
     * Model instance
     * @var Mage_Core_Catalog_Model_Product
     */
    protected $_product = null;

    protected $_productResult = null;

    protected $_typeProduct = "catalog/product";

    protected $_product_id = null;
    protected $_product_data = null;

    public function getProductId()
    {
        if ($this->_product_id != null) {
            return $this->_product_id;
        }
        return null;
    }

    public function getProductData()
    {
        if ($this->_product_data != null) {
            return $this->_product_data;
        }
        return null;
    }


    /**
     * Calculate Price
     *
     * @param   int|Mage_Catalog_Model_Product $product
     * @param   Varien_Object $request
     * @param   float $finalPrice
     * @param   string $type
     * @param   int $objectId
     * @return  Mage_Checkout_Model_Cart
     */
    public function CalculatePrice($product, $request, $finalPrice = null, $type = null, $objectId = 1)
    {
        Mage::log("ApiProductCatalog ", null, 'orden.log', true);

        //Mage::log(print_r('Product', true), null, 'tracer.log', true);

        //if ($this->getErrorAuthenticate() && !$this->getLocationAllow()) {
        if (!$this->getLocationAllow()) {
            return $this;
        }

        if (!is_object($product)) {
            $storeId = Mage::app()->getStore()->getStoreId();
            $product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($product);
        }

        $productId = $product->getId();
        $_data = null;

        if ($type == null) {
            $type = $product->getTypeId();
        }

        $defaultSwitch = false;
        switch ($type) {
            case 'simple':
                $finalPrice = isset($finalPrice) ? $finalPrice : $product->getFinalPrice();
                $category_id = $product->getSkyboxCategoryId();
                $category_id = isset($category_id) ? $category_id : $this->getCommodityFromCategory($product);
                $volWeight = !empty(trim($product->getDimensionalWeight())) ? $product->getDimensionalWeight() : 0;

                $_data = array(
                    'object_id' => 1,
                    'name' => $product->getName(),
                    'sku' => $product->getSku(),
                    'category_id' => $category_id,
                    'final_price' => $finalPrice,
                    'weight' => ($product->getWeight()) ? $product->getWeight() : 1,
                    'image_url' => $product->getImageUrl(),
                    'typeProduct' => $type,
                    'VolWeight' => $volWeight,
                    'merchantproductid' => $productId
                );
//                $this->_calculatePrice($_data);
                break;

            case 'configurable':

                $finalPrice = isset($finalPrice) ? $finalPrice : null;
                $weight = $product->getTypeInstance(true)->getWeight($product);
                $sku = $product->getSku();

                Mage::log("CalculatePrice configurable : " . $finalPrice, null, 'skyboxcheckout.log', false);

                if ($request) {
                    $childProduct = Mage::getModel('catalog/product_type_configurable')
                        ->getProductByAttributes($request->getData('super_attribute'), $product);

                    $productId = $childProduct->getId();
                    $sku = $childProduct->getSku();

                }

                $parentItem = null;

                if (isset($request) && !$finalPrice) {
                    Mage::log("CalculatePrice candidate INI", null, 'skyboxcheckout.log', false);
                    $_finalPrice = 0;

                    $cartCandidates = $product->getTypeInstance(true)
                        ->prepareForCartAdvanced($request, $product, 'full');

                    foreach ($cartCandidates as $candidate) {
                        // Child items can be sticked together only within their parent
                        $stickWithinParent = $candidate->getParentProductId() ? $parentItem : null;
                        $candidate->setStickWithinParent($stickWithinParent);

                        $candidate_getFinalPrice = $candidate->getPriceModel()->getFinalPrice($request->getQty(),
                            $product);
                        Mage::log("CalculatePrice candidate getFinalPrice: " . $candidate_getFinalPrice, null,
                            'skyboxcheckout.log', false);

                        $_finalPrice = $_finalPrice + $candidate_getFinalPrice;
                        if ($_finalPrice) {
                            break;
                        }
                    }
                    $finalPrice = $_finalPrice;
                    Mage::log("CalculatePrice candidate FIN", null, 'skyboxcheckout.log', false);
                }

                /* Weight start*/
                /*
                $options = array();
                // Get any super_attribute settings we need
                $productAttributesOptions = $product->getTypeInstance(true)->getConfigurableOptions($product);
                foreach ($productAttributesOptions as $productAttributeOption) {
                    $options[$product->getId()] = array();
                    foreach ($productAttributeOption as $optionValues) {
                        $val = ($optionValues['option_title']);
                        $options[$product->getId()][] = array (
                            $optionValues['sku'] =>  $optionValues['attribute_code']."=".$val

                        ) ;
                    }

                }
                $prod_weight = 1;
                if(!empty($optionValues)) {
                    $a = Mage::getModel('catalog/product')->loadByAttribute('sku',$optionValues['sku']);
                    //$product->load($product->getIdBySku($optionValues['sku']));
                    $prod_weight = $a->getWeight();
                    echo ":::".$prod_weight."::";
                }
                */
                /* Weight end*/

                $category_id = $product->getSkyboxCategoryId();
                $category_id = isset($category_id) ? $category_id : $this->getCommodityFromCategory($product);

                $volWeight = !empty(trim($product->getDimensionalWeight())) ? $product->getDimensionalWeight() : 0;
                $_data = array(
                    'object_id' => 1,
                    'name' => $product->getName(),
                    //'sku' => $product->getSku(),
                    'sku' => $sku,
                    'category_id' => $category_id,
                    'final_price' => $finalPrice,
                    'weight' => $weight ? $weight : 1,
                    'image_url' => $product->getImageUrl(),
                    'typeProduct' => $type,
                    'VolWeight' => $volWeight,
                    'merchantproductid' => $productId
                );

//                $this->_calculatePrice($_data);
                break;

            case 'bundle':
                //$_finalPrice = $product->getTotalBundleItemsPrice($product, $request->getQty());
                //$finalPrice = isset($finalPrice) ? $finalPrice : $_finalPrice;

                //Mage::log(print_r($product, true), null, 'cart.log', true);
                //Mage::log("Product: [" . $product->getId() . "] - Class: " . get_class($product), null, 'skyboxcheckout.log', false);

                //Mage::log(print_r($request, true), null, 'cart.log', true);

                $finalPrice = isset($finalPrice) ? $finalPrice : null;
                $weight = $product->getTypeInstance(true)->getWeight($product);
                $sku = $product->getSku();

                //if (isset($request) && !$finalPrice) {
                if (isset($request)) {

                    $parentItem = null;
                    $_finalPrice = null;
                    $_weight = 0;

                    $cartCandidates = $product->getTypeInstance(true)
                        ->prepareForCartAdvanced($request, $product, 'full');

                    foreach ($cartCandidates as $candidate) {
                        // Child items can be sticked together only within their parent
                        $stickWithinParent = $candidate->getParentProductId() ? $parentItem : null;
                        $candidate->setStickWithinParent($stickWithinParent);

                        if (!$_finalPrice) {
                            $_finalPrice = $candidate->getPriceModel()->getFinalPrice($request->getQty(), $product);
                            //break;
                        }
                        //$_weight = $_weight + $candidate->getPriceModel()->getWeight();
                        $_weight = $_weight + $candidate->getWeight();
                    }

                    $finalPrice = isset($finalPrice) ? $finalPrice : $_finalPrice;
                    if ($_weight > 0) {
                        $weight = $_weight;
                    }
                }

                //Mage::log("weight: " . $weight, null, 'cart.log', true);
                //Mage::log("sku: " . $sku, null, 'cart.log', true);

                $category_id = $product->getSkyboxCategoryId();
                $category_id = isset($category_id) ? $category_id : $this->getCommodityFromCategory($product);

                $volWeight = !empty(trim($product->getDimensionalWeight())) ? $product->getDimensionalWeight() : 0;
                $_data = array(
                    'object_id' => 1,
                    'name' => $product->getName(),
                    'sku' => $sku,
                    'category_id' => $category_id,
                    'final_price' => $finalPrice,
                    'weight' => ($weight) ? $weight : 1,
                    'image_url' => $product->getImageUrl(),
                    'typeProduct' => $type,
                    'VolWeight' => $volWeight,
                    'merchantproductid' => $productId
                );
//                $this->_calculatePrice($_data);
                break;

            case 'bundle_fixed':

                // @Note: It should be rewrite!!
                $finalPrice = isset($finalPrice) ? $finalPrice : null;
                $skyboxCategoryId = $product->getSkyboxCategoryId();
                $weight = $product->getTypeInstance(true)->getWeight($product);
                $sku = $product->getSku();

                $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
                    $product->getTypeInstance(true)->getOptionsIds($product), $product
                );

                $_finalPrice = null;
                $_weight = 0;
                $_skyboxCategoryId = null;

                foreach ($selectionCollection as $option) {
                    //if ($option->getData('is_default') == 0) continue;

                    $product_simple = Mage::getModel('catalog/product')
                        ->load($option->getId());

                    $_finalPrice = $_finalPrice + $product_simple->getFinalPrice();
                    $_weight = $_weight + $product_simple->getWeight();

                    if (!$_skyboxCategoryId) { // Set SkyboxCategory from the first Simple Product
                        // $_skyboxCategoryId = $product_simple->getSkyboxCategoryId();
                        $_skyboxCategoryId = $product->getSkyboxCategoryId();
                        $_skyboxCategoryId = isset($_skyboxCategoryId) ? $_skyboxCategoryId : $this->getCommodityFromCategory($product);
                    }
                }

                $finalPrice = isset($finalPrice) ? $finalPrice : $_finalPrice;

                if (!$weight) {
                    $weight = $_weight;
                }

                if (!$skyboxCategoryId) {
                    $skyboxCategoryId = $_skyboxCategoryId;
                }

                /*Mage::log("-------------------------", null, 'cart.log', true);
                Mage::log("Product: " . $product->getName(), null, 'cart.log', true);
                Mage::log("Price: " . $finalPrice, null, 'cart.log', true);
                Mage::log("weight: " . $weight, null, 'cart.log', true);
                Mage::log("sku: " . $sku, null, 'cart.log', true);*/


                $volWeight = !empty(trim($product->getDimensionalWeight())) ? $product->getDimensionalWeight() : 0;
                $_data = array(
                    'object_id' => 1,
                    'name' => $product->getName(),
                    'sku' => $sku,
                    'category_id' => $skyboxCategoryId,
                    'final_price' => $finalPrice,
                    'weight' => ($weight) ? $weight : 1,
                    'image_url' => $product->getImageUrl(),
                    'typeProduct' => $type,
                    'VolWeight' => $volWeight,
                    'merchantproductid' => $productId
                );

//                $this->_calculatePrice($_data);
                break;

            default:
                Mage::log("CalculatePrice:: Product Type (" . $type . ") is invalid or not supported at SkyboxCheckout CalculatePrice",
                    null, 'skyboxcheckout.log', false);
                trigger_error("CalculatePrice:: Product Type (" . $type . ") is invalid or not supported at SkyboxCheckout CalculatePrice");
                $defaultSwitch = true;
                break;
        }

        if (!$defaultSwitch) {
            //Mage::log(print_r('EnableTax: ', true), null, 'tracer.log', true);
            //Mage::log(print_r($this->getEnabledAddSumTaxToPrice(), true), null, 'tracer.log', true);
            if ($this->getEnabledAddSumTaxToPrice()) {
                $store = Mage::app()->getStore('default');
                $taxCalculation = Mage::getModel('tax/calculation');
                $request = $taxCalculation->getRateRequest(null, null, null, $store);
//            $product = Mage::registry('current_product');
                $taxClassId = $product->getTaxClassId();
                $percentTax = $taxCalculation->getRate($request->setProductClassId($taxClassId));

                $taxPrice = $_data['final_price'] * ($percentTax / 100);

                //Mage::log(print_r('Id product: ' . $product->getId(), true), null, 'tracer.log', true);
                //Mage::log(print_r('$taxPrice: ' . $taxPrice, true), null, 'tracer.log', true);
                //Mage::log(print_r('price: ' . $_data['final_price'], true), null, 'tracer.log', true);
                //Mage::log(print_r('$taxPrice+price: ' . ($taxPrice + $_data['final_price']), true), null, 'tracer.log', true);
                $comissionSkybox = $_data['final_price'] * (10 / 100);
                //Mage::log(print_r('comision skybox: ' . ($comissionSkybox), true), null, 'tracer.log', true);
                //Mage::log(print_r('price+comision skybox: ' . ($_data['final_price'] + $comissionSkybox), true), null, 'tracer.log', true);
                //Mage::log(print_r('$taxPrice+price+comision: ' . ($taxPrice + $_data['final_price'] + $comissionSkybox), true), null, 'tracer.log', true);
                //Mage::log(print_r('comision true: ' . (($taxPrice + $_data['final_price']) * (10 / 100)), true), null, 'tracer.log', true);
                $priceReally = ($taxPrice + $_data['final_price']) + ($taxPrice + $_data['final_price']) * (10 / 100);
                //Mage::log(print_r('$taxPrice+price+comision true: ' . ($priceReally), true), null, 'tracer.log', true);


                $_data['final_price'] = $_data['final_price'] + $taxPrice;
            }

            //Mage::log(print_r('####################### Data produc params #######################', true), null, 'tracer.log', true);
            //Mage::log(print_r($_data, true), null, 'tracer.log', true);
            // $this->_calculatePrice($_data);
            try {
                $this->_calculatePrice($_data);
            } catch (\Exception $e) {
                Mage::log($e->getMessage(), null, 'skyboxcheckout.log', false);
            }
        }

        Mage::log("Product Class: " . get_class($product), null, 'skyboxcheckout.log', false);
        Mage::log("Product Type: " . $type, null, 'skyboxcheckout.log', false);
        Mage::log(print_r($_data, true), null, 'skyboxcheckout.log', false);

        $this->_product_data = $_data;
        $this->_product_id = $productId;
        return $this;
    }

    private function _calculatePrice($data)
    {
        $msg = sprintf("CalculatePrice: [%s] - [%s] - [%s] - [Category_id: %s] - [Price: %s] - [weight: %s] - [typeProduct: %s]",
            $data['object_id'],
            $data['sku'],
            $data['name'],
            $data['category_id'],
            $data['final_price'],
            $data['weight'],
            $data['typeProduct']
        );

        Mage::log($msg, null, 'skyboxcheckout.log', false);

        $params = array(
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_MERCHANT => $this->getMerchant(),
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_TOKEN => $this->getAuthorizedToken(),
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_GUID => $this->getGuidApi(),
            'htmlobjectid' => $data['object_id'],
            'storeproductcode' => $data['sku'],
            'storeproductname' => $data['name'],
            'storeproductcategory' => $data['category_id'],
            'storeproductprice' => $data['final_price'],
            //'storeproductprice' => $product->getFinalPrice(),
            'weight' => ($data['weight']) ? $data['weight'] : 1,
            'weightunit' => $this->getWeightUnit(),
            'storeproductimgurl' => $data['image_url'],
            'merchantproductid' => $data['merchantproductid']
        );
        $this->CallApiRest(Skybox_Core_Model_Config::SKYBOX_ACTION_CALCULATE, $params);
    }

    public function HtmlTemplateButton()
    {
        if (!$this->getErrorAuthenticate() && $this->_getApi()->getLocationAllow()) {
            $params = array(
                Skybox_Core_Model_Config::SKYBOX_PARAMETER_MERCHANT => $this->getMerchant(),
                Skybox_Core_Model_Config::SKYBOX_PARAMETER_TOKEN => $this->getAuthorizedToken(),
                Skybox_Core_Model_Config::SKYBOX_PARAMETER_GUID => $this->getGuidApi()
            );

            $this->CallApiRest(Skybox_Core_Model_Config::SKYBOX_ACTION_GET_TEMPLATE_BUTTON, $params);
        }

        return $this;
    }

    public function GetTemplateProduct()
    {
        Mage::log("ApiProduct ", null, 'orden.log', true);
        if (!$this->getErrorAuthenticate() && $this->_getApi()->getLocationAllow() && !$this->_getApi()->ErrorServiceNotController()) {
            //if (!$this->getErrorAuthenticate() && $this->_getApi()->getLocationAllow() && !$this->_getApi()->ErrorService()) {
            $_productResult = $this->getResponse();
            $template = $_productResult->TooltipButtonTemplate;
            return $template;
//            $templateButton = $this->getHtmlTemplateButton();
//            if (empty($templateButton))
//                $this->HtmlTemplateButton();
//
//            /*if($_productResult->{Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_BUTTONERROR})
//            {*/
//            //Mage::log("_productResult--->" . json_decode($_productResult));
//            $templateButton = $this->getHtmlTemplateButton();
//            if (!empty($templateButton)) {
//                $template = $this->getHtmlTemplateButton();
////                Mage::log(print_r('response template buttom', true), null, 'tracer.log', true);
////                Mage::log(print_r($_productResult, true), null, 'tracer.log', true);
//                foreach ($_productResult as $key => $value) {
//                    $template = str_replace('{' . $key . '}', $value, $template);
//                }
//
//                // Just for {Block} crap
//                $template = str_replace('{Block}', '', $template);
//
//                return $template;
//            }
//
//            $displayUSD = "''"; //CartCountryIso
//            /*}else{
//                return "Plantilla de error";
//            }*/
//            //Mage::log(get_class($this) . " GetTemplateProduct() ", null, 'cart.log', true);
        } else {
            $template = '<div class="skx_content_button"></div>';
            return $template;
        }
        return "";
    }

    protected $_isUSD = null;

    public function IsUSD()
    {
        if (null == $this->_isUSD) {
            $this->_isUSD = ($this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_CART_CURRENCY_ISO,
                    "") == Skybox_Core_Model_Config::SKYBOX_CURRENCY_USD);
        }
        return $this->_isUSD;
    }

    public function getCustoms()
    {
        return $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_CUSTOMS, "0");
    }

    public function getShipping()
    {
        return $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_SHIPPING, "0");
    }

    public function getInsurance()
    {
        return $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_INSURANCE, "0");
    }

    public function getPrice()
    {
        return $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_PRICE, "0");
    }

    public function getTotalPrice()
    {
        return $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_TOTAL, "0");
    }

    public function getCustomsUSD()
    {
        return $this->IsUSD() ? $this->getCustoms() : $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_CUSTOMS_USD,
            "0");
    }

    public function getShippingUSD()
    {
        return $this->IsUSD() ? $this->getShipping() : $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_SHIPPING_USD,
            "0");
    }

    public function getInsuranceUSD()
    {
        return $this->IsUSD() ? $this->getInsurance() : $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_INSURANCE_USD,
            "0");
    }

    public function getPriceUSD()
    {
        return $this->IsUSD() ? $this->getPrice() : $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_PRICE_USD,
            "0");
    }

    public function getTotalPriceUSD()
    {
        return $this->IsUSD() ? $this->getTotalPrice() : $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_TOTAL_USD,
            "0");
    }

    public function getGuidSkybox()
    {
        return $this->_getApi()->getGuidApi();
    }

    public function getBasePrice()
    {
        return $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_BASE_PRICE, "0");
    }

    public function getBasePriceUSD()
    {
        return $this->IsUSD() ? $this->getBasePrice() : $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_BASE_PRICE_USD,
            "0");
    }

    public function getAdjustPrice()
    {
        return $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_ADJUST_PRICE, "0");
    }

    public function getAdjustPriceUSD()
    {
        return $this->IsUSD() ? $this->getAdjustPrice() : $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_ADJUST_PRICE_USD,
            "0");
    }

    public function getAdjustLabel()
    {
        return $this->getParameter(Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_LABEL_ADJUST, "");
    }

    /*Async Ini*/

    public function getUrl($product, $request, $finalPrice = null, $type = null, $objectId = 1)
    {
        Mage::log("ApiProductCatalog ", null, 'orden.log', true);

        //Mage::log(print_r('Product', true), null, 'tracer.log', true);

        //if ($this->getErrorAuthenticate() && !$this->getLocationAllow()) {
        if (!$this->getLocationAllow()) {
            return $this;
        }

        if (!is_object($product)) {
            $storeId = Mage::app()->getStore()->getStoreId();
            $product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($product);
        }

        $productId = $product->getId();
        $_data = null;

        if ($type == null) {
            $type = $product->getTypeId();
        }

        $defaultSwitch = true;
        switch ($type) {
            case 'simple':
                $finalPrice = isset($finalPrice) ? $finalPrice : $product->getFinalPrice();
                $category_id = $product->getSkyboxCategoryId();
                $category_id = isset($category_id) ? $category_id : $this->getCommodityFromCategory($product);
                $volWeight = !empty(trim($product->getDimensionalWeight())) ? $product->getDimensionalWeight() : 0;

                $_data = array(
                    'object_id' => $product->getId(),
                    'name' => $product->getName(),
                    'sku' => $product->getSku(),
                    'category_id' => $category_id,
                    'final_price' => $finalPrice,
                    'weight' => ($product->getWeight()) ? $product->getWeight() : 1,
                    'image_url' => $product->getImageUrl(),
                    'typeProduct' => $type,
                    'VolWeight' => $volWeight,
                    'merchantproductid' => $productId
                );
//                $this->_calculatePrice($_data);
                break;

            case 'configurable':

                $finalPrice = isset($finalPrice) ? $finalPrice : null;
                $weight = $product->getTypeInstance(true)->getWeight($product);
                $sku = $product->getSku();

                Mage::log("CalculatePrice configurable : " . $finalPrice, null, 'skyboxcheckout.log', false);

                if ($request) {
                    $childProduct = Mage::getModel('catalog/product_type_configurable')
                        ->getProductByAttributes($request->getData('super_attribute'), $product);

                    $productId = $childProduct->getId();
                    $sku = $childProduct->getSku();

                }

                $parentItem = null;

                if (isset($request) && !$finalPrice) {
                    Mage::log("CalculatePrice candidate INI", null, 'skyboxcheckout.log', false);
                    $_finalPrice = 0;

                    $cartCandidates = $product->getTypeInstance(true)
                        ->prepareForCartAdvanced($request, $product, 'full');

                    foreach ($cartCandidates as $candidate) {
                        // Child items can be sticked together only within their parent
                        $stickWithinParent = $candidate->getParentProductId() ? $parentItem : null;
                        $candidate->setStickWithinParent($stickWithinParent);

                        $candidate_getFinalPrice = $candidate->getPriceModel()->getFinalPrice($request->getQty(),
                            $product);
                        Mage::log("CalculatePrice candidate getFinalPrice: " . $candidate_getFinalPrice, null,
                            'skyboxcheckout.log', false);

                        $_finalPrice = $_finalPrice + $candidate_getFinalPrice;
                        if ($_finalPrice) {
                            break;
                        }
                    }
                    $finalPrice = $_finalPrice;
                    Mage::log("CalculatePrice candidate FIN", null, 'skyboxcheckout.log', false);
                }

                $category_id = $product->getSkyboxCategoryId();
                $category_id = isset($category_id) ? $category_id : $this->getCommodityFromCategory($product);

                $volWeight = !empty(trim($product->getDimensionalWeight())) ? $product->getDimensionalWeight() : 0;

                $_data = array(
                    'object_id' => $product->getId(),
                    'name' => $product->getName(),
                    //'sku' => $product->getSku(),
                    'sku' => $sku,
                    'category_id' => $category_id,
                    'final_price' => $finalPrice,
                    'weight' => $weight ? $weight : 1,
                    'image_url' => $product->getImageUrl(),
                    'typeProduct' => $type,
                    'VolWeight' => $volWeight,
                    'merchantproductid' => $productId
                );

//                $this->_calculatePrice($_data);
                break;

            case 'bundle':
                //$_finalPrice = $product->getTotalBundleItemsPrice($product, $request->getQty());
                //$finalPrice = isset($finalPrice) ? $finalPrice : $_finalPrice;

                //Mage::log(print_r($product, true), null, 'cart.log', true);
                //Mage::log("Product: [" . $product->getId() . "] - Class: " . get_class($product), null, 'skyboxcheckout.log', false);

                //Mage::log(print_r($request, true), null, 'cart.log', true);

                $finalPrice = isset($finalPrice) ? $finalPrice : null;
                $weight = $product->getTypeInstance(true)->getWeight($product);
                $sku = $product->getSku();

                //if (isset($request) && !$finalPrice) {
                if (isset($request)) {

                    $parentItem = null;
                    $_finalPrice = null;
                    $_weight = 0;

                    $cartCandidates = $product->getTypeInstance(true)
                        ->prepareForCartAdvanced($request, $product, 'full');

                    foreach ($cartCandidates as $candidate) {
                        // Child items can be sticked together only within their parent
                        $stickWithinParent = $candidate->getParentProductId() ? $parentItem : null;
                        $candidate->setStickWithinParent($stickWithinParent);

                        if (!$_finalPrice) {
                            $_finalPrice = $candidate->getPriceModel()->getFinalPrice($request->getQty(), $product);
                            //break;
                        }
                        //$_weight = $_weight + $candidate->getPriceModel()->getWeight();
                        $_weight = $_weight + $candidate->getWeight();
                    }

                    $finalPrice = isset($finalPrice) ? $finalPrice : $_finalPrice;
                    if ($_weight > 0) {
                        $weight = $_weight;
                    }
                }

                //Mage::log("weight: " . $weight, null, 'cart.log', true);
                //Mage::log("sku: " . $sku, null, 'cart.log', true);

                $volWeight = !empty(trim($product->getDimensionalWeight())) ? $product->getDimensionalWeight() : 0;
                $_data = array(
                    'object_id' => $product->getId(),
                    'name' => $product->getName(),
                    'sku' => $sku,
                    'category_id' => $product->getSkyboxCategoryId(),
                    'final_price' => $finalPrice,
                    'weight' => ($weight) ? $weight : 1,
                    'image_url' => $product->getImageUrl(),
                    'typeProduct' => $type,
                    'VolWeight' => $volWeight,
                    'merchantproductid' => $productId
                );
//                $this->_calculatePrice($_data);
                break;

            case 'bundle_fixed':
                $finalPrice = isset($finalPrice) ? $finalPrice : null;
                $skyboxCategoryId = $product->getSkyboxCategoryId();
                $weight = $product->getTypeInstance(true)->getWeight($product);
                $sku = $product->getSku();

                $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
                    $product->getTypeInstance(true)->getOptionsIds($product), $product
                );

                $_finalPrice = null;
                $_weight = 0;
                $_skyboxCategoryId = null;

                foreach ($selectionCollection as $option) {
                    //if ($option->getData('is_default') == 0) continue;

                    $product_simple = Mage::getModel('catalog/product')
                        ->load($option->getId());

                    $_finalPrice = $_finalPrice + $product_simple->getFinalPrice();
                    $_weight = $_weight + $product_simple->getWeight();

                    if (!$_skyboxCategoryId) { // Set SkyboxCategory from the first Simple Product
                        $_skyboxCategoryId = $product_simple->getSkyboxCategoryId();
                    }
                }

                $finalPrice = isset($finalPrice) ? $finalPrice : $_finalPrice;

                if (!$weight) {
                    $weight = $_weight;
                }

                if (!$skyboxCategoryId) {
                    $skyboxCategoryId = $_skyboxCategoryId;
                }

                /*Mage::log("-------------------------", null, 'cart.log', true);
                Mage::log("Product: " . $product->getName(), null, 'cart.log', true);
                Mage::log("Price: " . $finalPrice, null, 'cart.log', true);
                Mage::log("weight: " . $weight, null, 'cart.log', true);
                Mage::log("sku: " . $sku, null, 'cart.log', true);*/

                $volWeight = !empty(trim($product->getDimensionalWeight())) ? $product->getDimensionalWeight() : 0;
                $_data = array(
                    'object_id' => $product->getId(),
                    'name' => $product->getName(),
                    'sku' => $sku,
                    'category_id' => $skyboxCategoryId,
                    'final_price' => $finalPrice,
                    'weight' => ($weight) ? $weight : 1,
                    'image_url' => $product->getImageUrl(),
                    'typeProduct' => $type,
                    'VolWeight' => $volWeight,
                    'merchantproductid' => $productId
                );

//                $this->_calculatePrice($_data);
                break;

            default:
                Mage::log("CalculatePrice:: Product Type (" . $type . ") is invalid or not supported at SkyboxCheckout CalculatePrice",
                    null, 'skyboxcheckout.log', false);
                trigger_error("CalculatePrice:: Product Type (" . $type . ") is invalid or not supported at SkyboxCheckout CalculatePrice");
                $defaultSwitch = true;
                break;
        }

        if (!$defaultSwitch) {
            //Mage::log(print_r('EnableTax: ', true), null, 'tracer.log', true);
            //Mage::log(print_r($this->getEnabledAddSumTaxToPrice(), true), null, 'tracer.log', true);
            if ($this->getEnabledAddSumTaxToPrice()) {
                $store = Mage::app()->getStore('default');
                $taxCalculation = Mage::getModel('tax/calculation');
                $request = $taxCalculation->getRateRequest(null, null, null, $store);
//            $product = Mage::registry('current_product');
                $taxClassId = $product->getTaxClassId();
                $percentTax = $taxCalculation->getRate($request->setProductClassId($taxClassId));

                $taxPrice = $_data['final_price'] * ($percentTax / 100);

                //Mage::log(print_r('Id product: ' . $product->getId(), true), null, 'tracer.log', true);
                //Mage::log(print_r('$taxPrice: ' . $taxPrice, true), null, 'tracer.log', true);
                //Mage::log(print_r('price: ' . $_data['final_price'], true), null, 'tracer.log', true);
                //Mage::log(print_r('$taxPrice+price: ' . ($taxPrice + $_data['final_price']), true), null, 'tracer.log', true);
                $comissionSkybox = $_data['final_price'] * (10 / 100);
                //Mage::log(print_r('comision skybox: ' . ($comissionSkybox), true), null, 'tracer.log', true);
                //Mage::log(print_r('price+comision skybox: ' . ($_data['final_price'] + $comissionSkybox), true), null, 'tracer.log', true);
                //Mage::log(print_r('$taxPrice+price+comision: ' . ($taxPrice + $_data['final_price'] + $comissionSkybox), true), null, 'tracer.log', true);
                //Mage::log(print_r('comision true: ' . (($taxPrice + $_data['final_price']) * (10 / 100)), true), null, 'tracer.log', true);
                $priceReally = ($taxPrice + $_data['final_price']) + ($taxPrice + $_data['final_price']) * (10 / 100);
                //Mage::log(print_r('$taxPrice+price+comision true: ' . ($priceReally), true), null, 'tracer.log', true);
                $_data['final_price'] = $_data['final_price'] + $taxPrice;
            }

            //Mage::log(print_r('####################### Data produc params #######################', true), null, 'tracer.log', true);
            //Mage::log(print_r($_data, true), null, 'tracer.log', true);
            try {
                $this->_calculatePrice($_data);
            } catch (\Exception $e) {
                Mage::log($e->getMessage(), null, 'skyboxcheckout.log', false);
            }

        }

        Mage::log("Product Class: " . get_class($product), null, 'skyboxcheckout.log', false);
        Mage::log("Product Type: " . $type, null, 'skyboxcheckout.log', false);
        Mage::log(print_r($_data, true), null, 'skyboxcheckout.log', false);

        $data = $this->_callServiceSky($_data);
        /*var_dump($this); exit;
        $this->_product_data = $_data;
        $this->_product_id = $productId;
        $apiData = $this->_api;
        var_dump($apiData); exit;*/
        return $data;
    }

    /**
     * Return the Commodity from Category or Root
     *
     * @param $product
     * @return int
     */
    public function getCommodityFromCategory($product)
    {
        $result = 0;

        try {
            $categoryIds = $product->getCategoryIds();

            if (count($categoryIds)) {
                $categoryId = $categoryIds[0];
                $category = Mage::getModel('catalog/category')->load($categoryId);
                $result = $category->getSkyboxCategoryIdSelect();
            }

            if (empty($result)) {
                $categoryId = Mage::app()->getStore()->getRootCategoryId();
                $category = Mage::getModel('catalog/category')->load($categoryId);
                $result = $category->getSkyboxCategoryIdSelect();
            }

            $result = isset($result) ? $result : 0;

        } catch (\Exception $e) {
            \error_log("catch exception product_: " . $product . PHP_EOL, 3, BP . '/var/log/magento19.trace.log');
        }

        return $result;
    }

    private function _callServiceSky($data)
    {
        $msg = sprintf("CalculatePrice: [%s] - [%s] - [%s] - [Category_id: %s] - [Price: %s] - [weight: %s] - [typeProduct: %s]",
            $data['object_id'],
            $data['sku'],
            $data['name'],
            $data['category_id'],
            $data['final_price'],
            $data['weight'],
            $data['typeProduct']
        );

        $params = array(
            'htmlobjectid' => $data['object_id'],
            'code' => $data['sku'],
            'name' => $data['name'],
            'category' => $data['category_id'],
            'price' => $data['final_price'],
            //'storeproductprice' => $product->getFinalPrice(),
            'weight' => ($data['weight']) ? $data['weight'] : 1,
            'weightunit' => $this->getWeightUnit(),
            'imgurl' => $data['image_url'],
            /*'volweight' => $data['VolWeight'],
            'merchantproductid' => $data['merchantproductid']*/
        );
        //var_dump($params); exit;
        return $params;
    }

    /*Async End*/
}
