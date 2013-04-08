<?php
if (!defined('IN_WPRO')) define('IN_WPRO', true);
if (!defined('WPRO_DIR')) define('WPRO_DIR', dirname(dirname(__FILE__)) . '/');
require_once(WPRO_DIR.'/core/libs/compileJSCommon.inc.php');

$iframeDialogs = isset($_GET['iframeDialogs']) ? ($_GET['iframeDialogs'] ? true : false) : false;

if (!WPRO_USE_JS_SOURCE) {
	echo $fs->getContents(WPRO_DIR.'js/dialogEditorShared.js');
	echo $fs->getContents(WPRO_DIR.'core/js/wproPMenu.js');
	if ($iframeDialogs) {
		echo $fs->getContents(dirname(__FILE__).'/js/dragiframe.js');
	}
} else {
	echo $fs->getContents(WPRO_DIR.'js/dialogEditorShared_src.js');
	echo $fs->getContents(WPRO_DIR.'core/js/wproPMenu_src.js');
	if ($iframeDialogs) {
		echo $fs->getContents(dirname(__FILE__).'/js/dragiframe_src.js');
	}
}

?>;
if (typeof(wproAjaxRecordLoad) != 'undefined') {
	wproAjaxRecordLoad('<?php echo addslashes(wpro_detectURI()) ?>');
}