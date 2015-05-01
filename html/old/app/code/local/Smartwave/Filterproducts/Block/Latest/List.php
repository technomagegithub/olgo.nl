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
class Smartwave_Filterproducts_Block_Latest_List extends Mage_Catalog_Block_Product_List
{
    protected function _getProductCollection()
    {
        parent::__construct();
        $storeId    = Mage::app()->getStore()->getId();
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSort('created_at', 'desc')
            ->addAttributeToSelect('*')
            ->setPageSize($this->get_prod_count())
            ->addAttributeToSelect(array('name', 'price', 'small_image'))
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
			->setCurPage($this->get_cur_page());

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);

        $this->_productCollection = $products;

        return $this->_productCollection;
    }

    public function getTitle() {
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        $title = Mage::getStoreConfig('filterproducts/config6/title',$code);
        if ($title) {
            return $title;
        } else {
            return $this->__('Latest Products');
        }
    }

    public function isEnable() {
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        $enable = Mage::getStoreConfig('filterproducts/config6/active',$code);
        return $enable;
    }

    public function getSliderBlockName() {
        return 'latest';
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
		return (isset($_REQUEST['order'])) ? ($_REQUEST['order']) : 'position';
	}// get_order

    function get_order_dir()
	{
		return (isset($_REQUEST['dir'])) ? ($_REQUEST['dir']) : 'desc';
	}// get_direction
}

?>
