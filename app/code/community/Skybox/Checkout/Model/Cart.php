<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_Model_Cart extends Mage_Checkout_Model_Cart
{
    protected $_api = null;
    protected $_product = null;

    /* @return Skybox_Checkout_Model_Api_Checkout */
    protected function _getApi()
    {
        if (null === $this->_api) {
            $this->_api = Mage::getModel('skyboxcheckout/api_checkout');
        }
        return $this->_api;
    }

    /**
     * Return Skybox API Product
     *
     * @return  Skybox_Catalog_Model_Api_Product
     */
    protected function _getProductApi()
    {
        if (null === $this->_product) {
            $this->_product = Mage::getModel('skyboxcatalog/api_product');
        }
        return $this->_product;
    }

    /*
     * Grouped Product
     */
    public function addGroupedProduct($productInfo, $requestInfo = null)
    {
        $product = $this->_getProduct($productInfo);
        $request = $this->_getProductRequest($requestInfo);

        $quote = parent::addProduct($productInfo, $requestInfo);

        $super_group = $request->getData('super_group');
        //Mage::log(print_r($super_group, true), null, 'cart.log', true);

        foreach ($super_group as $item => $qty) {
            if ($qty <= 0) continue;

            //Mage::log("Product: " . $item . " - " . $qty, null, 'cart.log', true);

            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($item);

            $productId = $product->getId();

            if ($product->getStockItem()) {
                $minimumQty = $product->getStockItem()->getMinSaleQty();
                //If product was not found in cart and there is set minimal qty for it
                if ($minimumQty && $minimumQty > 0 && $qty < $minimumQty
                    && !$this->getQuote()->hasProductId($productId)
                ) {
                    $request->setQty($minimumQty);
                }
            }

            $this->_getProductApi()->CalculatePrice($product, null);

            if ($this->_getProductApi()->HasError()) {
                Mage::throwException(
                    Mage::helper('sales')->__('[sales] Failed to add the product to the cart.')
                );
                return $this;
            }

            $productId = $this->_getProductApi()->getProductId();

            $_data = $this->_getProductApi()->getProductData();
            $this->_getApi()->AddProductOfCart($_data, $qty);

            if ($this->_getApi()->HasError()) {
                Mage::throwException(Mage::helper('checkout')->__('[checkout] Failed to add the product to the cart.'));
                return $this;
            }

            $productIdSkybox = $this->_getApi()->getParameter(
                Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_ID, "0");

            $this->_updateQuoteItem($productId, $productIdSkybox);
        }

        return $quote;
    }

    /*
     * Bundle Product
     */
    public function addBundleProduct($productInfo, $requestInfo = null)
    {
        $product = $this->_getProduct($productInfo);
        $request = $this->_getProductRequest($requestInfo);

        //Mage::log(print_r($request, true), null, 'cart.log', true);

        $quote = parent::addProduct($productInfo, $requestInfo);

        // SkyBox Checkout
        $this->_getProductApi()->CalculatePrice($product, $request);

        if ($this->_getProductApi()->HasError()) {
            Mage::throwException(
                Mage::helper('sales')->__('[sales] Failed to add the product to the cart.')
            );
            return $this;
        }

        //$productId = $product->getId();
        $productId = $this->_getProductApi()->getProductId();

        Mage::dispatchEvent('checkout_cart_product_add_before', array(
            'product' => $product,
            'request' => $request
        ));

        $_data = $this->_getProductApi()->getProductData();

        Mage::log("Bundleproduct=Cart=".print_r($_data, true), null, 'cart.log', true);

        $this->_getApi()->AddProductOfCart($_data, $request->getQty());

        if ($this->_getApi()->HasError()) {
            Mage::throwException(Mage::helper('checkout')->__('[checkout] Failed to add the product to the cart.'));
            return $this;
        }

        $productIdSkybox = $this->_getApi()->getParameter(
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_ID, "0");

        $this->_updateQuoteItem($productId, $productIdSkybox);
        return $quote;
    }

    /**
     * Add product to shopping cart (quote)
     *
     * @param   int|Mage_Catalog_Model_Product $productInfo
     * @param   mixed $requestInfo
     * @return  Mage_Checkout_Model_Cart
     */
    public function addProduct($productInfo, $requestInfo = null)
    {
        $product = $this->_getProduct($productInfo);
        $request = $this->_getProductRequest($requestInfo);

        //Mage::log(print_r($request, true), null, 'cart.log', true);

        if ($product->isGrouped()) {
            Mage::log("Product: " . $product->getName() . " is Grouped", null, 'cart.log', true);
            return $this->addGroupedProduct($productInfo, $requestInfo);
        }

        if ($product->getTypeId() == 'bundle') {
            Mage::log("Product: " . $product->getName() . " is Bundle", null, 'cart.log', true);
            return $this->addBundleProduct($productInfo, $requestInfo);
        }

        // SkyBox Checkout
        $this->_getProductApi()->CalculatePrice($product, $request);

        if ($this->_getProductApi()->HasError()) {
            Mage::throwException(
                Mage::helper('sales')->__('[sales] Failed to add the product to the cart.')
            );
            return $this;
        }

        //$productId = $product->getId();
        $productId = $this->_getProductApi()->getProductId();

        if ($product->getStockItem()) {
            $minimumQty = $product->getStockItem()->getMinSaleQty();
            //If product was not found in cart and there is set minimal qty for it
            if ($minimumQty && $minimumQty > 0 && $request->getQty() < $minimumQty
                && !$this->getQuote()->hasProductId($productId)
            ) {
                $request->setQty($minimumQty);
            }
        }

        /*$product->setTaxPercent(0);
        $product->setTaxAmount(0);
        $product->setBaseTaxAmount(0);*/

        Mage::dispatchEvent('checkout_cart_product_add_before', array(
            'product' => $product,
            'request' => $request
        ));

        $_data = $this->_getProductApi()->getProductData();
        Mage::log("product=Cart=".print_r($_data, true), null, 'cart.log', true);
        $this->_getApi()->AddProductOfCart($_data, $request->getQty());

        if ($this->_getApi()->HasError()) {
            Mage::throwException(Mage::helper('checkout')->__('[checkout] Failed to add the product to the cart.'));
            return $this;
        }

        $productIdSkybox = $this->_getApi()->getParameter(
            Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_PRODUCT_ID, "0");

        $quote = parent::addProduct($productInfo, $requestInfo);
        $this->_updateQuoteItem($productId, $productIdSkybox);
        return $quote;
    }

    private function _updateQuoteItem($productId, $productIdSkybox)
    {
        Mage::log('pas x aqui2', null, 'minicars.log', true);

        foreach ($this->getQuote()->getAllItems() as $item) {

            if ($item->getProductId() == $productId) {

                if ($item instanceof Mage_Sales_Model_Quote_Item) {
                    $parentItem = $item->getParentItem();
                    if ($parentItem) {
                        $item = $parentItem;
                    }
                }

                $total = str_replace(",", "", $this->_getProductApi()->getTotalPriceUSD());
                $item->setIdProductSkybox($productIdSkybox);

                // Currency amounts in the default currency of that customer
                $item->setCustomsSkybox($this->_getProductApi()->getCustoms());
                $item->setShippingSkybox($this->_getProductApi()->getShipping());
                $item->setInsuranceSkybox($this->_getProductApi()->getInsurance());
                $item->setPriceSkybox($this->_getProductApi()->getPrice());
                $item->setTotalSkybox($this->_getProductApi()->getTotalPrice());
                $item->setRowTotal($total);

                // Currency amounts in the USD Currency
                $item->setCustomsUsdSkybox($this->_getProductApi()->getCustomsUSD());
                $item->setShippingUsdSkybox($this->_getProductApi()->getShippingUSD());
                $item->setInsuranceUsdSkybox($this->_getProductApi()->getInsuranceUSD());
                $item->setPriceUsdSkybox($this->_getProductApi()->getPriceUSD());
                $item->setTotalUsdSkybox($this->_getProductApi()->getTotalPriceUSD());

                $item->setGuidSkybox($this->_getProductApi()->getGuidSkybox()); //Set GUID


                $item->setBasePriceSkybox($this->_getProductApi()->getBasePrice());
                $item->setBasePriceUsdSkybox($this->_getProductApi()->getBasePriceUSD());
                $item->setAdjustTotalSkybox($this->_getProductApi()->getAdjustPrice());
                $item->setAdjustTotalUsdSkybox($this->_getProductApi()->getAdjustPriceUSD());
                $item->setAdjustLabelSkybox($this->_getProductApi()->getAdjustLabel());

                //$item->setOriginalCustomPrice($this->_getProductApi()->getTotalPriceUSD());
                //$item->setOriginalCustomPrice($total);
                $item->setOriginalCustomPrice($this->_getProductApi()->getPrice());
                //$skybox_total = str_replace(",", "", $this->_getProductApi()->getTotalPrice());
                $skybox_total = str_replace(",", "", $this->_getProductApi()->getPrice());
                $row_total = floatval($skybox_total) * $item->getQty();
                $item->setRowTotalSkybox($row_total);

                break;
            }
        }

    }

    /**
     * Remove item from cart
     *
     * @param   int $itemId
     * @return  Mage_Checkout_Model_Cart
     */
    public function removeItem($itemId)
    {
        $quoteItem = $this->getQuote()->getItemById($itemId);

        if (!$quoteItem) {
            Mage::throwException(Mage::helper('checkout')->__('ID incorrecto de producto.'));
            return $this;
        }
        Mage::log("|removeItem|" . $quoteItem->getIdProductSkybox());
        //Realizar llamada al servicio de agregar producto al carrito.
        $idProductSkybox = $quoteItem->getIdProductSkybox();
        $this->_getApi()->DeleteProductOfCart($idProductSkybox);

        if ($this->_getApi()->HasError()) {
            Mage::throwException(Mage::helper('checkout')->__('Error al eliminar producto al carrito.'));
            return $this;
        }

        return parent::removeItem($itemId);
    }

    /**
     * Update cart items information
     *
     * @param   array $data
     * @return  Mage_Checkout_Model_Cart
     */
    public function updateItems($data)
    {
        Mage::log('pas x aqui5', null, 'minicars.log', true);
        //Mage::log("Skybox_Checkout_Checkout_Model_Cart updateItem");
        //return parent::updateItems($data);
        // Mage::dispatchEvent('checkout_cart_update_items_before', array('cart'=>$this, 'info'=>$data));

        foreach ($data as $itemId => $itemInfo) {
            $item = $this->getQuote()->getItemById($itemId);
            if (!$item) {
                continue;
            }

            //        if(isset($itemInfo['price']) && !empty($itemInfo['price'])) {
            //            //price, base_price, custom_price, row_total, base_row_total, row_total_with_discount, original_custom_price
            //            $price = (float) $itemInfo['price'];
            //            $item->setPrice($price);
            //            $item->setRowTotal($price);
            //            $item->setBasePrice($price);
            //            continue;
            //        }

            if (!empty($itemInfo['remove']) || (isset($itemInfo['qty']) && $itemInfo['qty'] == '0')) {
                $this->removeItem($itemId);
                continue;
            }

            $qty = isset($itemInfo['qty']) ? (float)$itemInfo['qty'] : false;
            if ($qty > 0) {

                $idProductSkybox = $item->getIdProductSkybox();

                $this->_getApi()->UpdateProductOfCart($idProductSkybox, $qty);

                $item->setQty($qty);

                //$skybox_total = str_replace(",", "", $item->getTotalSkybox());
                $skybox_total = str_replace(",", "", $item->getPriceSkybox());


                $row_total = floatval($skybox_total) * $item->getQty();
                $item->setRowTotalSkybox($row_total);
            }
        }

        //Mage::dispatchEvent('checkout_cart_update_items_after', array('cart'=>$this, 'info'=>$data));
        $current = parent::updateItems($data);
        return $current;
    }

    /**
     * Mark all quote items as deleted (empty shopping cart)
     *
     * @return Mage_Checkout_Model_Cart
     */
    public function truncate()
    {
        foreach ($this->getQuote()->getAllItems() as $item) {
            //Mage::log("truncate() ". $item->getId(), null, 'api.log', true);
            $this->removeItem($item->getId());
        }

        parent::truncate();
        return $this;
    }

}