<?php 
    class Smartwave_Granada_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action{
        public function indexAction() {
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/granada_setting/"));
        }
        public function blocksAction() {
            $isoverwrite = Mage::helper('granada')->getConfig('install/overwrite_blocks');
            Mage::getSingleton('granada/import_cms')->importCms('cms/block', 'blocks', $isoverwrite);
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/granada_setting/"));
        }
        public function pagesAction() {
            $isoverwrite = Mage::helper('granada')->getConfig('install/overwrite_pages');
            Mage::getSingleton('granada/import_cms')->importCms('cms/page', 'pages', $isoverwrite);
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/granada_setting/"));
        }
    }
?>