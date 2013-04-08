<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_zoom {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerSelect('zoom', '', "WPro.##name##.plugins['wproCore_zoom'].zoom(this.value);", array('10%'=>'10%','25%'=>'25%','50%'=>'50%','75%'=>'75%','100%'=>'100%','150%'=>'150%','200%'=>'200%','500%'=>'500%',), '100%', '60', 22);
		array_push($EDITOR->_featureDefinitions['nongecko'][0], 'zoom');
	}
	function onBeforeMakeEditor(&$EDITOR) {
		if ($EDITOR->buttonIsEnabled('zoom')) {
			$EDITOR->addJSPlugin('wproCore_zoom', 'plugin_src.js');
		}
	}
}
?>