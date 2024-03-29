<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.3.0
 * @build     1072
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */



$installer = $this;
$version = Mage::helper('mstcore/version')->getModuleVersionFromDb('seoautolink');
if ($version == '1.0.2') {
    return;
} elseif ($version != '1.0.1') {
    die('Please, run migration 1.0.1');
}

$installer->startSetup();

Mage::helper('mstcore')->copyConfigData('seo/autolink/target', 'seoautolink/autolink/target');

$installer->endSetup();

Mage::getSingleton('core/config')->cleanCache();
