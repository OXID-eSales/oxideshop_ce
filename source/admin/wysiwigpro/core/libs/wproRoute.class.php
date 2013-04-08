<?php
if (!defined('WPRO_DIR')) exit;

/* class for processing routes */

class wproRoute {
	
	function processRequests() {
		
		if (isset($_SERVER['QUERY_STRING']) && (!isset($_GET) || (!count($_GET) && strlen($_SERVER['QUERY_STRING'])))) {
			
			// we might be in a framework such as CodeIgniter that deletes the $_GET vars.
			// re-create $_GET from the query string
			$matches = array();
			preg_match_all('#(^|[\?&])([a-z0-9\-_]+)=([^&]*)#si', $_SERVER['QUERY_STRING'], $matches, PREG_SET_ORDER);
			for ($i=0;$i<count($matches);$i++) {
				// Because WysiwygPro will remove slashes from $_GET if magic_quotes_gpc is on we should add some slashes in so the array is the same as the PHP generated $_GET would have been
				if (get_magic_quotes_gpc()) {
					$_GET[$matches[$i][2]] = addslashes(urldecode($matches[$i][3]));
				} else {
					$_GET[$matches[$i][2]] = urldecode($matches[$i][3]);
				}
				
			}
			
		}
		
		// get the requested file
		$req_path  = isset($_GET['wproroutelink']) ? $_GET['wproroutelink'] : '';
		if (!empty($req_path)) {
		
			$wpro_path = WPRO_DIR;
			
			// cannot include if IN_WPRO is defined for security purposes
			// this prevents out of order execution attacks
			// and makes this process no more dangerous than someone browsing the WysiwygPro directory
			if (defined('IN_WPRO')) exit('WysiwygPro. Route request could not be performed. Please ensure that the WysiwygPro class (or any other WysiwygPro scripts) are included AFTER the call to wproRoute::processRequests().' );
			
			// validate path by removing all dangerous characters, and since we know that all valid WPro files match this
			$req_path = preg_replace("/[^A-Za-z0-9_\-]/si", '', $req_path);
			
			// create path
			$req_path = str_replace('-', '/', $req_path).'.php';
			
			// extra out of order execution protection just to be on the safe side.
			if (stristr($req_path, '.class.php') 
			|| stristr($req_path, '.inc.php') 
			|| stristr($req_path, '.tpl.php')) exit;
						
			// initiate global vars
			global $EDITOR, $DIALOG, $WPRO_SESS, $wpro_inDialog;
			$EDITOR = NULL;
			$DIALOG = NULL;
			$WPRO_SESS = NULL;
			$wpro_inDialog = NULL;
			
			// validate and include file, prevent directory traversal.
			if (!defined('WPRO_IN_ROUTE')) define('WPRO_IN_ROUTE', true);
			// deleting globals might break the parent application, we have to trust the parent application is secure?!
			if (!defined('WPRO_ALLOW_GLOBALS')) define('WPRO_ALLOW_GLOBALS', true); // this is OK since the only global vars used by WPro have been initiated above
			// check for directory traversal and that file exists
			include_once($wpro_path.'core/libs/wproFilesystem.class.php');
			$fs = new wproFilesystem();
			if ($fs->folderNameOK($req_path) && is_file($wpro_path.$req_path)) {
				include_once($wpro_path.$req_path);	
				exit;
			}
		}
	
	}
	
	function getEditorURL() {
		require_once(WPRO_DIR.'config.inc.php');
		$EDITOR_URL = WPRO_EDITOR_URL;
		/* Routine for auto setting WPRO_EDITOR_URL, if this routine fails you will need to set it manually in the config file */
		if (empty($EDITOR_URL)) {	
			$wp_script_filename = isset($_SERVER["SCRIPT_FILENAME"]) ? str_replace(array('\\','\\\\','//'),'/',$_SERVER["SCRIPT_FILENAME"]) : str_replace(array('\\','\\\\','//'),'/',$_SERVER["PATH_TRANSLATED"]);
			$wp_script_name = $_SERVER["SCRIPT_NAME"];
			$wp_dir_name = str_replace(array('\\','\\\\','//'),'/',WPRO_DIR);
			$EDITOR_URL = preg_replace( '/^(.*?)'.str_replace('/','\/',quotemeta(preg_replace( '/'.str_replace('/','\/',quotemeta($wp_script_name)).'/i', '', $wp_script_filename))).'/i' , '', $wp_dir_name);
			//if (strtolower($EDITOR_URL) == strtolower($wp_dir_name)) {die('<div><b>WysiwygPro config error</b>: Could not auto-detect URL or wysiwygPro folder. You need to set the WPRO_EDITOR_URL constant in config.inc.php, or set the editorURL property when constructing the editor.</div>');}
		}
		
		$EDITOR_URL = wproCore::addTrailingSlash($EDITOR_URL);
		// web URL (strip out domain etc)
		if (preg_match('/^http(|s):\/\/.*?\//si', $EDITOR_URL )) {
			$EDITOR_URL = preg_replace('/^http(|s):\/\/[^\/]+/si', '', $EDITOR_URL);
		}
		return $EDITOR_URL;
	}

}

?>