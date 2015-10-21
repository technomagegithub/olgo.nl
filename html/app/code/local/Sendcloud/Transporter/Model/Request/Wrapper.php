<?php


class Sendcloud_Transporter_Model_Request_Wrapper 
{
	
	
	
	const HOST_URL_TEST = 'http://demo.sendcloud.nl/api/v2/';
	const HOST_URL_LIVE = 'https://panel.sendcloud.nl/api/v2/';
	
	/*
		API information
		API Key: Your public api key
		API Secret: Your secret api key
	*/
	protected $api_key;
	protected $api_secret;
	
	/*
	@var SendcloudApiParcelResource
	*/
	public $parcel;
	
	/*
	@var SendcloudApiShippingResource
	*/
	public $shipping_method;
	
	/*
	@var SendcloudApiUserResource
	*/
	public $user;
	
	/*
	@var SendcloudApiLabelResource
	*/
	public $label;
	
	/*
	@var apiUrl
	@description The API url which has been chosen by user. Will be filled in by setEnviroment()
	Called in the constructor
	*/
	protected $apiUrl;
	
	function __construct($env, $api_key, $api_secret) {
		$this->setApiKeys($api_key, $api_secret);
		$this->setEnviroment($env);
		
		$this->_setupResources();
	}
	
	
	function setEnviroment($enviroment) {
		if ($enviroment == 'live') {
			$this->apiUrl = self::HOST_URL_LIVE;
		}
		else {
			$this->apiUrl = self::HOST_URL_TEST;
		}
		
	}
	function setApiKeys($api_key = false, $api_secret = false) {
		if ($api_key || $api_secret) {
			$this->api_key = $api_key;
			$this->api_secret = $api_secret;
		}
		else {
			throw new SendcloudApiException('You must have an API key and an API secret key');
		}
	}
	
	 function _setupResources() {
		$this->parcels = new SendcloudApiParcelsResource($this);
		$this->shipping_methods = new SendcloudApiShippingResource($this);
		$this->user = new SendcloudApiUserResource($this);
		$this->label = new SendcloudApiLabelResource($this);
	}
	
	function getApiKey() {
		return $this->api_key;
	}
	
	function getApiSecret() {
		return $this->api_secret;
	}
	
	public function create($url, $post, $return_object) {
		return $this->sendRequest($url, 'post', $post, $return_object);
	}
	
	public function get($url, $params, $return_object) {
		return $this->sendRequest($url, 'get', $params, $return_object);
	}
	
	public function update($url, $params, $return_object) {
		return $this->sendRequest($url, 'put', $params, $return_object);
	}
	
	public function getUrl($url, $params = null) {
		
		$api_url			= $this->apiUrl;
		$api_parsed		= parse_url($api_url);
		$resource_url	= parse_url($url);
		
		
		$apiUrl = $api_parsed['scheme'].'://'.$this->getApiKey().':'.$this->getApiSecret().'@'.$api_parsed['host'].'/';
		
		if(isset($api_parsed['path']) && strlen(trim($api_parsed['path'], '/')))
		{
			$apiUrl .= trim($api_parsed['path'], '/').'/';
		}
		
		$apiUrl .= $resource_url['path'];
		
		if(isset($resource_url['query']))
		{
			$apiUrl .= '?'.$resource_url['query'];
		}
		elseif($params && is_array($params))
		{
			$queryParameters = array();

			foreach($params as $key => $value)
			{
				if(!is_array($value))
				{
					$queryParameters[] = $key.'='.urlencode($value);
				}
			}

			$queryParameters = implode('&', $queryParameters);

			if(!empty($queryParameters))
			{
				$apiUrl .= '?'.$queryParameters;
			}
		}

		return $apiUrl;
	}
	
	
	public function sendRequest($url, $method, $object, $return_object) {
		
		$curl_options = array();
		
		if ($method == 'post' || $method == 'put') {
			// there must be an object
			if (!$object) {
				throw new SendcloudApiException('There must be an object when we want to create or update');
			}
			
			$curl_options = array(
				CURLOPT_URL				=> $this->getUrl($url),
				CURLOPT_HTTPHEADER		=> array('Content-Type: application/json'),
				CURLOPT_CUSTOMREQUEST	=> strtoupper($method),
				CURLOPT_POSTFIELDS		=> json_encode($object),
			);
			
		}
		else {
			
			// The else. It's probally an get request
			$curl_options = array(
				CURLOPT_URL				=> $this->getUrl($url, $object),
			);
		}
		
		$curl_options += array(
			CURLOPT_HEADER				=> false,
			CURLOPT_RETURNTRANSFER		=> true,
			CURLOPT_SSL_VERIFYPEER		=> true,
			CURLOPT_SSLVERSION 			=> 1,
		);
		
		
		$curl_handler = curl_init();
		
		curl_setopt_array($curl_handler, $curl_options);
		
		// finally. Request the hole thing
		$response_body	= curl_exec($curl_handler);
		$response_body	= json_decode($response_body, true);
		$response_code	= curl_getinfo($curl_handler, CURLINFO_HTTP_CODE);
		if(json_last_error() != JSON_ERROR_NONE) {
			throw new SendCloudApiException("Error parsing json: ".json_last_error_msg());
		}
		if(curl_errno($curl_handler)){
			throw new SendCloudApiException(curl_error($curl_handler));
		}

		curl_close($curl_handler);

		
		if ($response_code < 200 || empty($response_body) || $response_code > 299 || array_key_exists('error', $response_body)) {
			$this->handleResponseError($response_code, $response_body);
		}
		
		
		$response_body = array_shift($response_body);

		if (array_key_exists($return_object, $response_body)) {
			return $response_body[$return_object];
		}
		else {
			return $response_body;
		}
		
	}
	
