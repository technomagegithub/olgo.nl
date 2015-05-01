<?php

class Smartwave_Granada_Block_Home_Product extends Mage_Catalog_Block_Product
{
    public function _prepareLayout() {
        $this->setTemplate('catalog/home/product.phtml');
    }
    public function get_ProductId() {
        $productId = $this->getProductId();
        if ($productId)
            return $productId;
        else
            return false;
    }
    public function getIconXPosition() {
        $iconXpos = $this->getIconXpos();
        if ($iconXpos)
            return $iconXpos;
        else
            return 0;
    }
    public function getIconYPosition() {
        $iconYpos = $this->getIconYpos();
        if ($iconYpos)
            return $iconYpos;
        else
            return 0;
    }
    public function getPopDirection() {
        $popupDir = $this->getPopupDirection();
        if ($popupDir)
            return$popupDir;
        else
            return 'left-top';
    }
}
