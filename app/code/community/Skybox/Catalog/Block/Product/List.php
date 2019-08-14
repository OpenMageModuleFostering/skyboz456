<?php
/**
 * Skybox Checkout
 *
 * @category    Skybox
 * @package     Skybox_Catalog
 * @copyright   Copyright (c) 2017 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

/**
 * Product list
 */
class Skybox_Catalog_Block_Product_List extends Mage_Catalog_Block_Product_List
{
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';

    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $layer = $this->getLayer();
            /* @var $layer Mage_Catalog_Model_Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if (Mage::registry('product')) {
                // get collection of categories this product is associated with
                $categories = Mage::registry('product')->getCategoryCollection()
                    ->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                if ($category->getId()) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                    $this->addModelTags($category);
                }
            }
            $this->_productCollection = $layer->getProductCollection();

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }

        return $this->_productCollection;
    }

    /**
     * Get catalog layer model
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('catalog/layer');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * Retrieve API Product
     *
     * @return Skybox_Catalog_Model_Api_Product
     */
    protected function _getApi()
    {
        if (null === $this->_api) {
            $this->_api = Mage::getModel('skyboxcatalog/api_product');
        }
        return $this->_api;
    }

    public function getUrlService($product)
    {

        $type = $product->getTypeId();
        switch ($type) {
            case 'simple':
                $template = $this->_getApi()->getUrl($product->getId(), null, $product->getFinalPrice(),
                    $product->getTypeId());
                break;
            case 'configurable':
                $template = $this->_getApi()->getUrl($product->getId(), null, $product->getFinalPrice(),
                    $product->getTypeId());
                break;
            case 'bundle':
                $template = $this->_getApi()->getUrl($product, null, $product->getFinalPrice(), 'simple');
                break;
        }
        return $template;
    }

    private function isEnable()
    {
        $isModuleEnable = Mage::getModel('skyboxcore/api_restful')->isModuleEnable();
        if (!$isModuleEnable) {
            return false;
        }

        /** @var Skybox_Core_Helper_Allow $allowHelper */
        $allowHelper = Mage::helper('skyboxcore/allow');

        if (!$allowHelper->isPriceEnabled()) {
            return false;
        }

        return true;
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
        if (!$this->isEnable()) {
            return parent::_beforeToHtml();
        }

        $api_checkout = Mage::getModel('skyboxcheckout/api_checkout');
        $api_checkout->InitializeBarSkybox();

        // $session = Mage::getSingleton("core/session", array("name" => "frontend"));
        // $sky = $session->getData("skyBox");

        $data = $api_checkout->getValueAccessService();

        $skyBoxUrlClient = Mage::helper('skyboxinternational/data')->getSkyBoxUrlAPI();
        $skyBoxUrlClient = $skyBoxUrlClient . ("multiplecalculate");

        $dataJson = null;
        $multiCalculate = 1;

        try {
            $products = $this->_getProductCollection();

            foreach ($products as $prod) {
                $product = Mage::getModel('catalog/product')->load($prod->getId());
                $data['listproducts'][] = $this->getUrlService($product);
                $dataJson = json_encode($data);
            }

            $response = $this->multiCalculatePrice($skyBoxUrlClient, $dataJson);
            // Mage::log(print_r($response, true), null, 'multicalculate.log', true);

        } catch (\Exception $e) {
            $multiCalculate = 0;
            // Mage::log(print_r($dataJson, true), null, 'multicalculate.log', true);
            // Mage::log("[multicalculate] " . $e->getMessage(), null, 'multicalculate.log', true);
        }

        Mage::unregister('skybox_multicalculate');
        Mage::register('skybox_multicalculate', $multiCalculate);
        // Mage::registry('skybox_multicalculate');

        return parent::_beforeToHtml();
    }

    /**
     * @deprecated
     */
    public function getTemplateServicio($objectProduct, $template)
    {
        /*2step execute get button template*/

        /*var_dump($a->getHtmlTemplateButton());exit;*//*3 step call calculate*/;
        if (1) {

            /*$objectProduct = json_decode($objectProduct);
            $objectProduct = json_decode(json_encode($objectProduct), true);*/
            //var_dump($objectProduct);exit;
            foreach ($objectProduct as $key => $value) {
                /*echo "==>".$key."<====";
                echo "==>".$value."<====";exit;-*/
                $template = str_replace('{' . $key . '}', $value, $template);
            }

            // Just for {Block} crap
            $template = str_replace('{Block}', '', $template);
            /*echo "<hit>";
            echo($template); exit;
            echo "</hit>";*/
            /*echo $template; exit;*/
            return $template;
        }
        //exit("debug si es que llega");
        /*var_dump($a); exit;
        var_dump(Mage::getSingleton('skyboxcore/session')); exit;--*/
    }

    /**
     * Return Multi CalculatePrice
     *
     * @param $url
     * @param $data
     * @return mixed
     * @throws Exception
     */
    function multiCalculatePrice($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);

        $response = json_decode($response, true);

        if ($response['StatusCode'] != 'Success') {
            throw new \Exception('Missing or invalid data send to MultiCalculate');
        }
        return $response;
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }

    public function addAttribute($code)
    {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;
    }

    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('catalog/config');
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Block_Product_List
     */
    public function prepareSortableFieldsByCategory($category)
    {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            if ($categorySortBy = $category->getDefaultSortBy()) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve block cache tags based on product collection
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(
            parent::getCacheTags(),
            $this->getItemsTags($this->_getProductCollection())
        );
    }
}
