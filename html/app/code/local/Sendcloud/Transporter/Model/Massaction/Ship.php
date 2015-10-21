<?php

class Sendcloud_Transporter_Model_Massaction_Ship {
	
	protected $errors;
	 
        
    /*
	Function to ship orders in mass action.
	*/
    public function shipOrders($orders)
    {
        
        
        $numAffectedOrders = 0;
		$comment = "Shipment created";
       
        
      
        
        foreach ($orders as $order){
           
		   
		   	// get config transporter
			$bulkConfig = Mage::getStoreConfig(
					   'sendcloud/transporter_shipped_bulk',
					   Mage::app()->getStore()
				   ); 
			if ($bulkConfig['notification'] == true) {	   
		    	$email = true; 
			}
			else {
				$email = false; 
			}
			
			
			
			$trackingNum = '';
			
			$includeComment = false;
			$comment = "Order Completed And Shipped Automatically via Sendcloud Transporter";
	 
			
			$convertor = Mage::getModel('sales/convert_order');
			$shipment = $convertor->toShipment($order);
	 
			foreach ($order->getAllItems() as $orderItem) {
				
				if (!$orderItem->getQtyToShip()) {
					continue;
				}
				if ($orderItem->getIsVirtual()) {
					continue;
				}
				$item = $convertor->itemToShipmentItem($orderItem);
				$qty = $orderItem->getQtyToShip();
				$item->setQty($qty);
				$shipment->addItem($item);
			}
	 
			
	 
			$data = array();
			$data['carrier_code'] = 'postnl';
			$data['title'] = 'PostNL';
			$data['number'] = '';
	 
			$track = Mage::getModel('sales/order_shipment_track')->addData($data);
			$shipment->addTrack($track);
	 
			$shipment->register();
			$shipment->addComment($comment, $email && $includeComment);
			
			
			$shipment->setEmailSent(true);
			$shipment->getOrder()->setIsInProcess(true);
	 
			$transactionSave = Mage::getModel('core/resource_transaction')
				->addObject($shipment)
				->addObject($shipment->getOrder())
				->save();
	 
			$shipment->sendEmail($email, ($includeComment ? $comment : ''));
			$order->setStatus('Complete');
			$order->addStatusToHistory($order->getStatus(), 'Order completed, automated with Sendcloud Transporter', false);
			
			$shipment->save();
        }
        
        
              
        
        return true; 
    }
	
	
	function getErrors() {
		return $this->errors;
	}
    
     
	
}