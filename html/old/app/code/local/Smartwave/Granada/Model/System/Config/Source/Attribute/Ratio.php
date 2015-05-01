<?php

class Smartwave_Granada_Model_System_Config_Source_Attribute_Ratio
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{    
    /**
     * Get list of product image effect proportions
     */
    public function getAllOptions()
    {
        if (!$this->_options)
        {
            $this->_options = array(
                array('value' => '0',               'label' => Mage::helper('granada')->__('Default')),
                array('value' => 'yes',              'label' => Mage::helper('granada')->__('Yes')),
                array('value' => 'no',      'label' => Mage::helper('granada')->__('No'))
            );
        }
        return $this->_options;
    }
}
