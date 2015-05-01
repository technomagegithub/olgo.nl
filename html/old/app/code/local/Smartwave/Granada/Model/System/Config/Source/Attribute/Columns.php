<?php

class Smartwave_Granada_Model_System_Config_Source_Attribute_Columns
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
				array('value' => '0',		'label' => 'Default'),
                array('value' => '2',       'label' => '2'),
                array('value' => '3',       'label' => '3'),
                array('value' => '4',       'label' => '4'),
                array('value' => '5',       'label' => '5'),
                array('value' => '6',       'label' => '6')
            );
        }
        return $this->_options;
    }
}
