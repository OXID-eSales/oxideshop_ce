<?php
if (!defined('IN_WPRO')) exit;
class wproFilePlugin_flash {
	
	var $extensions = array('.swf');
	var $description = 'Shockwave Flash';
	var $local = true; // does this plugin handle inserting files from the image manager?
	var $remote = true; // does this plugin handle inserting files form a remote web location?
	var $jsFile = 'plugin_src.js';
		
	function wproFilePlugin_flash () {
		if (isset($GLOBALS['DIALOG']))
			$this->description = $GLOBALS['DIALOG']->langEngine->get('files','swf');
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
		$return = array();
		if (file_exists($file)) {
					
			if ($arr = $DIALOG->plugins['wproCore_fileBrowser']->getMediaDimensions($file)) {
				$width = $arr['width']; $height=$arr['height'];
				$return['width'] = $width;
				$return['height'] = $height;
			}
			
			$getID3 = @new getID3();
			$file_info = @$getID3->analyze($file);
			
			if (isset($file_info['swf'])) {
				if (isset($file_info['swf']['header'])) {
					if (isset($file_info['swf']['header']['version'])) {
						$return['version'] = intval($file_info['swf']['header']['version']);
					}
				}
			}
						
		}
		if (empty($return)) $return = NULL;
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
		
}

?>