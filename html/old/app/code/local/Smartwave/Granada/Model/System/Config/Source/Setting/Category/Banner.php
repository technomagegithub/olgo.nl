<?php

class Smartwave_Granada_Model_System_Config_Source_Setting_Category_Banner
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'full', 'label' => Mage::helper('granada')->__('Full Width')),            
            array('value' => 'wide', 'label' => Mage::helper('granada')->__('Wide'))
        );
    }
}