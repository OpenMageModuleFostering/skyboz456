<?php

/**
 * Skybox Core
 *
 * @category    Skybox
 * @package     Skybox_Core
 * @copyright   Copyright (c) 2014 SkyBOX Checkout, Inc. (http://www.skyboxcheckout.com)
 */
class Skybox_Core_Model_Standard
{
    /**
     * Config instance
     * @var Skybox_Core_Model_Config
     */
    protected $_config = null;

    /**
     * Config Model Type
     *
     * @var string
     */
    protected $_typeConfig = "skyboxcore/config";

    /**
     * API instance
     * @var Skybox_Core_Model_Api_Restful
     */
    protected $_api = null;

    /**
     * Api Model Type
     *
     * @var string
     */
    protected $_apiType = 'skyboxcore/api_restful';

    /**
     * Helper instance
     * @var Skybox_International_Helper_Data
     */
    protected $_help = null;

    /**
     * Api Model Type
     *
     * @var string
     */
    protected $_helpType = 'skyboxinternational/data';

    /* @return Skybox_Core_Model_Config */
    protected function _getConfig()
    {
        if (null === $this->_config)
            $this->_config = Mage::getModel($this->_typeConfig);
        return $this->_config;
    }

    /* @return Skybox_Core_Model_Api_Restful */
    protected function _getApi()
    {
        if (null === $this->_api) {
            $this->_api = Mage::getModel($this->_apiType)->setConfigObject($this->_config);
        }
        return $this->_api;
    }

    /* @return Skybox_International_Helper_Data */
    protected function _getHelper()
    {
        if (null === $this->_help) {
            $this->_help = Mage::helper($this->_helpType);
        }
        return $this->_help;
    }

    public function __construct($params = array())
    {
        $this->_getConfig();
        $this->_getApi();
    }

    public function getMerchant()
    {
        return $this->_getHelper()->getMerchantCode();
    }

    public function getMerchantKey()
    {
        return $this->_getHelper()->getMerchantKey();
    }

    public function getEnabledAddSumTaxToPrice()
    {
        return $this->_getHelper()->getEnabledAddSumTaxToPrice();
    }

    public function getWeightUnit()
    {
        return $this->_getHelper()->getWeightUnit();
    }

    public function getAuthorizedToken()
    {
        return $this->_api->getAuthorizedToken();
    }

    public function getGuidApi()
    {
        return $this->_api->getGuidApi();
    }

    public function getResponse()
    {
        return $this->_api->getResponseJson();
    }

    public function getParameter($paramName, $valueDefault = null)
    {
        try {
            return $this->getResponse()->{$paramName};
        } catch (Exception $e) {
            return $valueDefault;
        }
    }

    public function getLocationAllow()
    {
        //return $this->_api->getLocationAllow() == "1";
        return $this->_api->getLocationAllow();
    }

    public function getStoreCode()
    {
        return $this->_api->getStoreCode();
    }

    public function getErrorAuthenticate()
    {
        return $this->_api->getErrorAuthenticate();
    }

    public function getHtmlTemplateBar()
    {
        return $this->_api->getHtmlTemplateBar();
    }

    public function getHtmlTemplateButton()
    {
        return $this->_api->getHtmlTemplateButton();
    }

    public function getHtmlTemplateButtonError()
    {
        return $this->_api->getHtmlTemplateButtonError();
    }

    public function HasError()
    {
        return $this->_api->HasError();
    }

    public function getStatusCode()
    {
        return $this->_api->getStatusCode();
    }

    public function getStatusMessage()
    {
        return $this->_api->getStatusMessage();
    }

