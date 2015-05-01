<?php 
class Smartwave_Granada_Model_Config_Generator extends Mage_Core_Model_Abstract
{ 
    public function __construct()
    {
        parent::__construct(); 
    } 
    
    public function generateCss($type, $websiteCode, $storeCode)
    {
        if ($websiteCode)
        { 
            if ($storeCode) 
            {
                $this->_generateStoreCss($type, $storeCode); 
            } 
            else 
            {
                $this->_generateWebsiteCss($type, $websiteCode); 
            }
        }
        else
        {
            $websites = Mage::app()->getWebsites(false, true);
            foreach ($websites as $website => $value) 
            {
                $this->_generateWebsiteCss($type, $website); 
            }
        } 
    }
    
    protected function _generateWebsiteCss($type, $websiteCode) 
    {
        $store = Mage::app()->getWebsite($websiteCode);
        foreach ($store->getStoreCodes() as $storeCode)
        { 
            if (!$this->_generateStoreCss($type, $storeCode))
                break;
        }
    } 
    
    protected function _generateStoreCss($type, $storeCode)
    {
        if (!Mage::app()->getStore($storeCode)->getIsActive()) 
            return false;
        
        $fileName = $type . '_' . $storeCode . '.css';            
        $file = Mage::helper('granada/config')->getGeneratedCssDir() . $fileName;
        $templateFile = 'smartwave/granada/css/' . $type .'.phtml';
        Mage::register('granada_css_generate_store', $storeCode);
        try
        { 
            $tempalte = Mage::app()->getLayout()->createBlock("core/template")->setData('area', 'frontend')->setTemplate($templateFile)->toHtml();
            if (empty($tempalte)) 
            {
                throw new Exception( Mage::helper('granada')->__("Template file is empty or doesn't exist: %s", $templateFile) );
                return false;
            }
            $io = new Varien_Io_File(); 
            $io->setAllowCreateFolders(true); 
            $io->open(array( 'path' => Mage::helper('granada/config')->getGeneratedCssDir() ));
            $io->streamOpen($file, 'w+'); 
            $io->streamLock(true); 
            $io->streamWrite($tempalte); 
            $io->streamUnlock(); 
            $io->streamClose(); 
        }
        catch (Exception $exception)
        { 
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('granada')->__('Failed generating CSS file: %s in %s', $fileName, Mage::helper('granada/config')->getGeneratedCssDir()). '<br/>Message: ' . $exception->getMessage());
            Mage::logException($exception);
            return false;
        }
        Mage::unregister('granada_css_generate_store');
        return true; 
    } 
}