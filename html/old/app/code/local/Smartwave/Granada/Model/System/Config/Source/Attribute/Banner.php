<?php

class Smartwave_Granada_Model_System_Config_Source_Attribute_Banner
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{    
    /**
     * Get list of available block column proportions
     */
    public function getAllOptions()
    {
        if (!$this->_options)
        {
            $this->_options = array(
                array('value' => '0',     'label' => 'Default'),
                array('value' => 'full',        'label' => 'Full Width'),
                array('value' => 'wide',       'label' => 'Wide')
            );
        }
        return $this->_options;
    }
}
