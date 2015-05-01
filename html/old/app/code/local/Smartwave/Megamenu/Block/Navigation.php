<?php

class Smartwave_Megamenu_Block_Navigation extends Mage_Catalog_Block_Navigation
{
    public function drawMegaMenuItem($category,$mode = 'dt',$position = 'left', $level = 0, $last = false)
    {
        $_menuHelper = Mage::helper('megamenu');
        if (!$category->getIsActive()) return '';
        $html = array();
        $id = $category->getId();
        // --- Block Options ---
        $catModel = Mage::getModel('catalog/category')->load($id);
        
        $blockPosition = $this->_getBlocks($catModel, 'sw_cat_block_position');        
        if (!$blockPosition) 
            $blockPosition = 'left';

        if (($position != 'all') && ($blockPosition != $position) && (Mage::getStoreConfig('granada_setting/header_settings/header_main_type') == 'top_main_type_3'))
            return '';
            
        $blockType = $this->_getBlocks($catModel, 'sw_cat_block_type');        
        if (!$blockType || $blockType == 'default')
            $blockType = $_menuHelper->getConfig('general/wide_style');    //Default Format is wide style.
        if ($mode == 'mb')
            $blockType = 'narrow';
        $block_top = $block_left = $block_right = $block_bottom = false;
        if ($blockType == 'wide' || $blockType == 'static') {
            // ---Get Static Blocks for category, only format is wide style, it is enable.
            if ($level == 0) {
            //  block top of category
                $block_top = $this->_getBlocks($catModel, 'sw_cat_block_top');
            //  block left of category
                $block_left = $this->_getBlocks($catModel, 'sw_cat_block_left');
            //  block left width of category
                $block_left_width = (int)$this->_getBlocks($catModel, 'sw_cat_left_block_width');
                if (!$block_left_width)
                    $block_left_width = 3;
            //  block right of category
                $block_right = $this->_getBlocks($catModel, 'sw_cat_block_right');
            //  block left width of category
                $block_right_width = (int)$this->_getBlocks($catModel, 'sw_cat_right_block_width');
                if (!$block_right_width)
                    $block_right_width = 3;
            //  block bottom of category
                $block_bottom = $this->_getBlocks($catModel, 'sw_cat_block_bottom');
            }
        }
		$catIcon = '';
		if ($this->_getBlocks($catModel, 'sw_cat_icon')) {
			$catIcon = '<span class="caticon-'.$this->_getBlocks($catModel, 'sw_cat_icon').'"></span>'; 
		}
        
        // ---get Category Label---
        $catLabel = $this->_getLabelHtml($catModel, $level);
        
        // --- Sub Categories ---
        $activeChildren = $this->_getActiveChildren($category, $level);
        
        // --- class for active category ---
        $active = ''; if ($this->isCategoryActive($category)) $active = ' act';
        
        // --- category name ---
        $name = $this->escapeHtml($category->getName());

		$staticWidth = "500";
		if($catModel->getData('sw_cat_static_width'))
            $staticWidth = $catModel->getData('sw_cat_static_width');

        if (Mage::getStoreConfig('megamenu/general/non_breaking_space')) {
            $name = str_replace(' ', '&nbsp;', $name);
        }
        $drawPopup = ($block_top || $block_left || $block_right || $block_bottom || count($activeChildren));
        if ($drawPopup) {
            //Has subcategories or static blocks
            if ($blockType == 'wide') {
                $parentClass = 'menu-full-width';
            } elseif($blockType=='static'){
				$parentClass = 'menu-static-width';
			}else {
                $parentClass = 'menu-item menu-item-has-children menu-parent-item';
            }            
            $html[] = '<li class="'.$parentClass.'">';
            $html[] = '<a href="'.$this->getCategoryUrl($category).'" class="'.$active.'">'.$catIcon.$name.$catLabel.'</a>';
            if ($mode != 'mb') {
				if ($blockType == 'static'){
					$html[] = '<div class="nav-sublist-dropdown" style="display: none; width:'.$staticWidth.'px;">';
	                $html[] = '<div class="container">';               
				} else {
					$html[] = '<div class="nav-sublist-dropdown" style="display: none;">';
	                $html[] = '<div class="container">';               
				}                
            }
            if (($level == 0 && $blockType == 'wide') || $level == 0 && $blockType == 'static') {
                if ($block_top)
                    $html[] = '<div class="top-mega-block">' . $block_top . '</div>';
                $html[] = '<div class="mega-columns clearfix">';
                if ($block_left)
                    $html[] = '<div class="left-mega-block col-sm-'.$block_left_width.'">' . $block_left . '</div>';
                if (count($activeChildren)) {
                    //columns for category
                    $columns = (int)$catModel->getData('sw_cat_block_columns');
                    if (!$columns || ($columns == 0))
                        $columns = $_menuHelper->getConfig('popup/category_columns');
                    
                    
                    //columns item width    
                    $columnsWidth = 12;
                    if ($block_left)
                        $columnsWidth = $columnsWidth - $block_left_width;
                    if ($block_right)
                        $columnsWidth = $columnsWidth - $block_right_width;
                        
                    //draw category menu items
                    $html[] = '<div class="block1 col-sm-'.$columnsWidth.'">';
                    $html[] = '<div class="row">';                    
                    $html[] = '<ul>';
                    $html[] = $this->drawColumns($activeChildren, $columns, count($activeChildren),'', 'wide');
                    $html[] = '</ul>';
                    $html[] = '</div>';
                    $html[] = '</div>';
                }
                if ($block_right)
                    $html[] = '<div class="right-mega-block col-sm-'.$block_right_width.'">' . $block_right . '</div>';
                $html[] = '</div>';				
                if ($block_bottom)
                    $html[] = '<div class="bottom-mega-block">' . $block_bottom . '</div>';				
            } else if ($level == 0 && $blockType == 'narrow') {                
                $html[] = '<ul>';
                $html[] = $this->drawColumns($activeChildren, '', count($activeChildren),'','narrow', $mode);
                $html[] = '</ul>';
            }
            if ($mode != 'mb') {
                $html[] = '</div>';
                $html[] = '</div>';   
            }
			$html[] = '</li>';
        } else {
            //Has no subcategories and static blocks
            $html[] = '<li>';
            $html[] = '<a href="'.$this->getCategoryUrl($category).'" class="'.$active.'">'.$catIcon.$name.'</a>';                
            $html[] = '</li>';
        }
        $html = implode("\n", $html);
        return $html;
    }
    
//    custom block 
    public function drawCustomBlock() 
    {
        $_menuHelper = Mage::helper('megamenu');
        $blockIds = $_menuHelper->getConfig('custom/custom_block');        
        if (!$blockIds) return;
        
        $html = array();
        $blockIds = preg_replace('/\s/', '', $blockIds);
        $IDs = explode(',', $blockIds);
        foreach ($IDs as $blockId) {
            $block = Mage::getModel('cms/block')->setStoreId(Mage::app()->getStore()->getId())->load($blockId);            
            if (!$block) continue;
                        
            $blockTitle = $block->getTitle();            
            $blockContent = $block->getContent(); 
            $proc_helper = Mage::helper('cms');
            $processor = $proc_helper->getPageTemplateProcessor();
            $blockContent = $processor->filter($blockContent);           
            if (!$blockContent) continue;
            $html[] = '<li class="menu-full-width">';
            $html[] = '<a href="javascript:void();" rel="#">';
            if ($_menuHelper->getConfig('general/non_breaking_space')) {
                $blockTitle = str_replace(' ', '&nbsp;', $blockTitle);
            }
            $html[] = $blockTitle;
            $html[] = '</a>';
            $html[] = '<div class="nav-sublist-dropdown">';
            $html[] = '<div class="container">';
            $html[] = $blockContent;
            $html[] = '</div>';
            $html[] = '</div>';            
            $html[] = '</li>';
        }
        if (!$html) return;
        $html = implode("\n", $html);
        return $html;
    }
    
