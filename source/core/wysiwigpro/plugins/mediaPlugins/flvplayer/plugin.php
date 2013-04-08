<?php
if (!defined('IN_WPRO')) exit;
class wproFilePlugin_flvplayer {
	
	var $extensions = array('.flv','.mp3', '.mp4', '.h264', '.xspf');
	var $description = 'FLV & MP3 Media Player';
	var $local = true; // does this plugin handle inserting files from the image manager?
	var $remote = true; // does this plugin handle inserting files form a remote web location?
	var $jsFile = 'plugin_src.js';
	
	function wproFilePlugin_flvplayer () {
		if (isset($GLOBALS['DIALOG']))
			$this->description = $GLOBALS['DIALOG']->langEngine->get('wproCore_fileBrowser','flvPlayer');
	}
		
	/* returns an associative array of values to be displayed on the image details pane */
	function displayDetails($file, &$response) {
		global $DIALOG;
		$return = NULL;
		if ($arr = $DIALOG->plugins['wproCore_fileBrowser']->getMediaDimensions($file)) {
			$width = $arr['width']; $height=$arr['height'];
			if (!empty($width)&&!empty($height)) {
				$return = array($DIALOG->langEngine->get('wproCore_fileBrowser','dimensions') => $width.' x '.$height);
			}
		}
		return $return;
	}
	
	/* returns an associative array of values to help populate the local options form */
	function getDetails($file, &$response) {
		global $DIALOG;
		$return = NULL;
		
		// playlists
		if (strrchr($file, '.') == '.xspf') {
			// get default values
			include(WPRO_DIR.'conf/defaultValues/wproCore_fileBrowser.inc.php');
			$return = array('width' => $defaultValues['flvplayerWidth'], 'height' => $defaultValues['flvplayerHeight'], 'playlist' => 1);
		} else {
		
			if ($arr = $DIALOG->plugins['wproCore_fileBrowser']->getMediaDimensions($file)) {
				$width = $arr['width']; $height=$arr['height'];
				if (!empty($width)&&!empty($height)) {
					$return = array('width' => $width, 'height' => $height);
				}
			}
		
		}
		return $return;
	}
	
	/* returns HTML for displaying local options */
	function displayLocalOptions($prefix) {
		global $DIALOG;
		
		$tpl = new wproTemplate();
		$tpl->assign('prefix', $prefix);
		$DIALOG->assignCommonVarsToTemplate($tpl);
		
		return $tpl->fetch(dirname(__FILE__).'/form.tpl.php');
	}
	
	/* returns HTML for displaying remote options */
	function displayRemoteOptions($prefix) {
		global $DIALOG;
		
		$tpl = new wproTemplate();
		$tpl->assign('prefix', $prefix);
		$DIALOG->assignCommonVarsToTemplate($tpl);
		
		return $tpl->fetch(dirname(__FILE__).'/form.tpl.php');
	}
	
	// returns html for displaying an embedded preview
	function displayPreview ($url) {
		return '<object id="flvp" name="flvp" width="100%" height="100%" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" title="">
<param name="movie" value="media/player.swf" />
<param name="flashvars" value="file='.htmlspecialchars($url).'&amp;autostart=true&amp;fullscreen=true" />
<param name="allowfullscreen" value="true" />
<param name="allowscriptaccess" value="always" /><embed id="flvp" name="flvp" width="100%" height="100%" src="media/player.swf" flashvars="file='.htmlspecialchars($url).'&amp;autostart=true&amp;fullscreen=true" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash"></embed></object>';
	}
		
}

?>