	function handleResponseError($response_code, $response_body) {
		$error = $response_body['error'];

		if (!is_string($error) && !is_null($error)) {
			switch($response_code) {
				case 404:
					$message = 'Page not found.';
					break;
				default:
					$message = $response_code.' - Unknown error';
			}
		} else {
			$message = $response_code.' - '.$response_body['error'];
		}

		if (!is_array($error) || !array_key_exists('code', $error)) {
			$code = -99;
		} else {
			$code = $error['code'];
		}

		throw new SendCloudApiException($message, $code);
	}
	
	
}


class SendcloudApiException extends Exception {
	
	function __consturct($message) {
		$this->message = $message;
	}
	
}

abstract class SendcloudApiAbstractResource {
	
	/**
	 * @var WebshopappApiClient
	 */
	protected $client;
	
	
	/**
	* Settings to other classes
	**/
	protected $create_request = true;
	protected $get_request = true;
	protected $update_request = true;
	
	protected $single_resource = '';
	protected $list_resource = '';
	protected $create_resource = '';
	protected $update_resource = '';
	protected $resource = '';
	
	
	function __construct($client) {
		$this->client = $client;
	}
	
	function create($object) {
		if ($this->create_request) {
			$data = array($this->create_resource => $object);
			return $this->client->create($this->resource, $data, $this->create_resource);
		}
	}
	
	function get($object_id = false, $params = null) {
		if ($this->get_request) {
			if ($object_id) {
				return $this->client->get($this->resource . '/' . $object_id, $params, $this->single_resource);
			}
			else {
				return $this->client->get($this->resource, $params, $this->list_resource );
			}
		}
	}
	
	function update($object_id = false, $data) {
		if ($this->update_request) {
			if ($object_id) {
				$fields = array($this->update_resource => $data);
	
				return $this->client->update($this->resource . '/' . $object_id, $fields, $this->update_resource);
			}
		}
		
	}
	
}


class SendcloudApiParcelsResource extends SendcloudApiAbstractResource {
	
	protected $resource = 'parcels';
	protected $create_resource = 'parcel';
	protected $update_resource = 'parcels';
	protected $list_resource = 'parcels';
	protected $single_resource = 'parcel';
	
	function create_bulk($object) {
		if ($this->create_request) {
			$data = array($this->list_resource => $object);
			
			return $this->client->create($this->resource, $data, $this->list_resource);
		}
	}
	
}

class SendcloudApiLabelResource extends SendcloudApiAbstractResource {
	
	protected $resource = 'labels';
	protected $list_resource = 'label';
	protected $single_resource = 'label';
	protected $create_resource = 'label';
	
	protected $create_request = true;
	//protected $
	
}

class SendcloudApiUserResource extends SendcloudApiAbstractResource {
	
	protected $resource = 'user';
	protected $list_resource = 'user';
	protected $single_resource = 'user';
	
	
	protected $create_request = false;
	protected $update_request = false;
	
	//protected $
	
}

class SendcloudApiShippingResource extends SendcloudApiAbstractResource {
	
	protected $resource = 'shipping_methods';
	protected $list_resource = 'shipping_methods';
	protected $single_resource = 'shipping_method';
	
	
	protected $create_request = false;
	protected $update_request = false;
	
	//protected $
	
}