    public function drawCustomMobileLinks()
    {
        $_menuHelper = Mage::helper('megamenu');
        $blockIds = $_menuHelper->getConfig('custom/custom_mobile_links');        
        if (!$blockIds) return;
        
        $html = array();
        $blockIds = preg_replace('/\s/', '', $blockIds);
        $IDs = explode(',', $blockIds);
        foreach ($IDs as $blockId) {
            $block = Mage::getModel('cms/block')->setStoreId(Mage::app()->getStore()->getId())->load($blockId);
            if (!$block) continue;
            $menuItemContent = $block->getContent();
            if(substr($menuItemContent, 0, 4) == '<ul>') {
                $menuItemContent = substr($menuItemContent, 4);                
            }
            if(substr($menuItemContent, strlen($menuItemContent) - 5) == '</ul>') {
                $menuItemContent = substr($menuItemContent, 0, - 5);                
            }
            $html[] = $menuItemContent; 
        }
        if (!$html) return;
        $html = implode("\n", $html);
        return $html;
    }
    
    public function drawCustomLinks($position = 'left')
    {
        $_menuHelper = Mage::helper('megamenu');
        if ($position == 'left') {
            $blockIds = $_menuHelper->getConfig('custom/custom_links_left');   
        } else if($position == 'right') {
            $blockIds = $_menuHelper->getConfig('custom/custom_links_right');
        } else {
            $blockIds = $_menuHelper->getConfig('custom/custom_links_left').','.$_menuHelper->getConfig('custom/custom_links_right');
        }
        if (!$blockIds) return;        
        
        $html = array();
        $blockIds = preg_replace('/\s/', '', $blockIds);
        $IDs = explode(',', $blockIds);
        foreach($IDs as $blockId) {
            $block = Mage::getModel('cms/block')->setStoreId(Mage::app()->getStore()->getId())->load($blockId);
            if (!$block) continue;
            $menuItemContent = $block->getContent();
            $proc_helper = Mage::helper('cms');
            $processor = $proc_helper->getPageTemplateProcessor();
            $menuItemContent = $processor->filter($menuItemContent);  
            if(substr($menuItemContent, 0, 4) == '<ul>') {
                $menuItemContent = substr($menuItemContent, 4);                
            }
            if(substr($menuItemContent, strlen($menuItemContent) - 5) == '</ul>') {
                $menuItemContent = substr($menuItemContent, 0, - 5);                
            }
            $html[] = $menuItemContent;   
        }
        if (!$html) return;
        
        $html = implode("\n", $html);
        return $html;
    }
    public function drawMenuItem($children, $level = 1, $type, $width, $mode = 'dt')
    {
        $keyCurrent = $this->getCurrentCategory()->getId();
        $html = '';        
        foreach ($children as $child)
        {
            if (is_object($child) && $child->getIsActive())
            {
                $activeChildren = $this->_getActiveChildren($child, $level);
                // --- class for active category ---
                $id = $child->getId();
                // --- Static Block ---
                $catModel = Mage::getModel('catalog/category')->load($id);
                $active = '';
                if ($this->isCategoryActive($child))
                {
                    $active = ' actParent';
                    if ($child->getId() == $keyCurrent) $active = ' act';
                }
                // ---category label
                $label = $this->_getLabelHtml($catModel, $level);
                // --- format category name ---
                $name = $this->escapeHtml($child->getName());
                if (Mage::getStoreConfig('megamenu/general/non_breaking_space'))
                    $name = str_replace(' ', '&nbsp;', $name);
                $class = 'menu-item';
                if (count($activeChildren) > 0) {
                    $class .= ' menu-item-has-children menu-parent-item';
                }
                if ($level == 1) {
                    if ($type == 'wide') {                        
						$class .= ' col-sw-'.$width;
                    }                    
                    $html .= '<li class="'.$class.' ">';					
					if ($type == 'wide' && $catModel->getThumbnail()) {
						$html .= '<div class="menu_thumb_img">';
						$html .= '<a class="menu_thumb_link" href="'. $this->getCategoryUrl($child) .'">';
						$html .= '<img src="' . Mage::getBaseUrl('media').'catalog/category/' . $catModel->getThumbnail() . '" alt="' . Mage::helper('catalog/output')->__("Thumbnail Image").'" />';
						$html .= '</a>';
						$html .= '</div>';
					}					
                    $html.= '<a class="level' . $level . $active . '" href="' . $this->getCategoryUrl($child) . '"><span>' . $name .$label. '</span></a>';    
                } else {
                    $html .= '<li class="'.$class.'">';
                    $html .= '<a class="level' . $level . $active . '" href="' . $this->getCategoryUrl($child) . '"><span>' . $name .$label. '</span></a>';
                }
                if (count($activeChildren) > 0)
                {
                    if ($mode != 'mb') {
                        $html.= '<div class="nav-sublist level' . $level . '">';   
                    }
                    $html.= '<ul>';
                    $html.= $this->drawMenuItem($activeChildren, $level + 1, $type, $width, $mode);
                    $html.= '</ul>';                    
                    if ($mode != 'mb') {
                        $html.= '</div>';   
                    }
                }
                $html .= '</li>';
            }
        }        
        return $html;
    }

