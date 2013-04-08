<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_specialCharacters {
	function init (&$DIALOG) {
		$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'specialchar'));
		$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_specialCharacters/dialog.tpl.php';
		$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_specialCharacters/dialog.css" type="text/css" />');
		$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_specialCharacters/dialog_src.js"></script>');
		$DIALOG->headContent->add('<script type="text/javascript" src="core/js/wproCookies_src.js"></script>');
		require(WPRO_DIR.'conf/specialCharacters.inc.php');
		$recentlyUsed = isset($_COOKIE['wproRecentlyUsedSpecialChars']) ? array_unique(explode(',',$_COOKIE['wproRecentlyUsedSpecialChars'])) : array();
		if (count($recentlyUsed) > 21) {
			$recentlyUsed = array_slice($recentlyUsed, 0, 21);
		}
		$DIALOG->template->bulkAssign(array(
			'symbols' => $symbols,
			'recentlyUsed' => $recentlyUsed,
		));
		$DIALOG->options = array(
			array(
				'type'=>'submit',
				'name'=>'ok',
				'disabled'=>'disabled',
				'value'=>$DIALOG->langEngine->get('core', 'insert'),
			),
			array(
				'onclick' => 'dialog.close()',
				'type'=>'button',
				'name'=>'close',
				'value'=>$DIALOG->langEngine->get('core', 'close'),
			)
		);
	}
}

?>