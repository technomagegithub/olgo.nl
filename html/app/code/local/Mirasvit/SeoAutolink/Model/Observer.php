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



class Mirasvit_SeoAutolink_Model_Observer extends Varien_Object
{
    /**
     * @var Mirasvit_SeoAutolink_Helper_Data
     */
    protected $helper;

    /**
     * @var Mirasvit_SeoAutolink_Model_Config
     */
    protected $config;

    /** @var int */
    protected $shortDescriptionCounter = 0;

    public function __construct()
    {
        $this->helper = Mage::helper('seoautolink');
        $this->config = Mage::getSingleton('seoautolink/config');
    }

    public function addCustomAttributeOutputHandler(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Helper_Output $outputHelper */
        $outputHelper = $observer->getEvent()->getHelper();
        $outputHelper->addHandler('productAttribute', $this);
        $outputHelper->addHandler('categoryAttribute', $this);
    }

    public function categoryAttribute(Mage_Catalog_Helper_Output $outputHelper, $outputHtml, $params)
    {
        if (!Mage::registry('current_category')) {
            return $outputHtml;
        }

        if ($params['attribute'] == 'description' &&
            in_array(Mirasvit_SeoAutolink_Model_Config_Source_Target::CATEGORY_DESCRIPTION, $this->config->getTarget())
        ) {
            $outputHtml = $this->helper->addLinks($outputHtml);
        }

        return $outputHtml;
    }

    public function productAttribute(Mage_Catalog_Helper_Output $outputHelper, $outputHtml, $params)
    {
        if (!Mage::registry('current_product')) {
            return $outputHtml;
        }

        if ($params['attribute'] == 'short_description'
            && $this->shortDescriptionCounter == 0  //we don't add links in short descriptions twice
            && in_array(Mirasvit_SeoAutolink_Model_Config_Source_Target::PRODUCT_SHORT_DESCRIPTION, $this->config->getTarget())
        ) {
            $outputHtml = $this->helper->addLinks($outputHtml);
            $this->shortDescriptionCounter++;
        }

        if ($params['attribute'] == 'description'
            && in_array(Mirasvit_SeoAutolink_Model_Config_Source_Target::PRODUCT_FULL_DESCRIPTION, $this->config->getTarget())
        ) {
            if ($this->helper->getMaxLinkPerPage()
                && $this->helper->getMaxLinkPerPage() >= $this->helper->getLinkSum()
            ) {
                $this->helper->setMaxLinkPerPage($this->helper->getMaxLinkPerPage() - $this->helper->getLinkSum());
            }
            $outputHtml = $this->helper->addLinks($outputHtml);
        }

        return $outputHtml;
    }

    public function cmsPageOutputHandler($e)
    {
        if (!in_array(Mirasvit_SeoAutolink_Model_Config_Source_Target::CMS_PAGE, $this->config->getTarget())) {
            return;
        }

        /** @var Mage_Cms_Model_Page $page */
        $page = $e->getPage();
        $outputHtml = $this->helper->addLinks($page->getContent());
        $page->setContent($outputHtml);
    }

//can't add link to reviews, because they use htmlspecialchars
//    public function reviewLoadAfterEvent($e) {
//        if (true || in_array(Mirasvit_SeoAutolink_Model_Config_Source_Target::CATEGORY_DESCRIPTION, $this->getConfig()->getTarget())) {
//            $review = $e->getDataObject();
//
//            $outputHtml = $review->getDetail();
//            $outputHtml = Mage::helper('seoautolink')->addLinks($outputHtml);
//            $review->setDetail($outputHtml);
//        }
//        return $this;
//    }
}
