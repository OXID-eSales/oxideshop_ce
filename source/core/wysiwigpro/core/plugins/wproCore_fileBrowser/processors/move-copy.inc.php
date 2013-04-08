<?php
if (!defined('IN_WPRO')) exit;

				$DIALOG->classIsolator = 'wproCore_fileBrowser_moveCopy';
				
				if ($action == 'copy') {
					$DIALOG->title = 'Copy Files';
				} else {
					$DIALOG->title = 'Move Files';
				}
				
				$DIALOG->reloadInFrame();
				
				$action = strtolower($action);
				
				$DIALOG->headContent->add('<script type="text/javascript" src="core/js/base64.js"></script>');
				$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_fileBrowser/js/move-copy_src.js"></script>');
				$DIALOG->headContent->add('<link type="text/css" rel="stylesheet" href="core/plugins/wproCore_fileBrowser/css/move-copy.css" />');
				
				$DIALOG->assign('action', $action);
				
				// check vars // source folder
				if (!isset($params['srcFolderID']) || !isset($params['srcFolderPath'])) {
					require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
					$msg = new wproMessageExit();
					$msg->msgCode = WP_CRITICAL;
					$msg->msg = 'Sorry not enough parameters.';
					$msg->alert();
				}
				if (!isset($params['destFolderID'])) {
					$params['destFolderID'] = $params['srcFolderID'];
				}
				if (!isset($params['destFolderPath'])) {
					$params['destFolderPath'] = '';
				}
				
				$params['srcFolderPath'] = base64_decode($params['srcFolderPath']);
				$params['destFolderPath'] = base64_decode($params['destFolderPath']);
				
				$overwrite = isset($params['overwrite']) ? $params['overwrite'] : false;
				
				$requiredPermissions = preg_replace("/[^a-z,]/si", '', isset($params['requiredPermissions']) ? $params['requiredPermissions'] : '');
				$DIALOG->assign('requiredPermissions', $requiredPermissions);
				
				// validate source folder
				$x = null;
				if ($srcArr = $this->getFolder($params['srcFolderID'], $params['srcFolderPath'], $x)) {
				
					$srcDirectory = $srcArr['directory'];
					$srcURL = $srcArr['URL'];
					$srcDir = $srcArr['dir'];
					
					$srcIsFile = is_file($srcDirectory);
					$srcExists = file_exists($srcDirectory);
					
					// can user move
					if ((!$srcDir->moveFolders && !$srcDir->moveFiles) && $action == 'move' ) {
						require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
						$msg = new wproMessageExit();
						$msg->msgCode = WP_WARNING;
						$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'moveSourcePermissionsError');
						$msg->alert();
							
					}
					// can user copy
					if ((!$srcDir->copyFolders && !$srcDir->copyFiles) && $action == 'copy' ) {
						require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
						$msg = new wproMessageExit();
						$msg->msgCode = WP_WARNING;
						$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'copySourcePermissionsError');
						$msg->alert();
					}
					
					// does this directory actually exist?
					if (!file_exists($srcDirectory) || is_file($srcDirectory)) {
						require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
						$msg = new wproMessageExit();
						$msg->msgCode = WP_WARNING;
						$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'folderNotExistError');
						$msg->alert();
					}
					
					// validate destination folder
					if ($destArr = $this->getFolder($params['destFolderID'], $params['destFolderPath'], $x)) {
					
						$destDirectory = $destArr['directory'];
						$destURL = $destArr['URL'];
						$destDir = $destArr['dir'];
						
						$destIsFile = is_file($destDirectory);
						$destExists = file_exists($destDirectory);
						
						// can user move
						if ((!$destDir->moveFolders && !$destDir->moveFiles) && $action == 'move' ) {
							require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
							$msg = new wproMessageExit();
							$msg->msgCode = WP_WARNING;
							$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'moveDestinationPermissionsError');;
							$msg->alert();
								
						}
						// can user copy
						if ((!$destDir->copyFolders && !$destDir->copyFiles) && $action == 'copy' ) {
							require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
							$msg = new wproMessageExit();
							$msg->msgCode = WP_WARNING;
							$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'copyDestinationPermissionsError');;
							$msg->alert();
						}
						
						// does this directory actually exist?
						if (!file_exists($destDirectory) || is_file($destDirectory)) {
							require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
							$msg = new wproMessageExit();
							$msg->msgCode = WP_WARNING;
							$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'folderNotExistError');
							$msg->alert();
						}
						
						$DIALOG->assign('destDirectory', $destDirectory);
						
					} else {
						require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
						$msg = new wproMessageExit();
						$msg->msgCode = WP_CRITICAL;
						$msg->msg = 'BAD DIRECTORY ID';
						$msg->alert();
					}
					
					$DIALOG->assign('srcFolderType', $srcDir->type);
					$DIALOG->assign('destFolderType', $destDir->type);
					
					$DIALOG->assign('srcFolderID', $params['srcFolderID']);
					$DIALOG->assign('srcFolderPath', $params['srcFolderPath']);
					
					$DIALOG->assign('destFolderID', $params['destFolderID']);
					$DIALOG->assign('destFolderPath', $params['destFolderPath']);
					
					$DIALOG->assign('goToDest', isset($params['goToDest']) ? $params['goToDest'] : false);
					$DIALOG->assign('overwrite', $overwrite);
					
					// move/copy the files if need be and then display confirmation if need be.
					if (isset($params['ok']) && !empty($params['files'])) {
						$moveCopyID = isset($params['moveCopyID']) ? $params['moveCopyID'] : '';
						// check for a valid move/copy id.
						if (isset($WPRO_SESS->data['move-copy'])) {
							if (isset($WPRO_SESS->data['move-copy'][$moveCopyID])) {
								
								$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/move-copyFinished.tpl.php';
								
								$fs = new wproFilesystem();
								
								$duplicate = array();
								$succeeded = array();
								$failed = array();
								
								// create array of files and folders
								if (!is_array($params['files'])) {
									$files = explode('/', $params['files']);
								} else {
									$files = $params['files'];
								}
								
								if ($action == 'move' && $srcDirectory == $destDirectory) {
									$succeeded = $files;
								} else {
									
									/* now actually move/copy the files */
									foreach ($files as $file) {
										if ($fs->fileNameOk($file)) {
											//$attempt = true;
											
											$isFile = is_file($srcDirectory.$file);
											$exists = file_exists($srcDirectory.$file);
											
											if (!$exists) {
												$failed[$file] = 'notExist';
												continue;
											}
											
											// check user can move the files and folders in the src dir
											if ($isFile && !$srcDir->moveFiles && $action == 'move') {
												$failed[$file] = 'srcCannotMoveFiles';
												continue;
											} else if ($isFile && !$srcDir->copyFiles && $action == 'copy') {
												$failed[$file] = 'srcCannotCopyFiles';
												continue;
											} else if (!$isFile && !$srcDir->moveFolders && $action == 'move') {
												$failed[$file] = 'srcCannotMoveFolders';
												continue;
											} else if (!$isFile && !$srcDir->copyFolders && $action == 'copy') {
												$failed[$file] = 'srcCannotCopyFolders';
												continue;
											}
											
											// check user can move the files and folders in the dest dir
											if ($isFile && !$destDir->moveFiles && $action == 'move') {
												$failed[$file] = 'destCannotMoveFiles';
												continue;
											} else if ($isFile && !$destDir->copyFiles && $action == 'copy') {
												$failed[$file] = 'destCannotCopyFiles';
												continue;
											} else if (!$isFile && !$destDir->moveFolders && $action == 'move') {
												$failed[$file] = 'destCannotMoveFolders';
												continue;
											} else if (!$isFile && !$destDir->copyFolders && $action == 'copy') {
												$failed[$file] = 'destCannotCopyFolders';
												continue;
											}
											
											// check file extension
											$extension = strrchr($file, '.');
											switch ($destDir->type) {
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
												$failed[$file] = 'unknown';	
												continue;									
											}
											
											// check filters
											// filter check 
											if (
											$fs->filterMatch($file, $srcDir->filters)
											||
											$fs->filterMatch($file, $destDir->filters)
											) {
												$failed[$file] = 'reserved';
												continue;
											}
											
											// if file is a folder check that the destination directory is not inside the folder to be moved...
											if (!$isFile) {
												if (substr($destDirectory.$file, 0, strlen($srcDirectory.$file.'/')) == $srcDirectory.$file.'/') {
													$failed[$file] = 'destInsideSrc';
													continue;
												}
											}
											
											/* check existing files */
											if (file_exists($destDirectory.$file) && !($action == 'copy' && ($srcDirectory.$file == $destDirectory.$file))) {
												if (!$destDir->overwrite) {
													$failed[$file] = 'duplicate';
													continue;
												} else {
													if (!$overwrite) {
														array_push($duplicate, $file);
														continue;
													} else {
														// delete thumbnail if file is to be overwritten
														if ($fs->fileNameOk($EDITOR->thumbnailFolderName)) {
															if (is_file($desDirectory.$EDITOR->thumbnailFolderName.'/'.$file)) {
																$fs->delete($destDirectory.$EDITOR->thumbnailFolderName.'/'.$file);
															} else if (is_file($destDirectory.$EDITOR->thumbnailFolderName.'/'.$file.'.png')) {
																$fs->delete($destDirectory.$EDITOR->thumbnailFolderName.'/'.$file.'.png');
															}
														}
													}
												}
											}
											// move files
											if ($action == 'move') {
												if ($fs->rename($srcDirectory.$file, $destDirectory.$file)) {
													// move thumbnail to destination
													if ($fs->fileNameOk($EDITOR->thumbnailFolderName)) {
														if (is_file($srcDirectory.$EDITOR->thumbnailFolderName.'/'.$file)) {
															
															// create folder
															if (!file_exists($destDirectory.$EDITOR->thumbnailFolderName)) {
																$fs->makeDir($destDirectory.$EDITOR->thumbnailFolderName, $EDITOR->folderCHMOD);
															}
															$fs->rename($srcDirectory.$EDITOR->thumbnailFolderName.'/'.$file, $destDirectory.$EDITOR->thumbnailFolderName.'/'.$file);
														} else if (is_file($srcDirectory.$EDITOR->thumbnailFolderName.'/'.$file.'.png')) {
															// create folder
															if (!file_exists($destDirectory.$EDITOR->thumbnailFolderName)) {
																$fs->makeDir($destDirectory.$EDITOR->thumbnailFolderName, $EDITOR->folderCHMOD);
															}
															$fs->rename($srcDirectory.$EDITOR->thumbnailFolderName.'/'.$file.'.png', $destDirectory.$EDITOR->thumbnailFolderName.'/'.$file.'.png');
														}
													}
													array_push($succeeded, $file);
												} else {
													$failed[$file] = 'unknown';
												}
											// copy files
											} else if ($action == 'copy') {								
												if ($srcDirectory.$file == $destDirectory.$file) {
													$destFile = $fs->resolveDuplicate($file, $srcDirectory);
													if ($fs->copy($srcDirectory.$file, $destDirectory.$destFile)) {
														array_push($succeeded, $destFile);
													} else {
														$failed[$file] = 'unknown';
													}										
												} else {
													if ($fs->copy($srcDirectory.$file, $destDirectory.$file)) {
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
														$failed[$file] = 'unknown';
													}
												}
											}
										} else {
											$failed[$file] = 'badName';
										}
									}
								}
								
								// trigger editor event
								if ($action=='move') {
									$EDITOR->triggerEvent('onFileMove', array('srcDirectory'=>$srcDirectory,'srcDirectoryURL'=>$srcURL,'srcDirectoryObject'=>$srcDir,'destDirectory'=>$destDirectory,'destDirectoryURL'=>$destURL,'destDirectoryObject'=>$destDir,'files'=>$succeeded));
								} else {
									$EDITOR->triggerEvent('onFileCopy', array('srcDirectory'=>$srcDirectory,'srcDirectoryURL'=>$srcURL,'srcDirectoryObject'=>$srcDir,'destDirectory'=>$destDirectory,'destDirectoryURL'=>$destURL,'destDirectoryObject'=>$destDir,'files'=>$succeeded));
								}
								
								// store success and failure in session for use by clean up routine.
								
								//$moveCopyID = md5(uniqid(rand(), true));
								//if (!isset($WPRO_SESS->data['move-copy'])) {
								//	$WPRO_SESS->data['move-copy'] = array();
								//}
								//$WPRO_SESS->data['move-copy'][$moveCopyID] = array();
								$WPRO_SESS->data['move-copy'][$moveCopyID]['action'] = $action;
								$WPRO_SESS->data['move-copy'][$moveCopyID]['succeeded'] = $succeeded;
								$WPRO_SESS->data['move-copy'][$moveCopyID]['overwrite'] = $duplicate;
														
								$WPRO_SESS->data['move-copy'][$moveCopyID]['srcFolderID'] = $params['srcFolderID'];
								$WPRO_SESS->data['move-copy'][$moveCopyID]['srcFolderPath'] = $params['srcFolderPath'];
								$WPRO_SESS->data['move-copy'][$moveCopyID]['destFolderID'] = $params['destFolderID'];
								$WPRO_SESS->data['move-copy'][$moveCopyID]['destFolderPath'] = $params['destFolderPath'];
								
								$WPRO_SESS->data['move-copy'][$moveCopyID]['goToDest'] = isset($params['goToDest']) ? $params['goToDest'] : false;
								
								$WPRO_SESS->doSave = true;
								
								$DIALOG->bulkAssign(array(
									'moveCopyID' => $moveCopyID,
									'succeeded' => $succeeded,
									'overwrite' => $duplicate,
									'failed' => $failed
								));
								
								$DIALOG->options = array(
									array(
										'type'=>'submit',
										'name'=>'ok',
										'value'=>$DIALOG->langEngine->get('core', 'ok'),
									),
									array(
										'onclick' => 'unloadDialog(true)',
										'type'=>'button',
										'name'=>'close',
										'value'=>$DIALOG->langEngine->get('core', 'cancel'),
									),
								);
										
										//if (count($failed)) {
											//$msg = 'One or more files or folders cannot be moved, they may be in use by another application, or the server may not have permission to modify them.';
											//$response->addAlert($msg);
										//}
							}	
						}
						// invalid id, exit without any message??
					} else {
						$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/move-copy.tpl.php';
						// build list of files in the viewed directory and display
						$fs = new wproFilesystem();
						$folders = $this->recursiveGetFolders($destDir->dir, $destDir->filters, $fs);
						$DIALOG->assign('folders', $folders);
						
						$moveCopyID = md5(uniqid(rand(), true));
						if (!isset($WPRO_SESS->data['move-copy'])) {
							$WPRO_SESS->data['move-copy'] = array();
						}
						$WPRO_SESS->data['move-copy'][$moveCopyID] = array();
						$WPRO_SESS->doSave = true;
						
						$DIALOG->assign('moveCopyID', $moveCopyID);
						
						$DIALOG->options = array(
							array(
								'type'=>'submit',
								'name'=>'ok',
								'value'=>$DIALOG->langEngine->get('core', 'ok'),
							),
							array(
								'onclick' => 'dialog.close()',
								'type'=>'button',
								'name'=>'close',
								'value'=>$DIALOG->langEngine->get('core', 'cancel'),
							),
						);
					}
				
				} else {
					require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
					$msg = new wproMessageExit();
					$msg->msgCode = WP_CRITICAL;
					$msg->msg = 'BAD DIRECTORY ID';
					$msg->alert();
				}


?>