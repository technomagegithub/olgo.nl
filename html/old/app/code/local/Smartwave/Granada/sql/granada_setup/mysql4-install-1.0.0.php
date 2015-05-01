<?php
/**
 * @package        Granada_Category_Columns
 * @author        SW-THEMES
 * @copyright    Copyright 2014
 */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_category', 'sw_gr_cat_hover_effect', array(
    'group'             => 'Product Image Effect',
    'label'             => 'Hover Effect',    
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'granada/system_config_source_attribute_effects',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => false,
    'is_html_allowed_on_front'    => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'default'           => '0'
));

$installer->addAttribute('catalog_category', 'sw_gr_cat_alt_img_col', array(
    'group'             => 'Product Image Effect',
    'label'             => 'Select Alternative Image By Column',
    'note'              => "This value is effective when Hover Effect is Alternative Image. Or when Default Hover Effect (Theme Settings > Category View > Hover Effect is Alternative Image and Hover Effect value of this category is Default, this value will be effect.)",    
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'granada/system_config_source_attribute_altimgcolumn',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => false,
    'is_html_allowed_on_front'    => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'default'           => '0'
));

$installer->addAttribute('catalog_category', 'sw_gr_cat_alt_img_col_val', array(
    'group'             => 'Product Image Effect',
    'label'             => 'Alternative Image Column Value',
    'note'              => "This value is effective when Hover Effect is Alternative Image. Or when Default Hover Effect (Theme Settings > Category View > Hover Effect is Alternative Image and Hover Effect value of this category is Default, this value will be effect.)",
    'type'              => 'text',
    'input'             => 'text',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => true,
    'is_html_allowed_on_front'    => true,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installer->addAttribute('catalog_category', 'sw_gr_cat_aspect_ratio', array(
    'group'             => 'Product Image Size',
    'label'             => 'Keep Image Aspect Ratio',
    'note'              => "If you set this value to 'No', please set the width and height of image",
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'granada/system_config_source_attribute_ratio',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => false,
    'is_html_allowed_on_front'    => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'default'           => '0'
));

$installer->addAttribute('catalog_category', 'sw_gr_cat_ratio_width', array(
    'group'             => 'Product Image Size',
    'label'             => 'Product Image Width',
    'note'              => "If 'Keep Image Aspect Ration' is 'No' or 'Keep Image Aspect Ration' is 'Default' and 'Theme Settings > Category View > Keep Image Aspect Ratio' is 'No', this value must be set.",
    'type'              => 'text',
    'input'             => 'text',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => true,
    'is_html_allowed_on_front'    => true,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installer->addAttribute('catalog_category', 'sw_gr_cat_ratio_height', array(
    'group'             => 'Product Image Size',
    'label'             => 'Product Image Height',
    'note'              => "If 'Keep Image Aspect Ration' is 'No' or 'Keep Image Aspect Ration' is 'Default' and 'Theme Settings > Category View > Keep Image Aspect Ratio' is 'No', this value must be set.",
    'type'              => 'text',
    'input'             => 'text',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => true,
    'is_html_allowed_on_front'    => true,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));

$installer->addAttribute('catalog_category', 'sw_gr_cat_columns', array(
    'group'             => 'Granada Category',
    'label'             => 'Product columns per row',    
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'granada/system_config_source_attribute_columns',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => false,
    'is_html_allowed_on_front'    => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'default'           => '0'
));

$installer->addAttribute('catalog_category', 'sw_gr_cat_banner_style', array(
    'group'             => 'Granada Category',
    'label'             => 'Category Banner Style',    
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'granada/system_config_source_attribute_banner',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => false,
    'is_html_allowed_on_front'    => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'default'           => '0'
));

$installer->addAttribute('catalog_category', 'sw_gr_cat_banner', array(
    'group'             => 'Granada Category',
    'label'             => 'Cagetory Banner Block',
    'note'              => "Please specify static block id to display category banner.",
    'type'              => 'text',
    'input'             => 'text',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => true,
    'is_html_allowed_on_front'    => true,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));

$installer->addAttribute('catalog_category', 'sw_gr_cat_position', array(
    'group'             => 'Granada Category',
    'label'             => 'Category Vertical Navigation Position',    
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'granada/system_config_source_attribute_catpos',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => false,
    'is_html_allowed_on_front'    => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'default'           => '0'
));

$installer->addAttribute('catalog_category', 'sw_gr_filter_position', array(
    'group'             => 'Granada Category',
    'label'             => 'Category Attribute Filter Position',    
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'granada/system_config_source_attribute_catpos',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => false,
    'is_html_allowed_on_front'    => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'default'           => '0'
));

$installer->addAttribute('catalog_category', 'sw_gr_cat_custom_left', array(
    'group'             => 'Granada Category',
    'label'             => 'SideBar Custom Block (left)',
    'note'              => "Please specify static block id to display custom block in left sidebar in category page.",
    'type'              => 'text',
    'input'             => 'text',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => true,
    'is_html_allowed_on_front'    => true,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));

$installer->addAttribute('catalog_category', 'sw_gr_cat_custom_right', array(
    'group'             => 'Granada Category',
    'label'             => 'SideBar Custom Block (right)',
    'note'              => "Please specify static block id to display custom block in right sidebar in category page.",
    'type'              => 'text',
    'input'             => 'text',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => true,
    'is_html_allowed_on_front'    => true,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));


$installer->endSetup();