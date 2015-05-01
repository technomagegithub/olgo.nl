<?php

class Smartwave_Granada_Model_System_Config_Source_Setting_Category_Effects
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'no', 'label' => Mage::helper('granada')->__('No Any Effect')),
            array('value' => 'alt_img', 'label' => Mage::helper('granada')->__('Alternative Image')),            
            array('value' => 'listed_img', 'label' => Mage::helper('granada')->__('Product Image List'))
        );
    }
}