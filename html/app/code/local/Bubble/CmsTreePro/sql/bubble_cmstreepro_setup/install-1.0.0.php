<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTreePro
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */

/* @var $installer Bubble_CmsTree_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
$tablePageTree = $installer->getTable('bubble_cms_page_tree');
$tablePageTreeVersion = $installer->getTable('bubble_cms_page_tree_version');

$username = 'admin';
if ($user = Mage::getSingleton('admin/session')->getUser()) {
    $username = $user->getUsername();
}
$username = $installer->getConnection()->quote($username);

$installer->run("
    DROP TABLE IF EXISTS `{$tablePageTreeVersion}`;
    CREATE TABLE `{$tablePageTreeVersion}` (
        `version_id` SMALLINT(6) NOT NULL AUTO_INCREMENT COMMENT 'Version ID',
        `page_id` SMALLINT(6) NOT NULL COMMENT 'Page ID',
        `is_draft` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Version Is Draft (Preview)',
        `username` VARCHAR(40) DEFAULT NULL COMMENT 'Username',
        `title` VARCHAR(255) DEFAULT NULL COMMENT 'Page Title',
        `root_template` VARCHAR(255) DEFAULT NULL COMMENT 'Page Template',
        `meta_keywords` TEXT COMMENT 'Page Meta Keywords',
        `meta_description` TEXT COMMENT 'Page Meta Description',
        `content_heading` VARCHAR(255) DEFAULT NULL COMMENT 'Page Content Heading',
        `content` MEDIUMTEXT COMMENT 'Page Content',
        `creation_time` TIMESTAMP NULL DEFAULT NULL COMMENT 'Page Creation Time',
        `layout_update_xml` TEXT COMMENT 'Page Layout Update Content',
        `custom_theme` VARCHAR(100) DEFAULT NULL COMMENT 'Page Custom Theme',
        `custom_root_template` VARCHAR(255) DEFAULT NULL COMMENT 'Page Custom Template',
        `custom_layout_update_xml` TEXT COMMENT 'Page Custom Layout Update Content',
        `custom_theme_from` DATE DEFAULT NULL COMMENT 'Page Custom Theme Active From Date',
        `custom_theme_to` DATE DEFAULT NULL COMMENT 'Page Custom Theme Active To Date',
    PRIMARY KEY (`version_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS Page Version Table';

    ALTER TABLE `{$tablePageTreeVersion}`
        ADD CONSTRAINT `FK_CMS_PAGE_TREE_ID` FOREIGN KEY (`page_id`)
        REFERENCES `{$tablePageTree}` (`page_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE;

    INSERT INTO `{$tablePageTreeVersion}` (`page_id`, `username`, `title`, `root_template`, `meta_keywords`, `meta_description`,
                                           `content_heading`, `content`, `creation_time`,
                                           `layout_update_xml`, `custom_theme`, `custom_root_template`,
                                           `custom_layout_update_xml`, `custom_theme_from`, `custom_theme_to`)
        SELECT `page_id`, {$username}, `title`, `root_template`, `meta_keywords`, `meta_description`,
               `content_heading`, `content`, `creation_time`,
               `layout_update_xml`, `custom_theme`, `custom_root_template`,
               `custom_layout_update_xml`, `custom_theme_from`, `custom_theme_to`
        FROM `{$tablePageTree}`;
");

$installer->endSetup();
