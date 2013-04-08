<?php
if (!defined('IN_WPRO')) exit;
class wproFilePlugin_images {
	
	var $extensions = array('.jpg','.jpeg','.gif','.png');
	var $description = 'Image';
	var $local = true; // does this plugin handle inserting files from the image manager?
	var $remote = true; // does this plugin handle inserting files form a remote web location?
	var $jsFile = 'plugin_src.js';
	
	function wproFilePlugin_images () {
		if (isset($GLOBALS['EDITOR']))
			$this->extensions = explode(',', str_replace(' ', '', strtolower($GLOBALS['EDITOR']->allowedImageExtensions)));
		if (isset($GLOBALS['DIALOG']))
			$this->description = $GLOBALS['DIALOG']->langEngine->get('wproCore_fileBrowser','image');
	}
	
	/* returns an associative array of values to be displayed on the image details pane */
	function displayDetails($file, &$response) {
		global $DIALOG;
		$return = NULL;
		if (file_exists($file)) {
			if (@list ($width, $height) = @getimagesize($file)) {
				$return = array($DIALOG->langEngine->get('wproCore_fileBrowser','dimensions') => $width.' x '.$height);
			}
		}
		return $return;
	}
	
	/* returns an associative array of values to help populate the local options form */
	function getDetails($file, &$response) {
		$return = NULL;
		if (file_exists($file)) {
			if (@list ($width, $height) = @getimagesize($file)) {
				$return = array('width' => $width, 'height' => $height);
			} else {
				$return = array('width' => '', 'height' => '');
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
		
		return $tpl->fetch(dirname(__FILE__).'/local.tpl.php');
	}
	
	/* returns HTML for displaying remote options */
	function displayRemoteOptions($prefix) {
		global $DIALOG;
		
		$tpl = new wproTemplate();
		$tpl->assign('prefix', $prefix);
		$DIALOG->assignCommonVarsToTemplate($tpl);
		
		return $tpl->fetch(dirname(__FILE__).'/remote.tpl.php');
	}
	
	
}

?>