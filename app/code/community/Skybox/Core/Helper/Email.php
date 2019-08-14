<?php

/**
 * Skybox Core
 *
 * @category    Skybox
 * @package     Skybox_Core
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

class Skybox_Core_Helper_Email extends Mage_Core_Helper_Abstract
{
    public function sendException($message)
    {
        $storeName = "[" . Mage::app()->getStore()->getName() . "]";
        $subject = $storeName . " - Exception generated at " . strftime("%H:%M:%S", time());
        return $this->email($subject, $message);
    }

    public function sendAPIError($message)
    {
        $storeName = "[" . Mage::app()->getStore()->getName() . "]";
        $subject = $storeName . " - Error trying to connect to API at " . strftime("%H:%M:%S", time());
        return $this->email($subject, $message);
    }

    public function email($subject, $body)
    {
        $skybox_email = Mage::helper('skyboxinternational/data')->getSkyboxEmail();

        if (!$skybox_email) {
            return false;
        }

        /* @var $mail Mage_Core_Model_Email */
        $mail = Mage::getModel('core/email');
        $mail->setToName('Magento Store');
        $mail->setToEmail($skybox_email);
        $mail->setBody($body);
        $mail->setSubject($subject);
        $mail->setFromEmail($skybox_email);
        $mail->setFromName('Magento Store');
        $mail->setType('text');

        try {
            $myEmail = $mail->send();
            Mage::log($myEmail);
            Mage::log('SkyboxCheckout: Your request email has been sent');
        } catch (Exception $e) {
            Mage::log('SkyboxCheckout: Unable to send');
        }
    }
}