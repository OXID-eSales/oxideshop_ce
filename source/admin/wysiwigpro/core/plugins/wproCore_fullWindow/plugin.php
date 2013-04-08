<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_fullWindow {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerButton('fullwindow', '', "WPro.##name##.fullWindow()", '##buttonURL##fullwindow.gif', 22, 22, 'fullwindow');
	}
	function onBeforeMakeEditor(&$EDITOR) {
		//if ($EDITOR->buttonIsEnabled('fullwindow')) {
			$EDITOR->addJSPlugin('wproCore_fullWindow', 'plugin_src.js');
		//}
	}	
}
?>