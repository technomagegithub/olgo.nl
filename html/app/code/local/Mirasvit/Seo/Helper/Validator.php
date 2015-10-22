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


class Mirasvit_Seo_Helper_Validator extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    public function testMagentoCrc()
    {
        $filter = array(
            'app/code/core/Mage/Core',
            'app/code/core/Mage/Review',
            'js'
        );

        return Mage::helper('mstcore/validator_crc')->testMagentoCrc($filter);
    }

    public function testMirasvitCrc()
    {
        $modules = array('SEO');
        return Mage::helper('mstcore/validator_crc')->testMirasvitCrc($modules);
    }

    public function testTablesExists()
    {
        $result = self::SUCCESS;
        $title = 'Advanced SEO Suite: Required tables exist';
        $description = array();

        $tables = array(
            'core/store',
            'seo/rewrite',
            'seo/rewrite_store',
            'seofilter/rewrite',
        );

        foreach ($tables as $table) {
            if (!$this->dbTableExists($table)) {
                $description[] = "Table '$table' does not exist";
                $result = self::FAILED;
            }
        }

        return array($result, $title, $description);
    }

    public function testConflictExtensions()
    {
        $result = self::SUCCESS;
        $title = 'Advanced SEO Suite: Conflict Extensions';
        $description = array();

        if (Mage::helper('mstcore')->isModuleInstalled('Pektsekye_OptionExtended')) {
            $result = self::FAILED;
            $description[] = "Pektsekye OptionExtended installed. \"Enable SEO-friendly URLs for Product Images\" will work incorrectly (images can be broken).";
            $description[] = "Please, change in file /app/code/local/Pektsekye/OptionExtended/Block/Product/View/Js.php following code \"->init(\$this->getProduct(), 'thumbnail', \$image)\" at \"->init(\$this->getProduct(), 'thumbnail', \$image, false)\"";
        }

        if (Mage::helper('mstcore')->isModuleInstalled('Amasty_Fpc')) {
            if (!file_exists(Mage::getBaseDir().'/app/etc/modules/1Mirasvit_Seo.xml')) {
                $result = self::FAILED;
                $description[] = "Amasty Fpc installed. Please, rename file /app/etc/modules/Mirasvit_Seo.xml to /app/etc/modules/1Mirasvit_Seo.xml";
            }
        }

        if (Mage::helper('mstcore')->isModuleInstalled('Creare_CreareSeoCore')) {
            $result = self::FAILED;
            $description[] = "Creare CreareSeoCore installed. Please disable this extension, because it has the same functions as Mirasvit SEO.";
        }

        if (Mage::helper('mstcore')->isModuleInstalled('Creare_CreareSeoSitemap')) {
            $result = self::FAILED;
            $description[] = "Creare_CreareSeoSitemap installed. Please disable this extension, because it has the same functions as Mirasvit SEO.";
        }

        return array($result, $title, $description);
    }
}