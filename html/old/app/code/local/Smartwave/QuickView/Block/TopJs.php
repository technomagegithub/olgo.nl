<?php
class Smartwave_QuickView_Block_TopJs extends Mage_Page_Block_Html
{
    public function _prepareLayout()
    {
        if (!Mage::getStoreConfig('quickview/general/enableview')) return;
        $zoomHelper = Mage::helper('smartwave_zoom');
        $layout = $this->getLayout();
        $head = $layout->getBlock('head');
        if (is_object($head)) {
            $head->addJs('smartwave/jquery/jquery-1.10.2.min.js');
            $head->addJs('smartwave/jquery/jquery-noconflict.js');
            $head->addJs('smartwave/jquery/plugins/fancybox/js/jquery.fancybox.js');
            $head->addJs('smartwave/jquery/plugins/elevate/jquery.elevatezoom.js');
			$head->addJs('smartwave/jquery/plugins/owlslider/js/owl.carousel.min.js');
            $head->addJs('varien/product.js');
            $head->addJs('varien/configurable.js');
            $head->addJs('calendar/calendar.js');
            $head->addJs('calendar/calendar-setup.js');
            $head->addItem('skin_js', 'js/bundle.js');
            $head->addItem('skin_js', 'quickview/js/sw_quickview.js');
            $head->addItem('js_css', 'smartwave/jquery/plugins/fancybox/css/jquery.fancybox.css');
			$head->addItem('js_css', 'smartwave/jquery/plugins/owlslider/css/owl.carousel.css');
            $head->addItem('js_css', 'calendar/calendar-win2k-1.css');
            $head->addItem('skin_css', 'quickview/css/styles.css');            
        }
        $this->setTemplate('smartwave/quickview/page/lablequickview.phtml');
    }
}
