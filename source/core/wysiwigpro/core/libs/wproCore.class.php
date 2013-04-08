<?php
if (!defined('IN_WPRO')&&!defined('WPRO_IN_ROUTE')) exit;

if (!function_exists('wpro_class_exists')) {
	function wpro_class_exists($classname) {
		static $version = -2;
		if ($version==-2) $version = version_compare(PHP_VERSION, "5");
		if ($version >= 0) {
			return class_exists($classname, false);
		} else {
			return class_exists($classname);
		}
	}
}

// core functions available to all classes.
class wproCore {
	
	function addTrailingSlash($var) {
		if (!empty($var)) {
			if (substr($var, strlen($var)-1) != '/') $var .= '/';
		}
		return $var;
	}
	
	function stripTrailingSlash($var) {
		if (!empty($var)) {
			if (substr($var, strlen($var)-1) == '/') $var = substr($var, 0, strlen($var)-1);
		}
		return $var;
	}
	
	function addLeadingSlash($var) {
		if (!empty($var)) {
			if (substr($var, 0, 1) != '/') $var = '/'.$var;
		}
		return $var;
	}
	
	function stripLeadingSlash($var) {
		if (!empty($var)) {
			if (substr($var, 0, 1) == '/') $var = substr($var, 1);
		}
		return $var;
	}
	
	
	function sendCacheHeaders() {
		static $beenHere = 0;
		if (empty($beenHere)) {
		//if ($this->sendCacheHeaders) {
			@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // expires in the past
			@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			@header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
			@header("Cache-Control: post-check=0, pre-check=0", false);
			@header("Pragma: no-cache");
		//}
		}
		$beenHere = 1;
	}
	
	function varReplace($code, $array) {
		$search = array();
		$replace = array();
		foreach($array as $k => $v) {
			array_push($search, '##'.$k.'##');
			array_push($replace, $v);
		}
		return str_replace($search, $replace, $code);
	}
	
	function varOK ($name) {
		if (preg_match("/^[A-Za-z0-9_]+$/D", $name)) {
			return true;
		} else {
			return false;
		}
	}
	
	function makeVarOK ($name) {
		return preg_replace("/[^A-Za-z0-9_]/si", '', $name);
	}
	
	// sorts a multi-dimensional array by a common index
	function arrayCSort($marray, $column, $sortflag) {
		foreach ($marray as $row) {
			$sortarr[] = strtolower($row[$column]);
		}
		if (isset($sortarr)) {
			if (!is_array($sortarr) ) {
				return $marray;
			}
		} else {
			return $marray;
		}
		array_multisort($sortarr, $sortflag, $marray, $sortflag);
		return $marray;
	}
	
	// decodes htmlspecialchars
	function htmlSpecialCharsDecode($str) {
		$str = str_replace("&lt;", "<", $str);
		$str = str_replace("&gt;", ">", $str);
		$str = str_replace("&quot;", '"', $str);
		$str = str_replace("&amp;", "&", $str);
		return $str;
	}	
}

?>