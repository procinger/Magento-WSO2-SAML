<?php

class Hukmedia_Wso2_Block_Config_Adminhtml_Form_Field_Customer_Attributenameformat extends Mage_Core_Block_Html_Select
{
    protected $_extraParams = 'style="width: 350px"';

    protected function _toHtml() {
        $attributeNameFormatArray = Mage::helper('hukmedia_wso2/config')->getAttributeNameFormat();

        foreach($attributeNameFormatArray as $attribute) {
                $this->addOption($attribute['value'], $attribute['label']);
        }

        return parent::_toHtml();
    }

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function getExtraParams() {
        return $this->_extraParams;
    }
}