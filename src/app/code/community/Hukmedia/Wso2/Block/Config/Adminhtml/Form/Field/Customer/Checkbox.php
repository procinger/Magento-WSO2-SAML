<?php

class Hukmedia_Wso2_Block_Config_Adminhtml_Form_Field_Customer_Checkbox extends Mage_Core_Block_Abstract {

    /**
     * Set element's HTML ID
     *
     * @param string $id ID
     * @return Mage_Core_Block_Html_Select
     */
    public function setId($id) {
        $this->setData('id', $id);
        return $this;
    }

    /**
     * Set element's CSS class
     *
     * @param string $class Class
     * @return Mage_Core_Block_Html_Select
     */
    public function setClass($class) {
        $this->setData('class', $class);
        return $this;
    }

    /**
     * Set element's HTML title
     *
     * @param string $title Title
     * @return Mage_Core_Block_Html_Select
     */
    public function setTitle($title) {
        $this->setData('title', $title);
        return $this;
    }

    /**
     * HTML ID of the element
     *
     * @return string
     */
    public function getId() {
        return $this->getData('id');
    }

    /**
     * CSS class of the element
     *
     * @return string
     */
    public function getClass() {
        return $this->getData('class');
    }

    /**
     * Returns HTML title of the element
     *
     * @return string
     */
    public function getTitle() {
        return $this->getData('title');
    }

    /**
     * Render HTML
     *
     * @return string
     */
    public function _toHtml() {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        $checkedHtml = ' #{option_extra_attr_' . self::calcOptionHash(1) . '}';

        $html = '<input type="hidden" ';
        $html .= 'name="' . $this->getName() . '" ';
        $html .= 'id="' . $this->getId() . '"';
        $html .= 'value="0" ';
        $html .= 'class="'. $this->getClass() .'"';
        $html .=  '/>';

        $html .= '<input type="checkbox" ';
        $html .= 'name="' . $this->getName() . '" ';
        $html .= 'id="' . $this->getId() . '"';
        $html .= 'value="1" ';
        $html .= 'class="'. $this->getClass() .'"';
        $html .=  $checkedHtml . '/>';
        return $html;

    }

    /**
     * Calculate CRC32 hash for option value
     *
     * @param string $optionValue Value of the option
     * @return string
     */
    public function calcOptionHash($optionValue)
    {
        return sprintf('%u', crc32($this->getName() . $this->getId() . $optionValue));
    }

    public function setInputName($value) {
        return $this->setName($value);
    }

}