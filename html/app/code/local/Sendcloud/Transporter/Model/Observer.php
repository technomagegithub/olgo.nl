<?php

class Sendcloud_Transporter_Model_Observer
{
   
	public function addMassAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
		
        if(get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction' && $block->getRequest()->getControllerName() == 'sales_order')
        {
			
            $block->addItem('sendcloud', array(
                'label' => 'Doorsturen naar Sendcloud',
                'url' => Mage::app()->getStore()->getUrl('sendcloud/index/bulk'),
            ));
			$block->addItem('sendcloud/mass_ship', array(
				'label' => 'Orders naar verzending omzetten',
				'url' => Mage::app()->getStore()->getUrl('sendcloud/mass/ship')
			));
        }
    }
	
	function checkOrderStatus($evt) {
		$order = $evt->getOrder();
		
		$automateTransport =  Mage::getStoreConfig(
                   'sendcloud/transporter_automate_transport',
                   Mage::app()->getStore()
               ); 
		
		// setting keys into variables 
		$enabled = $automateTransport['enabled'];
		$status_listener = $automateTransport['status'];
		if ($enabled) {
			if ($order) {
				
				/**
				 * This ugly code below is to make sure that only one request gets send out, otherwise
				 * two labels are put into SendCloud.nl
				 */
				$resource = Mage::getSingleton('core/resource');
				$readConnection = $resource->getConnection('core_read');
				$table = $resource->getTableName('sales/order');
				$order_id = $order->getId();

				$status = $readConnection->fetchOne('SELECT sendcloud_status_id FROM ' . $table . ' WHERE entity_id = '
					. (int)$order_id . ' LIMIT 1');
				$parcel_id = $readConnection->fetchOne('SELECT sendcloud_parcel_id FROM ' . $table . ' WHERE entity_id = '
					. (int)$order_id . ' LIMIT 1');

				if(!$parcel_id && (is_null($status) || $status == 0))
				{
					$fields = array();
					$fields['sendcloud_status_id'] = 1;
					$writeConnection = $resource->getConnection('core_write');

					$writeConnection->query('LOCK TABLES '.$table.' WRITE;');
					if($writeConnection->update($table, $fields, 'entity_id = '. (int)$order_id . ' and (sendcloud_status_id = 0 or sendcloud_status_id is null)') == 1) {
						$writeConnection->query('UNLOCK TABLES;');

						$parcel_id = $readConnection->fetchOne('SELECT sendcloud_parcel_id FROM ' . $table . ' WHERE entity_id = '
							. (int)$order_id . ' LIMIT 1');
						if (!$parcel_id && $order->getStatus() == $status_listener) {
							$orders = array($order);
							$requestModel = Mage::getModel('transporter/request');
							try {
								$requestModel->transport($orders);
							} catch (Exception $e) {
								$this->_handleErrors($e);
							}
						}

						$writeConnection->query('UPDATE ' . $table . ' SET sendcloud_status_id = 0 WHERE entity_id = ' . (int)$order_id . ' LIMIT 1');
					}
				}
				
			}
		}
	}
	
	private function _handleErrors($exception) {
		
		$error = "Sendcloud Transporter Automatisch doorsturen gestaakt:
		<li>" . $exception->getMessage() . "</li>";
		
		
		Mage::getSingleton('core/session')->addError($error);
	}
	
}