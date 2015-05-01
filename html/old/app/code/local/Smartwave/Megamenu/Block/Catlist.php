<?php
class Smartwave_Megamenu_Block_Catlist extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {   
        $layout = $this->getLayout();
        $topnav = $layout->getBlock('catalog.topnav');        
        if (is_object($topnav)) {
            $head = $layout->getBlock('head');
        }
    }   
}
