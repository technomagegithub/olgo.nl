<?php
/**
 * @package		Eternal_Megamenu
 * @author		Eternal Friend
 * @copyright	Copyright 2014
 */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_category', 'sw_cat_icon', array(
    'group'             => 'Menu',
    'label'             => 'Category Icon',
    'note'              => "This field is applicable only for top-level categories.",
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'megamenu/category_attribute_source_icon_codes',
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
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));

$installer->endSetup();