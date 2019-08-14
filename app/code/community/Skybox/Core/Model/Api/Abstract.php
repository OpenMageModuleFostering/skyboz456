<?php
/**
 * Skybox Checkout
 *
 * @category    Mage
 * @package     Skybox_Core
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

/**
 * Abstract class para la Api Skybox
 * @author      César Tapia M. <ctapia@skyworldint.com>
 */
class Skybox_Core_Model_Api_Abstract extends Varien_Object
{
	/**
     * Config instance
     * @var Skybox_Core_Model_Config
     */
    protected $_config = null;

    /**
    * Respuesta de api
    * @var string
    */
    protected $_response = null;

    /**
    * Solicitud de api
    * @var string
    */
    protected $_request = null;

    /**
     * Código de metodo actual
     * @var string
     */
    protected $_methodCode = null;

    /**
     * Código de metodo actual
     * @var array()
     */
    protected $_params = null;

    /*
     * @var string
     */
    protected $_status_code = null;

    /**
     * Seteamos la instancia Config
     * @param Skybox_Core_Model_Config $config
     * @return Skybox_Core_Model_Api_Abstract
     */
    public function setConfigObject(Skybox_Core_Model_Config $config)
    {
        $this->_config = $config;
        return $this;
    }

    public function setStatusCode($value)
    {
        $this->_status_code = $value;
    }

    public function getStatusCode()
    {
        //Mage::log("Error:" . $this->gerResponseJson()->{Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_STATUS});
        //return $this->getResponseJson()->{Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_STATUS};
        return $this->_status_code;
    }

    public function getStatusMessage()
    {
        return $this->getResponseJson()->{Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_MESSAGE};
    }

    /**
     * Seteamos la respuesta del api
     * @param string $response
     * @return Skybox_Core_Model_Api_Abstract
     */
    public function setResponse($response)
    {
    	//Mage::log('setResponse->' . $response);
    	if(!empty($response))
    		$this->_response = $response;
    	return $this;
    }

    public function getResponse()
    {
    	return $this->_response;
    }

    public function getResponseJson()
    {
    	//Mage::log('gerResponseJson->' . $this->_response);
    	return json_decode($this->_response);
    }

    /**
	* Setea url de llamda al ApiRest con sus parametros
	* @param String $action
	* @param Array $params
	* @return Skybox_Core_Model_Api_Abstract
	*/
	protected function setUrlService($method, $params)
	{
		$this->_methodCode = $method;
		$paramsRequest = '';
		if(!empty($params)) $paramsRequest = '?' . http_build_query($params);
		//$this->_request = $this->_config->skyboxDefaultApiUrl . $method . $paramsRequest;
        $this->_request = $this->_config->getSkyBoxUrlAPI() . $method . $paramsRequest;
		return $this;
	}

	/**
	* Retorna la url de llamada al servicio
	* @return String
	*/
	public function getUrlService()
	{
		Mage::log(" | getUrlService | " . $this->_request);
		return $this->_request;
	}

    /**
	* Obtenemos el objeto Session
	* @return Skybox_Core_Model_Session
	*/
	public function getSession()
	{
		return Mage::getSingleton('skybox/session');
	}

	/**
	* Obtenemos el objeto Cookie
	* @return Mage_Core_Model_Cookie
	*/
	public function getCookie()
	{
		return Mage::getModel('core/cookie');
	}

	/**
	* Obtenemos el objeto Http
	* @return Mage_Core_Helper_Http
	*/
	public function getHttp()
	{
		return Mage::helper('core/http');
	}

	/**
     * Current locale code getter
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }

    /**
	* Obtenemos inforamción del navegador del cliente
	* @return HTTP_USER_AGENT
	*/
	public function getUserAgent()
	{
		return $this->getHttp()->getHttpUserAgent();
	}

	/**
	* Obtenemos la Ip Local Remota del Cliente
	* @return REMOTE_ADDR
	*/
	public function getRemoteAddr()
	{
        return $_SERVER['REMOTE_ADDR'];
		//return $this->getHttp()->getRemoteAddr();
	}

	/**
	* Obtenemos la Ip Proxy del Client
	* @return HTTP_VIA or HTTP_X_FORWARDED_FOR
	*/
	public function getProxy()
	{
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}

	/**
	* Obtenemos la Ip Local del Cliente
	* @return String
	*/
	public function getHost()
	{
		return $this->getHttp()->getHttpHost();
	}

	/**
	* Obtenemos el lenguaje del Navegador del Cliente
	*/
	public function getLanguage()
	{
		return $this->getHttp()->getHttpAcceptLanguage();
	}

