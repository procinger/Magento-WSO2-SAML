<?php

class Hukmedia_Wso2_Model_Adminhtml_System_Config_Source_Nameidformat
{
    /**
     *
     * urn:oasis:names:tc:SAML:2.0:nameid-format:persistent
     * urn:oasis:names:tc:SAML:2.0:nameid-format:transient
     * urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified
     * urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName
     * urn:oasis:names:tc:SAML:1.1:nameid-format:WindowsDomainQualifiedName
     * urn:oasis:names:tc:SAML:2.0:nameid-format:kerberos
     * urn:oasis:names:tc:SAML:2.0:nameid-format:entity
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress', 'label' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress'),
            array('value' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified', 'label' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified'),
            array('value' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient', 'label' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient'),
            array('value' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName', 'label' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName'),
            array('value' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:WindowsDomainQualifiedName', 'label' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:WindowsDomainQualifiedName'),
            array('value' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:kerberos', 'label' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:kerberos'),
            array('value' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:entity', 'label' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:entity'),
        );
    }
}