<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_tagPath {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerAndEnableFeature('tagpath');
	}
	
	function onBeforeMakeEditor(&$EDITOR) {
		if ($EDITOR->featureIsEnabled('tagpath')) {
			$EDITOR->addJSPlugin('tagPath', 'plugin_src.js');
		}
	}	
}
?>