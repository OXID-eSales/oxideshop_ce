<?php
if (!defined('IN_WPRO')) exit;

		global $EDITOR, $DIALOG, $WPRO_SESS;
		//if (!$response) {
			$response = $DIALOG->createAjaxResponse();
		//}
		
		// initial var check...
		if (!isset($moveCopyID) 
		|| !is_string($moveCopyID)) {
			$response->addAlert('Datatype error.');
			$response->addScriptCall("FB.onFolderNotFound", "");
			$response->addScriptCall("dialog.hideLoadMessage", '');
			$response->addScriptCall("hideMessageBox", '');
			return $response;
		}
		
		$fs = new wproFilesystem();
		$failed = array();
		$succeeded = array();
		$moved = array();
		
		if (isset($WPRO_SESS->data['move-copy'])) {
			
			if (isset($WPRO_SESS->data['move-copy'][$moveCopyID])) {
				
				$moved = isset($WPRO_SESS->data['move-copy'][$moveCopyID]['succeeded']) ? $WPRO_SESS->data['move-copy'][$moveCopyID]['succeeded'] : array();
				$srcFolderID = isset($WPRO_SESS->data['move-copy'][$moveCopyID]['srcFolderID']) ? $WPRO_SESS->data['move-copy'][$moveCopyID]['srcFolderID'] : '';
				$srcFolderPath = isset($WPRO_SESS->data['move-copy'][$moveCopyID]['srcFolderPath']) ? $WPRO_SESS->data['move-copy'][$moveCopyID]['srcFolderPath'] : '';
				$destFolderID = isset($WPRO_SESS->data['move-copy'][$moveCopyID]['destFolderID']) ? $WPRO_SESS->data['move-copy'][$moveCopyID]['destFolderID'] : '';
				$destFolderPath = isset($WPRO_SESS->data['move-copy'][$moveCopyID]['destFolderPath']) ? $WPRO_SESS->data['move-copy'][$moveCopyID]['destFolderPath'] : '';
				
				$folderID = $srcFolderID;
				$folderPath = $srcFolderPath;
				
				$action = $WPRO_SESS->data['move-copy'][$moveCopyID]['action'];				
				
				$x = null;
				if ($srcArr = $this->getFolder($srcFolderID, $srcFolderPath, $x)) {
						
					$srcDirectory = $srcArr['directory'];
					$srcURL = $srcArr['URL'];
					$srcDir = $srcArr['dir'];
										
					// does this directory actually exist?
					if (!file_exists($srcDirectory) || is_file($srcDirectory)) {
						$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSFolderNotExistError'));
						$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, array(), $history, $response);
						$response->addScriptCall("dialog.hideLoadMessage", '');
						unset($WPRO_SESS->data['move-copy'][$moveCopyID]);
						$WPRO_SESS->doSave = true;
						return $response;
					}
										
					// validate destination folder
					if ($destArr = $this->getFolder($destFolderID, $destFolderPath, $x)) {
							
						$destDirectory = $destArr['directory'];
						$destURL = $destArr['URL'];
						$destDir = $destArr['dir'];
											
						// does this directory actually exist?
						if (!file_exists($destDirectory) || is_file($destDirectory)) {
							$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSFolderNotExistError'));
							$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, array(), $history, $response);
							$response->addScriptCall("dialog.hideLoadMessage", '');
							unset($WPRO_SESS->data['move-copy'][$moveCopyID]);
							$WPRO_SESS->doSave = true;
							return $response;
						}
						
						if (isset($WPRO_SESS->data['move-copy'][$moveCopyID]['overwrite'])) {
							if (count($WPRO_SESS->data['move-copy'][$moveCopyID]['overwrite']) && count($overwrite)) {
								if (!$destDir->overwrite) {
									$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSOverwritePermissionsError'));
									$this->displayFolderList($folderID, $folderPath, $page, $sortBy, $sortDir, $view, array(), $history, $response);
									$response->addScriptCall("dialog.hideLoadMessage", '');
									unset($WPRO_SESS->data['move-copy'][$moveCopyID]);
									$WPRO_SESS->doSave = true;
									return $response;
								} else { 
									foreach ($overwrite as $file) {
										if (in_array($file, $WPRO_SESS->data['move-copy'][$moveCopyID]['overwrite'])) {
											
											if (!$fs->fileNameOK($file)) {
												array_push($failed, $file);
												continue;
											}
											
											$isFile = is_file($srcDirectory.$file);
											$exists = file_exists($srcDirectory.$file);
											
											if (!$exists) {
												array_push($failed, $file);
												continue;
											}
											
											// check user can move the files and folders in the src dir
											if ($isFile && !$srcDir->moveFiles && $action == 'move') {
												array_push($failed, $file);
												continue;
											} else if ($isFile && !$srcDir->copyFiles && $action == 'copy') {
												array_push($failed, $file);
												continue;
											} else if (!$isFile && !$srcDir->moveFolders && $action == 'move') {
												array_push($failed, $file);
												continue;
											} else if (!$isFile && !$srcDir->copyFolders && $action == 'copy') {
												array_push($failed, $file);
												continue;
											}
											
											// check user can move the files and folders in the dest dir
											if ($isFile && !$destDir->moveFiles && $action == 'move') {
												array_push($failed, $file);
												continue;
											} else if ($isFile && !$destDir->copyFiles && $action == 'copy') {
												array_push($failed, $file);
												continue;
											} else if (!$isFile && !$destDir->moveFolders && $action == 'move') {
												array_push($failed, $file);
												continue;
											} else if (!$isFile && !$destDir->copyFolders && $action == 'copy') {
												array_push($failed, $file);
												continue;
											}
											
											// delete thumbnails in destination
											if ($fs->fileNameOk($EDITOR->thumbnailFolderName)) {
												if (is_file($destDirectory.$EDITOR->thumbnailFolderName.'/'.$file)) {
													$fs->delete($destDirectory.$EDITOR->thumbnailFolderName.'/'.$file);
												} else if (is_file($destDirectory.$EDITOR->thumbnailFolderName.'/'.$file.'.png')) {
													$fs->delete($destDirectory.$EDITOR->thumbnailFolderName.'/'.$file.'.png');
												}
											}
											// move files
											if ($action == 'move') {
												
												if ($fs->delete($destDirectory.$file) && $fs->rename($srcDirectory.$file, $destDirectory.$file)) {
													// move thumbnail to destination
													if ($fs->fileNameOk($EDITOR->thumbnailFolderName)) {
														if (is_file($srcDirectory.$EDITOR->thumbnailFolderName.'/'.$file)) {
															$fs->rename($srcDirectory.$EDITOR->thumbnailFolderName.'/'.$file, $destDirectory.$EDITOR->thumbnailFolderName.'/'.$file);
														} else if (is_file($srcDirectory.$EDITOR->thumbnailFolderName.'/'.$file.'.png')) {
															$fs->rename($srcDirectory.$EDITOR->thumbnailFolderName.'/'.$file.'.png', $destDirectory.$EDITOR->thumbnailFolderName.'/'.$file.'.png');
														}
													}
													array_push($succeeded, $file);
												} else {
													array_push($failed, $file);
												}
											// copy files
											} else if ($action == 'copy') {
												if ($fs->delete($destDirectory.$file) && $fs->copy($srcDirectory.$file, $destDirectory.$file)) {
													// copy thumbnail to destination
													if ($fs->fileNameOk($EDITOR->thumbnailFolderName)) {
														if (is_file($srcDirectory.$EDITOR->thumbnailFolderName.'/'.$file)) {
															// create folder
															if (!file_exists($destDirectory.$EDITOR->thumbnailFolderName)) {
																$fs->makeDir($destDirectory.$EDITOR->thumbnailFolderName, $EDITOR->folderCHMOD);
															}
															$fs->copy($srcDirectory.$EDITOR->thumbnailFolderName.'/'.$file, $destDirectory.$EDITOR->thumbnailFolderName.'/'.$file);
														} else if (is_file($srcDirectory.$EDITOR->thumbnailFolderName.'/'.$file.'.png')) {
															// create folder
															if (!file_exists($destDirectory.$EDITOR->thumbnailFolderName)) {
																$fs->makeDir($destDirectory.$EDITOR->thumbnailFolderName, $EDITOR->folderCHMOD);
															}
															$fs->copy($srcDirectory.$EDITOR->thumbnailFolderName.'/'.$file.'.png', $destDirectory.$EDITOR->thumbnailFolderName.'/'.$file.'.png');
														}
													}
													array_push($succeeded, $file);
												} else {
													array_push($failed, $file);
												}
												
											}
										}
									}								
								}
							}
						}
					} else {
						$response->addScriptCall("FB.onFolderNotFound", "");
					}
				} else {
					$response->addScriptCall("FB.onFolderNotFound", "");
				}
				$goToDest = $WPRO_SESS->data['move-copy'][$moveCopyID]['goToDest'];
				unset($WPRO_SESS->data['move-copy'][$moveCopyID]);
				$WPRO_SESS->doSave=true;
			}
		
			if (count($failed)) {
				$msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'JSOverwriteError');
				$response->addAlert($msg);
			}
			$highlight = array_merge($moved, $succeeded);
			
			// trigger editor event
			if ($action=='move') {
				$EDITOR->triggerEvent('onFileMove', array('srcDirectory'=>$srcDirectory,'srcDirectoryURL'=>$srcURL,'srcDirectoryObject'=>$srcDir,'destDirectory'=>$destDirectory,'destDirectoryURL'=>$destURL,'destDirectoryObject'=>$destDir,'files'=>$succeeded));
			} else {
				$EDITOR->triggerEvent('onFileCopy', array('srcDirectory'=>$srcDirectory,'srcDirectoryURL'=>$srcURL,'srcDirectoryObject'=>$srcDir,'destDirectory'=>$destDirectory,'destDirectoryURL'=>$destURL,'destDirectoryObject'=>$destDir,'files'=>$succeeded));
			}
			
			if ($goToDest) {
				$this->displayFolderList($destFolderID, $destFolderPath, $page, $sortBy, $sortDir, $view, $highlight, $history, $response);
			} else {
				$this->displayFolderList($srcFolderID, $srcFolderPath, $page, $sortBy, $sortDir, $view, $highlight, $history, $response);
			}
		}
		$response->addScriptCall("dialog.hideLoadMessage", '');
		return $response;


?>