<?php
if (!defined('IN_WPRO')) exit;
		
		global $EDITOR, $DIALOG, $WPRO_SESS;
		//if (!$response) {
			$response = $DIALOG->createAjaxResponse();
		//}
		
		// initial var check...
		if (!isset($folderId, $folderPath, $editorID, $task) 
		|| (!is_string($folderId)&&!is_int($folderId))||!is_string($folderPath)
		||!is_string($editorID)||!is_string($task)) {
			$response->addAlert('Datatype error.');
			$response->addScriptCall("dialog.hideLoadMessage", '');
			return $response;
		}
		
		$fs = new wproFilesystem();
		
		require_once(WPRO_DIR.'core/libs/wproImageEditor.class.php');
		$im = new wproImageEditor();
		
		$failed = array();
		
		if ($arr = $this->getFolder($folderId, $folderPath, $response)) {
			$directory = $arr['directory'];
			$URL = $arr['URL'];
			$dir = $arr['dir'];
			
			if (!$dir->editImages) {
				$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSEditPermissionsError'));
				$response->addScriptCall("dialog.close", '');
				$response->addScriptCall("dialog.hideLoadMessage", '');
				return $response;
			}
			
			if (isset($WPRO_SESS->data['imageEditor'])) {
				
				/*ob_start();
				echo $editorID;
				print_r($WPRO_SESS->data['imageEditor']);
				$response->addAlert(ob_get_contents());
				ob_end_clean();
				//return $response;*/
				
				if (isset($WPRO_SESS->data['imageEditor'][$editorID]) ) {
					
					$file = isset($WPRO_SESS->data['imageEditor'][$editorID]['file']) ? $WPRO_SESS->data['imageEditor'][$editorID]['file'] : '';
					$temp = isset($WPRO_SESS->data['imageEditor'][$editorID]['temp']) ? $WPRO_SESS->data['imageEditor'][$editorID]['temp'] : '';
					
					if (empty($temp)) {
						$extension = strrchr(strtolower($file),'.');
						$temp = $fs->resolveDuplicate(uniqid('_WPROTEMP_').$extension, $directory);
						$imageToEdit = $file;
						$canSave = false;
					} else {
						$imageToEdit = $temp;
						$canSave = true;
					}
					
					$file = $fs->makeFileNameOK($file);
					$imageToEdit = $fs->makeFileNameOK($imageToEdit);
					$temp = $fs->makeFileNameOK($temp);
					
					// check memory limit
					if (!stristr($task, 'sav')&&!strstr($task, 'next')&&!strstr($task, 'prev')) {
						// check memory limit
						if (!$im->_setMemoryForImage( $directory.$imageToEdit )) {
							$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSEditMemoryError'));
							list ($origwidth, $origheight) = getimagesize($directory.$imageToEdit);
							$response->addScriptCall('editFinished', $file, $file, $origwidth, $origheight, false);
							return $response;
						}
					}
					
					if (!empty($file) && !empty($temp) && is_file($directory.$imageToEdit)) {
					
						if ($task=='rotate') {
							if ($options == 90 || $options == 270) {							
								if ($resized = $im->rotate($directory.$imageToEdit, $directory.$temp, intval($options))) {
									$width = $resized[0];
									$height = $resized[1];
									$temp = basename($resized[2]);
									$WPRO_SESS->data['imageEditor'][$editorID]['temp'] = $temp;
									$response->addScriptCall('editFinished', $file, $temp, $width, $height, true);
									//$WPRO_SESS->save();
									$WPRO_SESS->doSave=true;
									$response->addScriptCall("dialog.hideLoadMessage", '');
									return $response;
								} else {
									array_push($failed, $file);
								}
							}
						} else if ($task == 'resize') {
								if ((!empty($options['width'])&&!empty($options['height']))&&is_numeric($options['width'])&&is_numeric($options['height'])) {
									if ($resized = $im->proportionalResize($directory.$imageToEdit, $directory.$temp, intval($options['width']), intval($options['height']))) {
										$width = $resized[0];
										$height = $resized[1];
										$temp = basename($resized[2]);
										$WPRO_SESS->data['imageEditor'][$editorID]['temp'] = $temp;
										$response->addScriptCall('editFinished', $file, $temp, $width, $height, true);
										//$WPRO_SESS->save();
										$WPRO_SESS->doSave=true;
										$response->addScriptCall("dialog.hideLoadMessage", '');
										return $response;
									} else {
										array_push($failed, $file);
									}
								} else {
									array_push($failed, $file);
								}
						} else if ($task == 'exitWithoutSaving') {
							// delete temp file
							if (is_file($directory.$temp)) {
								$fs->delete($directory.$temp);
							}
							$this->displayFolderList($folderId, $folderPath, $options['page'], $options['sortBy'], $options['sortDir'], $options['view'], array($file), false, $response);
							//$response->addScriptCall('closeSubDialog', '');
							unset($WPRO_SESS->data['imageEditor'][$editorID]);
							//$WPRO_SESS->save();
							$WPRO_SESS->doSave=true;
							$response->addScriptCall("dialog.hideLoadMessage", '');
							return $response;
						} else if ($task == 'initSaveAs') {
							$suggestExtension = strrchr(strtolower($file),'.');
							$suggest = $fs->resolveDuplicate($file, $directory);
							$suggest = basename(substr($suggest, 0, strlen($suggest)-strlen($suggestExtension)));
							$response->addScriptCall('showSaveAs', htmlspecialchars($suggest), htmlspecialchars($suggestExtension));
							$response->addScriptCall("dialog.hideLoadMessage", '');
							return $response;
						} else {
							if (substr($task, 0, 4) == 'save') {
								
								if (!$canSave || !is_file($directory.$temp)) {
									if ($task == 'saveAs') {
										copy ($directory.$file, $directory.$temp);
									} else {
										$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSSaveActionError'));
										$response->addScriptCall('dialog.hideLoadMessage', '');
										$response->addScriptCall('hideMessageBox', '');
										return $response;
									}
								}
								
								$extra = '';
								$extension1 = strrchr(strtolower($file),'.');
								$extension2 = strrchr(strtolower($temp),'.');
								
								if ($task == 'saveAs') {
									// bad file name
									if (!$fs->fileNameOk($options)) {
										$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSFileNameError'));
										
										$suggestExtension = strrchr(strtolower($file),'.');
										$suggest = $fs->resolveDuplicate($file, $directory);
										$suggest = basename(substr($suggest, 0, strlen($suggest)-strlen($suggestExtension)));
										$response->addScriptCall('showSaveAs', htmlspecialchars($suggest), htmlspecialchars($suggestExtension));
										$response->addScriptCall("dialog.hideLoadMessage", '');
										return $response;
									}
									if (is_file($directory.$options.$extension2)) {
										$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSFileNameTakenError'));
										
										$suggestExtension = strrchr(strtolower($file),'.');
										$suggest = $fs->resolveDuplicate($file, $directory);
										$suggest = basename(substr($suggest, 0, strlen($suggest)-strlen($suggestExtension)));
										$response->addScriptCall('showSaveAs', htmlspecialchars($suggest), htmlspecialchars($suggestExtension));
										$response->addScriptCall("dialog.hideLoadMessage", '');
										return $response;
									}
									
									$file = $options.$extension2;
									
								} else {
								
									if ($extension1 != $extension2) {
										$extra = '.png';
									}
									
									$fs->delete($directory.$file);
								}
								
								if ($fs->rename($directory.$temp, $directory.$file.$extra)) {
									// delete thumbnails
									if ($fs->fileNameOk($EDITOR->thumbnailFolderName)) {
										if (is_file($directory.$EDITOR->thumbnailFolderName.'/'.$file)) {
											$fs->delete($directory.$EDITOR->thumbnailFolderName.'/'.$file);
										} else if (is_file($directory.$EDITOR->thumbnailFolderName.'/'.$file.'.png')) {
											$fs->delete($directory.$EDITOR->thumbnailFolderName.'/'.$file.'.png');
										}
									}
									
									if ($task == 'saveAndExit') {
										$this->displayFolderList($folderId, $folderPath, $options['page'], $options['sortBy'], $options['sortDir'], $options['view'], array($file.$extra), false, $response);
										//$response->addScriptCall('closeSubDialog', '');
										unset($WPRO_SESS->data['imageEditor'][$editorID]);
										//$WPRO_SESS->save();
										$WPRO_SESS->doSave=true;
										$response->addScriptCall("dialog.hideLoadMessage", '');
										return $response;
									} else {
										unset($WPRO_SESS->data['imageEditor'][$editorID]['temp']);
										if ($task == 'save') {
											//$WPRO_SESS->save();
											$WPRO_SESS->doSave=true;
											$response->addScriptCall('saveFinished', '');
											$response->addScriptCall("dialog.hideLoadMessage", '');
											return $response;
										} elseif ($task=='saveAs') {
											$WPRO_SESS->data['imageEditor'][$editorID]['file'] = $file;
											list ($width, $height) = @getimagesize($directory.$file);
											$response->addScriptCall('editFinished', $file, '', $width, $height, false);
											$response->addScriptCall('hideMessageBox', '');
											$response->addScriptCall("dialog.hideLoadMessage", '');
											//$WPRO_SESS->save();
											$WPRO_SESS->doSave=true;
											return $response;
										}
									}
									
								} else {
									$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSSaveActionError'));
									if ($task == 'saveAndExit') {
										$this->displayFolderList($folderId, $folderPath, $options['page'], $options['sortBy'], $options['sortDir'], $options['view'], array($file), false, $response);
										unset($WPRO_SESS->data['imageEditor'][$editorID]);
										$WPRO_SESS->doSave=true;
									}
									$response->addScriptCall('dialog.hideLoadMessage', '');
									$response->addScriptCall('hideMessageBox', '');
									return $response;
								}
							}
							if ($task == 'next' || $task == 'saveThenNext' || $task == 'previous' || $task == 'saveThenPrevious') {
								//find next file
								// delete temp file
								if (isset($WPRO_SESS->data['imageEditor'][$editorID]['temp'])) {
									unset($WPRO_SESS->data['imageEditor'][$editorID]['temp']);
								}
								if (is_file($directory.$temp)) {
									$fs->delete($directory.$temp);
								}
								if ($task == 'next' || $task == 'saveThenNext') {
									$files = $fs->getFilesInDir($directory, 'name', 'asc', array('.jpg','.jpeg','.gif','.png'), $dir->filters);
								} else {
									$files = $fs->getFilesInDir($directory, 'name', 'desc', array('.jpg','.jpeg','.gif','.png'), $dir->filters);
								}
								
								$newFile = '';
								$found = false;
								
								if (!count($files)) {
									$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSNoFiles'));
									$response->addScriptCall("dialog.hideLoadMessage", '');
									$response->addScriptCall('dialog.close', '');
									return $response;
								}
								
								foreach ($files as $f) {
									$filename = $f['name'];
									if ($found) {
										$newFile = $filename;
										break;
									}
									if ($filename == $file) {
										$found=true;
									}
								}
								if (!$found || $newFile == '') {
									$newFile = $files[0]['name'];
								}
								$WPRO_SESS->data['imageEditor'][$editorID]['file'] = $newFile;
								list ($width, $height) = @getimagesize($directory.$newFile);
								$response->addScriptCall('editFinished', $newFile, '', $width, $height, false);
								//$WPRO_SESS->save();
								$WPRO_SESS->doSave=true;
								$response->addScriptCall("dialog.hideLoadMessage", '');
								return $response;
								
							}						
							
						}					
					}
				}
			}
			
			if (count($failed)) {

				$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSEditActionError'));
				//$response->addScriptCall("dialog.close", '');
				$response->addScriptCall('dialog.hideLoadMessage', '');
				$response->addScriptCall('hideMessageBox', '');
			} else {
			
				// trigger edit event
				$EDITOR->triggerEvent('onEditImage', array('directory'=>$directory,'directoryURL'=>$URL,'directoryObject'=>$dir,'file'=>isset($file)?$file:'', 'tmpFile'=>isset($tmp)?$tmp:'', 'task'=>$task, 'options'=>$options));
			}
		} else {
			$response->addScriptCall("FB.onFolderNotFound", "");
		}
		$response->addScriptCall("dialog.hideLoadMessage", '');
		return $response;
	
?>