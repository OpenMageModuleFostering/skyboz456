<?php

class Skybox_Checkout_Model_Quote_SubTotal extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    // Cantidad del recargo sin impuestos
    var $_amount;

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
     * @return  Skybox_Catalogo_Model_Api_Product
     */
    protected function _getProductApi()
    {
        if (null === $this->_product) {
            $this->_product = Mage::getModel('skyboxcatalog/api_product');
        }
        return $this->_product;
    }

    /**
     * Esta función es llamada cada vez que Magento requiere calcular los
     * totales por cualquier motivo: carrito actualizado, usuario se logea,
     * aplicar un cupón, selección de medios de envío, de pago, etc.
     *
     * Se trata de calcular lo que queremos añadir de recargo y actualizar
     * el total del carrito (Magento itera sobre los totales y cada uno añade
     * su parte)
     **/
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {

        parent::collect($address);  //Comentado por verificar

        // Si no hay items, no hay nada que hacer
        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

        $quote = $address->getQuote();
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }


        //$address->setSubTotal(100);
        //$address->setBaseSubTotal(100);

        $address->setRowtotalResult(100);

        //$address->setTaxAmount(0);

        //$address->save(); //Guardamos los Cambios
        Mage::log("quote->address->collect->getSubTotal->" . $address->getSubTotal(), null, 'SubTotalSales.log', true);
        Mage::log("quote->address->collect->getGrandTotal->" . $address->getGrandTotal(), null, 'SubTotalSales.log',
            true);

        Mage::log("quote->address->collect->Total->" . $totalskybox, null, 'SubTotalSales.log', true);
        Mage::log("quote->address->collect : fin", null, 'SubTotalSales.log', true);

        return $this;
    }

    /**
     * Esta función es llamada por Magento cuando quiere mostrar los totales en pantalla.
     *
     * El cálculo ya se habrá hecho y deberíamos guardarlo en algún sitio para aquí,
     * simplemente retornar el valor formateado y que Magento lo muestre.
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        parent::fetch($address); //Comentado por verificar

        if (!$this->isEnable()) {
            return $this;
        }

        $quote = $address->getQuote();
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }

        $_conceptsSkybox = $address->getConceptsSkybox();

        if ($_conceptsSkybox) {

            $ConceptsSkyboxjson = json_decode($address->getConceptsSkybox());

            $address->addTotal(array(
                'code' => 'subtotal',
                'title' => 'Sub Total',
                'value' => 150
            ));
            Mage::log("quote->address->fetch : fin", null, 'SubTotalSales.log', true);
        }

        return $this;
    }

    private function isEnable()
    {
        /** @var Skybox_Core_Model_Api_Restful $api_restful */
        $api_restful = Mage::getModel('skyboxcore/api_restful');
        $activation = $api_restful->isModuleEnable();

        if (!$activation) {
            return false;
        }

        /** @var Skybox_Core_Helper_Allow $allowHelper */
        $allowHelper = Mage::helper('skyboxcore/allow');

        $locationAllow = $allowHelper->getLocationAllow();

        if (!$locationAllow) {
            return false;
        }

        return true;
    }
}

