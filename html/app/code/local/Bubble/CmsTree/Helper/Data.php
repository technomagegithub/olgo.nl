<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getStore($id = null)
    {
        return Mage::app()->getStore($id);
    }

    public function getCustomerGroupId()
    {
        return Mage::getSingleton('customer/session')->getCustomerGroupId();
    }

    public function getCurrentCmsPage()
    {
        $page = Mage::getSingleton('cms/page');
        if (count($page->getData()) > 0 && !$page->isRoot()) {
            return $page;
        }

        return false;
    }

    public function getCmsRootPage($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore()->getId();
        }

        return Mage::getModel('cms/page')->loadRootByStoreId($storeId);
    }

    public function getStoreCmsPages()
    {
        $collection = $this->getCmsRootPage(0)->getChildren();
        foreach ($this->getCmsRootPage()->getChildren() as $page) {
            $collection->addItem($page);
        }

        return $collection;
    }
}
