<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_tagEditor {
	function init (&$DIALOG) {
		$DIALOG->title = $DIALOG->langEngine->get('wproCore_tagEditor', 'title');
	}
	function runAction ($action, $params) {
		global $DIALOG;
		if (!isset($params['tagName'])) {
			require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
			$msg = new wproMessageExit();
			$msg->msgCode = WPRO_CRITICAL;
			$msg->msg = 'No tag selected';
			$msg->alert();
		}
		require(WPRO_DIR.'conf/tagAttributes.inc.php');
		$tagName = strtoupper($DIALOG->makeVarOk($params['tagName']));
		$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_tagEditor/dialog.css" type="text/css" />');
		$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_tagEditor/dialog_src.js"></script>');
		if ($tagName=='INPUT') {
			$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_tagEditor/input_src.js"></script>');
		}
		$DIALOG->title = $DIALOG->langEngine->get('wproCore_tagEditor', 'title').' - '.$tagName;
		$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_tagEditor/dialog.tpl.php';
		$DIALOG->template->bulkAssign( array(
				'attributes' => $attributes,
				'tagInfo' => $tagInfo,
				'tagName' => $tagName,
		));
		$DIALOG->options = array(
			array(
				'type'=>'submit',
				'name'=>'ok',
				'value'=>$DIALOG->langEngine->get('core', 'apply'),
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