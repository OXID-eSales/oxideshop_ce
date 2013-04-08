<?php
if (!defined('IN_WPRO')) exit();
define('WPRO_IN_DIALOG', true);
/*
Include this file into the top of any web scripts to automatically have sessions and other stuff available 
Note: it will automatically unregister globals, if you are including it into a script and need to keep
the globals define 'WPRO_ALLOW_GLOBALS' as true before including.
*/

if (defined('E_STRICT')) {
	if (ini_get('error_reporting') == E_STRICT) {
		error_reporting(E_ALL);
	}
}

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
if (!defined('WPRO_ALLOW_GLOBALS')) {
	wpro_unregister_GLOBALS();
}
require_once(dirname(dirname(dirname(__FILE__))).'/config.inc.php'); 
$WPRO_EDITOR_URL = WPRO_EDITOR_URL;
/* Routine for auto setting $EDITOR_URL, if this routine fails you will need to set it manually in the config file */
if (empty($WPRO_EDITOR_URL)) {	
	$wp_script_filename = isset($_SERVER["SCRIPT_FILENAME"]) ? str_replace(array('\\','\\\\','//'),'/',$_SERVER["SCRIPT_FILENAME"]) : str_replace(array('\\','\\\\','//'),'/',$_SERVER["PATH_TRANSLATED"]);
	$wp_script_name = $_SERVER["SCRIPT_NAME"];
	$wp_dir_name = str_replace(array('\\','\\\\','//'),'/',WPRO_DIR);
	$WPRO_EDITOR_URL = preg_replace( '/^(.*?)'.str_replace('/','\/',quotemeta(preg_replace( '/'.str_replace('/','\/',quotemeta($wp_script_name)).'/i', '', $wp_script_filename))).'/i' , '', $wp_dir_name);
	/*if (strtolower($EDITOR_URL) == strtolower($wp_dir_name)) {die('<div><b>WysiwygPro config error</b>: Could not auto-detect URL or wysiwygPro folder. You need to set the $EDITOR_URL variable in config.inc.php, or set the editorURL property when constructing the editor.</div>');}*/
}
if (substr($WPRO_EDITOR_URL, strlen($WPRO_EDITOR_URL)-1) != '/') {$WPRO_EDITOR_URL .= '/';}

// stripslashes all POST GET & COOKIE data, this way we know it is all in the same format
if (function_exists('set_magic_quotes_runtime')) @set_magic_quotes_runtime(0); // Disable magic_quotes_runtime
function wpro_recursiveStripSlash($arr) {
	if (is_array($arr)) {
		foreach ($arr as $k => $v) {
			if (is_array($arr[$k])) {
				wpro_recursiveStripSlash($arr[$k]);
			} else {
				$arr[$k] = stripslashes($v);
			}
		}
		@reset($arr);
	}
	return $arr;
}
if (get_magic_quotes_gpc()) {
	$_GET = wpro_recursiveStripSlash($_GET);
	$_POST = wpro_recursiveStripSlash($_POST);
	$_COOKIE = wpro_recursiveStripSlash($_COOKIE);
	//$_REQUEST = array_merge($_POST, $_GET, $_COOKIE);
	//@reset($_REQUEST);
}

// include common libraries
require_once(WPRO_DIR.'core/libs/wproSession.class.php');
require_once(WPRO_DIR.'core/libs/wproLangLoader.class.php');
define('WPRO_DONT_SEND_CACHE_HEADERS', true);
require_once(WPRO_DIR.'wysiwygPro.class.php');

// check output buffering
if (WPRO_GZIP) {
	$wpro_doGzip = false;
	if (!isset($_GET['gzip'])) {
		if (!@ini_get( 'zlib.output_compression' )) {
			if (@ini_get('output_handler') != 'ob_gzhandler') {
				$wpro_doGzip = true;
			}
		}
	}
	if ($wpro_doGzip) {
		ob_start( 'ob_gzhandler' );
	} else {
		ob_start();
	}
	unset($wpro_doGzip);
} else {
	ob_start();
}

function wpro_xajaxCatchSessionTimeout () {
	if (WPRO_PATH_XAJAX == WPRO_DIR.'core/libs/xajax/') {
		require_once(WPRO_PATH_XAJAX."xajaxResponse.inc.php");
	} else {
		if (!wpro_class_exists('xajaxResponse')) require_once(WPRO_PATH_XAJAX."xajaxResponse.inc.php");
	}
	if (wpro_class_exists('wpro_xajaxResponse')) {
		$response = new wpro_xajaxResponse();
	} else {
		$response = new xajaxResponse();
	}
	
	$response->addAlert(WPRO_STR_JS_SESSION_TIMEOUT);
	
	return $response;
}
function wpro_xajaxCatchUnauthorized () {
	if (WPRO_PATH_XAJAX == WPRO_DIR.'core/libs/xajax/') {
		require_once(WPRO_PATH_XAJAX."xajaxResponse.inc.php");
	} else {
		if (!wpro_class_exists('xajaxResponse')) require_once(WPRO_PATH_XAJAX."xajaxResponse.inc.php");
	}
	if (wpro_class_exists('wpro_xajaxResponse')) {
		$response = new wpro_xajaxResponse();
	} else {
		$response = new xajaxResponse();
	}
	
	$response->addAlert(WPRO_STR_JS_UNAUTHORIZED);
	
	return $response;
}

