<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_find {
	function init (&$DIALOG) {
		$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'find'));
		$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_find/dialog.tpl.php';
		$DIALOG->headContent->add('<script type="text/javascript" src="core/js/wproFindAndReplace_src.js"></script>');
		$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_find/dialog_src.js"></script>');
		$DIALOG->options = array(
			array(
				'class' => 'button',
				'type' => 'button',
				'name' => 'replaceButton',
				'value' => $DIALOG->langEngine->get('wproCore_find', 'replace'),
				'disabled' => 'disabled',
				'onclick' => 'replaceText();top.focus();this.focus();',
			),
			array(
				'class' => 'button',
				'type' => 'button',
				'name' => 'replaceAllButton',
				'value' => $DIALOG->langEngine->get('wproCore_find', 'replaceAll'),
				'disabled' => 'disabled',
				'onclick' => 'replaceAllText();top.focus();this.focus();',
			),
			array(
				'class' => 'button',
				'type' => 'submit',
				'name' => 'findNextButton',
				'value' => $DIALOG->langEngine->get('wproCore_find', 'findNext'),
				'disabled' => 'disabled',
				'onclick' => 'findNext();top.focus();this.focus();return false;',
			),
			array(
				'class' => 'button',
				'type' => 'button',
				'name' => 'closeButton',
				'value' => $DIALOG->langEngine->get('core', 'close'),
				'onclick' => 'dialog.close()',
			)
		);
	}
}

?>