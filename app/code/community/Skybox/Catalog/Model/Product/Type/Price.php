<?php
/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Catalog
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

/**
 *
 * Skybox overriding product type price
 *
 * @author      César Tapia M. <ctapia@skyworldint.com>
 */
class Skybox_Catalog_Model_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price
{
	protected $_typeApi = 'skyboxcatalog/api_product';
	protected $_api = null;

	protected function _getApi()
	{
		if(null === $this->_api)
		{
			$this->_api = Mage::getModel($this->_typeApi);
		}

		return $this->_api;
	}

	/**
	* Recupera el precio final del producto
	*
	* @param float|null $qty
    * @param Mage_Catalog_Model_Product $product
	* @return float
	*/
	public function getFinalPrice($qty = null, $product)
	{
		/*if(!$this->_getApi()->getErrorAuthenticate() && $this->_getApi()->getLocationAllow())
		{
			if(is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
				return $product->getCalculatedFinalPrice();
			}

			$finalPrice= $this->getBasePrice($product, $qty);
			$product->setFinalPrice($finalPrice);

			Mage::dispatchEvent('catalog_product_get_final_price', array('product'=> $product, 'qty'=> $qty));

			$finalPrice = $product->getData('final_price');
			$finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
			$finalPrice = max(0, $finalPrice);
			//Call Calculate
			$this->_getApi()->CalculatePrice($product->getId(), $finalPrice);
			//Set final price for product
			$finalPrice = $this->_getApi()->getResponse()->TotalProduct;
			$product->setFinalPrice($finalPrice);

		  	return $finalPrice;
		}*/
		return parent::getFinalPrice($qty, $product);
	}
}
