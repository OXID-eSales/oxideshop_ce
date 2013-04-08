<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_fileBrowser {
	
	var $embedPlugins = array();
	
	function init(&$DIALOG) {
		global $EDITOR;
		$DIALOG->headContent->add('<script type="text/javascript" src="core/js/wproForms_src.js"></script>');
	}
	
	function addDialogJS () {
		global $EDITOR, $DIALOG;
		if (WPRO_COMPILE_JS_INCLUDES) {
			$DIALOG->headContent->add('<script type="text/javascript" src="'.htmlspecialchars($EDITOR->editorLink('core/plugins/wproCore_fileBrowser/compileJS.php?v='.$EDITOR->version)).'"></script>');
		} else {
			$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_fileBrowser/js/dialog_src.js"></script>');
		}
	}
	
	function instanceSupport ($params) {
		// --------------------
		// support for legacey trusted directories array.
		// simply map it to the new API
		
		global $EDITOR;
		global $trusted_directories;
		
		if (!empty($trusted_directories)) {
		
			// document dir
			$instance_doc_dir = '';
			if (isset ($params['instance_doc_dir'])) {
				$instance_doc_dir = $params['instance_doc_dir'];
			} else if (!empty($EDITOR->instanceDocDir)) {
				$instance_doc_dir = $EDITOR->instanceDocDir;
			}
			if (!empty($instance_doc_dir)) {
				if (isset ($trusted_directories[$instance_doc_dir])) {
					if (isset($trusted_directories[$instance_doc_dir][0]) && isset($trusted_directories[$instance_doc_dir][1])) {
						$EDITOR->documentDir = $trusted_directories[$instance_doc_dir][0];
						$EDITOR->documentURL = $trusted_directories[$instance_doc_dir][1];	
					}
				}
			}
	
			// image dir
			$instance_img_dir = '';
			if (isset ($params['instance_img_dir'])) {
				$instance_img_dir = $params['instance_img_dir'];
			} else if (!empty($EDITOR->instanceImgDir)) {
				$instance_img_dir = $EDITOR->instanceImgDir;
			}
			if (!empty($instance_img_dir)) {
				if (isset ($trusted_directories[$instance_img_dir])) {
					if (isset($trusted_directories[$instance_img_dir][0]) && isset($trusted_directories[$instance_img_dir][1])) {
						$EDITOR->imageDir = $trusted_directories[$instance_img_dir][0];
						$EDITOR->imageURL = $trusted_directories[$instance_img_dir][1];
					}
				}
			}
			
			// media dir
			$instance_media_dir = '';
			if (isset ($params['instance_media_dir'])) {
				$instance_media_dir = $params['instance_media_dir'];
			} else if (!empty($EDITOR->instanceMediaDir)) {
				$instance_media_dir = $EDITOR->instanceMediaDir;
			}
			if (!empty($instance_media_dir)) {
				if (isset ($trusted_directories[$instance_media_dir])) {
					if (isset($trusted_directories[$instance_media_dir][0]) && isset($trusted_directories[$instance_media_dir][1])) {
						$EDITOR->mediaDir = $trusted_directories[$instance_media_dir][0];
						$EDITOR->mediaURL = $trusted_directories[$instance_media_dir][1];
					}
				}
			}
			
		}
		// end legacy support
		// ------------------
	
	}
	
	// return false if no thumbnail support
	function buildThumbnails ($directory, $url, &$files, $urlBase) {
		return include(dirname(__FILE__).'/processors/buildThumbnails.inc.php');
	}
	
	// returns the URL to use for all actions.
	function getURLBase ($folderId, $path='') {
		global $EDITOR, $DIALOG, $WPRO_SESS;
		
		$wpsid = $WPRO_SESS->sessionId;
		$wpsname = $WPRO_SESS->sessionName;
		
		$str = 'dialog.php?dialog=wproCore_fileBrowser&folderID='.$folderId.'&folderPath='.urlencode(base64_encode($path)).($EDITOR->appendToQueryStrings ? '&'.$EDITOR->appendToQueryStrings : '').'&'.$wpsname.'='.$wpsid.'&'.session_name().'='.session_id();
		
		return $str;
	
	}
	
	// returns directory path and URL for requested folder.
	function getFolder($id='', $folderPath = '', &$response, $noError=false) {
		global $EDITOR, $DIALOG;
		
		// initial var check...
		if ((!is_string($id)&&!is_int($id))||!is_string($folderPath)) {
			if ($response) {
				$response->addAlert('Datatype error.');
				return false;
			} else {
				require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
				$msg = new wproMessageExit();
				$msg->msgCode = WPRO_CRITICAL;
				$msg->msg = 'Datatype error.';
				$msg->alert();
			}
		}
		
		$fs = new wproFilesystem();
		$this->instanceSupport($this->params);
		
		
		if ($id != '') {
		
			if ($id=='image') {
				$dir = $EDITOR->getDirectories('image');
				$dir = $dir[0];
			} else if ($id=='media') {
				$dir = $EDITOR->getDirectories('media');
				$dir = $dir[0];
			} else if ($id=='document'||$id=='link') {
				$dir = $EDITOR->getDirectories('document');
				$dir = $dir[0];
			} else {
				$dir = $EDITOR->getDirById(intval($id));
			}
			
			if ($dir) {
				if (!empty($dir->dir)) {
					$changed = false;
					// check validity of folderPath
					if ($folderPath!=''&&!$fs->dirNameOK($folderPath)) {
						if (!$noError) {
							if ($response) {
								$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser','badFolderPath'));
								return false;
							} else {
								require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
								$msg = new wproMessageExit();
								$msg->msgCode = WPRO_CRITICAL;
								$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser','JSBadFolderPath');
								$msg->alert();
							}
						} else {
							$folderpath = '';
							$changed = true;
						}
					}
					if ($folderPath == '/') {
						$folderpath = '';
					}
					if ($folderPath != '') {
						// add trailing /
						if (substr ($folderPath, strlen ($folderPath) - 1) != '/') {
							$folderPath.='/';
						}
						// remove beginning slash
						if (substr ($folderPath, 0, 1) == '/') {
							$folderPath = substr ($folderPath, 1);
						}
						
					}
					// check that folderpath does not match a filter
					if (!empty($folderPath)) {
						$fs = new wproFilesystem();
						if ($fs->filterMatch(basename($folderPath), $dir->filters)) {
							$folderpath = '';
						}
					}
					
					// check validity of dir object
					$oDirectory = $dir->dir;
					if ($oDirectory != '') {
						// add trailing /
						if (substr ($oDirectory, strlen ($oDirectory) - 1) != '/') {
							$oDirectory.='/';
						}
					}
					$oURL = $dir->URL;
					if ($oURL != '') {
						// add trailing /
						if (substr ($oURL , strlen ($oURL ) - 1) != '/') {
							$oURL .='/';
						}
					}
					
					if ($noError&&$folderPath != '') {
						if (!file_exists($oDirectory.$folderPath)) {
							$folderpath = '';
							$changed = true;
						}
					}
					
					// we now have the actual folder and the URL for the folder
					$directory = $oDirectory.$folderPath;
					$URL = $oURL.$folderPath;
					
					// if the folder does not exist, or is not a folder we must exit
					if (!file_exists($directory) || is_file($directory)) {
						if ($response) {
							$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser','fileNotExistError'));
							return false;
						} else {
							require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
							$msg = new wproMessageExit();
							$msg->msgCode = WPRO_CRITICAL;
							$msg->msg = $DIALOG->langEngine->get('wproCore_fileBrowser','JSFileNotExistError');
							$msg->alert();
						}
					}
					
					return array('dir' => $dir, 'directory'=>$directory, 'URL' => $URL, 'changed'=>$changed);
										
				} else {
					if ($response) {
						$response->addAlert('The specified directory object is not correctly configured');
						return false;
					} else {
						require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
						$msg = new wproMessageExit();
						$msg->msgCode = WPRO_CRITICAL;
						$msg->msg = 'The specified directory object is not correctly configured';
						$msg->alert();
					}
				}					
				
			} else {
				if ($response) {
					$response->addAlert('No directory matches this ID');
					return false;
				} else {
					require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
					$msg = new wproMessageExit();
					$msg->msgCode = WPRO_CRITICAL;
					$msg->msg = 'No directory matches this ID';
					$msg->alert();
				}
			}
			
		} else {
			if ($response) {
				$response->addAlert('No directory ID specified');
				return false;
			} else {
				require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
				$msg = new wproMessageExit();
				$msg->msgCode = WPRO_CRITICAL;
				$msg->msg = 'No directory ID specified';
				$msg->alert();
			}
		}
		
	}
	
	function displayFolderListWrapper($folderId=0, $folderPath='', $page=1, $sortBy='name', $sortDir='asc', $view='default', $highlight=array(), $history=true, $fakeresponse=NULL, $noError=false) {
		global $DIALOG;
		$response = $DIALOG->createAjaxResponse();
		return $this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, $highlight, $history, $response, $noError);
	}
	
	function displayFolderList($folderId=0, $folderPath='', $page=1, $sortBy='name', $sortDir='asc', $view='default', $highlight=array(), $history=true, &$response, $noError=false) {
		global $EDITOR, $DIALOG, $WPRO_SESS;
		
		// initial var check...
		if (!$response) {
			$response = $DIALOG->createAjaxResponse();
		}
		
		if ((!is_string($folderId)&&!is_int($folderId))||!is_string($folderPath)||!is_array($highlight)) {
			$response->addAlert('Datatype error.');
			$response->addScriptCall("FB.onFolderNotFound", "");
			return $response;
		}
		
		$page = intval($page);
		if ($page==0)$page=1;
		$pageLength = 100;
		
		if ($arr = $this->getFolder($folderId, $folderPath, $response, $noError)) {
			$directory = $arr['directory'];
			$URL = $arr['URL'];
			$dir = $arr['dir'];
			
			if ($arr['changed']) {
				$highlight = array();
			}
			
			$tpl = new wproTemplate();
			// if images, should we display thumbnails
			if ($EDITOR->thumbnails && $dir->type=='image') {
				
				if ($view == 'default') {
					$view = $EDITOR->defaultImageView;	
				}
				
				$thumbnails = $view=='thumbnails' ? true : false;
				
				if ($thumbnails) {
					$params['sortBy'] = 'name';
					$params['sortDir'] = 'asc';
				}
			} else {
				$thumbnails = false;
			}
			
			// get sort by and sort direction
			// get and validate sorting
			/*if (isset ($params['sortBy'])) {
				$sortBy = $params['sortBy'];
			} else {
				$sortBy = 'name';
			}*/
			if ($sortBy!='name'&&$sortBy!='type'&&$sortBy!='modified'&&$sortBy!='size') {
				$sortBy = 'name';
			}
			/*if (isset ($params['sortDir'])) {
				$sortDir = $params['sortDir'];
			} else {
				$sortDir = 'asc';
			}*/
			if ($sortDir!='asc'&&$sortDir!='desc') {
				$sortDir = 'asc';
			}
			
			$folderSortBy = 'name';
			if ($sortBy!='name') {
				$folderSortDir = 'asc';
			} else {
				$folderSortDir = $sortDir;
			}
			
			$tpl->assign('sortBy', $sortBy);
			$tpl->assign('sortDir', $sortDir);
			
			//if (isset($params['doHistory'])) {
				//$DIALOG->template->assign('doHistory', false);
			//} else {
				//$DIALOG->template->assign('doHistory', true);
			//}
			
			// build a standard URL base to use with everything.
			$urlBase = $this->getURLBase ($dir->id, $folderPath/*, '', $sortBy, $sortDir*/);
			
			// display the file list
									
			// what types of files to display?
			$fileTypes = array();
			switch ($dir->type) {
				case 'image' :
					$fileTypes = $EDITOR->allowedImageExtensions;
					break;
				case 'media' :
					$fileTypes = $EDITOR->allowedMediaExtensions;
					break;
				case 'document' :
				default:
					$fileTypes = $EDITOR->allowedDocExtensions;
					break;
					
			}
			
			$fs = new wproFilesystem();
			
			if ($dir->type == 'image') {
				$getDimensions = true;
			} else {
				$getDimensions = false;
			}
			
			// get the list of folders and files
			$files = $fs->getFilesInDir($directory, $sortBy, $sortDir, $fileTypes, $dir->filters, $getDimensions);
			// if images then we need to build the thumbnails!
			if ($thumbnails && count($files)) {
				if (!$this->buildThumbnails($directory, $URL, $files, $urlBase)) {
					$thumbnails = false;
				}
			}
			// build folders afterwards so that thumbnail folder is included.
			$folders = $fs->getFoldersInDir($directory, $folderSortBy, $folderSortDir, $dir->filters);
			
			$total = count($files) + count($folders);
			
			$numPages = ceil($total/$pageLength);
			
			// check for selected files and adjust current page if there is only one file selected.
			if (count($highlight)&&$numPages>1) {
				// OK, flip through the pages until we find the file.
				$j=0;
				$found = false;
				$num = count($folders);
				for ($i=0;$i<$num;$i++) {
					if (in_array($folders[$i]['name'], $highlight)) {
						$j = $i; $found = true;
						break;
					}
				}
				if (!$found) {
					$num = count($files);
					for ($i=0;$i<$num;$i++) {
						if (in_array($files[$i]['name'], $highlight)) {
							$j = $i;
							break;
						}
					}
				}
				$page = ceil($j/$pageLength);
				if ($page==0)$page=1;
			}
			
			// sanitize current page
			$page = intval($page);
			while ($page > $numPages) {
				$page --;
			}
			$start = ($page * $pageLength) - $pageLength;
			$end = $start + $pageLength;
			
			if ($end > $total) {
				$end = $total;
			}
			
			$tpl->assign('pageStart', $start); // no at page start
			$tpl->assign('pageEnd', $end); // no at page end
			
			$tpl->assign('pageCurrent', $page); // current page
			$tpl->assign('pageNumPages', $numPages); // number of pages
			$tpl->assign('pageLength', $pageLength); // length of each page
			$tpl->assign('pageTotal', $total); // total no of files
			
			$tpl->assign('dir', $dir);						
			
			$tpl->assign('thumbnails', $thumbnails);

			$tpl->assign('folders', $folders);
			$tpl->assign('files', $files);
			
			$tpl->assign('folderID', $dir->id);
			
			$tpl->assign('folderPath', $folderPath);
			
			$tpl->assign('folderURL', $URL);
			
			$tpl->assign('sortBy', $sortBy);
			$tpl->assign('sortDir', $sortDir);
			
			$tpl->assign('highlight', $highlight);	
			
			$tpl->assign('doHistory', false);
			
			/* generic assigns */
			/*$tpl->assignByRef('EDITOR', $EDITOR);
			$tpl->assignByRef('langEngine', $EDITOR->langEngine);
			$tpl->bulkAssign(array(
				'themeURL' => $EDITOR->themeFolderURL.$EDITOR->theme.'/',
				'editorURL' => $EDITOR->editorURL,
				'langURL' => $EDITOR->langFolderURL.$DIALOG->langEngine->actualLang.'/',
			));*/
			$DIALOG->assignCommonVarsToTemplate($tpl);
			
			/* end */
			
			
			$html = $tpl->fetch(WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/files.tpl.php');
			
			//$response->addAlert($html);
			
			// nonce
			$nonce = md5(uniqid(rand(), true));
			$response->addScriptCall("FB.setNonce", $nonce);
			$WPRO_SESS->addNonce($nonce, time()+3600);
			
			$response->addAssign("folderFrame", "innerHTML", $html);
			$response->addScriptCall("FB.onLoadFolder", $history);
			
		} else {
			$response->addScriptCall("FB.onFolderNotFound", "");
		}
		
		return $response;
	}
	
	function delete($folderId=0, $folderPath='', $files=array(), $page=1, $sortBy='name', $sortDir='asc', $view='thumbnails', $nonce='', $history=false) {
		return include(dirname(__FILE__).'/processors/delete.inc.php');		
	}
	
	function rename ($folderId=0, $folderPath='', $files=array(), $page=1, $sortBy='name', $sortDir='asc', $view='thumbnails', $nonce='', $history=false) {
		return include(dirname(__FILE__).'/processors/rename.inc.php');
	}
	
	function newFolder ($folderId=0, $folderPath='', $name='', $page=1, $sortBy='name', $sortDir='asc', $view='thumbnails', $nonce='', $history=false) {
		return include(dirname(__FILE__).'/processors/newFolder.inc.php');
	}
	
	function uploadFinished($folderId=0, $folderPath='', $uploadID='', $overwrite=array(), $page=1, $sortBy='name', $sortDir='asc', $view='thumbnails', $history=false) {
		return include(dirname(__FILE__).'/processors/uploadFinished.inc.php');
	}	
	
	function moveCopyFinished($moveCopyID='', $overwrite=array(), $page=1, $sortBy='name', $sortDir='asc', $view='thumbnails', $history=false) {
		return include(dirname(__FILE__).'/processors/moveCopyFinished.inc.php');
	}	
	
	function editImage($folderId='', $folderPath='', $editorID='', $task='', $options=null) {
		return include(dirname(__FILE__).'/processors/imageeditor.ajax.inc.php');	
	}
	
	function recursiveGetFolders($directory, $filters, &$fs, $path='') {
		if (substr($directory, strlen($directory)-1) != '/') {
			$directory.='/';
		}
		// get the folders
		$folders = $fs->getFoldersInDir($directory, 'name', 'asc', $filters);
		// loop through to add sub folders
		$n = count($folders);
		for ($i=0;$i<$n;$i++) {
			$folders[$i]['path'] = $path.$folders[$i]['name'].'/';
			$folders[$i]['children'] = $this->recursiveGetFolders($directory.$folders[$i]['name'], $filters, $fs, $folders[$i]['path']);
			
		}
		return $folders;
	}
	
	/* embed plugins */
	// load the embed plugins and sorts them by file extension for easy reference
	function loadEmbedPlugins () {
		global $EDITOR;
		$plugins = explode(',',$EDITOR->allowedMediaPlugins.',images,zz_other');
		asort($plugins);
		foreach ($plugins as $plugin) {
			$this->loadEmbedPlugin(trim($plugin));
		}
	}
	function loadEmbedPlugin($name) {
		static $fs;
		$fs = new wproFilesystem();
		$name = $fs->makeVarOK($name);
		if (!isset($this->embedPlugins[$name])) {
			if (!wpro_class_exists('wproFilePlugin_'.$name)) {
				$dir = WPRO_DIR.'plugins/mediaPlugins/';
				if (!$fs->includeFileOnce($name, $dir, '/plugin.php')) {
					return false;
				} else if (!wpro_class_exists('wproFilePlugin_'.$name)) {
					return false;
				}
			}
			
			@eval ('$this->embedPlugins["'.$name.'"] = new wproFilePlugin_'.$name.'();');
			
			$this->embedPlugins[$name]->name = $name;
			
			if (method_exists($this->embedPlugins[$name],'init')) {
				$this->embedPlugins[$name]->init($this);
			}
		}
		//$ret = & $p;
		return true;
	}
	
	function displayEmbedPluginsJS() {
		global $EDITOR, $DIALOG;
		$this->loadEmbedPlugins ();
		$fs = new wproFilesystem();
		if (WPRO_COMPILE_JS_INCLUDES) {
			$s = '';
			foreach ($this->embedPlugins as $name => $plugin) {
				$s.= ','.$fs->makeVarOK($name).'/'.$fs->makeFileNameOK($plugin->jsFile);
			}
			$DIALOG->headContent->add('<script type="text/javascript" src="'.htmlspecialchars($EDITOR->editorLink('core/plugins/wproCore_fileBrowser/compileFilePluginsJS.php?plugins='.base64_encode($s).'&v='.$EDITOR->version)).'"></script>');
		} else {
			foreach ($this->embedPlugins as $name => $plugin) {
				$DIALOG->headContent->add('<script type="text/javascript" src="plugins/mediaPlugins/'.$fs->makeVarOK($name).'/'.$plugin->jsFile.'"></script>');
			}
		}		
	}
	
	function getMediaDimensions ($file) {
		if (!wpro_class_exists('getID3')) require_once(WPRO_PATH_GETID3.'getid3.php');
		$return = false;
		if (file_exists($file)) {
			$getID3 = @new getID3();
			$file_info = @$getID3->analyze($file);
			$width = ''; $height = '';
			if (isset($file_info['video'])) {
				if (isset($file_info['video']['resolution_x'])&&isset($file_info['video']['resolution_y'])) {
					$width = $file_info['video']['resolution_x'];
					$height = $file_info['video']['resolution_y'];
					
				} else if (isset($file_info['video']['streams'])) {
					if (is_array($file_info['video']['streams'])) {
						foreach ($file_info['video']['streams'] as $k => $v) {
							if (isset($v['resolution_x'])&&isset($v['resolution_y'])) {
								$width = $v['resolution_x'];
								$height = $v['resolution_y'];
								break;								
							}
						}						
					}
				}
			}
			if (!empty($width)&&!empty($height)) {
				$return = array('width' => $width, 'height' => $height);
			}
		}
		return $return;
	}
	
	/* displays details for a selected file and populates local form  */
	function displayFileDetails($folderId=0, $folderPath='', $file='', $fromPlugin=true) {
		global $EDITOR, $DIALOG;
		
		$response = $DIALOG->createAjaxResponse();
		$response->addScriptCall('FB.displayExtraDetails', NULL);
		
		// initial var check...
		if (!isset($folderId, $folderPath, $file) 
		|| (!is_string($folderId)&&!is_int($folderId))||!is_string($folderPath)
		||!is_string($file)) {
			return $response;
		}
		
		$this->loadEmbedPlugins ();
		if ($arr = $this->getFolder($folderId, $folderPath, $response)) {
			$directory = $arr['directory'];
			$URL = $arr['URL'];
			$dir = $arr['dir'];
			$fs = new wproFilesystem();
			if ($fs->fileNameOk($file)&&is_file($directory.$file)) {
				$extension = strrchr($file, '.');
				foreach ($this->embedPlugins as $name => $plugin) {
					if ($plugin->local) {
						if ($fs->extensionOK($extension, $plugin->extensions)) {
							if ($fromPlugin) {
								$response->addScriptCall('FB.populateLocalOptions', $name, $plugin->getDetails($directory.$file, $response));
							}
							$response->addScriptCall('FB.displayExtraDetails', $plugin->displayDetails($directory.$file, $response));
							break;
						}
					}
				}
				if (!$fromPlugin||$fromPlugin=='false') {
					$arr = $fs->getFileInfo($extension);
					$arr['size'] = $fs->fileSize($directory.$file);
					$arr['description'] = $DIALOG->langEngine->get('files', $arr['description']);
					$response->addScriptCall('FB.linksPopulateLocalOptions', $arr);
				}
			}		
		}
		return $response;
	}
	
	/* displays details for a selected folder  */
	function displayFolderDetails($folderId=0, $folderPath='', $folder='') {
		global $EDITOR, $DIALOG;
		
		$response = $DIALOG->createAjaxResponse();
				
		// initial var check...
		if (!isset($folderId, $folderPath, $folder) 
		|| (!is_string($folderId)&&!is_int($folderId))||!is_string($folderPath)
		||!is_string($folder)) {
			return $response;
		}
		
		if ($arr = $this->getFolder($folderId, $folderPath, $response)) {
			$directory = $arr['directory'];
			$URL = $arr['URL'];
			$dir = $arr['directory'];
			$size = 0;
			$fs = new wproFilesystem();
			if ($fs->fileNameOk($folder)) {
				$size = $fs->dirSize($directory.$folder);
			}
			$response->addAssign('displayFolderSize', 'innerHTML', '<strong>'.$EDITOR->langEngine->get('wproCore_fileBrowser', 'size').'</strong> '.($fs->convertByteSize($size)));	
		}
		return $response;
	}
	
	/* end embed plugins */
	
	function runAction ($action, $params) {
		global $EDITOR, $DIALOG, $WPRO_SESS;
		
		$DIALOG->template->assign('wpsid', $WPRO_SESS->sessionId);
		$DIALOG->template->assign('wpsname', $WPRO_SESS->sessionName);
		$DIALOG->template->assign('action', $action);
		
		$DIALOG->options = array();
		
		$this->params = $params;
		
		if (isset($params['properties'])) {
			$DIALOG->assign('properties', true);
		} else {
			$DIALOG->assign('properties', false);
		}
		
		$DIALOG->template->assign('fbOptions', array(
					array(
						'class'=>'button',
						'type'=>'submit',
						'name'=>'ok',
						'value'=>$DIALOG->langEngine->get('core', 'insert'),
					),
					array(
						'class'=>'button',
						'onclick' => 'dialog.close()',
						'type'=>'button',
						'name'=>'close',
						'value'=>$DIALOG->langEngine->get('core', 'cancel'),
					),
				)
			);
		
				$canGD = false;
				$canGif = false;
				if (function_exists('imagecreate')) {
					//if (!(imagetypes() & IMG_PNG)) {
					//} else {
						$canGD = true;
					//}
					if (!function_exists('imagegif') && (!function_exists('imagecreatefromgif') || !function_exists('imagepng') ) ) {
						$canGif = false;
					} else {
						$canGif = true;
					}
				}
				$DIALOG->template->assign('canGD', $canGD);
				$DIALOG->template->assign('canGif', $canGif);
				
				if (!$canGD) {
					$EDIITOR->thumbnails = false;
				}
		
		switch (strtolower($action)) {
			
			case 'image' : case 'media' : case 'document' : case 'link' : case 'imageeditor' : case 'ajax':
				// register all the Ajax functions...
				$DIALOG->registerAjaxFunction(array('displayFolderList', &$this, 'displayFolderListWrapper'));
				$DIALOG->registerAjaxFunction(array('delete', &$this, 'delete'));
				$DIALOG->registerAjaxFunction(array('rename', &$this, 'rename'));
				$DIALOG->registerAjaxFunction(array('newFolder', &$this, 'newFolder'));
				$DIALOG->registerAjaxFunction(array('uploadFinished', &$this, 'uploadFinished'));
				$DIALOG->registerAjaxFunction(array('moveCopyFinished', &$this, 'moveCopyFinished'));
				$DIALOG->registerAjaxFunction(array('editImage', &$this, 'editImage'));
				
				$DIALOG->registerAjaxFunction(array('displayFileDetails', &$this, 'displayFileDetails'));
				$DIALOG->registerAjaxFunction(array('displayFolderDetails', &$this, 'displayFolderDetails'));
				// end
		}
			
			
		switch (strtolower($action)) {
						
			case 'ajax' :
				return true;
				break;
			case 'thumbnail' :
				
				require(dirname(__FILE__).'/processors/thumbnail.inc.php');
						
				break;
			case 'move' : case 'copy' :
			
				require(dirname(__FILE__).'/processors/move-copy.inc.php');
								
				break;
			case 'upload' :	
					
				require(dirname(__FILE__).'/processors/upload.inc.php');
								
				break;
			case 'outlook' :
				
				require(dirname(__FILE__).'/processors/outlook.inc.php');
								
				break;
			case 'preview' :
			
				$DIALOG->classIsolator = 'wproCore_fileBrowser_preview';
				//$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'image'));
				//$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/dialog.tpl.php';
				$DIALOG->headContent->add('<style type="text/css">
				body {
					background-color: #ffffff;
					background-image: none;
					padding: 10px;
					padding-top: 70px;
					text-align: center;
				}
				</style>');
				
				$DIALOG->chromeless = true;
				
				$DIALOG->bodyContent = '<p>'.$DIALOG->langEngine->get('wproCore_fileBrowser', 'preview').'</p>';
				
				
				break;
			case 'nopreview' :
			
				$DIALOG->classIsolator = 'wproCore_fileBrowser_preview';
				//$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'image'));
				//$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/dialog.tpl.php';
				$DIALOG->headContent->add('<style type="text/css">
				body {
					background-color: #ffffff;
					background-image: none;
					padding: 10px;
					padding-top: 60px;
					text-align: center;
				}
				</style>');
				
				$DIALOG->chromeless = true;
				
				$DIALOG->bodyContent = '<p>'.$DIALOG->langEngine->get('wproCore_fileBrowser', 'nopreview').'</p>';
				
				break;
			case 'imageeditor' :
				
				require(dirname(__FILE__).'/processors/imageeditor.inc.php');
				
				break;
			case 'linksbrowser' :
			
				$DIALOG->classIsolator = 'wproCore_fileBrowser_linksBrowser';
				$DIALOG->headContent->add('<style type="text/css">
				body {
					background-color: #ffffff;
					background-image: none;
				}
				</style>');
				$EDITOR->triggerEvent('onBeforeGetLinks');
				$DIALOG->assign('links', $EDITOR->links);
				$DIALOG->chromeless = true;
				$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/linksBrowser.tpl.php';
			
				break;
			case 'image' :
			
				require(dirname(__FILE__).'/processors/image.inc.php');
					
				break;
			case 'media' :
			
				require(dirname(__FILE__).'/processors/media.inc.php');
				
				break;
			case 'document' :
			
				require(dirname(__FILE__).'/processors/document.inc.php');
				
				break;
			case 'link' :
				
				require(dirname(__FILE__).'/processors/link.inc.php');
				
				break;
			case 'basiclink' :
			
				require(dirname(__FILE__).'/processors/basiclink.inc.php');
				
				break;
				
			case 'mediapreview' :
			
				require(dirname(__FILE__).'/processors/mediapreview.inc.php');
				
				break;
			default :
				// check vars
				require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
				$msg = new wproMessageExit();
				$msg->msgCode = WPRO_CRITICAL;
				$msg->msg = 'Sorry not enough parameters.';
				$msg->alert();
		}
	}
}

?>