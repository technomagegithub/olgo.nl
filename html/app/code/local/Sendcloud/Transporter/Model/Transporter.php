<?php
class Sendcloud_Transporter_Model_transporter extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('transporter/transporter');
    }
}
?>