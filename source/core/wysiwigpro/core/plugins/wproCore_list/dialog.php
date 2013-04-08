<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_list {
	function init (&$DIALOG) {
		$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_list/dialog.css" type="text/css" />');
		$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_list/dialog_src.js"></script>');
		$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'bulletsandnumbering'));
		$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_list/dialog.tpl.php';
	}
}
?>