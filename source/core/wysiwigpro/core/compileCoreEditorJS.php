<?php
if (!defined('IN_WPRO')) define('IN_WPRO', true);
if (!defined('WPRO_DIR')) define('WPRO_DIR', dirname(dirname(__FILE__)) . '/');
require_once(WPRO_DIR.'/core/libs/compileJSCommon.inc.php');

$browserType = isset($_GET['browserType']) ? $_GET['browserType'] : '';

if (!WPRO_USE_JS_SOURCE) {
	if ($browserType == 'msie') {
		echo $fs->getContents(dirname(__FILE__).'/js/ieSpecific.js');
	} elseif ($browserType == 'gecko') {
		echo $fs->getContents(dirname(__FILE__).'/js/mozSpecific.js');
	} elseif ($browserType == 'safari') {
		echo $fs->getContents(dirname(__FILE__).'/js/safSpecific.js');
	} elseif ($browserType == 'opera') {
		echo $fs->getContents(dirname(__FILE__).'/js/operaSpecific.js');
	}
	echo $fs->getContents(dirname(__FILE__).'/js/editor.js');
} else {
	if ($browserType == 'msie') {
		echo $fs->getContents(dirname(__FILE__).'/js/ieSpecific_src.js');
	} elseif ($browserType == 'gecko') {
		echo $fs->getContents(dirname(__FILE__).'/js/mozSpecific_src.js');
	} elseif ($browserType == 'safari') {
		echo $fs->getContents(dirname(__FILE__).'/js/safSpecific_src.js');
	} elseif ($browserType == 'opera') {
		echo $fs->getContents(dirname(__FILE__).'/js/operaSpecific_src.js');
	}
	echo $fs->getContents(dirname(__FILE__).'/js/editor_src.js');
}

?>;
if (typeof(wproAjaxRecordLoad) != 'undefined') {
	wproAjaxRecordLoad('<?php echo addslashes(wpro_detectURI()) ?>');
}