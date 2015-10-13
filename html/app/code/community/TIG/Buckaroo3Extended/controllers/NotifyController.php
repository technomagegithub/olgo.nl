<?php
class TIG_Buckaroo3Extended_NotifyController extends Mage_Core_Controller_Front_Action
{

    protected $_order;
    protected $_postArray;
    protected $_debugEmail;
    protected $_paymentMethodCode;
    protected $_paymentCode;
    protected $_processPush;


    public function setCurrentOrder($order)
    {
        $this->_order = $order;
    }

    public function getCurrentOrder()
    {
        return $this->_order;
    }

    public function setPostArray($array)
    {
        $this->_postArray = $array;
    }

    public function getPostArray()
    {
        return $this->_postArray;
    }

    public function setMethod($paymentMethod)
    {
        $this->_paymentMethodCode = $paymentMethod;
    }

    public function getMethod()
    {
        return $this->_paymentMethodCode;
    }

    public function setDebugEmail($debugEmail)
    {
        $this->_debugEmail = $debugEmail;
    }

    public function getDebugEmail()
    {
        return $this->_debugEmail;
    }

    public function setPushLock($orderId)
    {
        $this->_processPush = Mage::getModel('buckaroo3extended/process')->setId('push_' . $orderId);
    }

    public function getPushLock()
    {
        return $this->_processPush;
    }

    /**
     *
     * Prevents the page from being displayed using GET
     */
    public function preDispatch()
    {
        $postData = $this->getRequest()->getPost();
        if (empty($postData)) {
            echo 'Only Buckaroo can call this page properly.';
            exit;
        }
        return parent::preDispatch();
    }

    /**
     *
     * Handles 'pushes' sent by Buckaroo meant to update the current status of payments/orders
     */
    public function pushAction()
    {
        $postData = $this->getRequest()->getPost();

        $this->_debugEmail = '';
        if (isset($postData['brq_invoicenumber'])) {
            $this->_postArray = $postData;
            $orderId = $this->_postArray['brq_invoicenumber'];
        } else if (isset($postData['bpe_invoice'])) {
            $this->_restructurePostArray();
            $orderId = $this->_postArray['brq_invoicenumber'];
        } else {
            return false;
        }

        $this->_debugEmail .= 'Buckaroo push recieved at ' . date('Y-m-d H:i:s') . "\n";
        $this->_debugEmail .= 'Order ID: ' . $orderId . "\n";

        if (isset($postData['brq_test']) && $postData['brq_test'] == 'true') {
            $this->_debugEmail .= "\n/////////// TEST /////////\n";
        }

        $this->_order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

        if (!$this->_order) {
            return false;
        }


        //order exists, instantiate the lock-object for the push
        $this->setPushLock($this->_order->getId());

        if ($this->_processPush->isLocked())
        {
            $this->_debugEmail .= "\n".'Currently another push is being processed, the current push will not be processed.'."\n";
            $this->_debugEmail .= "\n".'sent from: ' . __FILE__ . '@' . __LINE__."\n";
            $module = Mage::getModel('buckaroo3extended/abstract', $this->_debugEmail);
            $module->setDebugEmail($this->_debugEmail);
            $module->sendDebugEmail();
            return false;
        }

        $this->_processPush->lockAndBlock();
        $this->_debugEmail .= "\n".'Push is gelocked, hij kan nu verwerkt worden.'."\n";

        $this->_paymentCode = $this->_order->getPayment()->getMethod();

        $this->_debugEmail .= 'Payment code: ' . $this->_paymentCode . "\n\n";
        $this->_debugEmail .= 'POST variables recieved: ' . var_export($this->_postArray, true) . "\n\n";

        try {
            list($module, $processedPush) = $this->_processPushAccordingToType();
        } catch (Exception $e) {
            $this->_debugEmail .= "An Exception occurred: " . $e->getMessage() . "\n";
            $this->_debugEmail .= "\nException trace: " . $e->getTraceAsString() . "\n";

            Mage::register('buckaroo_push-error', true);
            Mage::helper('buckaroo3extended')->logException($e);
            //this will allow the script to continue unhindered
            $processedPush = false;
            $module = Mage::getModel('buckaroo3extended/abstract', $this->_debugEmail);
        }
        $this->_debugEmail = $module->getDebugEmail();

        if ($processedPush === false) {
            $this->_debugEmail .= 'Push was not fully processed!';
        }

        $this->_debugEmail .= "\n".' sent from: ' . __FILE__ . '@' . __LINE__;

        // Remove the lock.
        $this->_processPush->unlock();
        $this->_debugEmail .= "\n".'Push is afgerond, lock is vrij gegeven'."\n";
        //send debug email
        $module->setDebugEmail($this->_debugEmail);
        $module->sendDebugEmail();
    }

