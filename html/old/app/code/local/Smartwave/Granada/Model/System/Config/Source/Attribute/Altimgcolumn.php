<?php

class Smartwave_Granada_Model_System_Config_Source_Attribute_Altimgcolumn
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
                array('value' => 'label',              'label' => Mage::helper('granada')->__('Label')),
                array('value' => 'position',      'label' => Mage::helper('granada')->__('Sort Order'))
            );
        }
        return $this->_options;
    }
}
