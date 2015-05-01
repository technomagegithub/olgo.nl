<?php

class Smartwave_Granada_Model_System_Config_Source_Attribute_Effects
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
                array('value' => 'no',              'label' => Mage::helper('granada')->__('No Any Effect')),
                array('value' => 'alt_img',         'label' => Mage::helper('granada')->__('Alternative Image')),
                array('value' => 'listed_img',      'label' => Mage::helper('granada')->__('Product Image List'))
            );
        }
        return $this->_options;
    }
}
