<?php

class Sendcloud_Transporter_IndexController extends Mage_Adminhtml_Controller_Action
{
	
	
	protected $parcels = array();
	protected $oders = array();
    
	
	
    public function bulkAction() {
		
		$post = $this->getRequest()->getPost();
		
		$order_ids = $post['order_ids'];
		
		
		$orders = array();
		
		foreach ($order_ids as $id) {
			$order = Mage::getModel('sales/order')->load($id);
			
			$orders[] = $order;
		}
		
		
		$requestModel = Mage::getModel('transporter/request');
		
		
		try {
			
			$requestModel->transport($orders);
			
			$this->_handleSuccess();
			
		}
		catch (Exception $e) {
			$this->_handleErrors($e);
			
		}
		
		Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order"));
		
		
	}
	
	private function _handleErrors($exception) {
		
		$error = "Doorsturen van de bestelling is gestaakt. De volgende fout is gevonden:
		<li>" . $exception->getMessage() . "</li>";
		
		
		Mage::getSingleton('core/session')->addError($error);
	}
	
	private function _handleSuccess() {
		$success = 'Bestellingen zijn met succes overgezet naar SendCloud!';
		Mage::getSingleton('core/session')->addSuccess($success);
	}
	
	
	public function parcelAction() {
		$params = $this->getRequest()->getParams();
		
		if ($params['id']) { 
			
			$id = $params['id'];
			
			$requestModel = Mage::getModel('transporter/request');
			$order = Mage::getModel('sales/order')->load($id);
			
			
			$orders = array($order);
			
			try {
				$requestModel->transport($orders);
				$this->_handleSuccess();
			}
			catch (Exception $e) {
				$this->_handleErrors($e);
			}
			
			
			
			switch($params['redirect']) {
				case 'order_view':
				$redirect = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view", array('order_id' => $params['id']));
				break;
				default:
				$redirect = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
				break;
			}
		
		}
		
		
		Mage::app()->getResponse()->setRedirect($redirect);
		
		
	}
	
	
	

	
	

}

?>