<?php

class Smartwave_Granada_Model_System_Config_Source_Setting_Slider_Position
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'bottom', 'label' => Mage::helper('granada')->__('Bottom')),
            array('value' => 'top', 'label' => Mage::helper('granada')->__('Top'))
        );
    }
}