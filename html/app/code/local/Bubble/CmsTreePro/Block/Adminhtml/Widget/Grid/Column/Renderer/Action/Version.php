<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTreePro
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTreePro_Block_Adminhtml_Widget_Grid_Column_Renderer_Action_Version
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }

        $out = array();
        foreach ($actions as $action) {
            if (is_array($action)) {
                $out[] = $this->_toLinkHtml($action, $row);
            }
        }

        return implode('<br />', $out);
    }
}