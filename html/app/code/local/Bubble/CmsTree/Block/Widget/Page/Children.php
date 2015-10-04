<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Block_Widget_Page_Children
    extends Mage_Core_Block_Abstract
    implements Mage_Widget_Block_Interface
{
    protected $_page;

    public function getPage()
    {
        if (null === $this->_page) {
            $pageId = $this->_getData('page_id');
            if ($pageId) {
                $this->_page = Mage::getModel('cms/page')->load($pageId);
            } else {
                $this->_page = $this->getCurrentPage();
            }
        }

        return $this->_page;
    }

    public function getCurrentPage()
    {
        return Mage::getSingleton('cms/page');
    }

    protected function _toHtml()
    {
        if (!$this->getPage()->getId()) {
            return '';
        }

        $activePages = $this->getPage()
            ->getChildren()
            ->addActiveFilter();
        $activePagesCount = $activePages->count();

        if (!$activePagesCount && !$this->_getData('page_id')) {
            // If no active children, display pages on the same level
            $activePages = $this->getPage()
                ->getParentPage()
                ->getChildren()
                ->addActiveFilter();
            $activePagesCount = $activePages->count();
            $this->setShowTopPage(false); // Force top page to be hidden to avoid current page to be displayed twice
        }

        if (!$activePagesCount) {
            // Should never occurs since we are displaying pages
            // on the same level if no active child is found
            return '';
        }

        $html = '';
        if ($this->getTitle()) {
            $html .= '<p class="page-children-title">' . $this->getTitle() . '</p>';
        }

        $html .= sprintf('<ul class="page-children %s">', $this->getCssClass());

        if ($this->getShowTopPage()) {
            $active = $this->getPage()->getId() == $this->getCurrentPage()->getId();
            $html .= '<li class="level0 first' . ($active ? ' active' : '') .'">';
            if (!$active) {
                $html .= '<a href="'. $this->getPage()->getUrl() . '">';
            }
            $html .= '<span>'. $this->escapeHtml($this->getPage()->getTitle()) .'</span>';
            if (!$active) {
                $html .= '</a>';
            }
            $html .= '<ul>';
        }

        $i = 0;
        foreach ($activePages as $page) {
            $html .= $this->_getPageChildrenHtml(
                $page,
                1,                             // level
                ($i == $activePagesCount - 1), // is last
                ($i == 0)                      // is first
            );
            $i++;
        }

        if ($this->getShowTopPage()) {
            $html .= '</ul>';
            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    protected function _getPageChildrenHtml($page, $level = 0, $isLast = false, $isFirst = false)
    {
        $html = array();

        // get all children
        $activeChildren = $page->getChildren()
            ->addActiveFilter();
        if (Mage::helper('cms/page')->isPermissionsEnabled($this->getStore())) {
            $activeChildren->addPermissionsFilter($this->getCustomerGroupId());
        }

        $activeChildrenCount = $activeChildren->count();
        $hasActiveChildren = ($activeChildrenCount > 0);

        // prepare list item html classes
        $classes = array();
        $classes[] = 'level' . $level;

        if ($isFirst) {
            $classes[] = 'first';
        }
        if ($isLast) {
            $classes[] = 'last';
        }
        if ($hasActiveChildren) {
            $classes[] = 'parent';
        }
        $active = $page->getId() == $this->getCurrentPage()->getId();
        if ($active) {
            $classes[] = 'active';
        }

        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }

        // assemble list item with attributes
        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';
        $html[] = $htmlLi;
        if (!$active) {
            $html[] = '<a href="'. $page->getUrl() . '">';
        }
        $html[] = '<span>'. $this->escapeHtml($page->getTitle()) .'</span>';
        if (!$active) {
            $html[] = '</a>';
        }

        if ($hasActiveChildren && (!$this->getLevels() || $this->getLevels() > ($level + 1))) {
            // render children
            $htmlChildren = '';
            $i = 0;
            foreach ($activeChildren as $child) {
                $htmlChildren .= $this->_getPageChildrenHtml(
                    $child,
                    ($level + 1),
                    ($i == $activeChildrenCount - 1),  // is last
                    ($i == 0)                          // is first
                );
                $i++;
            }

            if (!empty($htmlChildren)) {
                $html[] = '<ul class="level' . $level . '">';
                $html[] = $htmlChildren;
                $html[] = '</ul>';
            }
        }

        $html[] = '</li>';
        $html = implode("\n", $html);

        return $html;
    }

    public function getStore($id = null)
    {
        return Mage::app()->getStore($id);
    }

    public function getCustomerGroupId()
    {
        return Mage::getSingleton('customer/session')->getCustomerGroupId();
    }
}
