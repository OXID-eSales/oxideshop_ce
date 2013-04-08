<?php
if (!defined('IN_WPRO')) exit;

		global $EDITOR, $DIALOG, $WPRO_SESS;
		//if (!$response) {
			$response = $DIALOG->createAjaxResponse();
		//}
		
		// initial var check...
		if (!isset($folderId, $folderPath, $files, $nonce) 
		|| (!is_string($folderId)&&!is_int($folderId))||!is_string($folderPath)
		||!is_array($files)) {
			$response->addAlert('Datatype error.');
			$response->addScriptCall("FB.onFolderNotFound", "");
			$response->addScriptCall("dialog.hideLoadMessage", '');
			$response->addScriptCall("hideMessageBox", '');
			return $response;
		}
		
		// validate nonce token
		if (!$WPRO_SESS->checkNonce($nonce)) {
			$response->addAlert('Invalid transaction.');
			$response->addScriptCall("FB.onFolderNotFound", "");
			$response->addScriptCall("dialog.hideLoadMessage", '');
			$response->addScriptCall("hideMessageBox", '');
			return $response;
		}
		
		$fs = new wproFilesystem();
		$failed = array();
		$succeeded = array();
		
		$failed2 = array();
		$failed3 = array();
		
		$html = '';
		
		$folderMsg = false;
		$fileMsg = false;
		
		if ($arr = $this->getFolder($folderId, $folderPath, $response)) {
			$directory = $arr['directory'];
			$URL = $arr['URL'];
			$dir = $arr['dir'];
			
			if (!$dir->renameFiles&&!$dir->renameFolders) {
				$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSRenamePermissionsError'));
				$response->addScriptCall("dialog.hideLoadMessage", '');
				$response->addScriptCall("hideMessageBox", '');
				return $response;
			}
			
			foreach ($files as $old => $new) {
				$new = trim($new);
				if ($fs->fileNameOk($old)) {
					$isFile = is_file($directory.$old);
					$exists = file_exists($directory.$old);
					
					if (!$exists)  {
						$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSRenameFileNotFound').'\n\n'.$old);
						array_push($failed, $old);
						continue;
					}
					
					if ($isFile && !$dir->renameFiles) {
						if (!$fileMsg) {
							$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSRenameFilesPermissionsError'));
							$fileMsg = true;
						}
						array_push($failed, $old);
						continue;
					} else if (!$isFile && !$dir->renameFolders) {
						if (!$folderMsg) {
							$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSRenameFoldersPermissionsError'));
							$folderMsg = true;
						}
						array_push($failed, $old);
						continue;
					}
					
					$extension = strrchr($old, '.');
					if ($old != $new.$extension) {
					
						// check new name is OK.
						if (!$fs->fileNameOk($new)) {
							array_push($failed2, $old);
							$failed3[$old]['reason'] = 'illegal';
							$failed3[$old]['extension'] = $extension;
							$failed3[$old]['name'] = substr($old, 0, strlen($old)-strlen($extension));
							$failed3[$old]['new'] = $new.$extension;
							continue;
						}
						
						// check extensions
						switch ($dir->type) {
							case 'image' :
								$extensions = $EDITOR->allowedImageExtensions;
							break;
							case 'document' :
								$extensions = $EDITOR->allowedDocExtensions;
							break;
							case 'media' :
								$extensions = $EDITOR->allowedMediaExtensions;
							break;
						}
						if ($isFile&&!$fs->extensionOK($extension, $extensions)) {
							array_push($failed2, $old);
							$failed3[$old]['reason'] = 'illegal';
							$failed3[$old]['extension'] = $extension;
							$failed3[$old]['name'] = substr($old, 0, strlen($old)-strlen($extension));
							$failed3[$old]['new'] = $new.$extension;
							continue;
						}
						
						// check filters
						if ($fs->filterMatch($new.$extension, $dir->filters)) {
							array_push($failed2, $old);
							$failed3[$old]['reason'] = 'reserved';
							$failed3[$old]['extension'] = $extension;
							$failed3[$old]['name'] = substr($old, 0, strlen($old)-strlen($extension));
							$failed3[$old]['new'] = $new.$extension;
							continue;
						}
						
						// check a file with this name doesn't already exist
						if (file_exists($directory.$new.$extension)) {
							array_push($failed2, $old);
							$failed3[$old]['reason'] = 'duplicate';
							$failed3[$old]['extension'] = $extension;
							$failed3[$old]['name'] = substr($old, 0, strlen($old)-strlen($extension));
							$failed3[$old]['new'] = $new.$extension;
							continue;
						}
						
						if ($fs->rename($directory.$old, $directory.$new.$extension)) {
							// rename thumbnails
							if ($fs->fileNameOk($EDITOR->thumbnailFolderName)) {
								if (is_file($directory.$EDITOR->thumbnailFolderName.'/'.$old)) {
									$fs->rename($directory.$EDITOR->thumbnailFolderName.'/'.$old, $directory.$EDITOR->thumbnailFolderName.'/'.$new.$extension);
								} else if (is_file($directory.$EDITOR->thumbnailFolderName.'/'.$old.'.png')) {
									$fs->rename($directory.$EDITOR->thumbnailFolderName.'/'.$old.'.png', $directory.$EDITOR->thumbnailFolderName.'/'.$new.$extension.'.png');
								}
							}
							array_push($succeeded, $new.$extension);
						} else {
							array_push($failed, $old);
						}
					}
				} else {
					array_push($failed, $old);
				}
			}
			
			
			if (count($failed)) {
				$msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'JSRenameActionError');
				$response->addAlert($msg);
			}
			
			// trigger editor event
			$EDITOR->triggerEvent('onFileRename', array('directory'=>$directory,'directoryURL'=>$URL,'directoryObject'=>$dir,'files'=>$succeeded));
			
			$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, (empty($failed2) ? $succeeded : $failed2), $history, $response);
			
			if (!empty($failed3)) {
				foreach ($failed3 as $file => $data) {
					
					if ($data['reason'] == 'duplicate') {
						$html .= '<div class="smallWarning"><image src="'.$EDITOR->themeFolderURL.$EDITOR->theme.'/wysiwygpro/misc/warning16.gif" width="16" height="16" alt="" /> '.$fs->varReplace($DIALOG->langEngine->get('wproCore_fileBrowser', 'nameTaken'), array('oldname'=>htmlspecialchars($file),'newname'=>htmlspecialchars($data['new']))).'</div>';
					} else if ($data['reason'] == 'illegal') {
						$html .= '<div class="smallWarning"><image src="'.$EDITOR->themeFolderURL.$EDITOR->theme.'/wysiwygpro/misc/warning16.gif" width="16" height="16" alt="" /> '.$fs->varReplace($DIALOG->langEngine->get('wproCore_fileBrowser', 'illegalCharacters'), array('oldname'=>htmlspecialchars($file),'newname'=>htmlspecialchars($data['new']))).'</div>';
					} else if ($data['reason'] == 'reserved') {
						$html .= '<div class="smallWarning"><image src="'.$EDITOR->themeFolderURL.$EDITOR->theme.'/wysiwygpro/misc/warning16.gif" width="16" height="16" alt="" /> '.$fs->varReplace($DIALOG->langEngine->get('wproCore_fileBrowser', 'nameReserved'), array('oldname'=>htmlspecialchars($file),'newname'=>htmlspecialchars($data['new']))).'</div>';
					}
					
					$html .= '<div>'.$fs->varReplace($DIALOG->langEngine->get('wproCore_fileBrowser', 'enterNewName'), array('oldname'=>htmlspecialchars($file),'newname'=>htmlspecialchars($data['new']))).'<br /><br /><input size="40" type="text" name="renameFiles['.htmlspecialchars($data['name'].$data['extension']).']" value="'.htmlspecialchars($data['name']).'" />'.$data['extension'].'</div><hr />';
					
				}
				
				$response->addAssign("renameScroll", "innerHTML", $html);
			} else {
				$response->addScriptCall("hideMessageBox", '');
			}
			
			$response->addScriptCall("dialog.hideLoadMessage", '');
			
		} else {
			$response->addScriptCall("FB.onFolderNotFound", "");
		}
		
		return $response;


?>