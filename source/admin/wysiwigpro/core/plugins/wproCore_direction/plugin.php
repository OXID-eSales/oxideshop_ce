<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_direction {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerButton('dirltr', '', 'WPro.##name##.callFormatting(\'dirltr\')', '##buttonURL##dirltr.gif', 22, 22, 'dirltr');
		$EDITOR->registerButton('dirrtl', '', 'WPro.##name##.callFormatting(\'dirrtl\')', '##buttonURL##dirrtl.gif', 22, 22, 'dirrtl');
	}
	function onBeforeMakeEditor(&$EDITOR) {
		if ($EDITOR->buttonIsEnabled('dirltr')||$EDITOR->buttonIsEnabled('dirrtl')) {
			$EDITOR->addJSPlugin('wproCore_direction', 'plugin_src.js');
		}
	}
}
?>