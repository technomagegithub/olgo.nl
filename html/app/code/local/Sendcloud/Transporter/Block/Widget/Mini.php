<?php

class Sendcloud_Transporter_Block_Widget_Mini extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
     public function render(Varien_Object $row)
    {
		
		
		$loading_info = '<img width="20" height="20" src="' . $this->getSkinUrl('images/ajax-loader-tr.gif') . '" alt="' . $this->__('Loading...') . '"/>';
		
		$url = $this->getUrl('sendcloud/status', array('id'=>$row->getId()));
		 return <<<HTML
            
			<div class="mini_order_view_holder" id="mini_order_view_{$row->getId()}">
				
			</div>
			
			<script>
			Sendcloud_Transporter_LoadStatusParcel("{$url}", "#sendcloud_id_{$row->getId()}");
			</script>
			
HTML;
    }
}
