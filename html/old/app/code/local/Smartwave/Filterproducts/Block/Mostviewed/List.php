<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of List
 *
 * @author om
 */
class Smartwave_Filterproducts_Block_Mostviewed_List extends Mage_Catalog_Block_Product_List
{
    protected function _getProductCollection()
    {
        parent::__construct();
        $storeId    = Mage::app()->getStore()->getId();
        $products = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->addViewsCount()
    		->setPageSize($this->get_prod_count())
            ->addAttributeToSort($this->get_order(), $this->get_order_dir())
            ->setCurPage($this->get_cur_page());
		$productFlatData = Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
		if($productFlatData == "1")
		{
			$products->getSelect()->joinLeft(
		array('flat' => 'catalog_product_flat_'.$storeId),
	                "(e.entity_id = flat.entity_id ) ",	                
					array(
	                   'flat.name AS name','flat.small_image AS small_image','flat.price AS price','flat.special_price as special_price','flat.special_from_date AS special_from_date','flat.special_to_date AS special_to_date'
					)
	        );
		}
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);

        $this->_productCollection = $products;

        return $this->_productCollection;
    }

    public function getTitle() {
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        $title = Mage::getStoreConfig('filterproducts/config3/title',$code);
        if ($title) {
            return $title;
        } else {
            return $this->__('Most Viewed Product');
        }
    }

    public function isEnable() {
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        $enable = Mage::getStoreConfig('filterproducts/config3/active',$code);
        return $enable;
    }

    public function getSliderBlockName() {
        return 'mostview';
    }

    function get_prod_count()
	{
		//unset any saved limits
	    Mage::getSingleton('catalog/session')->unsLimitPage();
	    return (isset($_REQUEST['limit'])) ? intval($_REQUEST['limit']) : 9;
	}// get_prod_count

    function getItems()
    {
        return $this->_productCollection;
    }
    
	function get_cur_page()
	{
		return (isset($_REQUEST['p'])) ? intval($_REQUEST['p']) : 1;
	}// get_cur_page

    function get_order()
	{
		return (isset($_REQUEST['order'])) ? ($_REQUEST['order']) : 'views';
	}// get_order

    function get_order_dir()
	{
		return (isset($_REQUEST['dir'])) ? ($_REQUEST['dir']) : 'desc';
	}// get_direction
}

?>
