<?php

class Smartwave_Granada_Model_System_Config_Source_Setting_Page_Style
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'wide', 'label' => Mage::helper('granada')->__('Wide Version')),
            array('value' => 'boxed', 'label' => Mage::helper('granada')->__('Boxed Version'))
        );
    }
}