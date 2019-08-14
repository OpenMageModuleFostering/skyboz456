<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product list
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
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
                $template = $this->_getApi()->getUrl($product->getId(), null, $product->getFinalPrice(), $product->getTypeId());
                break;
            case 'configurable':
                $template = $this->_getApi()->getUrl($product->getId(), null, $product->getFinalPrice(), $product->getTypeId());
                break;
            case 'bundle':
                $template = $this->_getApi()->getUrl($product, null, $product->getFinalPrice(), 'simple');
                break;
        }
        return $template;
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {

        /*ini Load all product of catalog*/

        //var_dump($products); exit;
        $session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
        $sky = $session->getData("skyBox");
        /*1 step conexion done*/
        $_checkout = Mage::getModel('skyboxcheckout/api_checkout');
        $_cartDataURL = "";
        $_checkout->InitializeBarSkybox();
        $data = $_checkout->getValueAccessService();

        $skyBoxUrlClient = Mage::helper('skyboxinternational/data')->getSkyBoxUrlAPI();
        $skyBoxUrlClient = $skyBoxUrlClient . ("multiplecalculate");
        //var_dump($skyBoxUrlClient); "https://beta.skyboxcheckout.com/testapi/ApiRest/multiplecalculate"
        $products = $this->_getProductCollection();
        /*call*/
        /*Objeto producto catalogo*/
        foreach($products as $prod) {
            $product = Mage::getModel('catalog/product')->load($prod->getId());
            $data['listproducts'][] =  $this->getUrlService($product);
            $dataJson = json_encode($data);
        }
        $this->_getApi()->HtmlTemplateButton();
        $template = $this->_getApi()->getHtmlTemplateButton();

        $start = microtime(true);
        $resultObjectMultiCurl = $this->file_multi_get_contents_curl($skyBoxUrlClient, $dataJson);
        //var_export($resultObjectMultiCurl);
        $time_elapsed_secs = microtime(true) - $start;
        /*echo "<div style='position: absolute;
background: lightgreen;
font-weight: bolder;
z-index: 1111;'>Tiempo de respuesta (Multiple): {$time_elapsed_secs}</div>"."<br/>";*/

        $dataMultiCurl = array();
        $resultObjectMultiCurl = json_decode($resultObjectMultiCurl);
        $resultObjectMultiCurlArray = json_decode(json_encode($resultObjectMultiCurl), true);

        foreach($resultObjectMultiCurlArray['listCalculateResponse'] as $value) {
            $dataMultiCurl[$value["HtmlObjectId"]] = $this->getTemplateServicio($value, $template);
        }

        /*$dataMultiCurlVal = array();
        $cont = 0;
        foreach($products as $key => $prod) {
            $dataMultiCurlVal[$prod->getId()] = $dataMultiCurl[$cont];
            $cont++;
        }*/
        $session->setData("skyBox", $dataMultiCurl);
        /*end Load all product of catalog*/


        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $this->_getProductCollection()
        ));

        $this->_getProductCollection()->load();

        return parent::_beforeToHtml();
    }

    public function getTemplateServicio($objectProduct,$template)
    {






        /*2step execute get button template*/

        /*var_dump($a->getHtmlTemplateButton());exit;*/

        /*3 step call calculate*/;
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

    function file_multi_get_contents_curl($url, $data)
    {

        /*var_export($data); exit;*/
        if (!function_exists("curl_init")) die("cURL extension is not installed");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);

        /*var_export($response); exit;*/
        // Will dump a beauty json :3
        //var_dump(json_decode($result, true));
        return $response;
        /*
        $node_count = count($nodes);
        $curl_arr = array();
        $master = curl_multi_init();
        //curl_setopt($master, CURLOPT_SSL_VERIFYPEER, false);
        // Will return the response, if false it print the response
        //curl_setopt($master, CURLOPT_RETURNTRANSFER, true);

        for($i = 0; $i < count($nodes); $i++)
        {
            $start = microtime(true);
            $url =$nodes[$i];
            $curl_arr[$i] = curl_init($url);
            curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($master, $curl_arr[$i]);
            $total_time = round(microtime(true)-$start, 4);
            echo "Request #".($i+1).": send {$start} ; delay: {$total_time}"."<br/>";
        }

        $start = microtime(true);
        do
        {
            $startMultiExec = microtime(true);
            $curl_multi_exec = curl_multi_exec($master,$running);
            $totalMultiExec = round(microtime(true)-$startMultiExec, 4);
            //echo "Request MultiExec: send {$startMultiExec} ; delay: {$totalMultiExec}"."<br/>";
        }
        while($running > 0);
        $total_time = round(microtime(true)-$start, 4);
        echo "Request All: send {$start} ; delay: {$total_time}"."<br/>";

        echo 'results: '."<br/>";
        $results = array();
        for($i = 0; $i < $node_count; $i++)
        {
            $preresult = curl_multi_getcontent ( $curl_arr[$i] );
            $results[] = $preresult;
            echo "<pre>".var_export($results)."</pre>";
            echo( $i . '\n' . $results . '\n');
        }
        echo "<pre>".var_dump($results)."</pre>";

        echo 'done'."<br/>";*/
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
    public function prepareSortableFieldsByCategory($category) {
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
