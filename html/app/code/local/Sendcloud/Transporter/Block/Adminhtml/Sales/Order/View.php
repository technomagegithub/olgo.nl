<?php
class Sendcloud_Transporter_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View {
    public function  __construct() {

        parent::__construct();
		
		$order = $this->getOrder();
		$url = $this->getUrl('sendcloud/index/parcel', array('id'=>$order->getId(), 'redirect' => 'order_view'));
		$onclick = "setLocation('$url');";
		
        $this->_addButton('sendtosendcloud', array(
            'label'     => Mage::helper('sales')->__('Doorsturen naar Sendcloud'),
            'onclick'   => $onclick,
            'class'     => 'go'
        ), 0, 100, 'header', 'header');
    }
}
?>