<?php
/**
 * Skybox Catalog
 *
 * @category    Skybox
 * @package     Skybox_Catalog
 * @copyright   Copyright (c) 2014 Skybox Checkout. (http://www.skyboxcheckout.com)
 */

/**
 * Admin form widget
 *
 * @category   Skybox
 * @package    Skybox_Catalog
 */
class Skybox_Catalog_Block_Catalog_Category_Tab_Skyboxcheckout extends Mage_Adminhtml_Block_Widget_Form
{
    public $_commodities = null;

    private function _getCommoditiesData()
    {
        if ($this->_commodities == null) {

            /* var @api_checkout Skybox_Checkout_Model_Api_Checkout*/
            $api_checkout = Mage::getModel('skyboxcheckout/api_checkout');
            $categories = $api_checkout->GetCategories();

            $options = array('' => '');

            foreach ($categories as $obj) {
                $item = array(
                    $obj->IdCommoditie => $obj->Description,
                );
                //$options = array_merge($options, $item);
                $options = array_replace($options, $item);
            }
            $this->_commodities = $options;
        }
        return $this->_commodities;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('category');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        // Form 1
        // -----------------------------------------------------------------

        $fieldset = $form->addFieldset('form1_fieldset',
            array('legend' => Mage::helper('catalog')->__('For New Products'))
        );

        if ($model->getId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $options = $this->_getCommoditiesData();

        $fieldset->addField('skybox_category_id', 'select', array(
            'name' => 'skybox_category_id',
            'label' => Mage::helper('catalog')->__('Default commodity'),
            'title' => Mage::helper('catalog')->__('Default commodity'),
            //'note' => Mage::helper('catalog')->__('Custom Attribute note'),
            'values' => $options
        ));

        // Form 2
        // -----------------------------------------------------------------

        $fieldset = $form->addFieldset('form2_fieldset',
            array('legend' => Mage::helper('catalog')->__('For Existing Products in Category')));

        // Buttons
        $buttons = ' <br/>';
        $buttons .= '<br/><button type="button" onclick = "categorySubmit2(\'' . $this->getSaveUrl() . '\', true)" > Apply All Products </button>';
        $buttons .= '<br/><br/><button type="button" onclick = "categorySubmit3(\'' . $this->getSaveUrl() . '\', true)" > Apply All Products without Commodities </button>';

        $fieldset->addField('skybox_category_id_select', 'select', array(
            'name' => 'skybox_category_id_select',
            'label' => Mage::helper('catalog')->__('Select commodity'),
            'title' => Mage::helper('catalog')->__('Select commodity'),
            'values' => $options,
            'after_element_html' => $buttons
        ));

        $fieldset->addField('apply_button', 'hidden', array(
            'name' => 'apply_button',
        ));

        //Mage::log("category->getData()", null, 'skyboxcheckout.log', false);
        //Mage::log(print_r($model->getData(), true), null, 'skyboxcheckout.log', false);

        // Set Values
        $values = Mage::registry('category')->getData();
        $values['apply_button'] = 'nothing';

        $form->setValues($values);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'apply' => Mage::getConfig()->getBlockClassName('adminhtml / catalog_product_helper_form_apply')
        );
    }

    public function getSaveUrl(array $args = array())
    {
        //$params = array('_current' => true);
        //$params = array_merge($params, $args);
        $params = array();
        return $this->getUrl('*/*/save', $params);
    }

}