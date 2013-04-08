<?php
if (!defined('IN_WPRO')) exit;

		global $EDITOR, $DIALOG, $WPRO_SESS;
		//if (!$response) {
			$response = $DIALOG->createAjaxResponse();
		//}
				
		// initial var check...
		if (!isset($folderId, $folderPath, $name, $nonce) 
		|| (!is_string($folderId)&&!is_int($folderId))||!is_string($folderPath)
		||!is_string($name)) {
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
		
		$name = trim($name);
				
		if ($arr = $this->getFolder($folderId, $folderPath, $response)) {
			$directory = $arr['directory'];
			$URL = $arr['URL'];
			$dir = $arr['dir'];
			
			if (!$dir->createFolders) {
				$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, array(), $history, $response);
				$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSNewFolderPermissionsError'));
				$response->addScriptCall("dialog.hideLoadMessage", '');
				$response->addScriptCall("hideMessageBox", '');
				return $response;
			}
			
			if (!$fs->fileNameOK($name)) {
				$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, array(), $history, $response);
				$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSFileNameError'));
				$response->addScriptCall("document.dialogForm.newFolderName.focus", '');
				$response->addScriptCall("dialog.hideLoadMessage", '');
				return $response;
			}
			
			// filter check
			if ($fs->filterMatch($name, $dir->filters)) {
				$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, array(), $history, $response);
				$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSReservedNameError'));
				$response->addScriptCall("document.dialogForm.newFolderName.focus", '');
				$response->addScriptCall("dialog.hideLoadMessage", '');
				return $response;
			}
			
			$exists = file_exists($directory.$name);		
			if ($exists)  {
				$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, array(), $history, $response);
				$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSFolderExistsError'));
				$response->addScriptCall("document.dialogForm.newFolderName.focus", '');
				$response->addScriptCall("dialog.hideLoadMessage", '');
				return $response;
			}
			
			if ($fs->makeDir($directory.$name, $EDITOR->folderCHMOD)) {
				$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, array($name), $history, $response);
				$response->addScriptCall("dialog.hideLoadMessage", '');
				$response->addScriptCall("hideMessageBox", '');
				// trigger editor event
				$EDITOR->triggerEvent('onNewFolder', array('directory'=>$directory,'directoryURL'=>$URL,'directoryObject'=>$dir,'name'=>$name));
			} else {
				$this->displayFolderList($folderId, $folderPath, $page, $sortBy, $sortDir, $view, array(), $history, $response);
				$response->addAlert($DIALOG->langEngine->get('wproCore_fileBrowser', 'JSNewFolderActionError'));
				$response->addScriptCall("hideMessageBox", '');
				$response->addScriptCall("dialog.hideLoadMessage", '');
			}
		} else {
			$response->addScriptCall("FB.onFolderNotFound", "");
		}
		
		return $response;


?>