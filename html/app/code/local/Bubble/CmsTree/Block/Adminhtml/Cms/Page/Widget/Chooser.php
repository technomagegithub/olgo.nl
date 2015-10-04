<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Block_Adminhtml_Cms_Page_Widget_Chooser extends Mage_Adminhtml_Block_Cms_Page_Widget_Chooser
{
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $tree = Mage::getResourceModel('cms/page_tree')->load();
        $html = $tree->toSelectHtml($element->getName(), $element->getValue(), $element->getId());
        $element->setData('after_element_html', $html);
        $element->setValue(); // Not needed because page is already selected in select box

        return $element;
    }
}