<?php

class Hukmedia_Wso2_Model_Adminhtml_System_Config_Source_Claim extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_customerAttributeSelectRenderer;

    public function _prepareToRender() {
        $this->addColumn('local_customer_attribute', array(
            'label' => $this->helper('hukmedia_wso2')->__('Local customer attribute'),
            'renderer' => $this->_getCustomerAttributeSelectRenderer()
        ));

        $this->addColumn('wso2_customer_attribute', array(
            'label' => $this->helper('hukmedia_wso2')->__('WSO2 customer attribute'),
            'style' => ''
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

    protected function _prepareArrayRow(Varien_Object $row) {
        $row->setData(
            'option_extra_attr_' . $this->_getCustomerAttributeSelectRenderer()->calcOptionHash($row->getData('local_customer_attribute')),
            'selected="selected"'
        );
    }

}