<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2014 - 2017 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_Model_Quote_Address_Total_Rmt extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
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

        //Verificar esta parte porque se podria raelizar desde el Total


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
        parent::fetch($address);

        if (!$this->isEnable()) {
            return $this;
        }

        $quote = $address->getQuote();
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }

        $skyboxRMT = $address->getRmtSkybox();

        if ($skyboxRMT) {
            $RmtSkyboxjson = json_decode($skyboxRMT);

            Mage::log("quote->address->rmt->fetch : ini", null, 'TotalSales.log', true);

            $i = 0;
            foreach ($RmtSkyboxjson as $item) {
                $i += 1;
                $value = $item->Value;
                $value = str_replace(',', '', $value);
                // $value = abs($value);

                $address->addTotal(array(
                    'code' => 'checkout_total_rmt' . $i,
                    'title' => $item->Concept,
                    'value' => $value
                ));
                Mage::log("quote->address->rmt->fetch(Concepts)->" . $item->Concept . "=" . $item->Value, null,
                    'TotalSales.log', true);
            }

            Mage::log("quote->address->rmt->fetch : fini", null, 'TotalSales.log', true);

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
