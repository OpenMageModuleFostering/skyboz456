<?php

/**
 * Skybox Core
 *
 * @category    Skybox
 * @package     Skybox_Core
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

/**
 * Configuración del modelo
 * @author      César Tapia M. <ctapia@skyworldint.com>
 */
class Skybox_Core_Model_Config
{

    const SKYBOX_VERSION = "1.2.7";
    /**
     * Skybox cookie
     * @var string
     */
    const SKYBOX_COOKIE_API_GUID = "int_guid_api";

    /**
     * Skybox actions
     * @var string
     */
    const SKYBOX_ACTION_AUTHENTICATE = "AuthenticateService";
    const SKYBOX_ACTION_INITIALICE = "InitializeSessionUser";
    const SKYBOX_ACTION_CALCULATE = "Calculate";
    const SKYBOX_ACTION_CATEGORIES = "Categories";
    const SKYBOX_ACTION_ADD_PRODUCT_CART = "AddProductToCart";
    const SKYBOX_ACTION_DELETE_PRODUCT_CART = "DelProductOfCart";
    const SKYBOX_ACTION_UPDATE_PRODUCT_CART = "UpdateProductOfCart";
    const SKYBOX_ACTION_GET_TEMPLATE_BUTTON = "GetButtonTemplate";
    const SKYBOX_ACTION_GET_TOTAL_SHOPINGCART = "GetTotalShoppingCart";

    /**
     * Skybox errors
     * @var string
     */
    const SKYBOX_ERROR_SERVICE_NOT_CONTROLLER = "50000";
    const SKYBOX_ERROR_GCODE_IDMERCHANT_INVALID = "50001";
    const SKYBOX_ERROR_GCODE_TOKEN_EXPIRED = "50002"; 
    const SKYBOX_ERROR_GCODE_DEVICE_GUID_INVALID = "50003"; 
    const SKYBOX_ERROR_GCODE_PRODUCT_NOT_IN_SHOPPING_CART = "50004"; 
    const SKYBOX_ERROR_GCODE_COUNTRY_NOT_CONFIGURED_TO_STORE = "50005"; 
    const SKYBOX_ERROR_MERCHANT_KEY_INVALID = "50006"; 
    const SKYBOX_ERROR_GCODE_IP_NOT_ALLOWED = "50007"; 
    const SKYBOX_ERROR_GCODE_PRODUCT_CATEGORY_INVALID = "50008"; 
    const SKYBOX_ERROR_GCODE_PRODUCT_UNITWEIGHT_IS_NULL = "50009"; 
    const SKYBOX_ERROR_GCODE_PRODUCT_WEIGHT_INVALID = "50010";
    const SKYBOX_ERROR_GCODE_PRODUCT_WEIGHT_IS_NULL = "50011";    
    const SKYBOX_ERROR_GCODE_MERCHANT_KEY_IS_NULL = "50013";     
    const SKYBOX_ERROR_GCODE_PRODUCT_UNITWEIGHT_INVALID = "50014"; 
    const SKYBOX_ERROR_GCODE_STORE_CITY_NOT_CONFIGURED = "50015"; 
    const SKYBOX_ERROR_GCODE_TOKEN_IS_NULL = "50016"; 
    const SKYBOX_ERROR_GCODE_GUID_IS_NULL = "50017"; 
    const SKYBOX_ERROR_GCODE_REGION_NOT_CONFIGURED_TO_STORE = "50018";
    const SKYBOX_ERROR_GCODE_PRODUCT_NAME_IS_NULL = "50019"; 
    const SKYBOX_ERROR_GCODE_PRODUCT_CODE_IS_NULL = "50020";
    const SKYBOX_ERROR_GCODE_RATES_NOT_FOUND = "50021";

    /**
     * Skybox PARAMETERS REQUEST
     * @var string
     */
    const SKYBOX_PARAMETER_MERCHANT = "merchant";
    const SKYBOX_PARAMETER_MERCHANTKEY = "merchantKey";
    const SKYBOX_PARAMETER_GUID = "guid";
    const SKYBOX_PARAMETER_TOKEN = "token";

