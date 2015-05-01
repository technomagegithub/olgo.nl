<?php

class Smartwave_Megamenu_Model_Category_Attribute_Source_Type_Position
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
                array('value' => 'left',        'label' => 'Left'),
                array('value' => 'right',       'label' => 'Right')       
            );
        }
        return $this->_options;
    }
}
