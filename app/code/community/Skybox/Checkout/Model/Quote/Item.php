<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2017 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_Model_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    /**
     * Declare quote item quantity
     *
     * @param float $qty
     * @return Mage_Sales_Model_Quote_Item
     */
    public function setQty($qty)
    {
        $oldQty = $this->_getData('qty');

        parent::setQty($qty);
        $qty = $this->getQty();

        if ($oldQty != $qty) {
            $skyboxProductId = $this->getIdProductSkybox();

            if ($skyboxProductId) {
                $api_checkout = Mage::getModel('skyboxcheckout/api_checkout');
                $api_checkout->UpdateProductOfCart($skyboxProductId, $qty);
            }
        }
        return $this;
    }

}
