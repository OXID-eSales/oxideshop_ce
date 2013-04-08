<?php if (!defined('IN_WPRO')) exit;
/* 
A simple class for retrieving pages over http, supports basic authentication and proxy authentication.
SSL support requires CURL
*/

class wproWebAgent {
	function wproWebAgent() {
		$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
	}
	var $userAgent = '';
	
	var $headers = array(); // an array of additional headers
	
	var $proxyURL = ''; // proxy URL INCLUDING PORT NUMBER
	var $proxyPort = '';
	var $proxyUser = '';
	var $proxyPass = '';
	var $proxyAuthMethod = 'basic'; // proxy authentication method, 'basic' or 'NTLM'
	
	var $authUser = ''; // if resource requires authentication
	var $authPass = '';
	
	var $accept = 'image/gif, image/x-xbitmap, image/jpeg, image/pjpeg */*';
	var $referer = '';
	
	var $requestMethod = 'GET';
	
	var $postData = array(); // associative array of post data, same format as the PHP $_POST array.
	
	var $curlOptions = array(); // an array of curl options to be set. Format is the same as the curl_setopt_array function, but works with PHP 4

	var $timeout = 30;
	
	var $maxlength = 4000;
	
	function http_build_query( $array = NULL, $convention = '%s' ){ 
		if( count( $array ) == 0 ){ 
			return ''; 
		} else { 
			if( function_exists( 'http_build_query' ) ){ 
				$query = http_build_query( $array ); 
			} else { 
				$query = ''; 
				foreach( $array as $key => $value ){ 
					if( is_array( $value ) ){ 
						$new_convention = sprintf( $convention, $key ) . '[%s]'; 
						$query .= http_parse_query( $value, $new_convention ); 
					} else { 
						$key = urlencode( $key ); 
						$value = urlencode( $value ); 
						$query .= sprintf( $convention, $key ) . "=$value&"; 
					} 
				} 
			} 
			return $query; 
		} 
	}
	
	
	function fetch($url) {
		if (function_exists('curl_init')) {
			return $this->_curlFetch($url);
		} else {
			return $this->_fsockFetch($url);
		}
	}
	
	
	function _fsockFetch($url) {
		
		$headers = '';
		
		$url_parsed = parse_url($url);
		$scheme = $url_parsed["scheme"];
		$host = $url_parsed["host"];
		$port = isset($url_parsed["port"]) ? $url_parsed["port"] : 80;
		$path = isset($url_parsed["path"]) ? $url_parsed["path"] : '/';
		if (!empty($url_parsed["query"])) {
			$path .= "?".$url_parsed["query"];
		}
		
		$requestMethod = 'GET';
		if (strtoupper($this->requestMethod)=='POST') {
			$requestMethod = 'POST';
		}
		
		// proxy authentication
		// this only supports basic authentication.
		if (!empty($this->proxyURL)) {
			$this->proxyPort = empty($this->proxyPort) ? 80 : $this->proxyPort;
			$headers .= "$requestMethod $url HTTP/1.0\r\n";
			$headers .= "Host: $host\r\n";
			$headers .= "Proxy-Connection: Keep-Alive\r\n";
			if (!empty($this->proxyUser)) {
				$headers .= "Proxy-authorization: Basic ".base64_encode($this->proxyUser.":".$this->proxyPass)."\r\n";
			}
		} else {
			$headers .= "$requestMethod $path HTTP/1.0\r\n";
			$headers .= "Host: $host\r\n";
		}
		
		if (!empty($this->userAgent)) {
			$headers .= "User-Agent: ".$this->userAgent."\r\n";
		}
		if(!empty($this->accept)) {
			$headers .= "Accept: ".$this->accept."\r\n";
		}
		if(!empty($this->referer)) {
			$headers .= "Referer: ".$this->referer."\r\n";
		}
		if(!empty($this->authUser) && !empty($this->authPass)) {	
			$headers .= "Authorization: Basic ".base64_encode($this->authUser.":".$this->authPass)."\r\n";
		}
		
		$data = '';
		if ($requestMethod=='POST') {
			$data = $this->http_build_query($this->postData);
			$headers.="Content-Type: application/x-www-form-urlencoded\r\n";
			$headers.= "Content-Length: ".strlen($data)."\r\n";
		}
		
		$headers .= "Connection: Close\r\n";
		$headers .= "\r\n";
		
		if ($requestMethod=='POST'&&!empty($data)) {
			$headers .= $data;
		}	
		
		//exit($headers);	
		
		// make the connection
		if (!empty($this->proxyURL)) {
			$fp = @fsockopen($this->proxyURL, $this->proxyPort, $errno, $errstr, $this->timeout);
		} else {
			$fp = @fsockopen($host, $port, $errno, $errstr, $this->timeout);
		}
		if ($fp) {
			// set socket timeout
			socket_set_timeout($fp, $this->timeout);
			
			// send headers
			fwrite($fp, $headers, strlen($headers));
			
			// check for errors etc...
			$response = @fgets($fp);
			if (!$response) return false;
			if (strlen($response) < 15) {
				return false; // non standard http response;
			} else {
			//if (substr_count($response, "200 OK") > 0 || substr_count($response, "302 Found") > 0) {
				// get response
				$results = "";
				$inHeader = true;
				/*do {
					$_data = fread($fp, $this->maxlength);		
					if (strlen($_data) == 0) {
						break;
					}		
					$results .= $_data;
				} while(true);*/
				while($line = fgets($fp,$this->maxlength)) {
					if ($inHeader) {
						// check headers.
						// if a header begins with Location: or URI:, do a redirect
						if (preg_match("/^(Location:|URI:)/i",$line)) {
							// get URL portion of the redirect
							preg_match("/^(Location:|URI:)[ ]+(.*)/i",chop($line),$matches);
							// look for :// in the Location header to see if hostname is included
							if(!preg_match("|\:\/\/|",$matches[2])) {
								// no host in the path, so prepend
								$redirectaddr = $scheme."://".$host.":".$port;
								// eliminate double slash
								if(!preg_match("|^/|",$matches[2])) {
										$redirectaddr .= "/".$matches[2];
								} else {
										$redirectaddr .= $matches[2];
								}
							} else {
								$redirectaddr = $matches[2];
							}
							return $this->fetch($redirectaddr);
						}
					} else {
						$results.=$line;
					}				
					if ($line == "\r\n") $inHeader = false;
				}
				
				fclose($fp);
				return $results;  
								  
				
			//} else {
				//fclose($fp);
				//return substr($response,9,3); // return http error code
			}
		}
		return false; // socket connection failed.
	
	}
	
