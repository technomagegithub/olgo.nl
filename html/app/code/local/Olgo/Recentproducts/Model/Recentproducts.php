<?php

class Olgo_Recentproducts_Model_Recentproducts extends Mage_Core_Model_Abstract {
  
  public function getRecentProducts($products_count = 4, $category_id = false) {
    if ($category_id == false)
      $category_id = Mage::app()->getStore()->getRootCategoryId();
    
    $products = Mage::getModel('catalog/category')
            ->load($category_id)
            ->getProductCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status',1) // Don't load disabled products
            ->setOrder('entity_id', 'DESC')
            ->setPageSize($products_count);
            
    /*
    $products = Mage::getModel("catalog/product")
                 ->getCollection()
                 ->addAttributeToSelect('*')
                 ->addAttributeToFilter('status',1) // Don't load disabled products
                 ->setOrder('entity_id', 'DESC')
                 ->setPageSize($products_count);
    */
    return $products;
  }
}
?>
