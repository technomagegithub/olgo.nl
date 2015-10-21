<?php

class Sendcloud_Transporter_Model_Mysql4_Transporter_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
    {
        public function _construct()
        {
            parent::_construct();
            $this->_init('transporter/transporter');
        }
    }
	
?>