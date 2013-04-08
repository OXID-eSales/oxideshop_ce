<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_snippets {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerButton('snippets', '', "WPro.##name##.openDialogPlugin('wproCore_snippets',585,382,'','modeless')", '##buttonURL##snippets.gif', 22, 22);
	}	
}
?>