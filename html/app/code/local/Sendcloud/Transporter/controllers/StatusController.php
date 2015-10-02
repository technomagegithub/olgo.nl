<?php

class Sendcloud_Transporter_StatusController extends Mage_Adminhtml_Controller_Action
{
	
	function indexAction() {
		$params = $this->getRequest()->getParams();
		
		foreach ($_POST['parcels'] as $key => $value) {
			
			$r['html'] = $this->parcelStatus($key);
			$r['selector'] = $value['selector'];
			
			$return[] = $r;
		}
		
		echo json_encode(array('parcels' => $return));
		
		
	}
	
	function parcelStatus($id) {
		if (!empty($id)) {
			$id = $id;
			
			$order = Mage::getModel('sales/order')->load($id);
			
			if ($order) {
				
				$min_diff = 2 * 60;
				// get the unix timestamp of the last time checked
				$last_time = $order->getData('sendcloud_last_check');
				$parcel_id = $order->getData('sendcloud_parcel_id');
				if (time() - $last_time > $min_diff && !empty($parcel_id)) {
					//if so we can preform the status cronjob
					
					$requestModel = Mage::getModel('transporter/request');
					
					$status = $requestModel->statusParcel($parcel_id);
					
					
					
					if ($status && $requestModel->getErrorCode() != 404) {
						
						$parcel_status = $status->status;
						$order->setData('sendcloud_status_id', $parcel_status->status_id);
						
						$status_comment = array();
						$status_comment['status_title'] = $parcel_status->status_title;
						$status_comment['status_icon'] = $parcel_status->status_icon;
						$status_comment['status_comment'] = $parcel_status->status_comment;
						
						
						$order->setData('sendcloud_comment', serialize($status_comment));
						
						
						
						
						$status_id = $parcel_status->status_id;
						$status_comment = $parcel_status->status_comment;
						$status_icon = $parcel_status->status_icon;
						$status_title = $parcel_status->status_title;
						
						$order->setData('sendcloud_last_check', time());
						
						$order->save();
						
						
					}
					else {
						
						$html = $this->_getOrderTransportHtml($order);
					}
					
					
					
				}
				else {
	
					
					if (!empty($parcel_id) ) {
						$status_comment = unserialize($order->getData('sendcloud_comment'));
						
						if (is_array($status_comment)) { 
							$status_icon = $status_comment['status_icon'];
							$status_title = $status_comment['status_title'];
							$status_comment = $status_comment['status_comment'];
							$status_id = $order->getData('sendcloud_status_id');
						}
					}
					else {
						$url = $this->getUrl('transporter/index/parcel', array('id' => $id));
						$html = "<a href='{$url}'>Bestelling overzetten</a>";
					}
					
					
				}
				
				if (isset($status_id)) {
				if (!empty($status_icon)) {
					$html = "<img class='sendcloud_status_image' src='{$status_icon}' />";
					
				}
				else {
					$html = "
					<img class='sendcloud_status_image' src='http://panel.sendcloud.nl/api/status_image/{$status_id}' />
					";
					}?>
					<?php if (!empty($status_title)) { 
					$html .= "<span class='sendcloud_big_title'>{$status_title}</span>";
					} ?>
					<?php if (!empty($status_comment)) {
					$html .= "<span class='sendcloud_comment'>{$status_comment}</span>";
					 } ?>
					
				<?php
				}
				
			}
		}
		
		return $html;
	}
	
	private function _getOrderTransportHtml($order =false) {
		if ($order) {
			$url = $this->getUrl('transporter/index/parcel', array('id' => $order->getId()));
			$html = "<a href='{$url}'>Bestelling overzetten</a>";
			
			return $html;
		}
	}
	
   

}

?>