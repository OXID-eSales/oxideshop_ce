<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_insertHTML {
	function init (&$DIALOG) {
		/*$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_insertHTML/dialog_src.js"></script>');*/
		$DIALOG->headContent->add('<link rel="stylesheet" type="text/css" href="core/plugins/wproCore_insertHTML/dialog.css" />');
		$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'inserthtml'));
		$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_insertHTML/dialog.tpl.php';
		$DIALOG->options = array(
			array(
				'type'=>'submit',
				'name'=>'ok',
				'value'=>$DIALOG->langEngine->get('core', 'insert'),
			),
			array(
				'onclick' => 'dialog.close()',
				'type'=>'button',
				'name'=>'cancel',
				'value'=>$DIALOG->langEngine->get('core', 'cancel'),
			)
		);
	}
}

?>