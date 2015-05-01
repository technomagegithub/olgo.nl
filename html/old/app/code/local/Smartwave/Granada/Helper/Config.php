<?php

class Smartwave_Granada_Helper_Config extends Mage_Core_Helper_Abstract
{
    // Path and directory of the automatically generated CSS
    protected $_generatedCssFolder;
    protected $_generatedCssPath;
    protected $_generatedCssDir;
    
    public function __construct()
    {
        //Create paths
        $this->_generatedCssFolder = 'css/_config/';
        $this->_generatedCssPath = 'frontend/smartwave/granada/' . $this->_generatedCssFolder;
        $this->_generatedCssDir = Mage::getBaseDir('skin') . '/' . $this->_generatedCssPath;
    }
    
    // Get automatically generated CSS directory
    public function getGeneratedCssDir()
    {
        return $this->_generatedCssDir;
    }

    // Get design configuration css file path
    public function getDesignCssFile()
    {
        return $this->_generatedCssFolder . 'design_' . Mage::app()->getStore()->getCode() . '.css';
    }
    // Get setting configuration css file path
    public function getSettingCssFile()
    {
        return $this->_generatedCssFolder . 'setting_' . Mage::app()->getStore()->getCode() . '.css';
    }
}
