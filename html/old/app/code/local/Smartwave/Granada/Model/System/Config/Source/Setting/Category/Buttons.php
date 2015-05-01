<?php

class Smartwave_Granada_Model_System_Config_Source_Setting_Category_Buttons
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'grid-type-1', 'label' => Mage::helper('granada')->__('Type 1')),
            array('value' => 'grid-type-2', 'label' => Mage::helper('granada')->__('Type 2'))
        );
    }
}