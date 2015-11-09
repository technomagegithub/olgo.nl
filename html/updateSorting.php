<?php

require_once 'app/Mage.php';
Mage::app();

$storeId = Mage::app()->getStore()->getId();
$products = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)        
            ->addViewsCount() // Could set a from and to date
            ->joinField('inventory_in_stock', 'cataloginventory/stock_item','is_in_stock', 'product_id=entity_id', '{{table}}.is_in_stock=1');

Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);        

$products->getSelect()->limit( 500 );    


// Update
Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));

$categoryId = 2; //replace with your category id

$newPosition = 5;

$category = Mage::getModel('catalog/category')->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)->load($categoryId);


foreach($products as $product) {
 //$product->getViews(); // Don't need this it's already orderd
 $newPosition = $newPosition+5;
 $newOrder[$product->getId()] = $newPosition;
}


$categoryProduct = $category->getProductsPosition();
foreach ($categoryProduct as $id=>$value){
    $categoryProducts[$id] = $newOrder[$id];
}
//var_dump($categoryProducts);
$category->setPostedProducts($categoryProducts);
$category->save();





?>
