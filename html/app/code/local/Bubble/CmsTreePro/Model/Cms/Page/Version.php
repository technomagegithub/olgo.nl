<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTreePro
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTreePro_Model_Cms_Page_Version extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('bubble_cmstreepro/cms_page_version');
    }

    public function getPage()
    {
        return Mage::getModel('cms/page')->load($this->getPageId());
    }

    public function getUrl()
    {
        $page = $this->getPage();
        $store = Mage::app()->getStore($page->getStoreId());
        if ($store->isAdmin()) {
            $store = Mage::app()->getDefaultStoreView();
        }
        $route = sprintf(
            'cms/page/view/page_id/%d/version/%d',
            $page->getId(),
            $this->getId()
        );

        return $store->getUrl($route, array('_query' => array('___store' => $store->getCode())));
    }
}