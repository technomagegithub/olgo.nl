<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */

/* @var $installer Bubble_CmsTree_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
$tablePage = $installer->getTable('cms_page');
$tablePageStore = $installer->getTable('cms_page_store');
$tableStore = $installer->getTable('core_store');
$tableCustomerGroup = $installer->getTable('customer_group');
$tablePageTree = $installer->getTable('bubble_cms_page_tree');
$tablePermission = $installer->getTable('bubble_cms_page_permission');

$installer->run("
    DROP TABLE IF EXISTS `{$tablePageTree}`;
    CREATE TABLE `{$tablePageTree}` LIKE `{$tablePage}`;

    ALTER TABLE `{$tablePageTree}`
        ADD `store_id` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT 0,
        ADD `parent_id` SMALLINT( 6 ) NOT NULL DEFAULT 0,
        ADD `path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        ADD `position` SMALLINT( 6 ) UNSIGNED NOT NULL DEFAULT 0,
        ADD `level` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT 0,
        ADD `include_in_menu` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '1',
        ADD `children_count` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT 0;

    ALTER TABLE `{$tablePageTree}` CHANGE `identifier` `identifier` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

    ALTER TABLE `{$tablePageTree}` ADD INDEX ( `store_id` );

    ALTER TABLE `{$tablePageTree}` ADD FOREIGN KEY ( `store_id` ) REFERENCES `{$tableStore}` (
        `store_id`
    ) ON DELETE CASCADE ON UPDATE CASCADE;

    ALTER TABLE `{$tablePageTree}` ADD UNIQUE (
        `identifier` ,
        `store_id`
    );

    DROP TABLE IF EXISTS `{$tablePermission}`;
    CREATE TABLE `{$tablePermission}` (
        `permission_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `store_id` SMALLINT(5) UNSIGNED NOT NULL,
        `customer_group_id` SMALLINT(3) UNSIGNED NOT NULL,
        `page_id` SMALLINT(6) NOT NULL
    ) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

    ALTER TABLE `{$tablePermission}` ADD INDEX (`store_id`);
    ALTER TABLE `{$tablePermission}` ADD INDEX (`customer_group_id`);
    ALTER TABLE `{$tablePermission}` ADD INDEX (`page_id`);
    ALTER TABLE `{$tablePermission}` ADD UNIQUE (`store_id`, `customer_group_id`, `page_id`);

    ALTER TABLE `{$tablePermission}` ADD FOREIGN KEY (`store_id`) REFERENCES `{$tableStore}` (
        `store_id`
    ) ON DELETE CASCADE ON UPDATE CASCADE;

    ALTER TABLE `{$tablePermission}` ADD FOREIGN KEY (`customer_group_id`) REFERENCES `{$tableCustomerGroup}` (
        `customer_group_id`
    ) ON DELETE CASCADE ON UPDATE CASCADE;

    ALTER TABLE `{$tablePermission}` ADD FOREIGN KEY (`page_id`) REFERENCES `{$tablePageTree}` (
        `page_id`
    ) ON DELETE CASCADE ON UPDATE CASCADE;
");

Mage::app()->reinitStores(); // needed to have store list
$isSingleStoreMode = Mage::app()->isSingleStoreMode();
$stores = Mage::app()->getStores(!$isSingleStoreMode);

// Create pages
foreach ($stores as $store) {
    $storeId = $store->getId();

    // Retrieve old store pages
    $select = $connection->select()
        ->from(array('pages' => $tablePage))
        ->join(array('stores' => $tablePageStore), 'pages.page_id = stores.page_id', '')
        ->where('stores.store_id = ?', $storeId);

    if ($isSingleStoreMode) {
        $select->orWhere('stores.store_id = 0');
    }

    $pages = $connection->fetchAll($select);

    // Create default root page for this store
    $home = Bubble_CmsTree_Model_Cms_Page::createDefaultStoreRootPage($storeId);
    $homeId = $home->getId();

    if (count($pages) > 0) {
        $insertedPages = array();
        $childrenCount = 0;
        foreach ($pages as $page) {
            if (!isset($insertedPages[$page['identifier']])) {
                $insertedPages[$page['identifier']] = 0;
            } else {
                $insertedPages[$page['identifier']]++;
                $page['identifier'] = $page['identifier'] . '-' . $insertedPages[$page['identifier']];
            }
            unset($page['page_id']);
            $page['parent_id'] = $homeId;
            $page['store_id'] = $storeId;
            $connection->insert($tablePageTree, $page);
            $childrenCount++;
        }
        $installer->run("
            UPDATE `{$tablePageTree}` SET
                `parent_id` = '{$homeId}',
                `path` = CONCAT('{$homeId}/', `page_id`),
                `position` = `page_id`,
                `level` = '2'
            WHERE `store_id` = {$storeId} AND `page_id` != {$homeId};

            UPDATE `{$tablePageTree}` SET
                `path` = '{$homeId}',
                `identifier` = '',
                `level` = '1',
                `position` = '1',
                `children_count` = {$childrenCount}
            WHERE `store_id` = {$storeId} AND `page_id` = {$homeId};
        ");
    }
}

// Reinit default permissions
$connection->truncateTable($tablePermission);

// Create default permissions
foreach (Mage::getModel('cms/page')->getCollection() as $page) {
    foreach (Mage::getModel('customer/group')->getCollection() as $customerGroup) {
        $storeId = $page->getStoreId();
        $customerGroupId = $customerGroup->getId();
        $pageId = $page->getId();
        $installer->run("INSERT INTO `{$tablePermission}` (`store_id`, `customer_group_id`, `page_id`) VALUES ('{$storeId}', '{$customerGroupId}', '{$pageId}');");
    }
}

// Create shared pages between stores
$tablePageTreeStore = $installer->getTable('bubble_cms_page_tree_store');

$installer->run("
    DROP TABLE IF EXISTS `{$tablePageTreeStore}`;
    CREATE TABLE `{$tablePageTreeStore}` LIKE `{$tablePageStore}`;
");

$storeIds = array_keys(Mage::app()->getStores(false));
foreach ($storeIds as $storeId) {
    $installer->run("
        INSERT INTO `{$tablePageTreeStore}` (`page_id`, `store_id`)
        SELECT `page_id`, {$storeId} AS `store_id` FROM `{$tablePageTree}` WHERE `store_id` = '0';
    ");
}

$installer->endSetup();
