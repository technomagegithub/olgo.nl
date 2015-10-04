<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTreePro
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTreePro_Block_Adminhtml_Cms_Page_Edit_Tab_Versions
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('cmsPageVersionsGrid');
        $this->setDefaultSort('version_version_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('cms_page_versions_filter');
        $this->setUseAjax(true);
    }

    public function getCurrentUrl($params = array())
    {
        if (!isset($params['_current'])) {
            $params['_current'] = true;
        }

        return $this->getUrl('*/*/versions', $params);
    }

    public function canShowTab()
    {
        return true;
    }

    public function getId()
    {
        return 'cmsPageVersionsGrid';
    }

    public function getTabLabel()
    {
        return Mage::helper('bubble_cmstreepro')->__('Versions');
    }

    public function getTabTitle()
    {
        return Mage::helper('bubble_cmstreepro')->__('Versions');
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareCollection()
    {
        $page = Mage::registry('cms_page');
        $collection = Mage::getModel('bubble_cmstreepro/cms_page_version')
            ->getCollection()
            ->addFieldToFilter('page_id', $page->getId())
            ->addFieldToFilter('is_draft', 0);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('version_version_id', array(
            'header'            => Mage::helper('bubble_cmstreepro')->__('Version Id'),
            'index'             => 'version_id',
            'type'              => 'number',
            'column_css_class'  => 'v-middle',
        ));

        $this->addColumn('version_username', array(
            'width'             => '100px',
            'header'            => Mage::helper('cms')->__('Username'),
            'index'             => 'username',
            'type'              => 'text',
            'column_css_class'  => 'v-middle',
        ));

        $this->addColumn('version_title', array(
            'header'            => Mage::helper('cms')->__('Page Title'),
            'index'             => 'title',
            'type'              => 'text',
            'column_css_class'  => 'v-middle',
        ));

        $this->addColumn('version_root_template', array(
            'header'            => Mage::helper('cms')->__('Layout'),
            'index'             => 'root_template',
            'type'              => 'text',
            'frame_callback'    => array($this, 'decorateLayout'),
            'column_css_class'  => 'v-middle',
        ));

        $this->addColumn('version_creation_time', array(
            'header'            => Mage::helper('adminhtml')->__('Created At'),
            'align'             => 'right',
            'index'             => 'creation_time',
            'type'              => 'datetime',
            'width'             => '180px',
            'column_css_class'  => 'v-middle',
        ));

        $this->addColumn('version_action',
            array(
                'header'            => Mage::helper('adminhtml')->__('Action'),
                'width'             => '50px',
                'type'              => 'action',
                'getter'            => 'getId',
                'align'             => 'center',
                'filter'            => false,
                'sortable'          => false,
                'renderer'          => 'bubble_cmstreepro/adminhtml_widget_grid_column_renderer_action_version',
                'column_css_class'  => 'v-middle',
                'actions'       => array(
                    array(
                        'caption' => Mage::helper('bubble_cmstreepro')->__('Restore'),
                        'url'     => array(
                            'base'   => '*/*/restoreVersion',
                        ),
                        'field'   => 'id',
                        'confirm' => Mage::helper('bubble_cmstreepro')->__('This will load this version into current page without saving it. Continue?')
                    ),
                    array(
                        'caption' => Mage::helper('bubble_cmstreepro')->__('Preview'),
                        'url'     => array(
                            'base'   => '*/*/previewVersion',
                        ),
                        'field'   => 'id',
                        'target' => '_blank',
                    ),
                    array(
                        'caption' => Mage::helper('bubble_cmstreepro')->__('Delete'),
                        'url'     => array(
                            'base'   => '*/*/deleteVersion',
                        ),
                        'field'   => 'id',
                        'confirm' => Mage::helper('bubble_cmstreepro')->__('Are you sure?')
                    )
                ),
            )
        );

        return parent::_prepareColumns();
    }

    public function decorateLayout($value)
    {
        $text = $value;
        $options = Mage::getModel('page/source_layout')->toOptionArray();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                $text = $option['label'];
            }
        }

        return $text;
    }
}
