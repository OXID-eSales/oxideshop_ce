<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_codeCleanup {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerButton('codecleanup', '', "WPro.##name##.openDialogPlugin('wproCore_codeCleanup&action=clean',500,380)", '##buttonURL##codecleanup.gif', 22, 22);
		$EDITOR->registerButton('pastecleanup', '', "WPro.##name##.plugins['wproCore_codeCleanup'].open('##name##')", '##buttonURL##pastecleanup.gif', 22, 22);
	}
	function onBeforeMakeEditor(&$EDITOR) {
		if ($EDITOR->buttonIsEnabled('codecleanup')||$EDITOR->buttonIsEnabled('pastecleanup')) {
			$EDITOR->addJSPlugin('wproCore_codeCleanup', 'plugin_src.js');
		}
	}
}
?>