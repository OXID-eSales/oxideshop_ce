<?php
if (!defined('IN_WPRO')) exit;
class wproFilePlugin_youtube {
	
	var $extensions = array();
	var $description = 'You Tube Video';
	var $local = false; // does this plugin handle inserting files from the image manager?
	var $remote = true; // does this plugin handle inserting files form a remote web location?
	var $jsFile = 'plugin_src.js';
	
	function wproFilePlugin_youtube () {
		if (isset($GLOBALS['DIALOG']))
			$this->description = $GLOBALS['DIALOG']->langEngine->get('wproCore_fileBrowser', 'youtube');
	}
		
	/* returns an associative array of values to be displayed on the image details pane */
	function displayDetails($file, &$response) {
		return null;
	}
	
	/* returns an associative array of values to help populate the local options form */
	function getDetails($file, &$response) {
		return null;
	}
	
	/* returns HTML for displaying local options */
	function displayLocalOptions($prefix) {
		return '';
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