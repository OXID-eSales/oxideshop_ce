<?php
if (!defined('IN_WPRO')) define('IN_WPRO', true);
if (!defined('WPRO_DIR')) define('WPRO_DIR', dirname(dirname(__FILE__)) . '/');
require_once(WPRO_DIR.'/core/libs/compileJSCommon.inc.php');

if (!WPRO_USE_JS_SOURCE) {
	echo $fs->getContents(dirname(__FILE__).'/js/dialog.js');
} else {
	echo $fs->getContents(dirname(__FILE__).'/js/dialog_src.js');
}

?>;
if (typeof(wproAjaxRecordLoad) != 'undefined') {
	wproAjaxRecordLoad('<?php echo addslashes(wpro_detectURI()) ?>');
}