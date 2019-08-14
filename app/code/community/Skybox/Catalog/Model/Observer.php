<?php

/**
 * Skybox Checkout
 *
 * @category    Mage
 * @package     Skybox_Catalog
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

/**
 *
 * Catalog Model Observer
 *
 * Note: For Magento 1.8.x issues with Block Caching
 */
class Skybox_Catalog_Model_Observer
{
    private function getActive() {
//        $value = (bool)Mage::getStoreConfig('skyboxinternational/skyboxsettings/skyboxactive', Mage::app()->getStore());
        $value = Mage::getModel('skyboxcore/api_restful')->isModuleEnable();
        return $value;
    }

    public function hookToControllerActionPreDispatch($observer)
    {
        $active = $this->getActive();
        if (!$active) { return; }

        //Mage::log('router tivoli: '.$observer->getEvent()->getControllerAction()->getFullActionName(), null, 'tracer.log', true);
        if (($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_add')) {

            //Mage::log('hookToControllerActionPreDispatch '.$observer->getEvent()->getControllerAction()->getFullActionName(),null, 'tracer.log', true);
            Mage::dispatchEvent("add_to_cart_before", array('request' => $observer->getControllerAction()->getRequest()));
        }
    }

    public function hookToControllerActionPostDispatch($observer)
    {
        $active = $this->getActive();
        if (!$active) { return; }

        //Mage::log('router tivoli: '.$observer->getEvent()->getControllerAction()->getFullActionName(), null, 'tracer.log', true);
        if (($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_add')) {

            //Mage::log('hookToControllerActionPostDispatch: '.$observer->getEvent()->getControllerAction()->getFullActionName(),null, 'tracer.log', true);
            Mage::dispatchEvent("add_to_cart_after", array('request' => $observer->getControllerAction()->getRequest()));
        }
    }

    public function hookToAddToCartBefore($observer)
    {
        $active = $this->getActive();
        if (!$active) { return; }

        //Mage::log('function hookToAddToCartBefore: ',null, 'tracer.log', true);
        $key = Mage::getSingleton('core/session')->getFormKey();
        $observer->getEvent()->getRequest()->setParam('form_key', $key);
        $request = $observer->getEvent()->getRequest()->getParams();
    }

    public function hookToAddToCartAfter($observer)
    {
        $active = $this->getActive();
        if (!$active) { return; }

        $request = $observer->getEvent()->getRequest()->getParams();
        Mage::log("hookToAddToCartAfter ".print_r($request,true)." is added to cart.", null, 'cart.log', true);
    }

    public function injectTab(Varien_Event_Observer $observer)
    {
//        $active = $this->getActive();
//        if (!$active) { return; }

        // core_block_abstract_prepare_layout_after

        /* $block Mage_Adminhtml_Block_Catalog_Category_Tabs */
        $block = $observer->getEvent()->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Catalog_Category_Tabs) {

            if ($this->_getRequest()->getActionName() == 'edit' || $this->_getRequest()->getParam('type')) {

                $tab_ids = $block->getTabsIds();
                $tab_name = $tab_ids[3]; // SkyboxCheckout position

                if ($tab_name) {
                    $block->removeTab($tab_name);
                }

                // SkyboxCheckout Tab
                $block->addTab('custom_tab', array(
                    'label' => 'SkyboxCheckout',
                    'content' => $block->getLayout()
                            ->createBlock('skyboxcatalog/catalog_category_tab_skyboxcheckout')
                            ->toHtml()
                ));
            }
        }
    }

    public function saveCategory(Varien_Event_Observer $observer)
    {
        // catalog_category_prepare_save

        // Note: Already know. this is a piece of crap

        /* $category Mage_Catalog_Model_Category */
        $category = $observer->getEvent()->getCategory();

        $skybox_category_id = $this->_getRequest()->getPost('skybox_category_id');
        $skybox_category_id_select = $this->_getRequest()->getPost('skybox_category_id_select');
        $apply_button = $this->_getRequest()->getPost('apply_button');

        if ($skybox_category_id != $category->getData('skybox_category_id')) {
            //$category->setData('skybox_category_id', $skybox_category_id);
            $category->setSkyboxCategoryId($skybox_category_id);
            $msg = "[" . $category->getId() . "] saving skybox_category_id [" . $skybox_category_id . "]";
            Mage::log($msg, null, 'skyboxcheckout.log', true);
        }

        if ($skybox_category_id_select != $category->getData('skybox_category_id_select')) {
            //$category->setData('skybox_category_id_select', $skybox_category_id_select);
            $category->setSkyboxCategoryIdSelect($skybox_category_id_select);
            $msg = "[" . $category->getId() . "] saving skybox_category_id_select [" . $skybox_category_id_select . "]";
            Mage::log($msg, null, 'skyboxcheckout.log', true);
        }

        if ($skybox_category_id || $skybox_category_id_select) {
            Mage::log("Commodities saved!", null, 'skyboxcheckout.log', true);
            try {
                $category->getResource()->save($category);
            } catch (Exception $ex) {
                Mage::log("saveCategory() Exception: " . $ex->getMessage(), null, 'skyboxcheckout.log', true);
            }
        }

        if (isset($apply_button) && $apply_button != 'nothing') {

            $skybox_category_id_select = $this->_getRequest()->getPost('skybox_category_id_select');
            $skybox_category_id_select = ($skybox_category_id_select) ?
                $skybox_category_id_select :
                $category->getData('skybox_category_id_select');

            Mage::log("Apply Button: " . $apply_button, null, 'skyboxcheckout.log', true);
            switch ($apply_button) {
                case 'all_products':
                    $categoryIds = array($category->getId());
                    $this->_bulk_commodities($categoryIds, $skybox_category_id_select, true);
                    break;

                case 'all_products_without_commoditites':
                    $categoryIds = array($category->getId());
                    $this->_bulk_commodities($categoryIds, $skybox_category_id_select, false);
                    break;
            }
        }
        return $this;
    }

    public function  setCommodityInForm(Varien_Event_Observer $observer)
    {
        // catalog_product_prepare_save

        Mage::log('catalog_product_prepare_save', null, 'skyboxcheckout.log', true);

        $product = $observer->getEvent()->getProduct();
        $skybox_category_id = $this->_getRequest()->getPost('skybox_category_id');
        $commodity = $product->getData('skybox_category_id');

        // If the user change commodity manually
        if ($skybox_category_id && ($skybox_category_id != $product->getData('skybox_category_id'))) {
            $product->setData('skybox_category_id', $skybox_category_id);
            $product->getResource()->save($product);
            return $this;
        }

        // If commodity already set
        if ($commodity) {
            return $this;
        } else {
            $categoryIds = $product->getCategoryIds();

            if (empty($categoryIds)) {
                return $this;
            }

            $categoryId = reset($categoryIds); // current

            /* $category Mage_Catalog_Model_Category */
            $category = Mage::getModel('catalog/category')->load($categoryId);

            $value = $category->getData('skybox_category_id');

            if ($value) {
                $product->setData('skybox_category_id', $value);
                $product->getResource()->save($product);
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    sprintf("The category %s doesn't have any associated commodity.", $category->getData('title'))
                );
            }
        }
        return $this;
    }

    /**
     * Shortcut to getRequest
     */
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }

    private function _bulk_commodities($categoryIds, $value, $allProducts = true)
    {
        Mage::log("Apply Bulk ...", null, 'skyboxcheckout.log', true);

        /* @var $collection Mage_Catalog_Model_Product */
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('category_id', array('in' => $categoryIds));

        if (!$allProducts) {
            //$collection->addAttributeToFilter('skybox_category_id', '');
            $collection->addAttributeToFilter('skybox_category_id', array('null' => true));
        }

        //Mage::log($collection->getSelectSql(true), null, 'skyboxcheckout.log', true);
        $count = count($collection);

        foreach ($collection as $product) {
            try {
                Mage::log("Update Product: " . $product->getId(), null, 'skyboxcheckout.log', true);
                $product->setData('skybox_category_id', $value);
                $product->getResource()->save($product);
            } catch (Exception $ex) {
                Mage::log("saveCategory() Exception: " . $ex->getMessage(), null, 'skyboxcheckout.log', true);
            }
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(
            sprintf('%s products have been updated correctly.', $count)
        );
    }

}