// restore editor session
require_once(WPRO_DIR.'conf/customSessHandlers.inc.php');
$EDITOR = NULL;
$WPRO_SESS = new wproSession();
if (!$EDITOR = $WPRO_SESS->load()) {
	// invalid or expired session
	if (isset($_REQUEST[$WPRO_SESS->sessionName]) && !WPRO_ANONYMOUS_ACCESS) {
		if (isset($_POST['xajax'])) {
			if (WPRO_PATH_XAJAX == WPRO_DIR.'core/libs/xajax/') {
				require_once(WPRO_PATH_XAJAX."xajax.inc.php");
			} else {
				if (!wpro_class_exists('xajax')) require_once(WPRO_PATH_XAJAX."xajax.inc.php");
			}
			if (wpro_class_exists('wpro_xajax')) {
				$xajax = new wpro_xajax();
			} else {
				$xajax = new xajax();
			}
			$xajax->registerCatchAllFunction("wpro_xajaxCatchSessionTimeout");
			$xajax->processRequests();
		} else {
			// session has expired; alert user
			require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
			$msg = new wproMessageExit();
			$msg->msgCode = WPRO_WARNING;
			$msg->msg = WPRO_STR_SESSION_TIMEOUT;
			$msg->alert();
		}
	} else if (!WPRO_ANONYMOUS_ACCESS) {
		if (isset($_POST['xajax'])) {
			if (WPRO_PATH_XAJAX == WPRO_DIR.'core/libs/xajax/') {
				require_once(WPRO_PATH_XAJAX."xajax.inc.php");
			} else {
				if (!wpro_class_exists('xajax')) require_once(WPRO_PATH_XAJAX."xajax.inc.php");
			}
			if (wpro_class_exists('wpro_xajax')) {
				$xajax = new wpro_xajax();
			} else {
				$xajax = new xajax();
			}
			$xajax->registerCatchAllFunction("wpro_xajaxCatchUnauthorized");
			$xajax->processRequests();
		} else {
			// session does not exist and anonymous access is not allowed
			header('HTTP/1.0 401 Unauthorized');
			echo WPRO_STR_UNAUTHORIZED;
			exit;
		}
		
	} else {
		// session does not exist, anonymous access is allowed, generate editor with default settings
		$EDITOR = new wysiwygPro();
		$EDITOR->_createSession = false;
	}
}

// store unchanged editor
define('WPRO_SERIALIZED_EDITOR', serialize($EDITOR));

// load dialog configuration
require_once(WPRO_DIR.'conf/dialogConfig.inc.php');

// load version 2 config data as needed
$wpro_inDialog = true;
if ($EDITOR->_v2Mode == true) {
	require_once(WPRO_DIR.'core/libs/v2ConfigLoader.inc.php');
}

// load data from configuration files
$wpro_pConfigVars = array(
	'editorURL' => 'WPRO_EDITOR_URL',
	'imageDir' => 'WPRO_IMAGE_DIR',
	'imageURL' => 'WPRO_IMAGE_URL',
	'documentDir' => 'WPRO_DOC_DIR',
	'documentURL' => 'WPRO_DOC_URL',
	'mediaDir' => 'WPRO_MEDIA_DIR',
	'mediaURL' => 'WPRO_MEDIA_URL',
	'deleteFiles' => 'WPRO_DELETE_FILES',
	'deleteFolders' => 'WPRO_DELETE_FOLDERS',
	'renameFiles' => 'WPRO_RENAME_FILES',
	'renameFolders' => 'WPRO_RENAME_FOLDERS',
	'upload' => 'WPRO_UPLOAD',
	'overwrite' => 'WPRO_OVERWRITE',
	'moveFiles' => 'WPRO_MOVE_FILES',
	'moveFolders' => 'WPRO_MOVE_FOLDERS',
	'copyFiles' => 'WPRO_COPY_FILES',
	'copyFolders' => 'WPRO_COPY_FOLDERS',
	'createFolders' => 'WPRO_CREATE_FOLDERS',
	'editImages' => 'WPRO_EDIT_IMAGES',
	'directories' => 'WPRO_DIRECTORIES',
	'allowedImageExtensions' => 'WPRO_ALLOWED_IMAGE_EXTENSIONS',
	'allowedDocExtensions' => 'WPRO_ALLOWED_DOC_EXTENSIONS',
	'allowedMediaExtensions' => 'WPRO_ALLOWED_MEDIA_EXTENSIONS',
	'allowedMediaPlugins' => 'WPRO_ALLOWED_MEDIA_PLUGINS',
	'maxDocSize' => 'WPRO_MAX_DOC_SIZE',
	'maxMediaSize' => 'WPRO_MAX_MEDIA_SIZE',
	'maxImageSize' => 'WPRO_MAX_IMAGE_SIZE',
	'maxImageWidth' => 'WPRO_MAX_IMAGE_WIDTH',
	'maxImageHeight' => 'WPRO_MAX_IMAGE_HEIGHT',
	'folderCHMOD' => 'WPRO_FOLDER_CHMOD',
	'fileCHMOD' => 'WPRO_FILE_CHMOD'
);
foreach ($wpro_pConfigVars as $wpro_i => $wpro_v) {
	if (isset($$wpro_v)) {
		if ($wpro_i == 'allowedMediaPlugins') {
			$wpro_p = explode(',', $WPRO_ALLOWED_MEDIA_PLUGINS);
			foreach ($wpro_p as $wpro_pv) {
				$EDITOR->addMediaPlugin($EDITOR->makeVarOK(trim($wpro_pv)));
			}
			unset($wpro_p,$wpro_p,$wpro_pv);
		} else if (empty($EDITOR->$wpro_i)) {
			$EDITOR->$wpro_i = $$wpro_v;
		}
	}
}
unset($wpro_pConfigVars,$wpro_i,$wpro_v);

// load and validate the editor settings
$EDITOR->_createSession = false;
$EDITOR->_makeEditor();

if (!empty($EDITOR->route) && !defined('WPRO_IN_ROUTE')) exit ('Not routed.');

?>