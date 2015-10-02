<?php

class Sendcloud_Transporter_Model_Config_Enviroment {
	
    public function toOptionArray()
    {
		
		
		
		$return = array(
			array( 'value' => 'live', 'label' => 'Panel SendCloud'),
			array('value' => 'test', 'label' => 'Demo Panel SendCloud')
		);
		
		
		
		return $return;
	}


	
}