<?php


require_once 'app/Mage.php';
Mage::app();

// loads the proper email template
$emailTemplate  = Mage::getModel('core/email_template')->loadDefault('sales_email_order_template');
$emailTemplate = Mage::getModel('core/email_template')->loadByCode('Nieuwe factuur Olgo');
$emailTemplate = Mage::getModel('core/email_template')->loadByCode('Nieuw Account Olgo');
$emailTemplate = Mage::getModel('core/email_template')->loadByCode('Bevestigingscode nieuw account Olgo');
$emailTemplate = Mage::getModel('core/email_template')->loadByCode('Creditfactuur Bijgewerkt Olgo');
$emailTemplate = Mage::getModel('core/email_template')->loadByCode('Nieuwe creditfactuur Olgo');

// All variables your error log tells you that are missing can be placed like this:

$emailTemplateVars = array();
$emailTemplateVars['usermessage'] = "blub";
$emailTemplateVars['store'] = Mage::app()->getStore();
$emailTemplateVars['sendername'] = 'sender name';
$emailTemplateVars['receivername'] = 'receiver name';

// order you want to load by ID
$order = Mage::getModel('sales/order')->load(18);
$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
$emailTemplateVars['customer'] = $customer;
$emailTemplateVars['order'] = $order;

if ($order->hasInvoices()) {
    $invIncrementIDs = array();
    foreach ($order->getInvoiceCollection() as $inv) {
        $invIncrementIDs[] = $inv->getIncrementId();
        $emailTemplateVars['invoice'] = $inv;
    } 
}

if ($order->hasCreditmemos()) {
  $creditMemos = array();
  foreach($order->getCreditmemosCollection() as $credit) {
        $emailTemplateVars['creditmemo'] = $credit; // Yes we want only the last  one
  }
}

// load payment details:
// usually rendered by this template:
// web/app/design/frontend/base/default/template/payment/info/default.phtml
$order = $emailTemplateVars['order'];
$paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())->setIsSecureMode(true);
$paymentBlock->getMethod()->setStore(Mage::app()->getStore()); 

$emailTemplateVars['payment_html'] = $paymentBlock->toHtml();

//displays the rendered email template
echo $emailTemplate->getProcessedTemplate($emailTemplateVars);



?>
