<?php

class Olgo_Recentproducts_Model_Recentproducts extends Mage_Core_Model_Abstract {
  
  public function getRecentProducts($products_count = 4, $discount = false, $category_id = false) {
    if ($category_id == false)
      $category_id = Mage::app()->getStore()->getRootCategoryId();
    

    if ($discount == true) {
      // DISCOUNT PRODUCTS
      $dateToday = date('m/d/y');
      $tomorrow = mktime(0, 0, 0, date('m'), date('d')+1, date('y'));
      $dateTomorrow = date('m/d/y', $tomorrow);
      
      $products = Mage::getModel('catalog/category')
            ->load($category_id)
            ->getProductCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status',1) // Don't load disabled products
            ->addAttributeToFilter('special_price', array('gt' => 0))
            ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $dateToday))
            ->addAttributeToFilter('special_to_date', array('or'=> array(
                  0 => array('date' => true, 'from' => $dateTomorrow),
                  1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left')
            ->setOrder('entity_id', 'DESC')
            ->setPageSize($products_count);
    } else {
      // RECENT PRODUCTS
      $products = Mage::getModel('catalog/category')
            ->load($category_id)
            ->getProductCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status',1) // Don't load disabled products
            ->setOrder('entity_id', 'DESC')
            ->setPageSize($products_count);
    }
    return $products;
  }
}
?>
