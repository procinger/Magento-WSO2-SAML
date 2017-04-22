<?php

class Hukmedia_Wso2_Block_Config_Adminhtml_Form_Field_Customer_Attributes extends Mage_Core_Block_Html_Select
{
    protected function _toHtml() {
        $addressForm = Mage::getSingleton('customer/form');
        $addressForm->setFormCode('customer_account_create');
        $attributes = $addressForm->getAttributes();

        foreach($attributes as $option) {
            $this->addOption($option->getAttributeCode(), $option->getFrontendLabel());
        }

        return parent::_toHtml();
    }

    public function setInputName($value) {
        return $this->setName($value);
    }
}