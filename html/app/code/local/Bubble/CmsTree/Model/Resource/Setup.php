<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     1.3.9
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
    /**
     * Do not install module if Magento is not installed yet.
     * This prevents error during Mage_Cms data install.
     *
     * @see Mage_Core_Model_Resource_Setup::applyUpdates()
     */
    public function applyUpdates()
    {
        if (!Mage::isInstalled()) {
            $modules = Mage::getConfig()->getNode('modules')->children();
            $myModule = substr(__CLASS__, 0, strpos(__CLASS__, '_Model'));
            foreach ($modules as $moduleName => $moduleNode) {
                if ($moduleName != $myModule) {
                    Mage::getConfig()->addAllowedModules($moduleName);
                }
            }
            Mage::getConfig()->reinit();

            return $this;
        }

        return parent::applyUpdates();
    }
}