<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_insertHTML {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerButton('inserthtml', '', "WPro.##name##.openDialogPlugin('wproCore_insertHTML',500,400)", '##buttonURL##spacer.gif', 22, 22);
	}	
}
?>