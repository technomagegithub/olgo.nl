<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Block_Adminhtml_Customer_Group_Switcher extends Mage_Adminhtml_Block_Template
{
    public function getCustomerGroupId()
    {
        return $this->getRequest()->getParam('group');
    }

    public function getCustomerGroups()
    {
        return Mage::getModel('customer/group')->getCollection();
    }

    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }

        return $this->getUrl('*/*/*', array('_current' => true, 'group' => null));
    }
}