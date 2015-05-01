<?php
class Smartwave_Granada_Block_ConfigurableSwatches_Catalog_Media_Js_Product extends Mage_ConfigurableSwatches_Block_Catalog_Media_Js_Product {
    public function getProductImageFallbacksCustom($keepFrame = null) {
        /* @var $helper Mage_ConfigurableSwatches_Helper_Mediafallback */
        $helper = Mage::helper('granada/mediafallback');

        $fallbacks = array();
		$store = Mage::app()->getStore();
		$code  = $store->getCode();

        $products = $this->getProducts();

		$keepFrame = Mage::getStoreConfig("granada_setting/product_view/aspect_ratio",$code);
		$ratio_width = Mage::getStoreConfig("granada_setting/product_view/img_width",$code);
		$ratio_height = Mage::getStoreConfig("granada_setting/product_view/img_height",$code);
        if (Mage::getStoreConfig("zoom/general/enable",$code) && Mage::getStoreConfig("zoom/general/used_zoom",$code)){
            $ratio_width = $ratio_width * Mage::getStoreConfig("zoom/general/zoom_img_times",$code);
            $ratio_height = $ratio_height * Mage::getStoreConfig("zoom/general/zoom_img_times",$code);
        }
        /* @var $product Mage_Catalog_Model_Product */
        foreach ($products as $product) {
            if($keepFrame)
	            $imageFallback = $helper->getConfigurableImagesFallbackArrayCustom($product, $this->_getImageSizes(), $keepFrame, $ratio_width);
			else
				$imageFallback = $helper->getConfigurableImagesFallbackArrayCustom($product, $this->_getImageSizes(), $keepFrame, $ratio_width, $ratio_height);
            $fallbacks[$product->getId()] = array(
                'product' => $product,
                'image_fallback' => $this->_getJsImageFallbackString($imageFallback)
            );
        }

        return $fallbacks;
    }
}
?>