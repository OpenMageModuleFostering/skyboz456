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
       $ConceptsSkyboxjson=json_decode($order->getConceptsSkybox());

       foreach ($ConceptsSkyboxjson as $item) {
          if($item->Value > 0)
          {
            $i+=1;
            $this->getParentBlock()->addTotal(new Varien_Object(array(
              'code'=> 'checkout_total'.$i,
              'value'=> $item->Value,
              'base_value'=> $item->Value,
              'label'=> $item->Concept,
            )), 'subtotal', 'tax');
          }
            
        }
  	}
}
