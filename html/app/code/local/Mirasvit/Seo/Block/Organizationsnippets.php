<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.3.0
 * @build     1072
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Seo_Block_Organizationsnippets extends Mage_Core_Block_Template
{
    protected $_config;

    function __construct()
    {
        $this->_config = Mage::getSingleton('seo/config');
    }

    public function isOrganizationSnippets() {
        return $this->_config->isOrganizationSnippetsEnabled();
    }

    public function getName() {
        if ($this->_config->getNameOrganizationSnippets()) {
            $name = $this->_config->getManualNameOrganizationSnippets();
        } else {
            $name = trim(Mage::getStoreConfig('general/store_information/name'));
        }

        if ($name) {
            return "\"name\" : \"$name\",";
        }

        return false;
    }

    public function getCountryAddress() {
       if ($this->_config->getCountryAddressOrganizationSnippets()) {
            $countryAddress = $this->_config->getManualCountryAddressOrganizationSnippets();
        } else {
            $countryAddress = trim(Mage::app()->getLocale()->getCountryTranslation(Mage::getStoreConfig('general/store_information/merchant_country')));
        }

        if ($countryAddress) {
            return "\"addressCountry\": \"$countryAddress\",";
        }

        return false;
    }

    public function getAddressLocality() {
       if ($addressLocality = $this->_config->getManualLocalityAddressOrganizationSnippets()) {
            return "\"addressLocality\": \"$addressLocality\",";
       }

       return false;
    }

    public function getPostalCode() {
       if ($postalCode = $this->_config->getManualPostalCodeOrganizationSnippets()) {
            return "\"postalCode\": \"$postalCode\",";
       }

       return false;
    }

    public function getStreetAddress() {
        if ($this->_config->getStreetAddressOrganizationSnippets()) {
            $streetAddress = $this->_config->getManualStreetAddressOrganizationSnippets();
        } else {
            $streetAddress = trim(Mage::getStoreConfig('general/store_information/address'));
        }

        if ($streetAddress) {
            return "\"streetAddress\": \"$streetAddress\",";
        }

        return false;
    }

    public function getTelephone() {
        if ($this->_config->getTelephoneOrganizationSnippets()) {
            $telephone = $this->_config->getManualTelephoneOrganizationSnippets();
        } else {
            $telephone = trim(Mage::getStoreConfig('general/store_information/phone'));
        }

        if ($telephone) {
            return "\"telephone\": \"$telephone\",";
        }

        return false;
    }

    public function getFaxNumber() {
       if ($faxNumber = $this->_config->getManualFaxnumberOrganizationSnippets()) {
            return "\"faxNumber\": \"$faxNumber\",";
       }

       return false;
    }

    public function getEmail() {
        if ($this->_config->getEmailOrganizationSnippets()) {
            $email = $this->_config->getManualEmailOrganizationSnippets();
        } else {
            $email = trim(Mage::getStoreConfig('trans_email/ident_general/email'));
        }

        if ($email) {
            return "\"email\" : \"$email\",";
        }

        return false;
    }

    public function preparePostalAddress($countryAddress, $addressLocality, $postalCode, $streetAddress) {
        $postalAddress    = $countryAddress . $addressLocality . $postalCode . $streetAddress;
        if ($postalAddress && substr($postalAddress, -1) == ',' ) {
            $postalAddress = substr($postalAddress, 0, -1);
        }

        return $postalAddress;
    }

    public function getLogoUrl() {
        return Mage::getDesign()->getSkinUrl() . Mage::getStoreConfig('design/header/logo_src', Mage::app()->getStore()->getStoreId());
    }
}
