<?php

/* 
* WysiwygPro 3.2.1, 30 November 2009.
* (c) Copyright 2007 and forever thereafter Chris Bolt and ViziMetrics Inc.
*/


/* ---------------------------------------------------------------------
* WysiwygPro
* This class generates DHTML code for displaying an HTML word processor in a web page.
* 
* typical usage:
* 
* include_once('wysiwygpro/wysiwygPro.class.php');  	// include this script file
* $editor = new wysiwygPro();							// create a new instance of the WysiwygPro object
* $editor->name = 'content';							// give the editor a name (this has the same functionality as the name attribute on a textarea tag or form input)
* $editor->html = '<p>some html to edit</p>';			// start the editor with some html ready for editing (this has the same functionality as the value attribute on a textarea tag or form input)
* $editor->display();									// display the editor
* 
* -------------------------------------------------------------------------
*/

// set error reporting levels
if (defined('E_STRICT')) {
	if (!isset($WPRO_PRE_ERROR_LEVEL)) {$WPRO_PRE_ERROR_LEVEL = ini_get('error_reporting');}
	if ($WPRO_PRE_ERROR_LEVEL == E_STRICT) {
		error_reporting(E_ALL);
	}
}

// version 2 compatability required?
if (defined('WP_WEB_DIRECTORY') && !defined('WPRO_V2_MODE')) {
	define('WPRO_V2_MODE', true);
}

if (!defined('IN_WPRO')) define('IN_WPRO', true);

// set the WP file directory
if (!defined('WPRO_DIR')) define('WPRO_DIR', dirname(__FILE__) . '/');

// include required scripts
require_once(WPRO_DIR.'core/libs/wproCore.class.php');

// attempt to send nocache headers
function wproSendCacheHeaders () {
	static $beenHere = 0;
	if (empty($beenHere) && !headers_sent()) {
		@wproCore::sendCacheHeaders();
	}
	$beenHere ++;
}
if (!defined('WPRO_DONT_SEND_CACHE_HEADERS')) {
	wproSendCacheHeaders();
}

// function for detecting whether there is another editor already printed.
function wproIsSubsequent () {
	static $beenHere = 0;
	if (empty($beenHere)) {
		$beenHere = 1;
		return false;
	} else {
		return true;
	}
}
// function to ensure that JS plugins are not duplicated
function wproJSPluginAdded ($id) {
	static $plugins = array();
	if (in_array($id, $plugins)) {
		return true;
	} else {
		array_push($plugins, $id);
		return false;
	}	
}
// function to ensure that output is not duplicated
function wproOutputAdded ($data) {
	static $output = array();
	if (in_array($data, $output)) {
		return true;
	} else {
		array_push($output, $data);
		return false;
	}	
}
// include class file
require_once(WPRO_DIR.'core/libs/wysiwygPro.class.php');

// re-set error reporting level
if (isset($WPRO_PRE_ERROR_LEVEL)) {
	error_reporting($WPRO_PRE_ERROR_LEVEL);
}
?>