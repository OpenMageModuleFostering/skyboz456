<?php

class Skybox_Checkout_Model_Quote_Total extends Mage_Sales_Model_Quote_Address_Total_Abstract{
 
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
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        $activation = Mage::getModel('skyboxcore/api_restful')->isModuleEnable();
        if(!$activation) {
            return $this;
        }
        if($this->_getApi()->getLocationAllow()){ // Rogged

        
            parent::collect($address);  //Comentado por verificar
     
            // Si no hay items, no hay nada que hacer
            $items = $this->_getAddressItems($address);
            if (!count($items)) {
                return $this;
            }

            $quote= $address->getQuote();

//            $quote = Mage::helper('checkout/cart')->getCart()->getQuote();
            $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
            if($shippingMethod){
                Mage::log('set shipping method null',null,'tracer.log',true);
                $quote->getShippingAddress()->setShippingMethod(null);  //setting method to null
                $quote->save();
            }

            if(!$quote->isVirtual() && $address->getAddressType() == 'billing'){
                return $this;
            }

            //$quote->setCustomerTaxClassId(0);

            //Limpiamos los Valores de Impuestos
            /*$rates = array();
            $address->setAppliedTaxes($rates);*/

            /*--------Nuevos conceptos---------*/
            $totals=$this->_getApi()->GetTotalShoppingCart();

            if ($this->_getApi()->HasError()) {
                Mage::log("StatusCode: Error", null, 'TotalSales.log', true);
                 return $this;
            }

            Mage::log("quote->address->collect : ini", null, 'TotalSales.log', true);        

            //$StatusCode =  $totals->getParameter('StatusCode');
            //$StatusCode =  $totals->getParameter('StatusCode');//Comentado por estar repetido

            $Customs =  $totals->getParameter('Customs');
            $CustomsUSD =  $totals->getParameter('CustomsUSD');

            $Shipping =  $totals->getParameter('Shipping');
            $ShippingUSD =  $totals->getParameter('ShippingUSD');

            $Insurance =  $totals->getParameter('Insurance');
            $InsuranceUSD =  $totals->getParameter('InsuranceUSD');

            $Taxes =  $totals->getParameter('Taxes');
            $TaxesUSD =  $totals->getParameter('TaxesUSD');

            $Duties =  $totals->getParameter('Duties');
            $DutiesUSD =  $totals->getParameter('DutiesUSD');

            $Handling =  $totals->getParameter('Handling');
            $HandlingUSD =  $totals->getParameter('HandlingUSD');

            $Clearence =  $totals->getParameter('Clearence');
            $ClearenceUSD =  $totals->getParameter('ClearenceUSD');

            $Others =  $totals->getParameter('Others');
            $OthersUSD =  $totals->getParameter('OthersUSD');

            $Adjustment =  $totals->getParameter('Adjustment');
            $AdjustmentUSD =  $totals->getParameter('AdjustmentUSD');

            $listAdjustjson =  $totals->getResponse()->{'ListDetailConcepts'};

            $listRmtjson =  $totals->getResponse()->{'ListDetailAdjusts'};

            /*--------------------------------*/
            // Apuntamos lo que hemos calculado para usarlo luego
            // Idealmente esto debería ir a parar a la base de datos a un campo 
            // creado a los efectos 
            $address->setCustomsTotalSkybox($Customs);
            $address->setCustomsTotalUsdSkybox($CustomsUSD);

            $address->setShippingTotalSkybox($Shipping);
            $address->setShippingTotalUsdSkybox($ShippingUSD);

            $address->setInsuranceTotalSkybox($Insurance);
            $address->setInsuranceTotalUsdSkybox($InsuranceUSD);

            $address->setTaxesTotalSkybox($Taxes);
            $address->setTaxesTotalUsdSkybox($TaxesUSD);

            $address->setDutiesTotalSkybox($Duties);
            $address->setDutiesTotalUsdSkybox($DutiesUSD);

            $address->setHandlingTotalSkybox($Handling);
            $address->setHandlingTotalUsdSkybox($HandlingUSD);
            
            $address->setClearenceTotalSkybox($Clearence);
            $address->setClearenceTotalUsdSkybox($ClearenceUSD);

            $address->setOthersTotalSkybox($Others);
            $address->setOthersTotalUsdSkybox($OthersUSD);

            $address->setAdjustTotalSkybox($Adjustment);
            $address->setAdjustTotalUsdSkybox($AdjustmentUSD);

            $address->setConceptsSkybox(json_encode($listAdjustjson));

            $address->setRmtSkybox(json_encode($listRmtjson));

            $totalskybox=0;
            //$totalskybox=$Taxes+$Handling+$Shipping+$Insurance+$Clearence+$Duties+$Others-$Adjustment;
            $subtotalskybox=$totals->getResponse()->{'StoreProductPrice'};
            $totalskybox=$totals->getResponse()->{'TotalProduct'};
            //$totalskyboxbase=$TaxesUSD+$HandlingUSD+$ShippingUSD+$InsuranceUSD+$ClearenceUSD+$DutiesUSD+$OthersUSD-$AdjustmentUSD;
            $subtotalskyboxbase=$totals->getResponse()->{'StoreProductPriceUSD'};
            $totalskyboxbase=$totals->getResponse()->{'TotalProductUSD'};
            /*$address->setBasePaypalfeeAmount($this->_amount);*/   

            // Actualizamos el total de la quote
            //$address->setGrandTotal($address->getGrandTotal() +$totalskybox);
            //$address->setBaseGrandTotal($address->getBaseGrandTotal() + $totalskyboxbase);

            $address->setSubTotal(100);
            $address->setBaseSubTotal(100);
            $address->setGrandTotal($totalskybox);
            $address->setBaseGrandTotal($totalskyboxbase);

            //$address->setGrandTotalSkybox($address->getGrandTotal() + $totalskybox);
            //$address->setBaseGrandTotalSkybox($address->getBaseGrandTotal() + $totalskyboxbase);        

            $address->setSubtotalSkybox($subtotalskybox);
            $address->setBaseSubtotalSkybox($totalskyboxbase);
            $address->setGrandTotalSkybox($totalskybox);
            $address->setBaseGrandTotalSkybox($totalskyboxbase);

            //$address->setTaxAmount(0);

            //$address->save(); //Guardamos los Cambios
            Mage::log("quote->address->collect->getSubTotal->" . $address->getSubTotal(), null, 'TotalSales.log', true); 
            Mage::log("quote->address->collect->getGrandTotal->" . $address->getGrandTotal(), null, 'TotalSales.log', true); 

            Mage::log("quote->address->collect->Total->" . $totalskybox, null, 'TotalSales.log', true); 
            Mage::log("quote->address->collect : fin", null, 'TotalSales.log', true); 

            return $this;
        }
    }
 
    /**
     * Esta función es llamada por Magento cuando quiere mostrar los totales en pantalla.
     * 
     * El cálculo ya se habrá hecho y deberíamos guardarlo en algún sitio para aquí,
     * simplemente retornar el valor formateado y que Magento lo muestre.
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address) {
        $activation = Mage::getModel('skyboxcore/api_restful')->isModuleEnable();
        if(!$activation) {
            return $this;
        }
        if($this->_getApi()->getLocationAllow()){ // Rogged
            parent::fetch($address); //Comentado por verificar

            $quote= $address->getQuote();
            if(!$quote->isVirtual() && $address->getAddressType() == 'billing'){
                return $this;
            }
            
            $ConceptsSkyboxjson = json_decode($address->getConceptsSkybox());

            Mage::log("quote->address->fetch : ini", null, 'TotalSales.log', true);
            Mage::log("quote->address->fetch->getSubTotal->" . $address->getSubTotal(), null, 'TotalSales.log', true); 
            Mage::log("quote->address->fetch->getGrandTotal->" . $address->getGrandTotal(), null, 'TotalSales.log', true); 

            /*
             * Reescribimos el monto del SubTotal a Mostrar
             */
            $address->addTotal(array(
                 'code'=> 'subtotal',
                 'title'=>Mage::helper('sales')->__('Subtotal'),
                 'value'=> $address->getSubtotalSkybox()
            ));

            /*
             * Reescribimos el monto de los conceptos a Mostrar
             */
            $i=0;        
            foreach ($ConceptsSkyboxjson as $item) {
                if($item->Visible != 0)
                {
                    $i+=1;
                    $address->addTotal(array(
                            'code'  => 'checkout_total'.$i,
                            'title' => $item->Concept,
                            'value' => $item->Value
                        ));
                    Mage::log("quote->address->fetch(Concepts)->" . $item->Concept . "=" . $item->Value, null, 'TotalSales.log', true);
                }
            }
            Mage::log("quote->address->fetch : fin", null, 'TotalSales.log', true);        
            // Retornamos el total con su título
            return $this;
        }
    }
}

