<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_emoticons {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerButton('emoticon', '', "WPro.##name##.openDialogPlugin('wproCore_emoticons',272,245)", '##buttonURL##emoticon.gif', 22, 22);
	}
}
?>