    /**
     * Skybox PARAMETERS RESPONSE
     * @var string
     */
    const SKYBOX_PARAMETER_RESPONSE_TOKEN = "TokenCode";
    const SKYBOX_PARAMETER_RESPONSE_GUID = "Guid";
    const SKYBOX_PARAMETER_RESPONSE_MESSAGE = "StatusMessage";
    const SKYBOX_PARAMETER_RESPONSE_STATUS = "StatusCode";
    const SKYBOX_PARAMETER_RESPONSE_BARHTML = "BarHtmlTemplate";
    const SKYBOX_PARAMETER_RESPONSE_LOCATION_ALLOW = "LocationAllow";
    const SKYBOX_PARAMETER_RESPONSE_STORE_CODE = "StoreCode";
    const SKYBOX_PARAMETER_RESPONSE_BUTTONHTML = "HtmlTemplate";
    const SKYBOX_PARAMETER_RESPONSE_BUTTONERRORHTML = "HtmlTemplateError";
    const SKYBOX_PARAMETER_RESPONSE_BUTTONERROR = "CalcErrorCode";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_ID = "ProductId";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_CUSTOMS = "ProductCustoms";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_SHIPPING = "ProductShipping";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_INSURANCE = "ProductInsurance";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_TOTAL = "TotalProduct";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_PRICE = "StoreProductPrice";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_CUSTOMS_USD = "ProductCustomsUSD";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_SHIPPING_USD = "ProductShippingUSD";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_INSURANCE_USD = "ProductInsuranceUSD";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_TOTAL_USD = "TotalProductUSD";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_PRICE_USD = "StoreProductPriceUSD";
    const SKYBOX_PARAMETER_RESPONSE_CART_CURRENCY_ISO = "CartCurrencyIso";

    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_BASE_PRICE = "BaseProductPrice";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_BASE_PRICE_USD = "BaseProductPriceUSD";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_ADJUST_PRICE = "ProductAdjustment";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_ADJUST_PRICE_USD = "ProductAdjustmentUSD";
    const SKYBOX_PARAMETER_RESPONSE_PRODUCT_LABEL_ADJUST = "WidgetLabelAdjustment";

    const SKYBOX_PARAMETER_RESPONSE_CSS_VERSION = "CssVersion";

    const SKYBOX_CURRENCY_USD = "USD";

    /**
     * Default URL para Api Skybox Checkout
     *
     * @var string
     */
    //public $skyboxDefaultApiUrl = 'http://www.skyboxcheckout.com/Service/ApiRest/';
    //public $skyboxDefaultApiUrl = 'http://localhost:65065/ApiRest/';
    public $skyboxDefaultApiUrl = 'http://127.0.0.1:65065/ApiRest/';
    //public $skyboxDefaultApiUrl = 'http://checkout.skynet.com/Service/ApiRest/';
    //public $skyboxDefaultApiUrl = 'http://beta.skyboxcheckout.com/ServiceTest/ApiRest/';

    /**
     * Default URL para Skybox Checkout
     *
     * @var string
     */
    //public $skyboxDefaultUrl = 'http://www.skyboxcheckout.com/';
    //public $skyboxDefaultUrl = 'http://localhost:6761/';
    //public $skyboxDefaultUrl = 'http://127.0.0.1:6761/';
    public $skyboxDefaultUrl = 'http://127.0.0.1:6761/';
    //public $skyboxDefaultUrl = 'http://checkout.skynet.com/';

    /**
     * Código de Tienda
     *
     * @var string
     */
    protected $_merchantId = null;


    /**
     * Llave de Tienda
     *
     * @var string
     */
    protected $_merchantkey = null;

    /**
     * Código de Guid
     * @var string
     */
    protected $_guidapi = null;

    /**
     * Obtenemos el objeto Session
     * @return Skybox_Core_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('skyboxcore/session');
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
     *
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
     *
     * @return HTTP_USER_AGENT
     */
    public function getUserAgent()
    {
        return $this->getHttp()->getHttpUserAgent();
    }

    /**
     * Obtenemos la Ip Local Remota del Cliente
     *
     * @return REMOTE_ADDR
     */
    public function getRemoteAddr()
    {
        return $this->getHttp()->getRemoteAddr();
    }

    /**
     * Obtenemos la Ip Proxy del Client
     *
     * @return HTTP_VIA or HTTP_X_FORWARDED_FOR
     */
    public function getProxy()
    {
        $proxy = getenv('HTTP_X_FORWARDED_FOR');
        return isset($proxy) ? $proxy : "";
    }

    /**
     * Obtenemos la Ip Local del Cliente
     *
     * @return String
     */
    public function getHost()
    {
        return $this->getHttp()->getHttpHost();
    }

    /**
     * Obtenemos el lenguaje del Navegador del Cliente
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->getHttp()->getHttpAcceptLanguage();
    }

    public function getSkyBoxUrlAPI()
    {
        return Mage::helper('skyboxinternational/data')->getSkyboxUrlAPI();
    }

    public function getSkyBoxUrlMain()
    {
        return Mage::helper('skyboxinternational/data')->getSkyboxUrlMain();
    }
}