	/**
    * Sección de Validaciones
    */

	/**
	* Valida que no exista ningun error
	* @return bool
	*/
    public function HasError()
    {
    	/*if($this->ErrorService()) return true;
        if($this->ErrorKeyInvalid()) return true;
        if($this->ErrorIpNoAllow()) return true;
    	if($this->ErrorMerchantInvalid()) return true;
    	if($this->ErrorGuidInvalid()) return true;
    	if($this->ErrorTokenInvalid()) return true;
    	if($this->ErrorToAddProductToCart()) return true;*/

        if($this->ErrorRatesNotFound()) return true;

        if($this->ErrorServiceNotController()) return true;
        
        if($this->ErrorIpNoAllow()) return true;
        if($this->ErrorIdMerchantInvalid()) return true;
        if($this->ErrorGuidInvalid()) return true;
        if($this->ErrorTokenExpired()) return true;
        if($this->ErrorToAddProductToCart()) return true;
        if($this->ErrorMerchantNotConfigured()) return true;
        if($this->ErrorProductCategoryRequired()) return true;
        if($this->ErrorProductUnitWeigthRequired()) return true;
        if($this->ErrorProductWeigthInvalid()) return true;
        if($this->ErrorProductWeigthIsnull()) return true;
        if($this->ErrorProductUnitWeightInvalid()) return true;
        if($this->ErrorStoreCityNotConfigured()) return true;

        if($this->ErrorTokenIsnull()) return true;
        if($this->ErrorGuidIsnull()) return true;
        if($this->ErrorRegionNotConfigured()) return true;
        if($this->ErrorProductNameRequiered()) return true;
        if($this->ErrorProductCodeRequiered()) return true;

        if($this->ErrorMerchantKeyIsnull()) return true;
        if($this->ErrorMerchantKeyInvalid()) return true;      

    	return false;

    }

	/**
    * Valida si se devolvio un error por Merchant invalido
    * @return bool
    */
    public function ErrorIdMerchantInvalid()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_IDMERCHANT_INVALID;
    }
    public function ErrorMerchantNotConfigured()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_COUNTRY_NOT_CONFIGURED_TO_STORE;
    }
    /**
    * Valida si se devolvio un error por Guid invalido
    * @return bool
    */
    public function ErrorGuidInvalid()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_DEVICE_GUID_INVALID;
    }

    /**
    * Valida si se devolvio un error por Token invalido
    * @return bool
    */
    public function ErrorTokenExpired()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_TOKEN_EXPIRED;
    }

    /**
    * Valida si se devolvio un error de agregación de producto al carrito de compras
    * @return bool
    */
    public function ErrorToAddProductToCart()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_PRODUCT_NOT_IN_SHOPPING_CART;
    }

    /**
    * Valida si se devolvio un error no controlado la respuesta del servicio
    * @return bool
    */
    public function ErrorServiceNotController()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_SERVICE_NOT_CONTROLLER;
    }
   

    public function ErrorIpNoAllow()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_IP_NOT_ALLOWED;
    }
    public function ErrorProductCategoryRequired()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_PRODUCT_CATEGORY_INVALID;
    }
    public function ErrorProductUnitWeigthRequired()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_PRODUCT_UNITWEIGHT_IS_NULL;
    }
    public function ErrorProductWeigthInvalid()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_PRODUCT_WEIGHT_INVALID;
    }
    public function ErrorProductWeigthIsnull()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_PRODUCT_WEIGHT_IS_NULL;
    }
    
    public function ErrorProductUnitWeightInvalid()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_PRODUCT_UNITWEIGHT_INVALID;
    }
    public function ErrorStoreCityNotConfigured()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_STORE_CITY_NOT_CONFIGURED;
    }
    public function ErrorTokenIsnull()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_TOKEN_IS_NULL;
    }
    public function ErrorGuidIsnull()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_GUID_IS_NULL;
    }
    public function ErrorRegionNotConfigured()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_REGION_NOT_CONFIGURED_TO_STORE;
    }
    public function ErrorProductNameRequiered()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_PRODUCT_NAME_IS_NULL;
    }
    public function ErrorProductCodeRequiered()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_PRODUCT_CODE_IS_NULL;
    }
    public function ErrorMerchantKeyInvalid()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_MERCHANT_KEY_INVALID;
    }
    public function ErrorMerchantKeyIsnull()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_MERCHANT_KEY_IS_NULL;
    }
    public function ErrorRatesNotFound()
    {
        return $this->getStatusCode() == Skybox_Core_Model_Config::SKYBOX_ERROR_GCODE_RATES_NOT_FOUND;
    }
}