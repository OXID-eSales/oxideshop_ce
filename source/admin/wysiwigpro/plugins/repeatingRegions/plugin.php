<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_repeatingRegions {
	
	function onBeforeMakeEditor(&$EDITOR) {
		$EDITOR->addJSPlugin('repeatingRegions', 'plugin_src.js');
	}
		
}
?>