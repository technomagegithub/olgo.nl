<?php

require_once 'app/Mage.php';
Mage::app();


$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
$collection = Mage::getModel('catalog/product')->getCollection()
                ->addStoreFilter()
                ->addAttributeToSelect('*')  
                ->addAttributeToFilter('special_price', array('gt' => 0));

echo "Total products found : ".count($collection)."\n";

foreach ($collection as $product) {
        $price         = number_format($product->getPrice(),2);
        $special_price = number_format($product->getSpecialPrice(),2);
        if ($price == $special_price) {
          echo "\n".$price." - ".$special_price." are the same, resetting values";
          $product->setSpecialPrice('')->setSpecialFromDate('')->setSpecialToDate('')->save();
        }
}

echo "\nReindexing prices";
$process = Mage::getModel('index/indexer')->getProcessByCode('catalog_product_price');
$process->reindexAll();

echo "\nDone!";


?>
