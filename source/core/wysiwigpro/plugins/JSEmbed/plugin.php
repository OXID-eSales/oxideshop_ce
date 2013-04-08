<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_JSEmbed {
		
	function onBeforeMakeEditor(&$EDITOR) {
		$EDITOR->addJSPlugin('JSEmbed', 'plugin_src.js');
	}
		
}
?>