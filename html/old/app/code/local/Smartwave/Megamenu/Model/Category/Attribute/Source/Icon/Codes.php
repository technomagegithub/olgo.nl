<?php

class Smartwave_Megamenu_Model_Category_Attribute_Source_Icon_Codes
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
                array('value' => '',        'label' => ' '),
                array('value' => 'e000',        'label' => 'e000'),
                array('value' => 'e001',        'label' => 'e001'),
                array('value' => 'e002',        'label' => 'e002'),
                array('value' => 'e003',        'label' => 'e003'),
                array('value' => 'e004',        'label' => 'e004'),
                array('value' => 'e005',        'label' => 'e005'),
                array('value' => 'e006',        'label' => 'e006'),
                array('value' => 'e007',        'label' => 'e007'),
                array('value' => 'e008',        'label' => 'e008'),
                array('value' => 'e009',        'label' => 'e009'),
                array('value' => 'e00a',        'label' => 'e00a'),
                array('value' => 'e00b',        'label' => 'e00b'),
				array('value' => 'e00c',        'label' => 'e00c'),
                array('value' => 'e00d',        'label' => 'e00d'),
                array('value' => 'e00e',        'label' => 'e00e'),
                array('value' => 'e00f',        'label' => 'e00f'),
                array('value' => 'e010',        'label' => 'e010'),
                array('value' => 'e011',        'label' => 'e011'),
                array('value' => 'e012',        'label' => 'e012'),
                array('value' => 'e013',        'label' => 'e013'),
                array('value' => 'e014',        'label' => 'e014'),
                array('value' => 'e015',        'label' => 'e015'),
                array('value' => 'e016',        'label' => 'e016'),
                array('value' => 'e017',        'label' => 'e017'),
				array('value' => 'e018',        'label' => 'e018'),
                array('value' => 'e019',        'label' => 'e019'),
                array('value' => 'e01a',        'label' => 'e01a'),
                array('value' => 'e01b',        'label' => 'e01b'),
                array('value' => 'e01c',        'label' => 'e01c'),
                array('value' => 'e01d',        'label' => 'e01d'),
                array('value' => 'e01e',        'label' => 'e01e'),
                array('value' => 'e01f',        'label' => 'e01f'),
                array('value' => 'e020',        'label' => 'e020'),
                array('value' => 'e021',        'label' => 'e021'),
                array('value' => 'e022',        'label' => 'e022'),
                array('value' => 'e023',        'label' => 'e023'),
				array('value' => 'e024',        'label' => 'e024'),
				array('value' => 'e025',        'label' => 'e025'),
				array('value' => 'e026',        'label' => 'e026'),
				array('value' => 'e027',        'label' => 'e027'),
				array('value' => 'e028',        'label' => 'e028'),
				array('value' => 'e029',        'label' => 'e029')
            );
        }
        return $this->_options;
    }
}
