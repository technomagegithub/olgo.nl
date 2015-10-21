<?php



class Sendcloud_Transporter_Model_Request 
{
	
	/*
	@var $wrapper
	@class Sendcloud_Transporter_Model_Request_Wrapper
	*/
	protected $wrapper;
	
	function __construct() {
		
		// get config transporter
		$transporterConfig = Mage::getStoreConfig(
                   'sendcloud/transporter_api',
                   Mage::app()->getStore()
               ); 
		
		// setting keys into variables 
		$api_key = $transporterConfig['api_key'];
		$secret_key = $transporterConfig['secret_key'];
		$enviroment = $transporterConfig['enviroment'];
		
		try {
			// get the wrapper
			$wrapper = new Sendcloud_Transporter_Model_Request_Wrapper($enviroment, $api_key, $secret_key);
			$this->wrapper = $wrapper;
		}
		catch (Exception $e) {
			echo "No public key and Secret key found";
		}
		
	}
	
	function transport($orders = null)
	{
		
		
		
		if ($orders) {
			$parcels = array();
			
			foreach ($orders as $order) {
				$parcels[] = $this->getParcelFromOrder($order);
				
			}
			
			
			try {
				$parcels = $this->wrapper->parcels->create_bulk($parcels);
				
				$this->_fillParcelInformation($parcels);
				
			}
			catch(Exception $e) {
				throw $e;
			}
			
		}
		
	}
	
	function _fillParcelInformation($parcels) {
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$table = $resource->getTableName('sales/order');
		foreach ($parcels as $parcel) {
			$id = $parcel['reference'];
			$fields = array();
			$fields['sendcloud_parcel_id'] = $parcel['id'];
			$writeConnection->beginTransaction();
			$writeConnection->update($table, $fields, 'entity_id = '. (int)$id);
			$writeConnection->commit();

			$order = Mage::getModel('sales/order')->load($id);
			if ($order) {
				$order = $this->_changeOrderStatus($order);
//				$order->setData('sendcloud_parcel_id', $parcel['id']);
				$order->save();
			}
		}
	}
	
	function _changeOrderStatus($order) {
		// get config transporter
		$statusConfig = Mage::getStoreConfig(
                   'sendcloud/sendcloud_automate_status',
                   Mage::app()->getStore()
               ); 
		
		// setting keys into variables 
		$enabled = $statusConfig['enabled'];
		$status = $statusConfig['status'];
		
		
		if ($enabled) {
			
			switch($status) {
				case 'pending':
				/**
				* change order status to 'Pending'
				*/
				$order->setState(Mage_Sales_Model_Order::STATE_NEW, true)->save();
				break;
				case 'pending_paypal':
				
				/**
				* change order status to 'Pending Paypal'
				*/
				$order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true)->save();
				break;
				
				case 'processing':
				
				/**
				* change order status to 'Processing'
				*/
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
				break;
				
				case 'complete':
				/**
				* change order status to 'Complete'
				*/
				$order->setData('state', "complete");
				$order->setStatus("complete");
				
				$order->save();
				
				break;
				case 'closed':
				/**
				* change order status to 'Closed'
				*/
				$state = Mage_Sales_Model_Order::STATE_CLOSED;
				$order->setData('state', $state);
				$order->save();
				break;
				case 'canceled':
				/**
				* change order status to 'Canceled'
				*/
				$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
				break;
				
				case 'holded':
				/**
				* change order status to 'Holded'
				*/
				$order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true)->save();
				break;
	
			}
			
			$history = $order->addStatusHistoryComment('Order status automatisch veranderd door SendCloud Transporter', false);
			$history->setIsCustomerNotified(false);
			
		
		}
		
		
		return $order;
	}

	
	function getParcelFromOrder($order) {

		
		// Get the id of the orders shipping address
		$shippingId = $order->getShippingAddress()->getId();
	
		// Get shipping address data using the id
		$address = $order->getShippingAddress();
		
		$object = new Sendcloud_Transporter_Model_Parcel;
		
		$object->set('name', $address->getName());
		$object->set('company_name', $address->getCompany());
		$object->set('address', $address->getStreet(1));
		$object->set('address_2', $address->getStreet(2));
		$object->set('city', $address->getCity());
		$object->set('postal_code', $address->getPostcode());
		$object->set('telephone', $address->getTelephone());
		$object->set('country', Mage::getModel('directory/country')->load($address->getCountry())->getIso2Code());
		$object->set('order_number', $order->getRealOrderId());
		$object->set('email', $address->getEmail());
		$object->set('reference', $order->getId());
		
		
		$dataObject = new Sendcloud_Transporter_Model_Parcel_Data;
		
		$event = Mage::dispatchEvent('sendcloud_transporter_data', array('object' => $object, 'data_model' => $dataObject, 'order' => $order ));
		
	
		
		$object->set('data', $dataObject->toArray());
		
		
		// check if email is setted. If not. Get it with a alternative way
		if (!$object->get('email')) {
			$object->set('email', $order->getCustomerEmail());
		}
		
		$return = $object->toArray();
		
		

		return $return;
	}
	
}







?>