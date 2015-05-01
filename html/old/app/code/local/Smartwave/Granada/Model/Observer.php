<?php
/**
 * Call actions after configuration is saved
 */
class Smartwave_Granada_Model_Observer
{
    // After any system config is saved
    public function granada_controllerActionPostdispatchAdminhtmlSystemConfigSave()
    {
        $section = Mage::app()->getRequest()->getParam('section');
        
        $websiteCode = Mage::app()->getRequest()->getParam('website');
        $storeCode = Mage::app()->getRequest()->getParam('store');
        
        Mage::getSingleton('granada/config_generator')->generateCss('setting',$websiteCode, $storeCode);
        Mage::getSingleton('granada/config_generator')->generateCss('design',$websiteCode, $storeCode);
    }
    
    // After store view is saved
    public function granada_storeEdit(Varien_Event_Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        $storeCode = $store->getCode();
        $websiteCode = $store->getWebsite()->getCode();
        
        Mage::getSingleton('granada/config_generator')->generateCss('setting',$websiteCode, $storeCode);
        Mage::getSingleton('granada/config_generator')->generateCss('design',$websiteCode, $storeCode);
    }
}
