<?php
if (!defined('IN_WPRO')) exit;

		global $EDITOR, $DIALOG, $WPRO_SESS;
		//if (!$response) {
			$response = $DIALOG->createAjaxResponse();
		//}
		
		// initial var check...
		if (!isset($folderId, $folderPath, $uploadID) 
		|| (!is_string($folderId)&&!is_int($folderId))||!is_string($folderPath)
		||!is_string($uploadID)||!is_array($overwrite)) {
			$response->addAlert('Datatype error.');
			$response->addScriptCall("FB.onFolderNotFound", "");
			$response->addScriptCall("dialog.hideLoadMessage", '');
			$response->addScriptCall("hideMessageBox", '');
			return $response;
		}
		
		$fs = new wproFilesystem();
		$failed = array();
		$succeeded = array();
		$uploaded = array();
		
		if ($arr = $this->getFolder($folderId, $folderPath, $response)) {
			$directory = $arr['directory'];
			$URL = $arr['URL'];
			$dir = $arr['dir'];
			
			if (!$dir->upload) {
				$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSUploadPermissionsError'));
				$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, array(), $history, $response);
				$response->addScriptCall("dialog.hideLoadMessage", '');
				return $response;
			}
			
			if (isset($WPRO_SESS->data['uploads'])) {
			
				if (isset($WPRO_SESS->data['uploads'][$uploadID])) {
					
					$uploaded = isset($WPRO_SESS->data['uploads'][$uploadID]['succeeded']) ? $WPRO_SESS->data['uploads'][$uploadID]['succeeded'] : array();
				
					if (isset($WPRO_SESS->data['uploads'][$uploadID]['overwrite'])) {
						if (count($WPRO_SESS->data['uploads'][$uploadID]['overwrite'])) {
							if (!$dir->overwrite) {
								$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSOverwritePermissionsError'));
								$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, array(), $history, $response);
								$response->addScriptCall("dialog.hideLoadMessage", '');
								unset($WPRO_SESS->data['uploads'][$uploadID]);
								$WPRO_SESS->doSave=true;
								return $response;
							} else { 
								
								foreach ($overwrite as $file) {
									
									if (isset($WPRO_SESS->data['uploads'][$uploadID]['overwrite'][$file])) {
									
										$temp = $WPRO_SESS->data['uploads'][$uploadID]['overwrite'][$file];
													
									//if ($fs->fileNameOk($file)) {
										if ((!$file = $fs->makeFileNameOK($file))||(!$temp = $fs->makeFileNameOK($temp))) {
											array_push($failed, $file);
											continue;
										}
										
										//$isFile = is_file($directory.$file);
										// if it doesn't exist, who cares? we were going to delete it anyway!
										if (!file_exists($directory.$temp)) {
											continue;
										}
										if ($fs->delete($directory.$file) && $fs->rename($directory.$temp, $directory.$file)) {
											unset($WPRO_SESS->data['uploads'][$uploadID]['overwrite'][$file]);
											// delete thumbnails
											if ($fs->fileNameOk($EDITOR->thumbnailFolderName)) {
												if (is_file($directory.$EDITOR->thumbnailFolderName.'/'.$file)) {
													$fs->delete($directory.$EDITOR->thumbnailFolderName.'/'.$file);
												} else if (is_file($directory.$EDITOR->thumbnailFolderName.'/'.$file.'.png')) {
													$fs->delete($directory.$EDITOR->thumbnailFolderName.'/'.$file.'.png');
												}
											}
											array_push($succeeded, $file);
										} else {
											array_push($failed, $file);
										}				
									//} else {
									//	array_push($failed, $file);
									//}
									}
								}
							
								// silently cleanup temp files
								foreach ($WPRO_SESS->data['uploads'][$uploadID]['overwrite'] as $file => $temp) {
									if (!$temp = $fs->makeFileNameOK($temp)) {
										continue;
									}
									if (!is_file($directory.$temp)) {
										continue;
									}
									if ($fs->delete($directory.$temp)) {
									} else {
										$response->addAlert('Temp cleanup failed');
									}
								}
							}
						}
					}
					unset($WPRO_SESS->data['uploads'][$uploadID]);
					$WPRO_SESS->doSave=true;
				}
			}
			if (count($failed)) {
				$msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'JSOverwriteError');
				$response->addAlert($msg);
			}
			$highlight = array_merge($uploaded, $succeeded);
			
			// trigger upload event
			$EDITOR->triggerEvent('onUpload', array('directory'=>$directory,'directoryURL'=>$URL,'directoryObject'=>$dir,'files'=>$succeeded));
			
			$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, $highlight, $history, $response);
		} else {
			$response->addScriptCall("FB.onFolderNotFound", "");
		}
		$response->addScriptCall("dialog.hideLoadMessage", '');
		return $response;


?>