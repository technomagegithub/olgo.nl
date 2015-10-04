<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTreePro
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
require_once 'Bubble/CmsTree/controllers/Adminhtml/Cms/PageController.php';

class Bubble_CmsTreePro_Adminhtml_Cms_PageController extends Bubble_CmsTree_Adminhtml_Cms_PageController
{
    protected function _initPageVersion()
    {
        $versionId = (int) $this->getRequest()->getParam('id');
        $version = Mage::getModel('bubble_cmstreepro/cms_page_version')->load($versionId);
        Mage::register('cms_page_version', $version);

        return $version;
    }

    public function versionsAction()
    {
        $this->_initPage();
        $html = $this->getLayout()
            ->createBlock('bubble_cmstreepro/adminhtml_cms_page_edit_tab_versions')
            ->toHtml();

        $this->getResponse()
            ->setBody($html)
            ->sendResponse();
        exit;
    }

    public function previewPageAction()
    {
        // check if data has been sent
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);

            $page = Mage::getModel('cms/page')->load($data['page_id']);
            if (!$page->getId()) {
                Mage::throwException('Could not find page with id ' . $data['page_id']);
            }

            // init model and set data
            $version = Mage::getModel('bubble_cmstreepro/cms_page_version')
                ->addData($data)
                ->setIsDraft(1)
                ->save();

            $this->getResponse()
                ->setRedirect($version->getUrl())
                ->sendHeaders();
        }
    }

    public function restoreVersionAction()
    {
        $version = $this->_initPageVersion();
        if ($version && $version->getId()) {
            Mage::getSingleton('admin/session')->setRestoreVersionData($version->getData());
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('bubble_cmstreepro')->__('The version has been loaded successfully.')
            );
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('bubble_cmstreepro')->__('Unable to find a version to restore.')
            );
        }

        $this->_redirect('*/*/');
    }

    public function previewVersionAction()
    {
        $version = $this->_initPageVersion();
        if ($version && $version->getId()) {
            $this->getResponse()
                ->setRedirect($version->getUrl())
                ->sendHeaders();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('bubble_cmstreepro')->__('Unable to find a version to preview.')
            );
            $this->_redirect('*/*/');
        }
    }

    public function deleteVersionAction()
    {
        $version = $this->_initPageVersion();
        if ($version && $version->getId()) {
            try {
                $version->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('bubble_cmstreepro')->__('The version has been deleted.')
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('bubble_cmstreepro')->__('Unable to find a version to delete.')
            );
        }

        $this->_redirect('*/*/');
    }
}