    public function returnAction()
    {
        $postData = $this->getRequest()->getPost();
        if (isset($postData['brq_invoicenumber'])) {
            $orderId = $postData['brq_invoicenumber'];
        } else {
            $this->_redirect('');
            return;
        }

        $this->_order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

        $this->_paymentCode = $this->_order->getPayment()->getMethod();

        $debugEmail = 'Payment code: ' . $this->_paymentCode . "\n\n";
        $debugEmail .= 'POST variables received: ' . var_export($postData, true) . "\n\n";

        /**
         * @var TIG_Buckaroo3Extended_Model_Response_Return $module
         */
        $module = Mage::getModel(
            'buckaroo3extended/response_return',
            array(
                'order'      => $this->_order,
                'postArray'  => $postData,
                'debugEmail' => $debugEmail,
                'method'     => $this->_paymentCode,
            )
        );

        $module->processReturn();

        $this->_redirect('');
    }

    protected function _processPushAccordingToType()
    {
        if (
            $this->_order->getTransactionKey() == $this->_postArray['brq_transactions']
            || (isset($this->_postArray['brq_relatedtransaction_partialpayment'])
            && $this->_order->getTransactionKey() == $this->_postArray['brq_relatedtransaction_partialpayment'])
        ) {
            list($processedPush, $module) = $this->_updateOrderWithKey();
            return array($module, $processedPush);
        }

        $this->_paymentCode = $this->_order->getPayment()->getMethod();
        $merchantKey        = Mage::getStoreConfig('buckaroo/buckaroo3extended/key', $this->_order->getStoreId());

        //fix for payperemail transactions with different transaction keys but belongs to the same order
        if (
            $this->_paymentCode == 'buckaroo3extended_payperemail'
            && $this->_postArray['brq_transaction_method'] != 'payperemail'
            && $this->_order->getIncrementId() == $this->_postArray['brq_invoicenumber']
            && (
                isset($this->_postArray['brq_websitekey'])
                && $merchantKey == $this->_postArray['brq_websitekey']
               )
        ){
            list($processedPush, $module) = $this->_updateOrderWithKey();
            return array($module, $processedPush);
        }

        if ($this->_pushIsCreditmemo($this->_postArray)) {
            list($processedPush, $module) = $this->_updateCreditmemo();
            return array($module, $processedPush);
        }

        if (isset($this->_postArray['brq_amount_credit'])) {
            list($processedPush, $module) = $this->_newRefund();
            return array($module, $processedPush);
        }

        if (!$this->_order->getTransactionKey()) {
            list($processedPush, $module) = $this->_updateOrderWithoutKey();
            return array($module, $processedPush);
        }

        Mage::throwException('unable to process PUSH');
        return false;
    }

    protected function _updateOrderWithKey()
    {
        $this->_debugEmail .= "Transaction key matches the order. \n";

        $module = Mage::getModel(
            'buckaroo3extended/response_push',
            array(
                'order'      => $this->_order,
                'postArray'  => $this->_postArray,
                'debugEmail' => $this->_debugEmail,
                'method'     => $this->_paymentCode,
            )
        );

        $processedPush = $module->processPush();

        return array($processedPush, $module);
    }

