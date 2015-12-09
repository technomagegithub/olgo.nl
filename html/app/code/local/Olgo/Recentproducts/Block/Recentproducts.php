<?php

class Olgo_Recentproducts_Block_Recentproducts extends Mage_Core_Block_Template {
  
  public function getRecentProducts() {
    $arr_products   = array();

    // Default settings
    $products_count = 4;
    $image_size     = 100;
    $category_id    = false;
    $discount       = false;
    $showTitle      = false;

    // Get parameters
    if ($this->hasData('products_count')) {
      $products_count = $this->getData('products_count');
    }
    if ($this->hasData('category_id')) {
      $category_id = $this->getData('category_id');
    }
    if ($this->hasData('image_size')) {
      $image_size = $this->getData('image_size');
    }
    if ($this->hasData('discount')) {
      $discount = true;
    }
    if ($this->hasData('showTitle')) {
      $showTitle = true;
    }
   
    $products = Mage::getModel("recentproducts/recentproducts")->getRecentProducts($products_count, $discount, $category_id);
    
    foreach($products as $product) {
      $arr_products[] = array(
        'id' => $product->getId(),
        'name' => $product->getName(),
        'showTitle' => $showTitle,
        'url' => $product->getProductUrl(),
        'price' => Mage::helper('core')->currency($product->getFinalPrice(),true,false),
        'orgPrice' => Mage::helper('core')->currency($product->getPrice(),true,false),
        'thumbnailUrl' => $product->getThumbnailUrl($image_size,$image_size),
        'shortDescription' => $product->getShortDescription()
      );
    }
    
    return $arr_products;
  }
}
?>