    public function drawColumns($children, $columns = 1, $catNum = 1, $catLabel = '', $type, $mode = 'dt')
    {
        $html = '';        
		if ($columns < 1) $colWidth = 4;
		$colWidth = $columns;
        $chunks = $this->_explodeByColumns($children, $columns, $catNum);        

        $lastColumnNumber = count($chunks);        
        $i = 1;
        foreach ($chunks as $key => $value)
        {
            if ($type == 'wide') {                
				$class = 'col-sw-'.$colWidth; 
                if (!count($value)) continue;                
                $html.= $this->drawMenuItem($value, 1, $type, $colWidth);                
                if ($i == $colWidth)
                    $html .= '<li class="clearfix"></li>';    
            } else {
                $html .= $this->drawMenuItem($value, 1,'','',$mode);
            }
            $i++;
        }
        
        return $html;
    }

    protected function _getActiveChildren($parent, $level)
    {
        $activeChildren = array();
        // --- check level ---
        $maxLevel = (int)Mage::getStoreConfig('megamenu/general/max_level');
        if ($maxLevel > 0)
        {
            if ($level >= ($maxLevel - 1)) return $activeChildren;
        }
        // --- / check level ---
        if (Mage::helper('catalog/category_flat')->isEnabled())
        {
            $children = $parent->getChildrenNodes();
            $childrenCount = count($children);
        }
        else
        {
            $children = $parent->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = $children && $childrenCount;
        if ($hasChildren)
        {
            foreach ($children as $child)
            {
                if ($this->_isCategoryDisplayed($child))
                {
                    array_push($activeChildren, $child);
                }
            }
        }
        return $activeChildren;
    }

    private function _isCategoryDisplayed(&$child)
    {
        if (!$child->getIsActive()) return false;
        // === check products count ===
        // --- get collection info ---
        if (!Mage::getStoreConfig('megamenu/general/display_empty_categories'))
        {
            $data = $this->_getProductsCountData();
            // --- check by id ---
            $id = $child->getId();
            #Mage::log($id); Mage::log($data);
            if (!isset($data[$id]) || !$data[$id]['product_count']) return false;
        }
        // === / check products count ===
        return true;
    }

    private function _getProductsCountData()
    {
        if (is_null($this->_productsCount))
        {
            $collection = Mage::getModel('catalog/category')->getCollection();
            $storeId = Mage::app()->getStore()->getId();
            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active')
                ->setProductStoreId($storeId)
                ->setLoadProductCount(true)
                ->setStoreId($storeId);
            $productsCount = array();
            foreach($collection as $cat)
            {
                $productsCount[$cat->getId()] = array(
                    'name' => $cat->getName(),
                    'product_count' => $cat->getProductCount(),
                );
            }
            #Mage::log($productsCount);
            $this->_productsCount = $productsCount;
        }
        return $this->_productsCount;
    }

    private function _explodeByColumns($target, $num, $catNum)
    {
        $target = self::_explodeArrayByColumnsHorisontal($target, $num, $catNum);
        
        #return $target;
//        if ((int)Mage::getStoreConfig('megamenu/columns/integrate') && count($target))
        if (count($target))
        {
            // --- combine consistently numerically small column ---
            // --- 1. calc length of each column ---
            $max = 0; $columnsLength = array();
            foreach ($target as $key => $child)
            {
                $count = 0;
                $this->_countChild($child, 1, $count);
                if ($max < $count) $max = $count;
                $columnsLength[$key] = $count;
            }
            // --- 2. merge small columns with next ---
            $xColumns = array(); $column = array(); $cnt = 0;
            $xColumnsLength = array(); $k = 0;
            foreach ($columnsLength as $key => $count)
            {
                $cnt+= $count;
                if ($cnt > $max && count($column))
                {
                    $xColumns[$k] = $column;
                    $xColumnsLength[$k] = $cnt - $count;
                    $k++; $column = array(); $cnt = $count;
                }
                $column = array_merge($column, $target[$key]);
            }
            $xColumns[$k] = $column;
            $xColumnsLength[$k] = $cnt - $count;
            // --- 3. integrate columns of one element ---
            $target = $xColumns; $xColumns = array(); $nextKey = -1;
            if ($max > 1 && count($target) > 1)
            {
                foreach($target as $key => $column)
                {
                    if ($key == $nextKey) continue;
                    if ($xColumnsLength[$key] == 1)
                    {
                        // --- merge with next column ---
                        $nextKey = $key + 1;
                        if (isset($target[$nextKey]) && count($target[$nextKey]))
                        {
                            $xColumns[] = array_merge($column, $target[$nextKey]);
                            continue;
                        }
                    }
                    $xColumns[] = $column;
                }
                $target = $xColumns;
            }
        }
        $_rtl = Mage::getStoreConfigFlag('megamenu/general/rtl');
        if ($_rtl) {
            $target = array_reverse($target);
        }
        return $target;
    }

    private function _countChild($children, $level, &$count)
    {
        foreach ($children as $child)
        {
            if ($child->getIsActive())
            {
                $count++; $activeChildren = $this->_getActiveChildren($child, $level);
                if (count($activeChildren) > 0) $this->_countChild($activeChildren, $level + 1, $count);
            }
        }
    }
    
    //get static blocks in menu
    private function _getBlocks($model, $block_signal)
    {
        if (!$this->_tplProcessor)
        { 
            $this->_tplProcessor = Mage::helper('cms')->getBlockTemplateProcessor();            
        }
        return $this->_tplProcessor->filter( trim($model->getData($block_signal)) ); 
    }

    private static function _explodeArrayByColumnsHorisontal($list, $num, $catNum)
    {
        if ($num <= 0) return array($list);
        $partition = array();        
        $partition = array_pad($partition, $catNum, array());  
              
        $i = 0;
        foreach ($list as $key => $value) {
            $partition[$i][$key] = $value;
            if (++$i == $catNum) $i = 0;
        }
        return $partition;
    }
    
    //get Label for menu
    private function _getLabelHtml($catModel, $level)
    {
        $label = $catModel->getData('sw_cat_label');
        if ($label) {
            $labelContent = Mage::helper('megamenu')->getConfig('category_labels/'.$label);
            if ($labelContent) {
                if ($level = 0) {
                    return ' <span class="cat-label cat-label-'. $label .' pin-bottom">' . $labelContent . '</span>';
                } else {
                    return ' <span class="cat-label cat-label-'. $label .'">' . $labelContent . '</span>';
                }
            }
        }
        return '';
    }
    
    /**
     * Check if current url is url for home page
     *
     * @return true
     */
    public function getIsHomePage()
    {
        return $this->getUrl('') == $this->getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true));
    }

    public function getLogoSrc()
    {
        if (empty($this->_data['logo_src'])) {
            $this->_data['logo_src'] = Mage::getStoreConfig('design/header/logo_src');
        }
        return $this->getSkinUrl($this->_data['logo_src']);
    }

    public function getLogoAlt()
    {
        if (empty($this->_data['logo_alt'])) {
            $this->_data['logo_alt'] = Mage::getStoreConfig('design/header/logo_alt');
        }
        return $this->_data['logo_alt'];
    }    
    public function drawCategoryMenuItem($category, $index)
    {   
        $_menuHelper = Mage::helper('megamenu');
        if (!$category->getIsActive()) return '';
        
        $html = array();                          
        
        $id = $category->getId();
        $type = $_menuHelper->getConfig('general/wide_style');
        $columns = $_menuHelper->getConfig('popup/category_columns');
        // --- Block Options ---
        $catModel = Mage::getModel('catalog/category')->load($id);
        // --- category name ---
        $name = $this->escapeHtml($category->getName());
        if (Mage::getStoreConfig('megamenu/general/non_breaking_space')) {
            $name = str_replace(' ', '&nbsp;', $name);
        } 
               
        if($type == 'wide') {  
             $html[] = '<li class="col-sw-'.$columns.'">';
             if ($catModel->getThumbnail()) {
                $html[] = '<div class="menu_thumb_img">';
                $html[] = '<a class="menu_thumb_link" href="'. $this->getCategoryUrl($category) .'">';
                $html[] = '<img src="' . Mage::getBaseUrl('media').'catalog/category/' . $catModel->getThumbnail() . '" alt="' . Mage::helper('catalog/output')->__("Thumbnail Image").'" />';
                $html[] = '</a>';
                $html[] = '</div>';                
             } else {
                 $html[] = '<a class="level1" href="'. $this->getCategoryUrl($category) .'"><span>';
                 $html[] = $name;
                 $html[] = '</span></a>';
             }
             
             $html[] = '</li>';
             if ($index == $columns) {
                 $html[] = '<li class="clearfix"></li>';
             }
        } else {
            $html[] = '<li class="menu-item">';
            $html[] = '<a class="level1" href="'. $this->getCategoryUrl($category) .'"><span>';
            $html[] = $name;
            $html[] = '</span></a>';
            $html[] = '</li>';
        }        
        $html = implode("\n", $html);
        return $html;
    }
}
