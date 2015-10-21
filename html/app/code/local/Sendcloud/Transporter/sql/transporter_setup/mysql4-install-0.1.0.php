<?php

$installer = $this;
$installer->startSetup();

$installer = $this;
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'sendcloud_parcel_id', 'int');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'sendcloud_status_id', 'int');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'sendcloud_comment', 'text');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'sendcloud_last_check', 'varchar(255)');

$installer->endSetup();
