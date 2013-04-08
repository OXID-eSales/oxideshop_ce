<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_list {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerButton('bulletsandnumbering', '', 'WPro.##name##.openDialogPlugin(\'wproCore_list\',550,280)', '##buttonURL##bullets.gif', 22, 22, 'bulletsandnumbering');
	}
	function onBeforeMakeEditor (&$EDITOR) {
		if ($EDITOR->buttonIsEnabled('bulletsandnumbering')) {
			$EDITOR->addJSPlugin('wproCore_list', 'plugin_src.js');
			if (!$EDITOR->featureIsEnabled('dialogappearanceoptions')) {
				$EDITOR->setButtonFunction('bulletsandnumbering', 'WPro.##name##.openDialogPlugin(\'wproCore_list\',320,100)');
			} else if (!$EDITOR->featureIsEnabled('htmlDepreciated')) {
				$EDITOR->setButtonFunction('bulletsandnumbering', 'WPro.##name##.openDialogPlugin(\'wproCore_list\',550,270)');
			}
		}
	}
}
?>