    protected function _updateOrderWithoutKey()
    {
        $this->_debugEmail .= "Order does not yet have a transaction key and the PUSH does not constitute a refund. \n";

        $this->_order->setTransactionKey($this->_postArray['brq_transactions'])
              ->save();

        $this->_debugEmail .= "Transaction key saved: {$this->_postArray['brq_transactions']}";

        $module = Mage::getModel(
            'buckaroo3extended/response_push',
            array(
                'order'      => $this->_order,
                'postArray'  => $this->_postArray,
                'debugEmail' => $this->_debugEmail,
                'method'     => $this->_paymentCode,
            )
        );

        $processedPush = $module->processPush();

        return array($processedPush, $module);
    }

    /**
     * Creditmemo updates are currently not supported
     */
    protected function _updateCreditmemo()
    {
        $this->_debugEmail .= "Recieved PUSH to update creditmemo. Unfortunately the module does not support creditmemo updates at this time. The PUSH is ignored.";

        $debugEmailConfig = Mage::getStoreConfig('buckaroo/buckaroo3extended_advanced/debug_email', $this->_order->getStoreId());
        if (empty($debugEmailConfig))
        {
            return;
        }

        $mail = $this->_debugEmail;

        mail(
            Mage::getStoreConfig('buckaroo/buckaroo3extended_advanced/debug_email', $this->_order->getStoreId()),
            'Buckaroo 3 Extended Debug Email',
            $mail
        );

        return $this;
    }

    protected function _newRefund()
    {
        $this->_debugEmail .= "The PUSH constitutes a new refund. \n";

        if (isset($this->_postArray['ADD_refund_initiated_in_magento'])) {
            $this->_debugEmail .= "The order is already being refunded. \n";
             mail(
                Mage::getStoreConfig('buckaroo/buckaroo3extended_advanced/debug_email', $this->_order->getStoreId()),
                'Buckaroo 3 Extended Debug Email',
                $this->_debugEmail
            );
            exit;
        }

        $module = Mage::getModel(
            'TIG_Buckaroo3Extended_Model_Refund_Creditmemo',
            array(
                'order'      => $this->_order,
                'postArray'  => $this->_postArray,
                'debugEmail' => $this->_debugEmail,
            )
        );
        $module->setRequest($this->getRequest());

        try {
            $processedPush = $module->processBuckarooRefundPush();
        } catch (Exception $e) {
            Mage::logException($e);exit;
        }

        return array($processedPush, $module);
    }

    protected function _updateOrderWithoutMatchingKey()
    {
        //the following payment methods allow the payment to be performed with a different method than the initial transaction request
        if (
            $this->_order->getpayment()->getMethod() != $this->_postArray['brq_transaction_method']
            &&
                ($this->_order->getpayment()->getMethod() == 'payperemail'
                || $this->_order->getpayment()->getMethod() == 'onlinegiro'
                )
        ) {
            return $this->_updateOrderWithKey();
        }
        Mage::throwException('Unable to match push to order or creditmemo');
    }

    protected function _pushIsCreditmemo()
    {
        foreach ($this->_order->getCreditmemosCollection() as $creditmemo)
        {
            if ($creditmemo->getTransactionKey() == $this->_postArray['brq_transactions']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Restructure the Push message sent by the BPE 2.0 to one resembling a push message sent by BPE 3.0.
     * This way push messages sent to update a 2.0 transaction can still be processed.
     */
    protected function _restructurePostArray()
    {
        $postArray = array(
            'brq_amount'             => round($_POST['bpe_amount'] / 100, 2),
            'brq_currency'           => $_POST['bpe_currency'],
            'brq_invoicenumber'      => $_POST['bpe_invoice'],
            'brq_statuscode'         => $_POST['bpe_result'],
            'brq_statusmessage'      => null,
            'brq_test'               => $_POST['bpe_mode'] ? 'true' : 'false',
            'brq_timestamp'          => $_POST['bpe_timestamp'],
            'brq_transaction_method' => null,
            'brq_transaction_type'   => null,
            'brq_transactions'       => $_POST['bpe_trx'],
            'brq_signature'          => $_POST['bpe_signature2'],
            'isOldPost'              => true,
            'oldPost'                => $_POST,
        );

        $this->setPostArray($postArray);
    }
}