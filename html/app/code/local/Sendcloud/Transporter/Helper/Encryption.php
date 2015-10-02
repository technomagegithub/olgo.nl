<?php  

class Sendcloud_Transporter_Helper_Encryption {
    private $securekey, $iv;
	
    function __construct() {
        
        $this->iv = mcrypt_create_iv(128);
    }
	
	function setKey($key) {
		$this->securekey = hash('sha256',$key,TRUE);
	}
    function encode($input) {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->securekey, $input, MCRYPT_MODE_ECB, $this->iv));
    }
    function decode($input) {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->securekey, base64_decode($input), MCRYPT_MODE_ECB, $this->iv));
    }
}

?>