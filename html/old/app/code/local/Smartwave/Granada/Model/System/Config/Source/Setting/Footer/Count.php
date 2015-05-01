<?php

class Smartwave_Granada_Model_System_Config_Source_Setting_Footer_Count
{
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label' => Mage::helper('granada')->__('1 Product')),
            array('value' => '2', 'label' => Mage::helper('granada')->__('2 Products')),
            array('value' => '3', 'label' => Mage::helper('granada')->__('3 Products'))
        );
    }
}