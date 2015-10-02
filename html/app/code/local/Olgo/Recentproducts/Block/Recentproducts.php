<?php

class Olgo_Recentproducts_Block_Recentproducts extends Mage_Core_Block_Template {
  
  public function getRecentProducts() {
    $arr_products = array();
    $products_count = 4;
    $image_size = 100;
    $category_id = false;
    if ($this->hasData('products_count')) {
      $products_count = $this->getData('products_count');
    }
    if ($this->hasData('category_id')) {
      $category_id = $this->getData('category_id');
    }
    if ($this->hasData('image_size')) {
      $image_size = $this->getData('image_size');
    }
    $products = Mage::getModel("recentproducts/recentproducts")->getRecentProducts($products_count, $category_id);
    
    foreach($products as $product) {
      $arr_products[] = array(
        'id' => $product->getId(),
        'name' => $product->getName(),
        'url' => $product->getProductUrl(),
        'price' => Mage::helper('core')->currency($product->getFinalPrice(),true,false),
        'thumbnailUrl' => $product->getThumbnailUrl($image_size,$image_size)
      );
    }
    
    return $arr_products;
  }
}
?>
