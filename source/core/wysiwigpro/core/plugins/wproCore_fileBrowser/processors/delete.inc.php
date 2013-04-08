<?php
if (!defined('IN_WPRO')) exit;

		global $EDITOR, $DIALOG, $WPRO_SESS;
		//if (!$response) {
			$response = $DIALOG->createAjaxResponse();
		//}
		
		// initial var check...
		if (!isset($folderId, $folderPath, $files, $nonce) 
		|| (!is_string($folderId)&&!is_int($folderId))||!is_string($folderPath)||!is_array($files)) {
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
		
		$folderMsg = false;
		$fileMsg = false;
		
		if ($arr = $this->getFolder($folderId, $folderPath, $response)) {
			$directory = $arr['directory'];
			$URL = $arr['URL'];
			$dir = $arr['dir'];
			
			if (!$dir->deleteFiles&&!$dir->deleteFolders) {
				$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, array(), $history, $response);
				$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSDeletePermissionsError'));
				$response->addScriptCall("dialog.hideLoadMessage", '');
				$response->addScriptCall("hideMessageBox", '');
				return $response;
			}
			
			foreach ($files as $file) {
				if ($fs->fileNameOk($file)) {
					
					// if it doesn't exist, who cares? we were going to delete it anyway!
					if (!file_exists($directory.$file)) {
						continue;
					}
					
					$isFile = is_file($directory.$file);
					
					if ($isFile && !$dir->deleteFiles) {
						if (!$fileMsg) {
							$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSDeleteFilesPermissionsError'));
							$fileMsg = true;
						}
						array_push($failed, $file);
						continue;
					} else if (!$isFile && !$dir->deleteFolders) {
						if (!$folderMsg) {
							$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSDeleteFoldersPermissionsError'));
							$folderMsg = true;
						}
						array_push($failed, $file);
						continue;
					}
					
					// check filters
					if ($fs->filterMatch($file, $dir->filters)) {
						array_push($failed, $file);
						continue;
					}
					// check file extension
					$extension = strrchr($file, '.');
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
						array_push($failed, $file);
						continue;
					}
					
					if ($fs->delete($directory.$file)) {
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
				} else {
					array_push($failed, $file);
				}
			}
			if (count($failed)) {
				$msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'JSDeleteActionError');
				
				$response->addAlert($msg);
			}
			
			// trigger event
			$EDITOR->triggerEvent('onFileDelete', array('directory'=>$directory,'directoryURL'=>$URL,'directoryObject'=>$dir,'files'=>$succeeded));
			
			$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, array(), $history, $response);
			
		} else {
			$response->addScriptCall("FB.onFolderNotFound", "");
		}
		$response->addScriptCall("dialog.hideLoadMessage", '');
		return $response;


?>