<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_bookmark {
	function init (&$DIALOG) {
		$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_bookmark/dialog_src.js"></script>');
		$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_bookmark/dialog.css" type="text/css" />');
		$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'bookmark'));
		$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_bookmark/dialog.tpl.php';
		$DIALOG->options = array(
			array(
				'onclick' => 'dialog.close()',
				'type'=>'button',
				'name'=>'cancel',
				'value'=>$DIALOG->langEngine->get('core', 'close'),
			)
		);
	}
}

?>