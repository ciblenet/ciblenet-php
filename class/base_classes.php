<?php
define('CS_REST_GET', 'GET');
define('CS_REST_POST', 'POST');
define('CS_REST_PUT', 'PUT');
define('CS_REST_DELETE', 'DELETE');

class CIB_REST_Result {

    var $response;
    
    var $http_status_code;
    
    function __construct($response, $code) {
        $this->response = $response;
        $this->http_status_code = $code;
    }

    function was_successful() {
        return $this->http_status_code >= 200 && $this->http_status_code < 300;
    }
}

class Ciblenet
{
	private $_app_id;
	private $_app_key;
	private $_api_url;
	var $protocol = 'http';
	var $host = 'api.ciblenet.com';
	var $_base_route;
	var $_client_base_route;
	
	var $_default_call_options;
	
	public function __construct($app_id, $app_key, $api_url = null)
	{
		$this->_app_id = $app_id;
		$this->_app_key = $app_key;
		$this->_api_url = $api_url;
		
	 	$this->_base_route = $this->protocol.'://'.$this->host.'/v1/';
	 	
	 	
	 	$this->_default_call_options = array (
            'contentType' => 'application/json; charset=utf-8', 
            'deserialise' => true,
        );

	}
	
	public function sendRequest($request_params)
	{
		$request_params['uri'] = $_SERVER['REQUEST_URI'];
	
		$enc_request = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->_app_key, json_encode($request_params), MCRYPT_MODE_ECB));
		$params = array();
		$params['enc_request'] = $enc_request;
		$params['app_id'] = $this->_app_id;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_api_url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result = curl_exec($ch);
	
		$result = @json_decode($result);
		
		if( $result == false || isset($result->success) == false ) {
			throw new Exception('Request was not correct');
		}
		
		if( $result->success == false ) {
			throw new Exception($result->errormsg);
		}
		
		return $result->data;
	}
	
	function get_request($route, $call_options = array()){
	print_r($this->_base_route.'get/'.$route);
	    return $this->_call($call_options, CS_REST_GET, $this->_base_route.'get/'.$route);
	}
	
	
	 function _call($call_options, $method, $route, $data = NULL) {
		$call_options['route'] = $route;
        $call_options['method'] = $method;
        $call_options['api_key'] = $this->_app_key;
        $call_options['api_url'] = $this->_api_url;
        
		//print_r($call_options);
		
        $call_options = array_merge($this->_default_call_options, $call_options);
        
        $call_result = self::make_call($call_options);
        
        if($call_options['deserialise']) {
            $call_result['response'] = json_decode($call_result['response']);
        }
        
        return new CIB_REST_Result($call_result['response'], $call_result['code']);
       // return $route;
	}
	
	 function make_call($call_options) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $call_options['route']);
        curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $call_options);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     	//curl_setopt($ch, CURLOPT_HEADER, true);
       // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
       // curl_setopt($ch, CURLOPT_USERPWD, $call_options['credentials']);
       // curl_setopt($ch, CURLOPT_USERAGENT, $call_options['userAgent']);
       // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: '.$call_options['contentType']));
     
        $response = curl_exec($ch);

        $result = array(
			'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
		    'response' => $response
        );
        
 		curl_close($ch);
 		return $result;
	}
}

?>