<?php

class Smartwave_Granada_Model_System_Config_Source_Setting_Category_Altimgcolumn
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'label', 'label' => Mage::helper('granada')->__('Label')),            
            array('value' => 'position', 'label' => Mage::helper('granada')->__('Sort Order'))
        );
    }
}