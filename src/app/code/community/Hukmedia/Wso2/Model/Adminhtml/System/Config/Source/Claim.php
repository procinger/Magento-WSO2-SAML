<?php

class Hukmedia_Wso2_Model_Adminhtml_System_Config_Source_Claim extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_customerAttributeSelectRenderer;
    protected $_isRequiredAttributeCheckboxRenderer;
    protected $_attributeNameFormatSelectRenderer;

    public function _prepareToRender() {
        $this->addColumn('wso2_attribute_is_required', array(
            'label' => $this->helper('hukmedia_wso2')->__('Required'),
            'index' => ' is_required',
            'renderer' => $this->_getAttributeIsRequiredRenderer()
        ));

        $this->addColumn('local_customer_attribute', array(
            'label' => $this->helper('hukmedia_wso2')->__('Local customer attribute'),
            'index' => 'local_attribute',
            'renderer' => $this->_getCustomerAttributeSelectRenderer(),
            'style' => "width: 450px"
        ));

        $this->addColumn('wso2_customer_attribute', array(
            'label' => $this->helper('hukmedia_wso2')->__('WSO2 claim attribute'),
            'index' => 'remote_attribute',
            'style' => 'width: 100px'
        ));

        $this->addColumn('wso2_attribute_name_format', array(
            'label' => $this->helper('hukmedia_wso2')->__('Attribute name format'),
            'index' => 'attribute_name_format',
            'renderer' => $this->_getAttributeNameFormatSelectRenderer()

        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = $this->helper('hukmedia_wso2')->__('Add');
    }

    protected function _getCustomerAttributeSelectRenderer() {
        if(!$this->_customerAttributeSelectRenderer) {
            $this->_customerAttributeSelectRenderer = $this->getLayout()
                ->createBlock('hukmedia_wso2/config_adminhtml_form_field_customer_attributes')
                ->setIsRenderToJsTemplate(true);
        }

        return $this->_customerAttributeSelectRenderer;

    }

    protected function _getAttributeIsRequiredRenderer() {
        if(!$this->_isRequiredAttributeCheckboxRenderer) {
            $this->_isRequiredAttributeCheckboxRenderer = $this->getLayout()
                ->createBlock('hukmedia_wso2/config_adminhtml_form_field_customer_checkbox')
                ->setIsRenderToJsTemplate(true);
        }

        return $this->_isRequiredAttributeCheckboxRenderer;
    }

    protected function _getAttributeNameFormatSelectRenderer() {
        if(!$this->_attributeNameFormatSelectRenderer) {
            $this->_attributeNameFormatSelectRenderer = $this->getLayout()
                ->createBlock('hukmedia_wso2/config_adminhtml_form_field_customer_attributenameformat')
                ->setIsRenderToJsTemplate(true);
        }

        return $this->_attributeNameFormatSelectRenderer;

    }

    protected function _prepareArrayRow(Varien_Object $row) {
        $row->setData(
            'option_extra_attr_' . $this->_getCustomerAttributeSelectRenderer()->calcOptionHash($row->getData('local_customer_attribute')),
            'selected="selected"'
        );

        $row->setData(
            'option_extra_attr_' . $this->_getAttributeIsRequiredRenderer()->calcOptionHash($row->getData('wso2_attribute_is_required')),
            'checked="checked"'
        );

        $row->setData(
            'option_extra_attr_' . $this->_getAttributeNameFormatSelectRenderer()->calcOptionHash($row->getData('wso2_attribute_name_format')),
            'selected="selected"'
        );
    }

}