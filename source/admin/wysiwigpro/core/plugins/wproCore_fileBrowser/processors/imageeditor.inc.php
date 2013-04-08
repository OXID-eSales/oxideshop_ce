<?php
if (!defined('IN_WPRO')) exit;
				
				// check vars
				if (!isset($params['folderID']) || !isset($params['folderPath']) || empty($params['image'])) {
					require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
					$msg = new wproMessageExit();
					$msg->msgCode = WPRO_CRITICAL;
					$msg->msg = 'Sorry not enough parameters.';
					$msg->alert();
				}
				
				$params['folderPath'] = base64_decode($params['folderPath']);
				
				$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'image'));
				$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/imageEditor.tpl.php';
				$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_fileBrowser/css/imageEditor.css" type="text/css" />');
				$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_fileBrowser/js/imageEditor_src.js"></script>');
				
				$x = null;
				if ($arr = $this->getFolder($params['folderID'], $params['folderPath'], $x)) {
				
					$editorID = md5(uniqid(rand(), true));
					
					$directory = $arr['directory'];
					$URL = $arr['URL'];
					$dir = $arr['dir'];
					
					$fs = new wproFilesystem();
					$image = $fs->makeFileNameOK($params['image']);
					
					if (!file_exists($directory.$image)||!$image) {
						require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
						$msg = new wproMessageExit();
						$msg->msgCode = WPRO_CRITICAL;
						$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'fileNotExistError');
						$msg->alert();
					}
					
					// check extension
					// check file extension
					$extension = strrchr($image, '.');
					if (!$fs->extensionOK($extension, array('.jpg','.jpeg','.gif','.png'))) {
						require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
						$msg = new wproMessageExit();
						$msg->msgCode = WPRO_CRITICAL;
						$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'editImageExtensionError');
						$msg->alert();									
					}
					
					// check filters
					// filter check 
					if ($fs->filterMatch($image, $dir->filters)) {
						require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
						$msg = new wproMessageExit();
						$msg->msgCode = WPRO_CRITICAL;
						$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'Bad name');
						$msg->alert();
					}

					
					if (!isset($WPRO_SESS->data['imageEditor'])) {
						$WPRO_SESS->data['imageEditor'] = array();
					}
					$WPRO_SESS->data['imageEditor'][$editorID] = array();
					$WPRO_SESS->data['imageEditor'][$editorID]['temp'] = '';
					$WPRO_SESS->data['imageEditor'][$editorID]['file'] = $image;
					$WPRO_SESS->doSave = true;
					
					list ($width, $height) = @getimagesize($directory.$image);
					$width = intval($width);
					$height = intval($height);
					
					if ($width==0||$height==0) {
						require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
						$msg = new wproMessageExit();
						$msg->msgCode = WPRO_CRITICAL;
						$msg->msg = 'bad dimensions';
						$msg->alert();
					}
				
					// can user edit?
					if (!$dir->editImages) {
						require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
						$msg = new wproMessageExit();
						$msg->msgCode = WPRO_WARNING;
						$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'editPermissionsError');
						$msg->alert();
							
					}
										
					$DIALOG->template->assign('width', $width);
					$DIALOG->template->assign('height', $height);
					$DIALOG->template->assign('editorID', $editorID);
					
					$DIALOG->template->assign('image', $image);
					$DIALOG->template->assign('dir', $dir);
					$DIALOG->template->assign('folderPath', $params['folderPath']);
					$DIALOG->template->assign('folderID', $params['folderID']);
					
				} else {
					require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
					$msg = new wproMessageExit();
					$msg->msgCode = WPRO_CRITICAL;
					$msg->msg = 'BAD DIRECTORY ID';
					$msg->alert();
				}
				
				$DIALOG->options = array(
					array(
						'type'=>'submit',
						'name'=>'save',
						'disabled'=>'disabled',
						'value'=>$DIALOG->langEngine->get('wproCore_fileBrowser', 'save'),
					),
					array(
						'type'=>'button',
						'onclick' => 'initSaveAs()',
						'name'=>'saveAs',
						//'disabled'=>'disabled',
						'value'=>$DIALOG->langEngine->get('wproCore_fileBrowser', 'saveAs'),
					),
					array(
						'onclick' => 'confirmClose(true)',
						'type'=>'button',
						'name'=>'close',
						'value'=>$DIALOG->langEngine->get('core', 'close'),
					),
				);
				

?>