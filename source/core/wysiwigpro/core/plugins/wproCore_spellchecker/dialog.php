<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_spellchecker {
	function init (&$DIALOG) {
		global $WPRO_SESS, $EDITOR;
		
		$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_spellchecker/dialog.css" type="text/css" />');
		$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_spellchecker/dialog_src.js"></script>');
		$DIALOG->headContent->add('<script type="text/javascript" src="core/js/wproCookies.js"></script>');
		
		$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'spelling'));
		$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_spellchecker/dialog.tpl.php';
		require_once(WPRO_DIR.'conf/spellchecker.inc.php');
		require_once(WPRO_DIR.'core/plugins/wproCore_spellchecker/config.inc.php');
		
		// language
		if (!empty($EDITOR->htmlLang)) {
			$dictionary = $DIALOG->EDITOR->htmlLang;
		} else {
			$dictionary = $DIALOG->EDITOR->lang;
		}
		
		$DIALOG->template->assign('dictionary', $dictionary);
		//$DIALOG->template->assign('SPELLCHECKER_API', $SPELLCHECKER_API);
		
		$sid = $WPRO_SESS->sessionId;
		$wpsname = $WPRO_SESS->sessionName;
		$DIALOG->template->assign('sid', $WPRO_SESS->sessionId);
		$DIALOG->template->assign('wpsname', $WPRO_SESS->sessionName);
		
		//if ($SPELLCHECKER_API=='http') {
			//$authstring = '<input type="hidden" name="wpsid" value="'.base64_encode($EDITOR->_sessionId).'" />';
			//$DIALOG->template->assign('authenticationstring', $DIALOG->EDITOR->_jsEncode($authstring));
		//	$DIALOG->template->assign('spellcheckerURL', WPRO_CENTRAL_SPELLCHECKER_URL);
		//} else {
			$DIALOG->template->assign('spellcheckerURL', $EDITOR->editorLink('core/plugins/wproCore_spellchecker/checkSpelling.php?'.$wpsname.'='.$sid.($EDITOR->appendToQueryStrings ? '&'.$EDITOR->appendToQueryStrings : '').($EDITOR->appendSid ? strip_tags(defined('SID') ? '&'.SID : '') : '')));
		//}

		$DIALOG->options = array(
			array(
				'onclick'=>'dialog.doFormSubmit()',
				'type'=>'button',
				'name'=>'ok',
				'disabled'=>'disabled',
				'value'=>$DIALOG->langEngine->get('core', 'apply')
			),
			array(
				'onclick' => 'dialog.close()',
				'type'=>'button',
				'name'=>'cancel',
				'value'=>$DIALOG->langEngine->get('core', 'cancel')
			)
		);
	}
}

?>