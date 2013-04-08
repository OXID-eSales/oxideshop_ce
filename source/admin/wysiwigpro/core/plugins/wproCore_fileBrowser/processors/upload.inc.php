<?php
if (!defined('IN_WPRO')) exit;
								
				$DIALOG->title='Upload files from your computer';
				
				$DIALOG->reloadInFrame();
				
				$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_fileBrowser/js/upload_src.js"></script>');
				$DIALOG->headContent->add('<style type="text/css">
.overwriteTable th {
	font-weight: normal;
	text-align: left;
	border-bottom: 1px solid #cccccc;
}
</style>');
				
				// check vars
				if (!isset($params['folderID']) || !isset($params['folderPath'])) {
					require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
					$msg = new wproMessageExit();
					$msg->msgCode = WPRO_CRITICAL;
					$msg->msg = 'Sorry not enough parameters.';
					$msg->alert();
				}
				
				$params['folderPath'] = base64_decode($params['folderPath']);
				
					//function upload ($folderId, $folderPath, $sortBy='name', $sortDir='asc', $history=true) {
				//global $EDITOR, $DIALOG;
				//if (!$response) {
					//$response = $DIALOG->createAjaxResponse();
				//}
				// check directory
				$x = null;
				if ($arr = $this->getFolder($params['folderID'], $params['folderPath'], $x)) {
				
					$directory = $arr['directory'];
					$URL = $arr['URL'];
					$dir = $arr['dir'];
				
					// can user upload
					if (!$dir->upload) {
						require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
						$msg = new wproMessageExit();
						$msg->msgCode = WPRO_WARNING;
						$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'uploadPermissionsError');
						$msg->alert();
							
					}
					
					// does this directory actually exist?
					if (!file_exists($directory) || is_file($directory)) {
						require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
						$msg = new wproMessageExit();
						$msg->msgCode = WPRO_WARNING;
						$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser', 'folderNotExitsError');
						$msg->alert();
					}
					
					$DIALOG->template->assign('directory', $directory);
					$DIALOG->template->assign('URL', $URL);
				
					// if files have been requested for upload we'd better do something about it eh?
					if (isset($_FILES['files'])) {
						
						$uploadID = isset($params['uploadID']) ? $params['uploadID'] : '';
						// check for a valid upload id.
						if (isset($WPRO_SESS->data['uploads'])) {
							if (isset($WPRO_SESS->data['uploads'][$uploadID])) {
						
								//print_r($_FILES);
								//exit;
							
								$fs = new wproFilesystem();
								
								// check disk quota
								$quota = $fs->returnBytes($dir->diskQuota);
								if ($quota > 0) {
									$dirSize = intval($fs->dirSize($dir->dir));
									// loop through and add size of each file...
									if (isset($_FILES['files'])) {
										$num = count($_FILES['files']['tmp_name']);
										for ($i=0; $i<=$num - 1; $i++) {
											if (@isset($_FILES[$field]['size'][$i])) {
												$dirSize += intval($_FILES[$field]['size'][$i]);											
											}
										}
									}
									if ($quota < $dirSize) {
										require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
										$msg = new wproMessageExit();
										$msg->msgCode = WPRO_WARNING;
										$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser','uploadExceedsQuota');
										$msg->alert();
									}
								}								
								
								$maxwidth=500;
								$maxheight=500;
								$chkimgwidth = false;
								switch ($dir->type) {
									case 'image' :
										/* extensions and file sizes */ 
										$extensions = $EDITOR->allowedImageExtensions;
										$sizeLimit = $fs->returnBytes($EDITOR->maxImageSize);
										$chkimgwidth = true;
										$maxwidth = $EDITOR->maxImageWidth;
										$maxheight = $EDITOR->maxImageHeight;
										if (isset($params['maxWidth']) && isset($params['maxHeight'])) {
											// create resize images
											$w = intval($params['maxWidth']);
											$h = intval($params['maxHeight']);
											if ((!empty($w) && !empty($h)) && ($w <= $maxwidth && $h <= $maxheight)) {
												$maxwidth = $w;
												$maxheight = $h;
											}
										}
									break;
									case 'document' :
										$extensions = $EDITOR->allowedDocExtensions;
										$sizeLimit = $fs->returnBytes($EDITOR->maxDocSize);
									break;
									case 'media' :
										$extensions = $EDITOR->allowedMediaExtensions;
										$sizeLimit = $fs->returnBytes($EDITOR->maxMediaSize);
									break;
									
									
								}
								
								$overwrite =false;
								if ($dir->overwrite) {
									if (isset($params['overwrite'])) {
										if ($params['overwrite']) {
											$overwrite = true;
										}
									}
								}
								if ($errors = $fs->uploadFiles('files', $directory, $extensions, $dir->filters, $sizeLimit, $overwrite, $chkimgwidth, $maxwidth, $maxheight, $EDITOR->fileCHMOD)) {
								
									/*$errors = array();
									$errors['fatal'] = array(); // an array of files that failed to upload
									$errors['resized'] = array(); // images resized to maximum allowed size
									$errors['renamed'] = array(); // an array of files which had to be slightly re-named
									$errors['overwrite'] = array(); // an array of files where a file with that name already exists
									$errors['succeeded'] = array(); // array of files successfully uploaded, if files were renamed this has the renamed name not the original.
									*/
									
									//echo '<pre>';
									//print_r($errors);
									
									if (!empty($errors['resized'])) {
										foreach($errors['resized'] as $file => $array) {
											if ($file != basename($array[2])) {
												if (isset($errors['overwrite'][$file])) {
													$errors['overwrite'][$file] = basename($array[2]);
												}
											}							
										}
									}
									
									if (!empty($errors['overwrite']) && !$dir->overwrite) {
										foreach($errors['overwrite'] as $file => $temp) {
											$errors['fatal'][$file]  = 'duplicate';
										}	
										$errors['overwrite'] = array();					
									}
									
									if (!empty($errors['overwrite'])) {
										foreach($errors['overwrite'] as $file => $temp) {
											if (isset($errors['fatal'][$file])) {
												unset($errors['overwrite'][$file]);
												if($errors['fatal'][$file]!='badDimensions'){
													$errors['fatal'][$file]  = 'duplicate';	
												}								
											} else {
												foreach ($errors['succeeded'] as $k => $v) {
													if ($v == $temp) {
														unset($errors['succeeded'][$k]);
														break;
													}
												}						
											}		
										}
									}
									
									
									$WPRO_SESS->data['uploads'][$uploadID]['succeeded'] = $errors['succeeded'];
									$WPRO_SESS->data['uploads'][$uploadID]['overwrite'] = $errors['overwrite'];
									$WPRO_SESS->doSave = true;
									//print_r($errors);
									//exit;
									
									// trigger upload events (we will do this again on the remaining  files in the upload finished function)
									$EDITOR->triggerEvent('onUpload', array('directory'=>$directory,'directoryURL'=>$URL,'directoryObject'=>$dir,'files'=>$errors['succeeded']));
									
									$DIALOG->headContent->add('<style type="text/css">
								#errors {
									height: 350px;
									overflow: auto;
								}					
								</style>');
									$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/uploadFinished.tpl.php';
									
									
									// calculate max size allowed by server
									// the smaller of these two values is our upload limit -  I think? any PHP experts want to comment on this?
									$php_max_upload = $fs->returnBytes(ini_get('upload_max_filesize'));
									$php_max_post = $fs->returnBytes(ini_get('post_max_size'));
									$maxUpload = 0;
									
									if ($php_max_post > $php_max_upload) {
										$maxUpload = $php_max_upload;
									} else {
										$maxUpload = $php_max_post;
									}
									
									if ($sizeLimit > $maxUpload) {
										$sizeLimit = $maxUpload;
									}
									
									$DIALOG->template->assign('maxFileSize', $fs->convertByteSize($sizeLimit));
									$DIALOG->template->assign('maxTotalSize', $fs->convertByteSize($maxUpload));
									
									
									
									$DIALOG->template->bulkAssign(
									array('errors' => $errors,
									'uploadID' => $uploadID,
									'extensions' => $extensions,
									'sizeLimit' => $sizeLimit,
									'maxWidth' => $maxwidth,
									'maxHeight' => $maxheight,
									'dir' => $dir,
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
								
								} else {
									require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
									$msg = new wproMessageExit();
									$msg->msgCode = WPRO_CRITICAL;
									$msg->msg = 'Upload failed due to an unknown error.';
									$msg->alert();
								}
							}
						}	
					
					} else {
						
						header("Connection: close", true);
						
						// no upload sent, show regular dialog
						$fs = new wproFilesystem();
						// check disk quota
						$quota = $fs->returnBytes($dir->diskQuota);
						if ($quota > 0) {
							$dirSize = intval($fs->dirSize($dir->dir));
							if ($quota <= $dirSize) {
								require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
								$msg = new wproMessageExit();
								$msg->msgCode = WPRO_WARNING;
								$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser','quotaExceeded');
								$msg->alert();
							}
						}
						
						$uploadID = md5(uniqid(rand(), true));
						//exit($uploadID);
						if (!isset($WPRO_SESS->data['uploads'])) {
							$WPRO_SESS->data['uploads'] = array();
						}
						$WPRO_SESS->data['uploads'][$uploadID] = array();
						$WPRO_SESS->doSave = true;
						
						$DIALOG->assign('uploadID',$uploadID);
						
						// display the dialog.
						$DIALOG->headContent->add('<style type="text/css">
						#files {
							height: 150px;
							overflow: auto;
						}
						#uploadMessage {
							display: none;
							text-align: center;
						}	
						</style>');
				
						$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/upload.tpl.php';
						$DIALOG->formTags = false;
						//if ($EDITOR->_browserType == 'safari') $DIALOG->formOnSubmit = false;
						//$DIALOG->formEnctype = 'multipart/form-data';
						$DIALOG->template->assign('mode', $dir->type);
						$DIALOG->template->assign('dir', $dir);
						$DIALOG->options = array(
							array(
								'onclick' => 'doUpload()',
								'type'=>'button',
								'name'=>'ok',
								'value'=>$DIALOG->langEngine->get('wproCore_fileBrowser', 'upload'),
							),
							array(
								'onclick' => 'dialog.close()',
								'type'=>'button',
								'name'=>'close',
								'value'=>$DIALOG->langEngine->get('core', 'cancel'),
							),
						);
						
						$fs = new wproFilesystem();
						
						switch ($dir->type) {
							case 'image' :
								/* extensions and file sizes */ 
								$sizeLimit = $fs->returnBytes($EDITOR->maxImageSize);
								$extensions = $EDITOR->allowedImageExtensions;
							break;
							case 'document' :
								$sizeLimit = $fs->returnBytes($EDITOR->maxDocSize);
								$extensions = $EDITOR->allowedDocExtensions;
							break;
							case 'media' :
								$sizeLimit = $fs->returnBytes($EDITOR->maxMediaSize);
								$extensions = $EDITOR->allowedMediaExtensions;
							break;
						}
						
						// calculate max size allowed by server
						// the smaller of these two values is our upload limit -  I think? any PHP experts want to comment on this?
						$php_max_upload = $fs->returnBytes(ini_get('upload_max_filesize'));
						$php_max_post = $fs->returnBytes(ini_get('post_max_size'));
						$maxUpload = 0;
						
						if ($php_max_post > $php_max_upload) {
							$maxUpload = $php_max_upload;
						} else {
							$maxUpload = $php_max_post;
						}
						
						if (isset($dirSize)&&isset($quota)) {
							if ($quota>0) {
								$space = $quota-$dirSize;
								if ($maxUpload>$space) {
									$maxUpload=$space;
								}
							}
						}
						
						if ($sizeLimit > $maxUpload) {
							$sizeLimit = $maxUpload;
						}
						
						$DIALOG->template->assign('maxFileSize', $fs->convertByteSize($sizeLimit));
						$DIALOG->template->assign('maxTotalSize', $fs->convertByteSize($maxUpload));
						$DIALOG->template->assign('extensions', str_replace(strrchr(strtoupper($extensions), ','), ' or '.str_replace(',', '', strrchr(strtoupper($extensions), ',')), strtoupper($extensions)));
						
						$uploadErrorMsg = false;
						
						if (isset($_SERVER['HTTP_REFERER']) && isset($_SERVER['REQUEST_URI'])) {
							if (substr($_SERVER['HTTP_REFERER'], strpos($_SERVER['HTTP_REFERER'], '?')) 
							== substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?'))) {
								$uploadErrorMsg = true;
							}
						}
						
						$DIALOG->template->assign('showUploadError', $uploadErrorMsg);
						
					}	
				} else {
					require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
					$msg = new wproMessageExit();
					$msg->msgCode = WPRO_CRITICAL;
					$msg->msg = 'BAD DIRECTORY ID';
					$msg->alert();
				}


?>