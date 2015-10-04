<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Block_Adminhtml_Cms_Page_Edit extends Mage_Adminhtml_Block_Cms_Page_Edit
{
    public function __construct()
    {
        parent::__construct();
        $this->_removeButton('back');
        $this->_removeButton('reset');
        $this->_removeButton('saveandcontinue');
        $page = Mage::registry('cms_page');
        if ($page && $page->getId() && $page->getParentId()) {
            $this->_addButton('delete', array(
                'label'   => Mage::helper('cms')->__('Delete Page'),
                'onclick' => "deleteConfirm('" .
                    Mage::helper('adminhtml')->__('Are you sure you want to do this?') . "', '" .
                    Mage::helper('adminhtml')->getUrl('*/*/delete', array('page_id' => $page->getId())) . "')",
                'class'   => 'scalable delete',
                'level'   => -1
            ));
        }
    }

    public function getHeaderText()
    {
        $page = Mage::registry('cms_page');
        if ($page && $page->getId()) {
            return Mage::helper('cms')->__("Edit Page '%s'", $this->htmlEscape($page->getTitle()))
                . ' (ID: ' . $page->getId() . ')';
        }

        return Mage::helper('cms')->__('New Page');
    }

    public function getFormActionUrl()
    {
        $args = array();

        if ($this->getRequest()->has('store')) {
            $args['store'] = $this->getRequest()->getParam('store');
        }

        if ($this->getRequest()->has('parent')) {
            $args['parent'] = $this->getRequest()->getParam('parent');
        }

        return $this->getUrl('*/*/save', $args);
    }

    public function getTabsHtml()
    {
        return $this->getLayout()->getBlock('cms_page_edit_tabs')->removeTab('hierarchy')->toHtml();
    }
}