<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_ruler {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerButton('ruler', '', "WPro.##name##.openDialogPlugin('wproCore_ruler',320,210)", '##buttonURL##ruler.gif', 22, 22);
		$EDITOR->registerButton('rulerproperties', '', "WPro.##name##.openDialogPlugin('wproCore_ruler',320,210)", '##buttonURL##ruler.gif', 22, 22, 'rulerproperties');
		$EDITOR->registerButton('defaultruler', '', "WPro.##name##.insertAtSelection('<hr>')", '##buttonURL##ruler.gif', 22, 22);
	}	
	function onBeforeMakeEditor (&$EDITOR) {
		if ($EDITOR->buttonIsEnabled('rulerproperties')) {
			$EDITOR->addJSPlugin('wproCore_ruler', 'plugin_src.js');
		}
		if (!$EDITOR->featureIsEnabled('dialogappearanceoptions')) {
			$EDITOR->setButtonFunction('ruler', "WPro.##name##.openDialogPlugin('wproCore_ruler',320,100)");
			$EDITOR->setButtonFunction('rulerproperties', "WPro.##name##.openDialogPlugin('wproCore_ruler',320,100)");	
		} else if (!$EDITOR->featureIsEnabled('htmlDepreciated')) {
			$EDITOR->setButtonFunction('ruler', "WPro.##name##.openDialogPlugin('wproCore_ruler',320,190)");
			$EDITOR->setButtonFunction('rulerproperties', "WPro.##name##.openDialogPlugin('wproCore_ruler',320,190)");	
		}
	}
}
?>