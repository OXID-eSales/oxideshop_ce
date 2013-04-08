<?php
/*
* WysiwygPro Theme file
* Theme: default
* Author: Chris Bolt
*/
if (!defined('IN_WPRO')) exit;
class wproPlugin_defaultTheme {
	function onBeforeDisplayDialog () {
		global $EDITOR, $DIALOG;
		if ($EDITOR->_browserType == 'safari') {
			$DIALOG->headContent->add('<style type="text/css">
/* selected item colors */
.selected,button.selected,a.selected {
	color:#000000;
	background-color:#b5d5ff;
}
/* iframe dialog title bar */
.titleBar {
	background-color:#b5d5ff;
	color:#000000;
}
</style>');
		}
	}
}

?>