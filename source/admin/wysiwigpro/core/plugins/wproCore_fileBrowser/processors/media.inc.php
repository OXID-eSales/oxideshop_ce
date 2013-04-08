<?php
if (!defined('IN_WPRO')) exit;
				$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_fileBrowser/css/dialog.css" type="text/css" />');
				$DIALOG->headContent->add('<script type="text/javascript" src="core/js/base64.js"></script>');
				$this->addDialogJS();
				$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_fileBrowser/js/media_src.js"></script>');
				$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'media'));
				$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/dialog.tpl.php';
				
				// if no directory set?
				$dirs = $EDITOR->getDirectories('media');
				$DIALOG->assign('dirs', $dirs);
				
				$this->displayEmbedPluginsJS();
				$DIALOG->assign('allowedExtensions', explode(',', str_replace(' ', '', strtolower($EDITOR->allowedMediaExtensions))));
				
				$DIALOG->assign('mediaExtensions', explode(',', str_replace(' ', '', strtolower($EDITOR->allowedMediaExtensions))));
				$DIALOG->assign('imageExtensions', explode(',', str_replace(' ', '', strtolower($EDITOR->allowedImageExtensions))));
				$DIALOG->assign('docExtensions', explode(',', str_replace(' ', '', strtolower($EDITOR->allowedDocExtensions))));
?>