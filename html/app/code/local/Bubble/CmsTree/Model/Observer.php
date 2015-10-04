<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Model_Observer
{
    /**
     * @var Bubble_CmsTree_Helper_Data
     */
    protected $_helper;

    public function __construct()
    {
        $this->_helper = Mage::helper('bubble_cmstree');
    }

    /**
     * Adds CMS pages to top menu
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeTopmenuHtml(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfigFlag('bubble_cmstree/general/include_in_menu')) {
            $block = $observer->getEvent()->getBlock();
            $block->addCacheTag(Mage_Cms_Model_Page::CACHE_TAG);
            $this->_addPagesToMenu(
                $this->_helper->getStoreCmsPages(), $observer->getMenu(), $block, true
            );
        }
    }

    /**
     * Recursively adds CMS pages to top menu
     *
     * @param Varien_Data_Tree_Node_Collection|array $pages
     * @param Varien_Data_Tree_Node $parentPageNode
     * @param Mage_Page_Block_Html_Topmenu $menuBlock
     * @param bool $addTags
     */
    protected function _addPagesToMenu($pages, $parentPageNode, $menuBlock, $addTags = false)
    {
        $pageModel = Mage::getModel('cms/page');
        foreach ($pages as $page) {
            if (!$page->getIsActive() || !$page->getIncludeInMenu() || !$this->_isAllowed($page)) {
                continue;
            }

            $nodeId = 'page-node-' . $page->getId();

            $pageModel->setId($page->getId());
            if ($addTags) {
                $menuBlock->addModelTags($pageModel);
            }

            $tree = $parentPageNode->getTree();
            $pageData = array(
                'name'      => $page->getTitle(),
                'id'        => $nodeId,
                'url'       => $page->getUrl(),
                'is_active' => $this->_isActiveMenuPage($page)
            );
            $pageNode = new Varien_Data_Tree_Node($pageData, 'id', $tree, $parentPageNode);
            $parentPageNode->addChild($pageNode);

            $children = $page->getChildren();
            if (Mage::helper('cms/page')->isPermissionsEnabled($this->_helper->getStore())) {
                $children->addPermissionsFilter($this->_helper->getCustomerGroupId());
            }

            $this->_addPagesToMenu($children, $pageNode, $menuBlock, $addTags);
        }
    }

    protected function _isActiveMenuPage($page)
    {
        $current = $this->_helper->getCurrentCmsPage();
        if ($current) {
            return in_array($page->getId(), $current->getPathIds());
        }

        return false;
    }

    protected function _isAllowed($page)
    {
        return Mage::helper('cms/page')->isAllowed(
            $this->_helper->getStore()->getId(),
            $this->_helper->getCustomerGroupId(),
            $page->getId()
        );
    }
}