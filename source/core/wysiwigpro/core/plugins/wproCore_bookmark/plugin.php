<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_bookmark {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerButton('bookmark', '', "WPro.##name##.openDialogPlugin('wproCore_bookmark',325,210)", '##buttonURL##bookmark.gif', 22, 22);
		$EDITOR->registerButton('bookmarkproperties', '', "WPro.##name##.openDialogPlugin('wproCore_bookmark',320,210)", '##buttonURL##bookmark.gif', 22, 22, 'bookmarkproperties');
	}
	function onBeforeMakeEditor(&$EDITOR) {
		if ($EDITOR->buttonIsEnabled('bookmarkproperties')) {
			$EDITOR->addJSPlugin('wproCore_bookmark', 'plugin_src.js');
		}
	}	
}
?>