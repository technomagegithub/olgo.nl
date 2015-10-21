<?php

/* A class set up the parcel data */

class Sendcloud_Transporter_Model_Parcel {
	
	protected $data;
	
	function set($key, $data) {
		$this->data[$key] = $data;
	}
	
	function get($key) {
		$this->data[$key];
	}
	
	function toArray() {
		return $this->data;
	}
	
}