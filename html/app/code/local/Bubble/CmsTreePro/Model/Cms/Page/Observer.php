<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTreePro
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTreePro_Model_Cms_Page_Observer
{
    public function savePageAfter(Varien_Event_Observer $observer)
    {
        $page = $observer->getEvent()->getObject();
        if ($page && $page->getId() && !$page->getVersionSaved()) {
            $version = Mage::getModel('bubble_cmstreepro/cms_page_version')
                ->setData($page->getData())
                ->unsCreationTime()
                ->setUsername($this->_getUsername())
                ->save();
            $page->setVersionSaved(true);
            $version->getResource()->deletePageDrafts($page);
        }
    }

    public function loadPageAfter(Varien_Event_Observer $observer)
    {
        $versionId = Mage::app()->getRequest()->getParam('version');
        if ($versionId) {
            $version = Mage::getModel('bubble_cmstreepro/cms_page_version')->load($versionId);
            $page = $observer->getEvent()->getObject();
            if ($version->getId() && $version->getPageId() == $page->getId()) {
                $page->addData($version->getData());
            }
        }
    }

    public function renderAdminEditPageBefore()
    {
        if ($data = Mage::getSingleton('admin/session')->getRestoreVersionData()) {
            Mage::getSingleton('admin/session')->unsRestoreVersionData();
            $page = Mage::registry('cms_page');
            if ($page && $page->getId() == $data['page_id']) {
                $page->addData($data);
            }
        }
    }

    protected function _getUsername()
    {
        $username = null;
        if (Mage::getSingleton('api/session')->isLoggedIn()) {
            $username = Mage::getSingleton('api/session')->getUser()->getUsername();
        } elseif (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
        }

        return $username;
    }
}