	function _curlFetch ($url) {
		// init
		$ch = curl_init();
		// URL to fetch
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($this->requestMethod));
		if (strtoupper($this->requestMethod)=='POST') {
			curl_setopt($ch, CURLOPT_POST, true);
		}
		if (!ini_get('safe_mode') && !ini_get('open_basedir')) @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		// user agent
		if (!empty($this->userAgent)) {
			curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
		}
		// accept
		if(!empty($this->accept)) {
			array_push($this->headers, "Accept: ".$this->accept);
		}
		//referer
		if (!empty($this->referer)) {
			curl_setopt($ch, CURLOPT_REFERER, $this->referer);
		}
		// proxy authentication
		if (!empty($this->proxyURL)) {
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
			// auth method??
			if (defined('CURLOPT_PROXYAUTH')) {
				if (!empty($this->proxyAuthMethod)) {
					switch (strtolower($this->proxyAuthMethod)) {
						case 'basic' :
							curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
							break;
						case 'ntlm' :
							curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
							break;
					}
				}
			}
			// port??
			if (!empty($this->proxyPort) && !preg_match("/:[0-9]+$/si", $this->proxyURL)) {
				$this->proxyURL .= ':'.$this->proxyPort;
			}
			// proxy url
			curl_setopt($ch, CURLOPT_PROXY, $this->proxyURL);
			// password
			if (!empty($this->proxyUser) && !empty($this->proxyPass)) {
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyUser.':'.$this->proxyPass);
			}
		}
		// authorization
		if(!empty($this->authUser) && !empty($this->authPass)) {	
			if (defined('CURLOPT_HTTPAUTH')) {
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			}
			curl_setopt($ch, CURLOPT_USERPWD, $this->authUser.":".$this->authPass);
		}
		// POST data
		$pdata = array();
		foreach($this->postData as $k => $v) {
			$str = urlencode($k).'='.urlencode($v);
			array_push($pdata, $str);
		}
		$pdata = implode('&',$pdata);
		if (!empty($pdata)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $pdata);
		}
		// custom headers
		if (!empty($this->headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		}
		// custom options
		foreach($this->curlOptions as $k => $v) {
			curl_setopt($ch, $k, $v);
		}
		
		// fetch data
		$data = false;
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}
?>