<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_spellchecker {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerButton('spelling', '', "WPro.##name##.openDialogPlugin('wproCore_spellchecker',550,400, 'resizable=yes')", '##buttonURL##spelling.gif', 22, 22);
	}	
}
?>