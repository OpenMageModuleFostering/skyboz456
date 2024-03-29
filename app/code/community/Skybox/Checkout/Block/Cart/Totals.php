<?php

/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Checkout
 * @copyright   Copyright (c) 2014 - 2017 Skybox Checkout. (http://www.skyboxcheckout.com)
 */
class Skybox_Checkout_Block_Cart_Totals extends Mage_Checkout_Block_Cart_Abstract
{
    protected $_totalRenderers;
    protected $_defaultRenderer = 'checkout/total_default';
    protected $_totals = null;

    public function getTotals()
    {
        if (is_null($this->_totals)) {
            return parent::getTotals();
        }
        return $this->_totals;
    }

    public function setTotals($value)
    {
        $this->_totals = $value;
        return $this;
    }

    protected function _getTotalRenderer($code)
    {
        $blockName = $code . '_total_renderer';
        $block = $this->getLayout()->getBlock($blockName);
        if (!$block) {
            $block = $this->_defaultRenderer;
            $config = Mage::getConfig()->getNode("global/sales/quote/totals/{$code}/renderer");
            if ($config) {
                $block = (string)$config;
            }

            $block = $this->getLayout()->createBlock($block, $blockName);
        }
        /**
         * Transfer totals to renderer
         */
        $block->setTotals($this->getTotals());
        return $block;
    }

    public function renderTotal($total, $area = null, $colspan = 1)
    {
        $code = $total->getCode();
        /**
         * Integration 3 start, show price shop in cart*
         */
        $allowRunByIntegration3 = true;
        //$typeIntegration = Mage::getStoreConfig('settings/typeIntegration');
        $typeIntegration = Mage::helper('skyboxinternational/data')->getSkyboxIntegration();
        if ($typeIntegration == 3) {
            $allowRunByIntegration3 = false;
        }

        $locationAllow = Mage::getModel('skyboxcheckout/api_checkout')->getLocationAllow();

        /**
         * Integration 3 end, show price shop in cart*
         */
        if ($allowRunByIntegration3 && $locationAllow) {
            if ($code != "subtotal") {
                if ($total->getAs()) {
                    $code = $total->getAs();
                }
                return $this->_getTotalRenderer($code)
                    ->setTotal($total)
                    ->setColspan($colspan)
                    ->setRenderingArea(is_null($area) ? -1 : $area)
                    ->toHtml();
            }
        } else {
            if ($total->getAs()) {
                $code = $total->getAs();
            }
            return $this->_getTotalRenderer($code)
                ->setTotal($total)
                ->setColspan($colspan)
                ->setRenderingArea(is_null($area) ? -1 : $area)
                ->toHtml();

        }

    }

    /**
     * Render totals html for specific totals area (footer, body)
     *
     * @param   null|string $area
     * @param   int $colspan
     * @return  string
     */
    public function renderTotals($area = null, $colspan = 1)
    {
        $html = '';
        foreach ($this->getTotals() as $total) {
            if ($total->getArea() != $area && $area != -1) {
                continue;
            }
            $html .= $this->renderTotal($total, $area, $colspan);
        }
        return $html;
    }

    /**
     * Check if we have display grand total in base currency
     *
     * @return bool
     */
    public function needDisplayBaseGrandtotal()
    {
        $quote = $this->getQuote();
        if ($quote->getBaseCurrencyCode() != $quote->getQuoteCurrencyCode()) {
            return true;
        }
        return false;
    }

    /**
     * Get formated in base currency base grand total value
     *
     * @return string
     */
    public function displayBaseGrandtotal()
    {
        $firstTotal = reset($this->_totals);
        if ($firstTotal) {
            $total = $firstTotal->getAddress()->getBaseGrandTotal();
            return Mage::app()->getStore()->getBaseCurrency()->format($total, array(), true);
        }
        return '-';
    }

    /**
     * Get active or custom quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if ($this->getCustomQuote()) {
            return $this->getCustomQuote();
        }

        if (null === $this->_quote) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }
}
