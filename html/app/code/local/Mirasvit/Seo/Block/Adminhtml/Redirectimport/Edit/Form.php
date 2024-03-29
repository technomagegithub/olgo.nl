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




class Mirasvit_Seo_Block_Adminhtml_Redirectimport_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getData('action'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ));

        $general = $form->addFieldset('general_fieldset', array(
            'legend' => Mage::helper('seo')->__('General'),
            'class'  => 'fieldset-wide'
        ));

        $example = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'seo/seo_redirect_example.csv';
        $general->addField('file', 'file', array(
            'name'     => 'file',
            'label'    => Mage::helper('seo')->__('Import File (.csv)'),
            'required' => true,
            'note'	=> "<a href='$example'>Example of CSV file</a>"
        ));

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}