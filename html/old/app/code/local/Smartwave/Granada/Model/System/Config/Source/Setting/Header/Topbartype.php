<?php

class Smartwave_Granada_Model_System_Config_Source_Setting_Header_Topbartype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'none', 'label' => Mage::helper('granada')->__('None')),
            array('value' => 'top_type_1', 'label' => Mage::helper('granada')->__('Type1')),
            array('value' => 'top_type_2', 'label' => Mage::helper('granada')->__('Type2')),
            array('value' => 'top_type_3', 'label' => Mage::helper('granada')->__('Type3'))            
        );
    }
}