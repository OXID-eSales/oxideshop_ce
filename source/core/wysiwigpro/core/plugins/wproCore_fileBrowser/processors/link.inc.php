<?php
if (!defined('IN_WPRO')) exit;
				$chooser = isset($params['chooser']) ? true : false;
				$type = preg_replace("/[^a-z,]/si", '', isset($params['dirs'])?$params['dirs']:'link');
				
				$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_fileBrowser/css/dialog.css" type="text/css" />');
				//if (!$chooser) {
					$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_fileBrowser/css/link.css" type="text/css" />');
				//}
				$DIALOG->headContent->add('<script type="text/javascript" src="core/js/base64.js"></script>');
				$this->addDialogJS();
				$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_fileBrowser/js/links_src.js"></script>');
				
				if ($chooser) {
					$DIALOG->headContent->add('<style type="text/css">.outlookBar{height:415px;}</style>');
				}
				
				//$DIALOG->headContent->add('<!--[if lt IE 8]><style type="text/css">#sitePreview{zoom:50%;width:760px;height:670px;}</style><![endif]-->');
				
				
				$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'link'));
				$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/dialog.tpl.php';
				
				$dirs = $EDITOR->getDirectories($type);
				$DIALOG->assign('dirs', $dirs);
				
				$wpsid = $WPRO_SESS->sessionId;
				$wpsname = $WPRO_SESS->sessionName;
				
				$DIALOG->assign('chooser', $chooser);
				$DIALOG->assign('chooserType', $type);
								
				$linksBrowserURL = str_replace(array('?&','&&'), array('?','&'),  (
					
						empty($EDITOR->linksBrowserURL) ? 
						
						$EDITOR->editorLink('dialog.php?dialog=wproCore_fileBrowser&action=linksBrowser') : 
						
						$EDITOR->linksBrowserURL.(strstr($EDITOR->linksBrowserURL, '?') ? '&' : '?')
					
					
					).($EDITOR->appendToQueryStrings ? '&'.$EDITOR->appendToQueryStrings : '').'&'.$wpsname.'='.$wpsid.($EDITOR->appendSid ? strip_tags(defined('SID') ? '&'.SID : '') : ''));
								
				$DIALOG->assign('linksBrowserURL', $linksBrowserURL );
								
				$DIALOG->assign('mediaExtensions', explode(',', str_replace(' ', '', strtolower($EDITOR->allowedMediaExtensions))));
				$DIALOG->assign('imageExtensions', explode(',', str_replace(' ', '', strtolower($EDITOR->allowedImageExtensions))));
				$DIALOG->assign('docExtensions', explode(',', str_replace(' ', '', strtolower($EDITOR->allowedDocExtensions))));
				
				$EDITOR->triggerEvent('onBeforeGetLinks');
?>