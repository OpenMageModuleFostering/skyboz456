<?php

/**
 * Skybox Core
 *
 * @category    Skybox
 * @package     Skybox_Core
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Core_Model_Api_Restful extends Skybox_Core_Model_Api_Abstract
{
    public $_skybox_guid = "";

    /**
     * Guardamos en sesión el Html Template del Botón
     * @param string $value
     * @return Skybox_Core_Model_Api_Restful
     */
    public function setHtmlTemplateButton($value = null)
    {
        $html = $this->getResponseJson()->{Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_BUTTONHTML};
        $htmlError = $this->getResponseJson()->{Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_BUTTONERRORHTML};
        $this->_config->getSession()->setHtmlTemplateButton($html);
        $this->_config->getSession()->setHtmlTemplateButtonError($htmlError);
        return $this;
    }

    /**
     * Se recupera el Html Template del Botón almacenado en la sesión
     * @return String
     */
    public function getHtmlTemplateButton()
    {
        return $this->_config->getSession()->getHtmlTemplateButton();
    }

    /**
     * Guardamos en sesión el Html Template de la barra
     * @param string $value
     * @return Skybox_Core_Model_Api_Restful
     */
    public function setHtmlTemplateBar($value = null)
    {
        $html = $this->getResponseJson()->{Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_BARHTML};
        $this->_config->getSession()->setHtmlTemplateBar($html);
        return $this;
    }

    /**
     * Se recupera el Html Template del Botón almacenado en la sesión
     * @return String
     */
    public function getHtmlTemplateBar()
    {
        return $this->_config->getSession()->getHtmlTemplateBar();
    }

    public function setGuidApi($value = null)
    {
        $guidId = null;

        if (!$guidId) {
            $guidId = $this->getResponseJson()->{Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_GUID};
            Mage::log("GuidID Not cart: " . $guidId);
        }

        $this->_config->getSession()->setCookieGuid($guidId);
        $this->_skybox_guid = $guidId;
        Mage::log("GuidID found: " . $guidId);
        return $this;
    }

    public function getGuidApi()
    {
        $guidId = null;

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        foreach ($quote->getAllItems() as $item) {
            $guidId = $item->getGuidSkybox();
             Mage::log(' | CallApiRest->Error1 | ', null, 'guid.log', true);
            Mage::log("getGuidApi(): GuidID found (quote):  ".$guidId, null, 'guid.log', true);
            break;
        }

        if (!$guidId) {
            $skyboxSession = $this->_config->getSession()->getCookieGuid();
            if ($skyboxSession) {
                Mage::log(' | CallApiRest->Error2 | ', null, 'guid.log', true);
                Mage::log("Call getGuidApi() from getSession(): " .$this->_config->getSession()->getCookieGuid(),null, 'guid.log', true);
                $guidId = $this->_config->getSession()->getCookieGuid();
            }
        }
        return $guidId;
    }

    public function compareGuid()
    {
        $already_guid = $this->getGuidApi();
        $called_guid = $this->getResponseJson()->{Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_GUID};

        if ($already_guid == $called_guid) {
            return true;
        }
        return false;
    }

    /**
     * Seteamos si la localización del usuario esta permitida
     * @param string $value
     * @return Skybox_Core_Model_Api_Restful
     */
    public function setLocationAllow($value = null)
    {
        $allow = $this->getResponseJson()->{Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_LOCATION_ALLOW};
        $this->_config->getSession()->setLocationAllow($allow);
        return $this;
    }

    public function getLocationAllow()
    {
        return $this->_config->getSession()->getLocationAllow();
    }

    /**
     * Seteamos error del servicio
     * @param string $value
     * @return Skybox_Core_Model_Api_Restful
     */
    public function setErrorAuthenticate()
    {
        $this->_config->getSession()->setErrorAuthenticate($this->HasError());
        return $this;
    }

    public function getErrorAuthenticate()
    {
        return $this->_config->getSession()->getErrorAuthenticate();
    }

    /**
     * Guardamos en sesión el token de autorización
     * @param string $value
     * @return Skybox_Core_Model_Api_Restful
     */
    public function setAuthorizedToken($value = null)
    {
        $token = $this->getResponseJson()->{Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_TOKEN};
        $this->_config->getSession()->setTokenApiRest($token);
        return $this;
    }

    /**
     * Obtenemos el token de autorización almacenado en sesión
     * @return string
     */
    public function getAuthorizedToken()
    {
        //return $this->_config->getSession()->getTokenApiRest() ?: "";
        $algo = $this->_config->getSession()->getTokenApiRest();
        if (!empty($algo)) {
            return $this->_config->getSession()->getTokenApiRest();
        }
        return "";
    }

    /**
     * Sección de llamadas a los metodos del ApiRest
     */

    /**
     * Se realiza llamado al metodo del Servicio y se guarda resultado
     * @return Skybox_Core_Model_Api_Restful
     */
    protected function SetResponseService()
    {
        //$this->_response = file_get_contents($this->getUrlService());
        $this->_response = $this->file_get_contents_curl($this->getUrlService());
        return $this;
    }

    /**
     * Invocamos al servicio
     * @return Skybox_Core_Model_Api_Standard
     */
    public function CallService($method, $params)
    {
        $start_time = time();
        try {
            /**
             * Pasos
             * 1: Generamos la Url del Servicio
             * 2: Guardamos la solicitud que se realiza al servicio
             * 3: Invocamos al response del Servicio
             * 4: Asignamos valores a los parametros
             * 5: Guardamos la respuesta del servicio
             */

            $this->setUrlService($method, $params)
                ->SaveRequestService()
                ->SetResponseService()
                ->SetValuesResponse()
                ->SaveResponseService($method, $start_time);
        } catch (Exception $e) {
            //throw new Exception($e->getMessage(), 1);
            Mage::log($e->getMessage());
            Mage::helper('skyboxcore/email')->sendException($e->getMessage());
            return $this;
        }

        return $this;
    }

    /**
     * Seteamos valores a los parametros correspondientes a partir del methodo invocado
     * @return Skybox_Core_Model_Api_Restful
     */
    protected function SetValuesResponse()
    {
        $status = $this->getResponseJson()->{Skybox_Core_Model_Config::SKYBOX_PARAMETER_RESPONSE_STATUS};
        $this->setStatusCode($status);

        switch ($this->_methodCode) {
            case Skybox_Core_Model_Config::SKYBOX_ACTION_AUTHENTICATE:
                $this->setAuthorizedToken();
                $this->setErrorAuthenticate();
                break;

            case Skybox_Core_Model_Config::SKYBOX_ACTION_INITIALICE:
                //$this->setGuidApi();

                if ($this->compareGuid() && $this->getGuidApi()) {
                    $this->getGuidApi();
                } else {
                    $this->setGuidApi();
                }
                $this->setLocationAllow();
                $this->setHtmlTemplateBar();
                break;
            case Skybox_Core_Model_Config::SKYBOX_ACTION_GET_TEMPLATE_BUTTON:
                $this->setHtmlTemplateButton();
                break;
        }

        return $this;
    }



    /**
     * Sesión de registro de log
     */

    /**
     * Se registra la llamada al servicio
     */
    public function SaveRequestService()
    {
        //Acciones
        return $this;
    }

    /**
     * SaveResponseService
     * Save Service Response
     *
     * @param string $action
     * @param string $start_time
     * @return Skybox_Core_Model_Api_Restful
     */
    public function SaveResponseService($action, $start_time)
    {
        $skybox_log = Mage::helper('skyboxinternational/data')->getSkyboxLog();

        if ($skybox_log) {
            $data = array(
                'action' => $action,
                'request' => $this->getUrlService(),
                'response' => $this->getResponse(),
                'timestamp_request' => $start_time,
                'timestamp_response' => time(),
            );

            try {
                /* @var $log_service Skybox_Core_Model_Logservice */
                $log_service = Mage::getModel('skyboxcore/logservice');
                $log_service->setData($data);
                $log_service->save();

            } catch (Exception $e) {
                Mage::log($e->getMessage());
            }
        }
        return $this;
    }

    /*
     * file_get_contents_curl
     */

    function file_get_contents_curl($url)
    {
        if (!function_exists("curl_init")) die("cURL extension is not installed");
        $ch = curl_init();
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // To force the use of a new connection instead of a cached one
        //curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        // Execute
        $result = curl_exec($ch);

        // Will dump a beauty json :3
        //var_dump(json_decode($result, true));
        return $result;
    }
}