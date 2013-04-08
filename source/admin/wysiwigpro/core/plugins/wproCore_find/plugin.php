<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_find {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerButton('find', '', "WPro.##name##.openDialogPlugin('wproCore_find',440,151,'','modeless')", '##buttonURL##find.gif', 22, 22);
	}	
}
?>