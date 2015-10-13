<?php
class TIG_Buckaroo3Extended_Model_Observer_Abstract extends TIG_Buckaroo3Extended_Model_Abstract
{
    protected $_storeId;
    /**
     *  @var Mage_Sales_Model_Order $_order
     */
    protected $_order;
    protected $_billingInfo;
    protected $_method = '';

    public function getMethod()
    {
        return $this->_method;
    }

    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    public function __construct()
    {
        $this->setStoreId(Mage::app()->getStore()->getId());
        $this->_loadLastOrder();
        $this->_setOrderBillingInfo();
    }

    /**
     * Each payment method has it's own observer. When one of the observers is called, this checks if it's
     * payment method is being used and therefore, if this observer needs to do anything.
     *
     * @param $observer
     * @return bool
     */
    protected function _isChosenMethod($observer)
    {
        $ret = false;

        $chosenMethod = $observer->getOrder()->getPayment()->getMethod();

        if ($chosenMethod === $this->_code) {
            $ret = true;
            if($observer->getOrder()->getPaymentMethodUsedForTransaction()) {
                $this->setMethod($observer->getOrder()->getPaymentMethodUsedForTransaction());
            }
        }
        return $ret;
    }

    /**
     * Add credit management required fields to the request
     *
     * @param $vars
     * @param string $serviceName
     * @return mixed
     */
    protected function _addCreditManagement(&$vars, $serviceName = 'creditmanagement')
    {
        $method = $this->_order->getPayment()->getMethod();

        $dueDaysInvoice = Mage::getStoreConfig('buckaroo/' . $method . '/due_date_invoice', $this->getStoreId());
        $dueDays = Mage::getStoreConfig('buckaroo/' . $method . '/due_date', $this->getStoreId());

        $invoiceDate = date('Y-m-d', mktime(0, 0, 0, date("m")  , (date("d") + $dueDaysInvoice), date("Y")));
        $dueDate = date('Y-m-d', mktime(0, 0, 0, date("m")  , (date("d") + $dueDaysInvoice + $dueDays), date("Y")));

        if (array_key_exists('customVars', $vars) && array_key_exists($serviceName, $vars['customVars']) && is_array($vars['customVars'][$serviceName])) {
            $vars['customVars'][$serviceName] = array_merge($vars['customVars'][$serviceName], array(
                'DateDue'                 => $dueDate,
                'InvoiceDate'             => $invoiceDate,
            ));
        } else {
            $vars['customVars'][$serviceName] = array(
                'DateDue'                 => $dueDate,
                'InvoiceDate'             => $invoiceDate,
            );
        }

        return $vars;
    }

    /**
     * Currently used by all payment methods except payment guarantee
     *
     * @param $vars
     */
    protected function _addAdditionalCreditManagementVariables(&$vars)
    {
        $VAT = 0;
        foreach($this->_order->getFullTaxInfo() as $taxRecord)
        {
            $VAT += $taxRecord['amount'];
        }

        $reminderLevel = Mage::getStoreConfig('buckaroo/buckaroo3extended_' . $this->_method . '/reminder_level', $this->getStoreId());

        $creditmanagementArray = array(
                'AmountVat'        => $VAT,
                'CustomerType'     => 1,
                'MaxReminderLevel' => $reminderLevel,
        );

        if (array_key_exists('customVars', $vars) && is_array($vars['customVars']['creditmanagement'])) {
            $vars['customVars']['creditmanagement'] = array_merge($vars['customVars']['creditmanagement'], $creditmanagementArray);
        } else {
            $vars['customVars']['creditmanagement'] = $creditmanagementArray;
        }

        if (empty($vars['customVars']['creditmanagement']['PhoneNumber']) && !empty($vars['customVars']['creditmanagement']['MobilePhoneNumber'])) {
            $vars['customVars']['creditmanagement']['PhoneNumber'] = $vars['customVars']['creditmanagement']['MobilePhoneNumber'];
        }
    }

