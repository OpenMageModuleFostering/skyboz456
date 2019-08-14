<?php
class Skybox_Checkout_Model_Pay extends Mage_Payment_Model_Method_Abstract
{

    protected $_code = 'skyboxcheckout_pay';

    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canUseCheckout          = false;

}