    /**
     * Se invoca al servicio para generar Token de Seguridad
     * @return Skybox_Core_Model_Standard
     */
    public function AuthenticateService()
    {
        //$this->_getApi(); // Why? http://goo.gl/xYs9zM
        Mage::log("AuthenticateService ", null, 'autenticate.log', true);

        $successService = false;
        $errorService = 1;

        while (!$successService) {
            $params = array(
                Skybox_Core_Model_Config::SKYBOX_PARAMETER_MERCHANT => $this->getMerchant(),
                Skybox_Core_Model_Config::SKYBOX_PARAMETER_MERCHANTKEY => $this->getMerchantKey()
            );

            Mage::log('|AuthenticateService|' . json_encode($params));

            $this->_api->CallService(Skybox_Core_Model_Config::SKYBOX_ACTION_AUTHENTICATE, $params);

            //if ($this->_api->ErrorKeyInvalid() || $this->_api->ErrorIpNoAllow()) break;
            //if (!($this->_api->ErrorService())) $successService = true;
            if ($this->_api->ErrorMerchantKeyInvalid() || $this->_api->ErrorIpNoAllow()) break;
            if (!($this->_api->ErrorServiceNotController())) $successService = true;
            $errorService++;
            if ($errorService > 3) break;
        }

        if (!$successService) {
            //throw new Exception("Error Processing Request", 1);
            //Mage::throwException('Error: al comunicarse con el servidor, si el error persiste comunicarse con su administrador');
        }

        return $this;
    }

    public function CallApiRest($action, $params)
    {

        $start = microtime(true);

        Mage::log("CallApiRest ", null, 'standart.log', true);

        $successService = false;
        $errorService = 1;
        $successAuthenticate = true;
        try {
            //$this->_getApi(); // Why? http://goo.gl/xYs9zM
            $variable = $this->getAuthorizedToken();
            if (empty($variable)) {
                Mage::log("empty ", null, 'standart.log', true);

                $this->AuthenticateService();
                $params = $this->resetValueDefault($params);
                $successAuthenticate = !$this->getErrorAuthenticate();
            }

            if ($successAuthenticate) {
                 Mage::log("successAuthenticate ", null, 'standart.log', true);

                while (!$successService) {
                    Mage::log(" ---------------------------------------------");
                    Mage::log(" | Call | " . $action);
                    $this->_api->CallService($action, $params);

                    if ($this->_api->HasError()) {
                        Mage::log("HasError ", null, 'standart.log', true);
                        Mage::log(' | CallApiRest->Error | ');
                        $errorService++;
                        //if ($this->_api->ErrorTokenInvalid()) $this->AuthenticateService();
                        if ($this->_api->ErrorTokenExpired()) $this->AuthenticateService();
                        if ($errorService > 3) break;
                        $params = $this->resetValueDefault($params);
                    } else {
                        Mage::log(' | CallApiRest->Success | ');
                        $successService = true;
                    }
                }
            }
        } catch (Exception $e) {
            Mage::log(' | CallApiRest->Error | ' . $e->getMessage());
            Mage::helper('skyboxcore/email')->sendException($e->getMessage());
            $successService = false;
        }

        if (!$successService) {
            Mage::log(' | Error Processing Request / CallApiRest | ' . $action);
            //throw new Exception("Error Processing Request", 1);
            //Mage::throwException('Error: al comunicarse con el servidor, si el error persiste comunicarse con su administrador');
            //Mage::helper('skyboxcore/email')->sendAPIError($e->getMessage());
        }


        $total_time = round(microtime(true)-$start, 4);
        Mage::log('Servicio generado ['.$action.']: '.$total_time.' segundos.', null, 'timer.log',true);

        return $this;
    }

    /**
     * Setea los valores por defecto
     * @return array()
     */
    protected function resetValueDefault($params)
    {
        foreach ($params as $key => $val) {
            if ($key == Skybox_Core_Model_Config::SKYBOX_PARAMETER_TOKEN) $params[$key] = $this->_api->getAuthorizedToken();
            if ($key == Skybox_Core_Model_Config::SKYBOX_PARAMETER_GUID) $params[$key] = $this->_api->getGuidApi();
        }

        return $params;
    }
}