<?php
/*
provides compatibility with WP 2.x syntax for opening the image browser
*/
session_start();
$returnFunction = isset($_GET['return_function']) ? $_GET['return_function'] : '';
if (empty($returnFunction)) exit;
$window = isset($_GET['window']) ? $_GET['window'] : '';
if (empty($window)) exit;
$type='image';
switch ($window) {
	case 'image.php' :
		$type='image';
		break;
	case 'document.php' :
		$type = 'document';
		break;
	case 'media.php' :
		$type = 'media';
		break;
	case 'link.php' :
		$type = 'link';
		break;
}
if (file_exists(dirname(__FILE__).'/config.php')) {
	require_once(dirname(__FILE__).'/config.php');
}
define('WPRO_DONT_SEND_CACHE_HEADERS', true);
require_once(dirname(__FILE__).'/wysiwygPro.class.php');

$editor = new wysiwygPro();
$editor->fetchFileBrowserJS('openfilebrowser', true);
$url = $EDITOR->editorLink('dialog.php?dialog=wproCore_fileBrowser&action=link&chooser=true&dirs='.$type.'&'.(isset($editor->sess) ? $editor->sess->sessionName : 'wprosid').'='.(isset($editor->sess) ? $editor->sess->sessionId : '') . strip_tags('&'.session_name().'='.session_id()) . ($editor->appendToQueryStrings ? '&' . $editor->appendToQueryStrings : ''));
$editor->sess->save($editor);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title></title>
<style type="text/css">
html, body {
	height: 100%;
	width: 100%;
	margin: 0px;
	padding: 0px;
}
</style>
</head>
<body>
<script type="text/javascript">
var p;
if (opener) {
	p = opener;
	var hd = parseInt(window.outerHeight) - parseInt(window.innerHeight);
	var wd = parseInt(window.outerWidth) - parseInt(window.innerWidth);
	window.resizeTo(760+wd, 425+hd);
} else if (dialogArguments) {
	p = dialogArguments;
	var width = 760;
	var height = 425;
	if (parseInt(navigator.appVersion.replace(/[\s\S]*?MSIE\s([0-9\.]*)[\s\S]*?/gi, "$1") ) < 7) {
		var nWidth = width + 12;
		var nHeight = height + 56;
	} else {
		var nWidth = width;
		var nHeight = height;
	}
	window.dialogWidth = nWidth+'px';
	window.dialogHeight = nHeight+'px';
}
if (typeof(p.WPRO_FB_RETURN_FUNCTION)=='undefined')) {
	p.WPRO_FB_RETURN_FUNCTION = {};
}
if (typeof(p.WPRO_FB_GET_FUNCTION)=='undefined')) {
	p.WPRO_FB_GET_FUNCTION = {};
}
p.WPRO_FB_RETURN_FUNCTION['<?php echo addslashes((isset($editor->sess) ? $editor->sess->sessionName : 'wprosid').'='.(isset($editor->sess) ? $editor->sess->sessionId : '')) ?>'] = p.<?php echo preg_replace("/[^a-zA-Z0-9_]/", '', $returnFunction) ?>;
</script>
<iframe src="<?php echo htmlspecialchars($url); ?>" width="100%" height="100%" frameborder="0"></iframe>
</body>
</html>
