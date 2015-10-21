<?php


class Sendcloud_Transporter_MassController extends Mage_Adminhtml_Controller_Action

{
	
	function shipAction() {
		$post = $this->getRequest()->getPost();
		
		$order_ids = $post['order_ids'];
		
		$shipModel = Mage::getModel('transporter/massaction_ship');
		
		foreach ($order_ids as $id) {
			$order = Mage::getModel('sales/order')->load($id);
			
			$orders[] = $order;
		}
		
		try {
			$shipModel->shipOrders($orders);
			$this->handleSuccess('Orders zijn met success omgezet naar een shipment');
		}
		catch (Exception $e) {
			$this->handleError($e);
		}
		
		Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order"));
		
	}
	
	function handleSuccess($message) {
		Mage::getSingleton('core/session')->addSuccess($message);
	}
	
	private function handleError($exception) {
		
		$error = "Omzetten van orders naar verzending is niet gelukt:<br />
		" . $exception->getMessage() . "";
		
		
		Mage::getSingleton('core/session')->addError($error);
	}
}