    /**
     * Add the customer variables to the request
     *
     * @param $vars
     * @param string $serviceName
     * @return mixed
     */
    protected function _addCustomerVariables(&$vars, $serviceName = 'creditmanagement')
    {
        if (Mage::helper('buckaroo3extended')->isAdmin()) {
            $additionalFields = Mage::getSingleton('core/session')->getData('additionalFields');
        } else {
            $additionalFields = Mage::getSingleton('checkout/session')->getData('additionalFields');
        }

        if (isset($additionalFields['BPE_Customergender'])) {
            $gender = $additionalFields['BPE_Customergender'];
        } else {
            $gender = 0;
        }

        if (isset($additionalFields['BPE_customerbirthdate'])) {
            $dob = $additionalFields['BPE_customerbirthdate'];
        } else {
            $dob = '';
        }

        if (isset($additionalFields['BPE_Customermail'])) {
            $mail = $additionalFields['BPE_Customermail'];
        } else {
            $mail = $this->_billingInfo['email'];
        }

        $customerId = $this->_order->getCustomerId()
            ? $this->_order->getCustomerId()
            : $this->_order->getIncrementId();

        $firstName              = $this->_billingInfo['firstname'];
        $lastName               = $this->_billingInfo['lastname'];
        $address                = $this->_processAddressCM();
        $houseNumber            = $address['house_number'];
        $houseNumberSuffix      = $address['number_addition'];
        $street                 = $address['street'];
        $zipcode                = $this->_billingInfo['zip'];
        $city                   = $this->_billingInfo['city'];
        $state                  = $this->_billingInfo['state'];
        $fax                    = $this->_billingInfo['fax'];
        $country                = $this->_billingInfo['countryCode'];
        $processedPhoneNumber   = $this->_processPhoneNumberCM();
        $customerLastNamePrefix = $this->_getCustomerLastNamePrefix();
        $customerInitials       = $this->_getInitialsCM();

        $array = array(
            'CustomerCode'           => $customerId,
            'CustomerFirstName'      => $firstName,
            'CustomerLastName'       => $lastName,
            'FaxNumber'              => $fax,
            'CustomerInitials'       => $customerInitials,
            'CustomerLastNamePrefix' => $customerLastNamePrefix,
            'CustomerBirthDate'      => $dob,
            'Customergender'         => $gender,
            'Customeremail'          => $mail,
            'ZipCode'                => array(
                'value' => $zipcode,
                'group' => 'address'
            ),
            'City'                   => array(
                'value' => $city,
                'group' => 'address'
            ),
            'State'                  => array(
                'value' => $state,
                'group' => 'address'
            ),
            'Street'                 => array(
                'value' => $street,
                'group' => 'address'
            ),
            'HouseNumber'            => array(
                'value' => $houseNumber,
                'group' => 'address'
            ),
            'HouseNumberSuffix'      => array(
                'value' => $houseNumberSuffix,
                'group' => 'address'
            ),
            'Country'                => array(
                'value' => $country,
                'group' => 'address'
            )
        );

        if (array_key_exists('customVars', $vars) && array_key_exists($serviceName, $vars['customVars']) && is_array($vars['customVars'][$serviceName])) {
            $vars['customVars'][$serviceName] = array_merge($vars['customVars'][$serviceName], $array);
        } else {
            $vars['customVars'][$serviceName] = $array;
        }

        if ($processedPhoneNumber['mobile']) {
            $vars['customVars'][$serviceName] = array_merge($vars['customVars'][$serviceName], array(
                'MobilePhoneNumber' => $processedPhoneNumber['clean'],
            ));
        } else {
            $vars['customVars'][$serviceName] = array_merge($vars['customVars'][$serviceName], array(
                'PhoneNumber' => $processedPhoneNumber['clean'],
            ));
        }

        return $vars;
    }

    /**
     *
     * Processes billingInfo array to get the initials of the customer
     *
     * @return string
     */
    protected function _getInitialsCM()
    {
        $firstname = $this->_billingInfo['firstname'];

        $initials = '';
        $firstnameParts = explode(' ', $firstname);

        foreach ($firstnameParts as $namePart) {
            $initials .= strtoupper($namePart[0]) . '.';
        }

        return $initials;
    }

    /**
     *
     * Processes the customer's billing_address so as to fit the SOAP request. returning an array
     *
     * @return array
     */
    protected function _processAddressCM()
    {
        //get address from billingInfo
        $address = $this->_billingInfo['address'];

        $ret = array();
        $ret['house_number'] = '';
        $ret['number_addition'] = '';
        if (preg_match('#^(.*?)([0-9]+)(.*)#s', $address, $matches)) {
            if ('' == $matches[1]) {
                // Number at beginning
                $ret['house_number'] = trim($matches[2]);
                $ret['street']         = trim($matches[3]);
            } else {
                // Number at end
                $ret['street']            = trim($matches[1]);
                 $ret['house_number']    = trim($matches[2]);
                 $ret['number_addition'] = trim($matches[3]);
            }
        } else {
             // No number
             $ret['street'] = $address;
        }

         return $ret;
    }

