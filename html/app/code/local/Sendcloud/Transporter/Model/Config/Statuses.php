<?php

class Sendcloud_Transporter_Model_Config_Statuses {
	
    public function toOptionArray()
    {
		
		$statuses = Mage::getModel('sales/order_status')  
        ->getCollection();
		
		$return = array();
		
		foreach ($statuses as $status) {
			$return[] = array('value' => $status->getId(), 'label' => Mage::helper('transporter/data')->__($status->getStoreLabel()));
			
			
			
		}
	
		return $return;
	}


	
}