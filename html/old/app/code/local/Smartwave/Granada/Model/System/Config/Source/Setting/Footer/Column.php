<?php

class Smartwave_Granada_Model_System_Config_Source_Setting_Footer_Column
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('granada')->__('Do not show')),
            array('value' => 'custom', 'label' => Mage::helper('granada')->__('Static Block')),
            array('value' => 'sale_products', 'label' => Mage::helper('granada')->__('Sale Products')),
            array('value' => 'best_seller', 'label' => Mage::helper('granada')->__('Best Seller Products')),
            array('value' => 'new_products', 'label' => Mage::helper('granada')->__('New Products')),
            array('value' => 'latest_products', 'label' => Mage::helper('granada')->__('Latest Products')),
            array('value' => 'flickr', 'label' => Mage::helper('granada')->__('Photos Flickr')),
            array('value' => 'twitter', 'label' => Mage::helper('granada')->__('Twitter')),
            array('value' => 'facebook', 'label' => Mage::helper('granada')->__('Facebook')),
            array('value' => 'blog', 'label' => Mage::helper('granada')->__('Recent Posts'))
        );
    }
}