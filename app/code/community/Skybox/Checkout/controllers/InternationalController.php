<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_InternationalController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        /** @var $checkout_cart Mage_Checkout_Model_Session */
        $checkout_cart = Mage::getSingleton('checkout/cart');
        $items = $checkout_cart->getItems();

        if (!$items) {
            $this->getResponse()->setRedirect(
                Mage::getUrl('checkout/cart')
            );
        }

        $cart_count = Mage::helper('checkout/cart')->getSummaryCount();

        /** @var $config Skybox_Checkout_Model_Config */
        $config = Mage::getModel('skyboxcore/config');
        $cart = $config->getSession()->getCartSkybox();

        $cartItemCount = 0;
        if (!empty($cart)) {
            $cartItemCount = intval($cart->{'CartItemCount'});
        }

        /** @var $api_mproduct Skybox_Catalog_Model_Api_Product */
        $api_mproduct = Mage::getModel('skyboxcatalog/api_product');

        if ($cart_count != $cartItemCount && $api_mproduct->getLocationAllow()) {

            $processUrl = Mage::getUrl() . 'skbcheckout/process';
            $returnUrl = Mage::getUrl() . 'skbcheckout/international';

            $url = $processUrl . '?return_url=' . $returnUrl;
            $this->getResponse()->setRedirect($url);
        }

        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        //$this->getLayout()->getBlock('root')->setTemplate('skybox/checkout/pagecheckout.phtml');
        $this->renderLayout();
    }

    public function successAction()
    {
        // Mage::log("Call true: successAction limpiar carro", null, 'local.log', true);
        // Mage::getSingleton('checkout/cart')->truncate()->save();
        Mage::getSingleton('checkout/cart')->truncateMgCart()->save();

        $_config = Mage::getModel('skyboxcore/config');
        $cart = $_config->getSession()->getCartSkybox();
        $id = $cart->{'LanguageId'};

        $message_success = $this->languages($id);

        Mage::getSingleton('core/session')->setSkyboxSuccessMsg($message_success);

        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->renderLayout();
    }

    private function languages($id)
    {
        $languages = array(
            0 => "Thanks for your purchase, We have received your order and we will contact you. We will send you a confirmation email.",
            1 => "Thanks for your purchase, We have received your order and we will contact you. We will send you a confirmation email.",
            // 2 => "&#33;Gracias por su compra! Hemos recibido su orden y nos pondremos en contacto contigo. Le enviaremos un correo electrónico de confirmaci&oacute;n",
            2 => "Gracias por su compra! Le enviaremos un correo electrónico de confirmaci&oacute;n.",
            3 => "'Obrigado por sua compra, Recebemos seu pedido e entraremos em contato com você. Nós lhe enviaremos um e-mail de confirma&ccedil;&atilde;o.",
        );
        if (isset($languages[$id])) {
            return $languages[$id];
        } else {
            return $languages[0];
        }
    }

}
