<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_Block_Sales_Order_Total extends Mage_Sales_Block_Order_Totals
{

	public function initTotals() {

       parent::_initTotals();
       $order= $this->getOrder();

        //Mage::log('$order', null, 'tracer.log', true);
        //Mage::log(print_r($order->debug(), true), null, 'tracer.log', true);

       $ConceptsSkyboxjson=json_decode($order->getConceptsSkybox());

        //Mage::log('$ConceptsSkyboxjson', null, 'tracer.log', true);
//        Mage::log(print_r($ConceptsSkyboxjson, true), null, 'tracer.log', true);
        if(count($ConceptsSkyboxjson)>0){

            $i = 0;
            foreach ($ConceptsSkyboxjson as $item) {
                if($item->Visible != 0)
                {
                    $i+=1;
                    $this->getParentBlock()->addTotal(new Varien_Object(array(
                        'code'=> 'checkout_total'.$i,
                        'value'=> $item->ValueUSD,//$item->Value
                        'base_value'=> $item->ValueUSD,//$item->Value
                        'label'=> $item->Concept,
                    )), 'subtotal', 'tax');
                }
            }
            if($order->getShippingAmount() == 0){
                $this->getParentBlock()->removeTotal('shipping');
            }
        }
  	}
}
