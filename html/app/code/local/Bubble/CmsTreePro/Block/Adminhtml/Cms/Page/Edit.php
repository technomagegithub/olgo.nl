<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTreePro
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTreePro_Block_Adminhtml_Cms_Page_Edit extends Bubble_CmsTree_Block_Adminhtml_Cms_Page_Edit
{
    public function __construct()
    {
        parent::__construct();
        $this->_updateButton('save', 'onclick', "editForm.submit('" . $this->getFormActionUrl() . "')");
        $page = Mage::registry('cms_page');
        if ($page && $page->getId()) {
            $this->_addButton('preview', array(
                'label'     => Mage::helper('bubble_cmstreepro')->__('Preview Page'),
                'class'     => 'scalable go',
                'onclick'   => "previewAction('edit_form', editForm, '" . $this->getUrl('*/*/previewPage') . "')",
                'level'     => 0,
            ));
        }
    }
}