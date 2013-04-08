<?php
if (!defined('IN_WPRO')) define('IN_WPRO', true);
if (!defined('WPRO_DIR')) define('WPRO_DIR', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
require_once(WPRO_DIR.'/core/libs/compileJSCommon.inc.php');

$plugins = explode(',',isset($_GET['plugins']) ? base64_decode($_GET['plugins']) : '');
foreach ($plugins as $plugin) {
	$p = explode('/',$plugin);
	if (isset($p[1])) {
		$extension = strrchr(strtolower($p[1]),'.');
		if ($extension!='.js') continue;
		if (!WPRO_USE_JS_SOURCE) {
			$p[1] = str_replace('_src.js', '.js', $p[1]);
		}
		$dir = WPRO_DIR.'/plugins/mediaPlugins/';
		
		$p[0] = $fs->makeVarOK($p[0]);
		$p[1] = $fs->makeFileNameOK($p[1]);
		
		if (!$p[0] || !$p[1]) {
			continue;
		}
		
		$file = $fs->fileName($dir.$p[0].'/'.$p[1]);
		if (!file_exists($file)) continue;
		echo '
/* 
=============================================================
Begin '.$p[0].'/'.$p[1].'
=============================================================
*/
';
		echo $fs->getContents($file);
	}
}


?>;
if (typeof(wproAjaxRecordLoad) != 'undefined') {
	wproAjaxRecordLoad('<?php echo addslashes(wpro_detectURI()) ?>');
}