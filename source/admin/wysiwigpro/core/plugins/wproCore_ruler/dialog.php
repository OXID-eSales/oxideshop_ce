<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_ruler {
	function init (&$DIALOG) {
		$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_ruler/dialog_src.js"></script>');
		$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'ruler'));
		$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_ruler/dialog.tpl.php';
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