<?php

class Olgo_Recentproducts_Block_Recentproducts extends Mage_Core_Block_Template {
  
  public function getRecentProducts() {
    $arr_products = array();
    $products_count = 4;
    $category_id = false;
    if ($this->hasData('products_count')) {
      $products_count = $this->getData('products_count');
    }
    if ($this->hasData('category_id')) {
      $category_id = $this->getData('category_id');
    }
    $products = Mage::getModel("recentproducts/recentproducts")->getRecentProducts($products_count, $category_id);
    
    foreach($products as $product) {
      $arr_products[] = array(
        'id' => $product->getId(),
        'name' => $product->getName(),
        'url' => $product->getProductUrl(),
        'thumbnailUrl' => $product->getThumbnailUrl(100,100)
      );
    }
    
    return $arr_products;
  }
}
?>
