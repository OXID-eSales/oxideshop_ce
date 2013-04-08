<?php
if (!defined('IN_WPRO')) exit;
if (defined('E_STRICT')) {
	if (ini_get('error_reporting') == E_STRICT) {
		error_reporting(E_ALL);
	}
}
if (ini_get('display_errors') == true) {
	ini_set('display_errors', false);
}
if (!function_exists('wpro_unregister_GLOBALS')) {
	// Turn register globals off and unregister all global variables
	function wpro_unregister_GLOBALS() {
		if (!ini_get('register_globals')) return;
	
		if (isset($_REQUEST['GLOBALS'])) exit('Register Globals attack detected.');
	
		// Variables that shouldn't be unset
		$ok = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES', '_SESSION');
		
		// Unset globals not in the allowed array.
		foreach ( $GLOBALS as $k => $v ) {
			if (!in_array($k, $ok)) {
				unset($GLOBALS[$k]);
			}
		}
	}
}
function wpro_detectURI() {

		$arr = array();

		// try the request URL
		if (!empty($_SERVER['REQUEST_URI'])) {
			$arr = parse_url($_SERVER['REQUEST_URI']);
		}
		
		// check and get path
		if (empty($arr['path'])) {
			if (!empty($_SERVER['PATH_INFO'])) {
				$path = parse_url($_SERVER['PATH_INFO']);
			} else {
				$path = parse_url($_SERVER['PHP_SELF']);
			}
			$arr['path'] = $path['path'];
		}
		
		// check and get query string
		if (empty($arr['query'])) {
			if (!empty($_SERVER['QUERY_STRING'])) {
				$arr['query'] = $_SERVER['QUERY_STRING'];
			}
		}

		if (!empty($arr['query'])) {
			$arr['query'] = '?'.$arr['query'];
		}

		// Add the query string
		$url = $arr['path'].(empty($arr['query'])?'':$arr['query']);

		return $url;
}

wpro_unregister_GLOBALS();
require_once(dirname(__FILE__).'/wproFilesystem.class.php');
require_once(WPRO_DIR.'config.inc.php');
if (WPRO_GZIP_JS) {
	$doGzip = false;
	if (!@ini_get( 'zlib.output_compression' )) {
		if (@ini_get('output_handler') != 'ob_gzhandler') {
			$doGzip = true;
		}
	}
	if ($doGzip) {
		// do not gzip if IE 6
		$browser_string = strtolower($_SERVER["HTTP_USER_AGENT"]);
		
		$is_opera = strstr($browser_string, 'opera');
		$is_konq = strstr($browser_string, 'khtml');
		$is_ie = strstr($browser_string, 'msie');
		$is_gecko = strstr($browser_string, 'gecko');
		
		$ie_version = 7;
		
		if ($is_ie && !$is_opera && !$is_konq && !$is_gecko) {
			// ie detection
			$ie_version = preg_replace('/.*msie/sm', '', $browser_string);
			$ie_version = substr ($ie_version, 0,4);		
		}
		
		if (intval($ie_version) > 6) {
			ob_start( 'ob_gzhandler' );
		}
	}
}

header("Content-type: text/javascript;charset=UTF-8");

$fs = new wproFilesystem();
?>