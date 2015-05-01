<?php
class Smartwave_Granada_Block_Home_Category_Products extends Mage_Catalog_Block_Product_List
{
    protected function _getProductCollection()
    {
        $storeId    = Mage::app()->getStore()->getId();

        $category_id = $this->getCategoryId();

        if($category_id) {
            $category = Mage::getModel('catalog/category')->load($category_id);

            $products = Mage::getModel('catalog/product')->getCollection()
                ->addCategoryFilter($category)
                ->addAttributeToSelect('*')
                ->setStoreId($storeId)
                ->addStoreFilter($storeId);
        }
        else {
            $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->setStoreId($storeId)
                ->addStoreFilter($storeId);
        }
        $product_count = $this->getProductCount();

        if($product_count)
        {
            $products->setPageSize($product_count);
        }


        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);
        Mage::getModel('review/review')->appendSummary($products);
        $this->_productCollection = $products;

        return $this->_productCollection;
    }
    function getItems()
    {
        return $this->_productCollection;
    }
    public function get_product_column()
    {
        $columns = 4;
        if ($this->getProductsColumn())
            $columns = $this->getProductsColumn();
        return $columns;
    }

    public function get_cat_title(){
        $category_id = $this->getCategoryId();
        if($category_id) {
            $category = Mage::getModel('catalog/category')->load($category_id);
            return $category->getName();
        } else {
            return $this->__('All Products');
        }
    }

    public function get_block(){
        $blockId = $this->getStaticId();
        if($blockId){
            $catalog_cms_block = Mage::getModel('cms/block')->load($blockId);
            if($catalog_cms_block->getIsActive()){
                return $this->getLayout()->createBlock('cms/block')->setBlockId($blockId)->toHtml();
            }
        }
        return false;
    }

    public function get_category_id(){
        $cat_id = $this->getCategoryId();
        return $cat_id;
    }

    public function get_title_color(){
        $title_color = $this->getTitleColor();
        if($title_color){
            return $title_color;
        }
        return false;
    }

    public function get_icon_code(){
        $icon_code = $this->getIconCode();
        if($icon_code){
            return $icon_code;
        }
        return false;
    }
}