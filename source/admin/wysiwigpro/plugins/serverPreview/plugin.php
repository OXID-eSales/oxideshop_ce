<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_serverPreview {
	var $URL = '';
	/* called before generating editor output */
	function onBeforeMakeEditor (&$EDITOR) {
		if (!empty($this->URL)) {
			$EDITOR->addJSPlugin('serverPreview', 'plugin_src.js');
			$EDITOR->addConfigJS('WPro.##name##._serverPreviewURL = "'.addslashes($this->URL).'";');
		}
	}
}
?>