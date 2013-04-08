<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_specialCharacters {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerButton('specialchar', '', "WPro.##name##.openDialogPlugin('wproCore_specialCharacters',540,382,'','modeless')", '##buttonURL##specialchar.gif', 22, 22);
		$EDITOR->registerAndEnableFeature('special', array('specialchar'));
	}	
}
?>