    /**
     * processes the customer's phone number so as to fit the betaalgarant SOAP request
     *
     * @return array
     */
    protected function _processPhoneNumberCM()
    {
        $additionalFields = Mage::getSingleton('checkout/session')->getData('additionalFields');
        if (isset($additionalFields['BPE_PhoneNumber'])) {
            $number = $additionalFields['BPE_PhoneNumber'];
        } else {
            $number = ($this->_billingInfo['telephone'])?:'1234567890';
        }


        //the final output must like this: 0031123456789 for mobile: 0031612345678
        //so 13 characters max else number is not valid
        //but for some error correction we try to find if there is some faulty notation

        $return = array("orginal" => $number, "clean" => false, "mobile" => false, "valid" => false);
        //first strip out the non-numeric characters:
        $match = preg_replace('/[^0-9]/Uis', '', $number);
        if ($match) {
            $number = $match;
        }

        if (strlen((string)$number) == 13) {
            //if the length equal to 13 is, then we can check if its a mobile number or normal number
            $return['mobile'] = $this->_isMobileNumber($number);
            //now we can almost say that the number is valid
            $return['valid'] = true;
            $return['clean'] = $number;
        } elseif (strlen((string) $number) > 13) {
            //if the number is bigger then 13, it means that there are probably a zero to much
            $return['mobile'] = $this->_isMobileNumber($number);
            $return['clean'] = $this->_isValidNotation($number);
            if(strlen((string)$return['clean']) == 13) {
                $return['valid'] = true;
            }

        } elseif (strlen((string)$number) == 12 or strlen((string)$number) == 11) {
            //if the number is equal to 11 or 12, it means that they used a + in their number instead of 00
            $return['mobile'] = $this->_isMobileNumber($number);
            $return['clean'] = $this->_isValidNotation($number);
            if(strlen((string)$return['clean']) == 13) {
                $return['valid'] = true;
            }

        } elseif (strlen((string)$number) == 10) {
            //this means that the user has no trailing "0031" and therfore only
            $return['mobile'] = $this->_isMobileNumber($number);
            $return['clean'] = '0031'.substr($number,1);
            if (strlen((string) $return['clean']) == 13) {
                $return['valid'] = true;
            }
        } else {
            //if the length equal to 13 is, then we can check if its a mobile number or normal number
            $return['mobile'] = $this->_isMobileNumber($number);
            //now we can almost say that the number is valid
            $return['valid'] = true;
            $return['clean'] = $number;
        }

        return $return;
    }

    /**
     * validate the phonenumber
     *
     * @param $number
     * @return mixed
     */
    protected function _isValidNotation($number) {
        //checks if the number is valid, if not: try to fix it
        $invalidNotations = array("00310", "0310", "310", "31");
        foreach($invalidNotations as $invalid) {
            if( strpos( substr( $number, 0, strlen($invalid) ), $invalid ) !== false ) {
                $valid = substr($invalid, 0, -1);
                if (substr($valid, 0, 2) == '31') {
                    $valid = "00" . $valid;
                }
                if (substr($valid, 0, 2) == '03') {
                    $valid = "0" . $valid;
                }
                if ($valid == '3'){
                    $valid = "0" . $valid . "1";
                }
                $number = str_replace($invalid, $valid, $number);
            }
        }
        return $number;
    }

