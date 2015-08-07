<?php

/**
 * An exception thrown by classes in the Spinternet_Api_GroupManagement package.
 *
 * @author Joris Pinnoo <jorisp@spinternet.be>
 * @package Spinternet_Api_GroupManagement
 */

final class Scoutnet_Api_GroupManagement_Exception extends Exception {

	public function __construct($message, $code = 0) {
		$exMessage = 'Scoutnet Api: ' . $message;
		$exCode = (float)$code;
		parent::__construct($exMessage, $exCode);
	}

}

/**
 * Scoutnet.be API
 * 
 * @package Scoutnet_Api_Call
 */


class Scoutnet_Api_Call {
	
/**
 * @var string The service URL used on the production server.
 */

private static $_API_HOST = "";
private static $_API_HOST_GROUP = "apis.scoutnet.be/group/v1/"; //generally don't change this
private static $_API_HOST_MEMBER = "apis.scoutnet.be/member/v4/"; //generally don't change this
	
private static $_devkey;
private static $_appkey;
private static $_secret;
private static $_debug;
private static $instance;
//public $warnings=array();
	
	/**
	 * Class constructor.
	 * 
	 * @param string $devkey : emailaddress from developer (deprecated)
	 * @param string $appkey : sn-number: snxxxx
	 * @param string $secret : secret key
	 * @param boolean $debug : enable debug info
	 * @throws Scoutnet_Api_GroupManagement_Exception When the given $devkey $appkey $secret is not valid
	 * @ignore
	 */
	public function __construct($app,$devkey,$appkey,$secret,$debug) {
	
		if ($app=='group'){self::$_API_HOST = self::$_API_HOST_GROUP;}
		if ($app=='member'){self::$_API_HOST = self::$_API_HOST_MEMBER;}

		if (!$this->_isValidDevKey($devkey)) {
			$errMsg = sprintf('The given devkey is invalid: "%s"', $devkey);
			throw new Scoutnet_Api_GroupManagement_Exception($errMsg);
		}
		
		self::$_devkey = $devkey;
		
		if (!$this->_isValidAppKey($appkey)) {
			$errMsg = sprintf('The given appkey is invalid: "%s"', $appkey);
			throw new Scoutnet_Api_GroupManagement_Exception($errMsg);
		}
		self::$_appkey = $appkey;
		
		if (!$this->_isValidKey($secret)) {
			$errMsg = sprintf('The given secret is invalid: "%s"', $secret);
			throw new Scoutnet_Api_GroupManagement_Exception($errMsg);
		}
		self::$_secret = $secret;

		if ($debug){self::$_debug = true;}
		
		if (!isset(self::$instance)) { 
		self::$instance = $this;
	}
		
		
		
	}
	

	private function _checkEmail($str) {
		if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $str)){return true;}else{return false;}
	}
	
	
	/**
	 * First Checks if the given key can be a valid Spinternet Developper Key.
	 * 
	 * @return boolean TRUE if the given developper key (public key) could be valid
	 */
	
	private function _isValidDevKey($key) {
		if ((!empty($key))&&(self::_checkEmail($key))){return true;}else{return false;}
	}
	
	/**
	 * First Checks if the given key can be a valid Spinternet Application Key.
	 * 
	 * @return boolean TRUE if the given application key (public key) could be valid
	 */
	
	private function _isValidAppKey($key) {
		$regex = '/^sn[0-9]{4}$/';
    	return preg_match( $regex , $key);
		return true;
	}
	
	
	private function _isValidKey($key) {
		return (strlen($key) == 36);
	}

	private function _createMessageSig($params, $secret){
		return hash_hmac('md5', $params, $secret);
	}

	private function _parse_query($var){
		//echo $var;
		 $var  = html_entity_decode($var);
		//echo $var;
		 $var  = explode('&', $var);
		 $arr  = array();

		foreach($var as $val) {
			$x = explode('=', $val);
			if ($x[0]!=''){$arr[$x[0]] = $x[1];}
		}
	unset($val, $x, $var);
	ksort($arr);
	return $arr;
	} 

	public function run($endpoint,$method,$args){

$https=true; // ALWAYS https !

$query_str='';
$num=0;
// todo de get variabelen alfabetisch sorteren
// indien je maar bepaalde velden wilt opvragen een "field" papameter voorzien

$url = sprintf('%s://%s',($https === true ? "https" : "http"),self::$_API_HOST.$endpoint);

$url_components = parse_url($url);
if (isset($url_components['query'])){
$tmp_arr = $this->_parse_query($url_components['query']);
$num = count($tmp_arr);
if($num>0){
$query_str = '?'.rawurldecode(http_build_query($tmp_arr));
}
unset($tmp_arr);
}

$url = $url_components['scheme'].'://'.$url_components['host'].$url_components['path'].$query_str;

	if (($method=='GET')||(($method=='DELETE'))){

	$sig = $this->_createMessageSig($url, self::$_secret);
		
	}else{
	$payload = array('parameters'=>$args, 'header'=>array('appkey'=>self::$_appkey));
	$postData = json_encode($payload);
	
	$sig = $this->_createMessageSig($postData, self::$_secret);
	}

if($num>0){
$url .= "&sig=".self::$_appkey.$sig;
}else{
$url .= "?sig=".self::$_appkey.$sig;
}


$headers = null;

$c = curl_init();

curl_setopt($c, CURLOPT_URL, $url);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($c, CURLOPT_REFERER, 'http://site.com/ref_page');
curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 4);
	if ($headers) {
		curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
	}
	
curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($c, CURLOPT_TIMEOUT, 10);
curl_setopt($c, CURLOPT_USERAGENT, 'scoutnet-API-PHP');
curl_setopt($c, CURLOPT_CUSTOMREQUEST, $method);
if ($method=='POST'){
curl_setopt($c, CURLOPT_POST, 1);
curl_setopt($c, CURLOPT_POSTFIELDS, $postData);
}
if ($method=='PUT'){
//curl_setopt($c, CURLOPT_PUT, 1);
curl_setopt($c, CURLOPT_POSTFIELDS, $postData);
}
$result = curl_exec($c);
$httpCode = curl_getinfo($c, CURLINFO_HTTP_CODE);
// waarom krijgen we een 500 ?? -> antwoord invalid SSL certifacte on development
//var_dump(curl_getinfo($c));

//print curl_error($c);

curl_close($c);

$decoded = json_decode($result, true);

return array('http'=>$httpCode,'raw'=>$result,'decoded'=>$decoded);




	}


}

