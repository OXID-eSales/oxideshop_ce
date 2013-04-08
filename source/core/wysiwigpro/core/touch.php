<?php
if (!defined('IN_WPRO')) define('IN_WPRO', true);
if (defined('E_STRICT')) {
	if (ini_get('error_reporting') == E_STRICT) {
		error_reporting(E_ALL);
	}
}
if (ini_get('display_errors') == true) {
	ini_set('display_errors', false);
}
if (!isset($_GET['name'])) return;
define('WPRO_CONTENT_TYPE_HEADER_SENT', true);
require_once(dirname(dirname(__FILE__)).'/config.inc.php');
header("Content-type: text/javascript");
require_once(WPRO_DIR.'core/libs/wproSession.class.php');
require_once(WPRO_DIR.'wysiwygPro.class.php');
require_once(WPRO_DIR.'conf/customSessHandlers.inc.php');
$WPRO_SESS = new wproSession();
if ($EDITOR = $WPRO_SESS->load()) {
	$EDITOR->sendCacheHeaders();
	$name = $EDITOR->getJSName($_GET['name']);
	if (!empty($name)) {
		$action = isset($_GET['action']) ? $_GET['action'] : 'touch';
		if ($action == 'touch') {
			
			$url = 	addslashes($EDITOR->editorURL);
			$sid = $WPRO_SESS->sessionName.'='.addslashes($WPRO_SESS->sessionId);
			$phpsid = addslashes($EDITOR->appendSid ? strip_tags(defined('SID') ? SID : '') : '');
			$append = addslashes(!empty($EDITOR->appendToQueryStrings) ? $EDITOR->appendToQueryStrings : '');
			$route = addslashes($EDITOR->route);
			
			echo '
			var wpro_sess_refresh_success = false;
			/*try{*/
			wpro_sessTimeout ("'.addslashes($name).'", "'.$sid.'", \''.$phpsid.'\',  \''.$url.'\',  \''.$append.'\', '.intval(WPRO_SESS_REFRESH).', \''.$route.'\');
			wpro_sess_refresh_success=true;
			/*}catch(e){};*/
			/*if (wpro_sess_refresh_success) {
				alert("'.$name.' called session refresh.");
			} else {
				alert("'.$name.' session refresh failed!!");
			}*/';
			
			
		} else {
			if ($WPRO_SESS->usePHPEngine) {
			} else {
				$WPRO_SESS->destroy();
			}
		}
	}
}
// prevent PHP from outputting html tags if nothing has been output
echo '/* '.rand().' end */';
?>