    /**
     * Checks if the number is a mobile number or not.
     *
     * @param string $number
     *
     * @return boolean
     */
    protected function _isMobileNumber($number) {
        //this function only checks if it is a mobile number, not checking valid notation
        $checkMobileArray = array("3106","316","06","00316","003106");
        foreach($checkMobileArray as $key => $value) {

            if(strpos(substr($number, 0, strlen($value)), $value) !== false) {

                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    protected function _getCustomerLastNamePrefix()
    {
        $lastName = $this->_billingInfo['lastname'];

        $lastNameBits = explode(' ', $lastName);

        if (count($lastNameBits === 1)) {
            return '';
        }

        $lastNameEnd = end($lastNameBits);
        unset($lastNameEnd);

        $prefix = implode(' ', $lastNameBits);
        return $prefix;
    }

    /**
     * Certain payment methods require a list of other payment methods that will be used to finalize the payment.
     * This method forms that list
     *
     * @return string
     */
    protected function _getPaymentMethodsAllowed()
    {
        $configAllowed = Mage::getStoreConfig('buckaroo/' . $this->_code . '/allowed_methods', $this->_order->getStoreId());

        $allowedArray = explode(',', $configAllowed);

        if (in_array('all', $allowedArray)) {
            $allowedArray = array(
                'amex',
                'directdebit',
                'giropay',
                'ideal',
                'mastercard',
                'onlinegiro',
                'paypal',
                'paysafecard',
                'sofortueberweisung',
                'transfer',
                'visa',
                'maestro',
                'visaelectron',
                'vpay',
                'bancontactmrcash',
            );
        }

        $allowedString = implode(',', $allowedArray);

        return $allowedString;
    }

    /**
     * @param $enrolled
     * @param $authenticated
     * @param $order Mage_Sales_Model_Order
     * @return mixed|null
     */
    protected function _getSecureStatus($enrolled, $authenticated, $order)
    {
        $status = null;
        $useSuccessStatus = Mage::getStoreConfig('buckaroo/' . $this->_code . '/active_status', $order->getStoreId());

        if ($enrolled && $authenticated && $useSuccessStatus) {
            switch ($order->getState()) {
                case Mage_Sales_Model_Order::STATE_PROCESSING:
                    $status = Mage::getStoreConfig(
                        'buckaroo/' . $this->_code . '/secure_status_processing',
                        $order->getStoreId());
                    break;
            }
        } elseif (!$enrolled || !$authenticated) {
            switch ($order->getState()) {
                case Mage_Sales_Model_Order::STATE_PROCESSING:
                    $status = Mage::getStoreConfig(
                        'buckaroo/' . $this->_code . '/unsecure_status_processing',
                        $order->getStoreId());
                    break;
            }
        }

        return $status;
    }

    /**
     * @param $enrolled
     * @param $authenticated
     * @param $order Mage_Sales_Model_Order
     */
    protected function _updateSecureStatus($enrolled, $authenticated, $order)
    {
        $shouldHold = Mage::getStoreConfig('buckaroo/' . $this->_code . '/unsecure_hold', $order->getStoreId());

        if (
            (!$enrolled || !$authenticated)
            && $shouldHold
            && $order->canHold())
        {
            $order->hold()->save();
        }

        $status = $this->_getSecureStatus($enrolled, $authenticated, $order);

        $enrolledString = $enrolled ? 'yes' : 'no';
        $authenticatedString = $authenticated ? 'yes' : 'no';

        if ($status) {
            $order->setStatus($status)
                  ->addStatusHistoryComment(
                      Mage::helper('buckaroo3extended')->__("3D Secure enrolled: %s<br/>3D Secure authenticated: %s", $enrolledString, $authenticatedString),
                      $status
                  );
        } else {
            $order->addStatusHistoryComment(
                      Mage::helper('buckaroo3extended')->__("3D Secure enrolled: %s<br/>3D Secure authenticated: %s", $enrolledString, $authenticatedString)
                  );
        }

        $order->save();
    }

    /**
     * @return int|mixed
     */
    protected function _getServiceVersion()
    {
        $version = Mage::getStoreConfig('buckaroo/' . $this->_code . '/service_version', $this->getStoreId());
        if (is_null($version)) {
            $version = 1;
        }

        return $version;
    }

    /**
     * @param $order Mage_Sales_Model_Order
     * @return int|mixed
     */
    protected function _getRefundServiceVersion($order)
    {
        $versionUsed = $order->getBuckarooServiceVersionUsed();

        if (!is_null($versionUsed)) {
            return $versionUsed;
        }

        return $this->_getServiceVersion();
    }

    /**
     * @param $order
     * @param $shippingAddress
     * @return array|bool
     */
    protected function _getSellerProtectionVars($order, $shippingAddress)
    {
        $checkForSellerProtection = Mage::helper('buckaroo3extended')->checkSellersProtection($order);

        if ($checkForSellerProtection){
            $arrayCustom = array(
                'Name'              =>  $shippingAddress['lastname'],
                'Street1'           =>  $shippingAddress['street'],
                'CityName'          =>  $shippingAddress['city'],
                'StateOrProvince'   =>  $shippingAddress['region'],
                'PostalCode'        =>  $shippingAddress['postcode'],
                'Country'           =>  $shippingAddress['country_id'],
                'AddressOverride'   =>  'TRUE'
            );
            return $arrayCustom;
        } else {
            return false;
        }
    }

    /**
     * @param $order Mage_Sales_Model_Order
     */
    protected function _addCommentHistoryForVirtual($order)
    {
        if($order->getIsVirtual()) {
            $checkForSellerProtection = Mage::helper('buckaroo3extended')->checkSellersProtection($order);
            if (!$checkForSellerProtection) {
                $commentVirtual = Mage::helper('buckaroo3extended')->__('The order consists of virtual product(s), which is not supported by Seller Protection.');
                $order->addStatusHistoryComment($commentVirtual)
                      ->save();
            }
        }
    }
}