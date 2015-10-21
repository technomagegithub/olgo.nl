<?php

class Sendcloud_Transporter_Block_Widget_Column extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
     public function render(Varien_Object $row)
	 {
		 
		 
		 $loading_info = '<img width="20" height="20" src="' . $this->getSkinUrl('images/ajax-loader-tr.gif') . '" alt="' . $this->__('Loading...') . '"/>';
		
		$id = $row->getId();
		
		$url = $this->getUrl('sendcloud/status');
		 return <<<HTML
            
			<div class="sendcloud_holder" id="sendcloud_id_{$id}">
				{$loading_info}
			</div>
			
			<script>
			var parcel_url = "{$url}";
			Sendcloud_Transporter_LoadStatusParcel("{$id}", "#sendcloud_id_{$id}");
			</script>
			
HTML;
    
		
    }
}
