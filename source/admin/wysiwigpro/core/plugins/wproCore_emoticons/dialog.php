<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_emoticons {
	function init (&$DIALOG) {
		global $EDITOR;
		
		$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'emoticon'));
		
		$DIALOG->reloadInFrame();
		
		$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_emoticons/dialog.tpl.php';
		$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_emoticons/dialog.css" type="text/css" />');
		$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_emoticons/dialog_src.js"></script>');
		
		$DIALOG->template->assign('emoticonDir', $EDITOR->emoticonDir);
		$DIALOG->template->assign('emoticonURL', $EDITOR->emoticonURL);
		
		if (WPRO_EMOTICON_DIR && $EDITOR->emoticonDir != WPRO_EMOTICON_DIR) {
			// load from custom directory
			$DIALOG->template->assign('custom', true);
			require_once(WPRO_DIR.'core/libs/wproFilesystem.class.php');
			$fs = new wproFilesystem();
			$DIALOG->template->assign('files', $fs->getFilesInDir($EDITOR->emoticonDir, 'name', 'asc', array('.gif','.png','.jpg','.jpeg') ));
		} else {
			// load local smilies
			$emoticonDir = $EDITOR->emoticonDir;
			$emoticonURL = $EDITOR->emoticonURL;
			require(WPRO_DIR.'conf/emoticons.inc.php');
			$DIALOG->template->assign('emoticons', $emoticons);
			$DIALOG->template->assign('custom', false);
		}
		
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
				'value'=>$DIALOG->langEngine->get('core', 'cancel'),
			)
		);
	}
}

?>