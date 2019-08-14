<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_CalculateController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $isAjax = Mage::app()->getRequest()->isAjax();

        if ($isAjax) {

            $productId = ($this->getRequest()->getParam('product_id')) ?
                $this->getRequest()->getParam('product_id') : null;

            $price = ($this->getRequest()->getParam('price')) ?
                $this->getRequest()->getParam('price') : null;

            $_request = ($this->getRequest()->getParam('request')) ?
                $this->getRequest()->getParam('request') : null;

            if (!isset($productId) || !isset($price)) {
                //trigger_error('Invalid Product or Price.');
                $this->_error('Invalid Product or Price.');
            }

            if ($_request) {
                parse_str($_request, $request);
                $request = $this->_array_filter_recursive($request);
                $request = $this->_getProductRequest($request);
                //Mage::log(print_r($request, true), null, 'cart.log', true);
            }

            /** @var Skybox_Catalog_Model_Api_Product $productAPI */
            $productAPI = Mage::getModel('skyboxcatalog/api_product');

            /* $product Mage_Catalog_Model_Product */
            $product = Mage::getModel('catalog/product')->load($productId);
            $type = $product->getTypeId();
            $template = null;
            //Mage::log(print_r('calculateController: '.$type, true), null, 'tracer.log', true);
            switch ($type) {
                case 'simple':
                    $template = $productAPI->CalculatePrice($product->getId(), null, $product->getFinalPrice(), 'simple')
                        ->GetTemplateProduct();
                    break;
                case 'configurable':
                    $template = $productAPI->CalculatePrice($product->getId(), null, $price, $product->getTypeId())
                        ->GetTemplateProduct();
                    break;
                case 'bundle':
                    try {
                        $template = $productAPI->CalculatePrice($product->getId(), $request, $price, 'bundle')
                            ->GetTemplateProduct();
                    } catch (Exception $e) {
                        Mage::logException($e);
                        $template = '';
                    }
                    break;
                default;
                    $this->_error('Invalid or not supported Product Type.');
                    break;
            }

            $extraHtml = '<div style="font-weight:bold" id="skybox-configurable-price-from-'
                . $product->getId()
                //. $this->getIdSuffix()
                . ''
                . '">'
                . $template
                . '</p>'
                . '<div style="clear:both"></div>';

            $this->getResponse()->setBody($extraHtml);
        }
    }

    private function _error($message = null)
    {
        if ($message == null) {
            $message = "Unexpected error";
        }
        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('error' => $message)));
    }

    /**
     * Get request for product
     *
     * @param   mixed $requestInfo
     * @return  Varien_Object
     */
    private function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof Varien_Object) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new Varien_Object(array('qty' => $requestInfo));
        } else {
            $request = new Varien_Object($requestInfo);
        }

        if (!$request->hasQty()) {
            $request->setQty(1);
        }

        return $request;
    }

    /*
     * Array Filter Recursive (utils)
     */
    private function _array_filter_recursive($input)
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->_array_filter_recursive($value);
            }
        }

        return array_filter($input);
    }
}
