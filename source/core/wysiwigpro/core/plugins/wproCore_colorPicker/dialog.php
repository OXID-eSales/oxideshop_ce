<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_colorPicker {
	function init (&$DIALOG) {
		$this->DIALOG = &$DIALOG;
		$DIALOG->title = $DIALOG->langEngine->get('wproCore_colorPicker', 'title');
	}
	function runAction ($action, $params) {
		global $EDITOR;
		$DIALOG = &$this->DIALOG;
		$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_colorPicker/dialog.tpl.php';
		$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_colorPicker/dialog.css" type="text/css" />');
		$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_colorPicker/dialog_src.js"></script>');
		$DIALOG->headContent->add('<script type="text/javascript" src="core/js/wproCookies_src.js"></script>');
		require(WPRO_DIR.'conf/colorSwatches.inc.php');
		if (!empty($EDITOR->colorSwatches)) {
			$colors['Site colors'] = $EDITOR->colorSwatches;
		}
		if (!$EDITOR->featureIsEnabled('webColors')) {
			if (isset($colors['Web safe']) ) {
				unset($colors['Web safe']);
			}
		}				
		$recentlyUsed = isset($_COOKIE['wproRecentlyUsedColors']) ? array_unique(explode('|',$_COOKIE['wproRecentlyUsedColors'])) : array();
		if (count($recentlyUsed) > 19) {
			$recentlyUsed = array_slice($recentlyUsed, 0, 19);
		}		
		$DIALOG->template->bulkAssign( array(
			'selectedColor' => isset($params['selectedColor']) ? $params['selectedColor'] : '',
			'colors' => $colors,
			'recentlyUsed' => $recentlyUsed,
		));
	}
}

?>