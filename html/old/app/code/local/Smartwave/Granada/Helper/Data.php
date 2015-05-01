<?php 
class Smartwave_Granada_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCurrentStoreCode() {
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        return $code;
    }
    public function getCategoryColumns($code) {
        $columns = $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_cat_columns');
        if ($columns) {
            return $columns;
        } else {
            return Mage::getStoreConfig('granada_setting/category_grid/columns', $code);
        }
    }

    // get config for theme setting
    public function getConfig($optionName)
    {
        if (Mage::registry('granada_css_generate_store')) {
            $store_code = Mage::registry('granada_css_generate_store');
            $store_id = Mage::getModel('core/store')->load($store_code)->getId();
        } else {
            $store_id = NULL;
        }
        return Mage::getStoreConfig('granada_setting/' . $optionName, $store_id);
    }
    // get config group for theme setting section
    public function getConfigGroup($storeId = NULL)
    {
        if (!$storeId) {
            $store_code = Mage::registry('granada_css_generate_store');
            $storeId = Mage::getModel('core/store')->load($store_code)->getId();
        }

        if ($storeId)
            return Mage::getStoreConfig('granada_setting', $storeId);
        else
            return Mage::getStoreConfig('granada_setting');
    }

    // get config for theme design
    public function getConfigDesign($optionName)
    {
        if (Mage::registry('granada_css_generate_store')) {
            $store_code = Mage::registry('granada_css_generate_store');
            $store_id = Mage::getModel('core/store')->load($store_code)->getId();
        } else {
            $store_id = NULL;
        }
        return Mage::getStoreConfig('granada_design/' . $optionName, $store_id);
    }

    // get config group for theme design section
    public function getConfigGroupDesign($storeId = NULL)
    {
        if (!$storeId) {
            $store_code = Mage::registry('granada_css_generate_store');
            $storeId = Mage::getModel('core/store')->load($store_code)->getId();
        }

        if ($storeId)
            return Mage::getStoreConfig('granada_design', $storeId);
        else
            return Mage::getStoreConfig('granada_design');
    }
    public function getBannerStyle ($code) {
        $cat_banner_style = $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_cat_banner_style');
        if ($cat_banner_style) {
            return $cat_banner_style;
        } else {
            return Mage::getStoreConfig('granada_setting/category_settings/banner_style', $code);
        }
    }
    
    private function _getBlocks($model, $block_signal) {
        if (!isset($this->_tplProcessor) || !$this->_tplProcessor)
        { 
            $this->_tplProcessor = Mage::helper('cms')->getBlockTemplateProcessor();            
        }
        return $this->_tplProcessor->filter( trim($model->getData($block_signal)) ); 
    }
    
    public function getCategoryBannerBlock() {
        $cat_banner = $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_cat_banner');        
        if($cat_banner) {            
            return $cat_banner;
        }
        return false;
    }
    
    public function getEffect() {
         $effect = $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_cat_hover_effect');
         if ($effect) {
             return $effect;
         } else {
              return Mage::getStoreConfig('granada_setting/category_settings/prod_img_effect', $this->getCurrentStoreCode());
         }  
    }
    
    public function getAltCol() {
        if ($this->getEffect() == 'alt_img') {
            $sort_order = $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_cat_alt_img_col');
            if ($sort_order) {
                return $sort_order;
            } else {
                return Mage::getStoreConfig('granada_setting/category_settings/alt_image_column', $this->getCurrentStoreCode());
            }
        }
    }
    
    public function getAltColVal() {
        if ($this->getEffect() == 'alt_img') {
            $sort_order = $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_cat_alt_img_col_val');
            if ($sort_order) {
                return $sort_order;
            } else {
                return Mage::getStoreConfig('granada_setting/category_settings/alt_image_column_value', $this->getCurrentStoreCode());
            }
        }
    }
    
    public function getCurCat() {
        return $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_cat_banner');    
    }
    
    public function isKeepRatio() {
         $ratio = $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_cat_aspect_ratio');
         
         if ($ratio) {
             if ($ratio == 'no') {
                 return '0';
             } else {
                 return '1';
             }
         } else {
             return Mage::getStoreConfig('granada_setting/category_settings/aspect_ratio', $this->getCurrentStoreCode());
         }
    }
    
    public function getImgWidth() {
        $imgWidth =  $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_cat_ratio_width');
        if ($imgWidth) {
            return $imgWidth;
        } else {
            $imgWidth = Mage::getStoreConfig('granada_setting/category_settings/ratio_width', $this->getCurrentStoreCode());
            if ($imgWidth) {
                return $imgWidth;
            } else {
                return 350;
            }
        }  
    }
    
    public function getImgHeight() {
        $imgHeight =  $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_cat_ratio_height');
        if ($imgHeight) {
            return $imgHeight;
        } else {
            $imgHeight = Mage::getStoreConfig('granada_setting/category_settings/ratio_height', $this->getCurrentStoreCode());
            if ($imgHeight) {
                return $imgHeight;
            } else {
                return 350;
            }
        }    
    }
    
    public function getCatNavPosition() {
        $pos =  $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_cat_position');
        if ($pos) {
            return $pos;
        } else {
            return Mage::getStoreConfig('granada_setting/category_settings/main_cat_position', $this->getCurrentStoreCode());
        }
    }
    
    public function getAttrFilterPosition() {
        $pos =  $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_filter_position');
        if ($pos) {
            return $pos;
        } else {
            return Mage::getStoreConfig('granada_setting/category_settings/filter_position', $this->getCurrentStoreCode());
        }
    }
    
    public function getCatCustomBlock($position = 'left') {
        if ($position == 'left') {
            $blockIds =  $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_cat_custom_left');
            if ($blockIds) {
                return $blockIds;
            } else {
                return Mage::getStoreConfig('granada_setting/category_settings/cat_custom_left', $this->getCurrentStoreCode());
            }   
        } elseif ($position == 'right') {
            $blockIds =  $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_gr_cat_custom_right');
            if ($blockIds) {
                return $blockIds;
            } else {
                return Mage::getStoreConfig('granada_setting/category_settings/cat_custom_right', $this->getCurrentStoreCode());
            }
        } else {
            return false;
        }
    }

	public function getHomeUrl() {
        return array(
            "label" => $this->__('Home'),
            "title" => $this->__('Home Page'),
            "link" => Mage::getUrl('')
        );
    }

	public function getShoppingCartCrumb() {
        return array(
            "label" => $this->__('Shopping Cart'),
            "title" => $this->__('Shopping Cart')
        );
    }
	public function getCheckoutCrumb() {
        return array(
            "label" => $this->__('Checkout'),
            "title" => $this->__('Checkout')
        );
    }

    public function getPreviousProduct()
    {
        $_prev_prod = NULL;
        $_product_id = Mage::registry('current_product')->getId();

        $cat = Mage::registry('current_category');
        if($cat) {
            $category_products = $cat->getProductCollection()->addAttributeToSort('position', 'asc');
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($category_products);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($category_products);

            $store = Mage::app()->getStore();
            $code = $store->getCode();
            if (!Mage::getStoreConfig("cataloginventory/options/show_out_of_stock", $code))
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($category_products);

            $items = $category_products->getItems();
            $cat_prod_ids = (array_keys($items));

            $_pos = array_search($_product_id, $cat_prod_ids); // get position of current product

            // get the next product url
            if (isset($cat_prod_ids[$_pos - 1])) {
                $_prev_prod = Mage::getModel('catalog/product')->load($cat_prod_ids[$_pos - 1]);
            } else {
                return false;
            }
        }
        if($_prev_prod != NULL){
            return $_prev_prod;
        } else {
            return false;
        }

    }


    public function getNextProduct()
    {
        $_next_prod = NULL;
        $_product_id = Mage::registry('current_product')->getId();

        $cat = Mage::registry('current_category');

        if($cat) {
            $category_products = $cat->getProductCollection()->addAttributeToSort('position', 'asc');
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($category_products);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($category_products);

            $store = Mage::app()->getStore();
            $code = $store->getCode();
            if (!Mage::getStoreConfig("cataloginventory/options/show_out_of_stock", $code))
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($category_products);

            $items = $category_products->getItems();
            $cat_prod_ids = (array_keys($items));

            $_pos = array_search($_product_id, $cat_prod_ids); // get position of current product

            // get the next product url
            if (isset($cat_prod_ids[$_pos + 1])) {
                $_next_prod = Mage::getModel('catalog/product')->load($cat_prod_ids[$_pos + 1]);
            } else {
                return false;
            }
        }

        if($_next_prod != NULL){
            return $_next_prod;
        } else {
            return false;
        }
    }
    public function isEnabledonConfig($id){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        if(Mage::getStoreConfig("granada_setting/product_view_custom_tab/custom_tab",$code))
            return true;
        if(Mage::getStoreConfig("granada_setting/product_view_custom_tab/".$id,$code))
            return true;
        return false;
    }
    public function isEnabledfromCategory(){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        if(Mage::getStoreConfig("granada_setting/product_view_custom_tab/from_category",$code))
            return true;
        return false;
    }
    public function getTabIdField($type,$id){
        $num = substr($id,-1);
        $config_id = "";
        switch($type){
            case "attribute":
                $config_id = "attribute_tab_id_".$num;
                break;
            case "static_block":
                $config_id = "static_block_tab_id_".$num;
                break;
        }
        return $config_id;
    }
    public function isEnabledonParentCategory($attribute, $category){
        //$category = Mage::getModel("catalog/category")->load($category_id);
        if($category->getData($attribute) == "yes"){
            return true;
        }
        if($category->getData($attribute) == "no"){
            return false;
        }
        if(!$category->getData($attribute)){
            if($category->getId() == Mage::app()->getStore()->getRootCategoryId()){
                return true;
            }
            return $this->isEnabledonParentCategory($attribute, $category->getParentCategory());
        }
    }
    public function isEnabledonCategory($type, $id, $product_id){
        $product = Mage::getModel("catalog/product")->load($product_id);
        $attribute = "";
        if($type=="attribute"){
            $attribute = "sw_product_attribute_tab_".substr($id,-1);
        }else{
            $attribute = "sw_product_staticblock_tab_".substr($id,-1);
        }
        $category = $product->getCategory();
        if(!$category){
            $c = $product->getCategoryCollection()->addAttributeToSelect("*");
            $category = $c->getFirstItem();
        }
        return $this->isEnabledonParentCategory($attribute, $category);
    }
    public function isEnabledTab($type, $id, $product_id){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();

        if(!$this->isEnabledonConfig($id)){
            return false;
        }
        $config_id = Mage::getStoreConfig("granada_setting/product_view_custom_tab/".$this->getTabIdField($type,$id),$code);
        if(!$config_id)
            return false;
        if(!$this->getTabTitle($type, $id, $product_id))
            return false;
        if($this->isEnabledfromCategory()){
            if(!$this->isEnabledonCategory($type, $id, $product_id))
                return false;
        }
        return true;
    }
    public function getTabTitle($type, $id, $product_id){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        $config_id = Mage::getStoreConfig("granada_setting/product_view_custom_tab/".$this->getTabIdField($type,$id),$code);
        $title = "";
        switch($type){
            case "attribute":
                $product = Mage::getModel("catalog/product")->load($product_id);
                $title = $product->getResource()->getAttribute($config_id)->getStoreLabel();
                if(!$product->getResource()->getAttribute($config_id)->getFrontend()->getValue($product))
                    $title = "";
                break;
            case "static_block":
                $block = Mage::getModel("cms/block")->setStoreId(Mage::app()->getStore()->getId())->load($config_id);
                $title = $block->getTitle();
                if(!$block->getIsActive())
                    $title = "";
                break;
        }
        return $title;
    }
    public function getTabContents($type, $id, $product_id){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        $config_id = Mage::getStoreConfig("granada_setting/product_view_custom_tab/".$this->getTabIdField($type,$id),$code);
        $content = "";
        switch($type){
            case "attribute":
                $product = Mage::getModel("catalog/product")->load($product_id);
                $content = $product->getResource()->getAttribute($config_id)->getFrontend()->getValue($product);
                break;
            case "static_block":
                $block = Mage::getModel("cms/block")->setStoreId(Mage::app()->getStore()->getId())->load($config_id);
                $content = $block->getContent();
                $proc_helper = Mage::helper('cms');
                $processor = $proc_helper->getPageTemplateProcessor();
                $content = $processor->filter($content);
                break;
        }
        return $content;
    }
}