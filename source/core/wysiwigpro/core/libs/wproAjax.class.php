<?php
if (!defined('IN_WPRO')) exit;
if (!defined('WPRO_DIR')) exit;
class wproAjax {

	var $xajax = null;
	var $languages = array();
	var $themes = array();
	var $editorURL = '';
	var $route = '';
	var $usingWpAjax = true;
	var $registeredFunctions = array();
	var $registeredSaveFunctions = array();
	var $editor = null;
	
	var $bannedProperties = array('emoticonDir','emoticonURL','imageDir','imageURL','documentDir','documentURL','mediaDir','mediaURL','deleteFiles','deleteFolders','renameFiles','renameFolders','upload','overwrite','moveFiles','moveFolders','copyFiles','copyFolders','createFolders','editImages','folderCHMOD','fileCHMOD','thumbnailFolderName','allowedImageExtensions','allowedDocExtensions','allowedMediaExtensions','maxDocSize','maxMediaSize','maxImageSize','maxImageWidth','maxImageHeight','directories','diskQuota','dirFilters');
	var $allowedProperties = array('name', 'value');
	
	var $bannedMethods = array('createDirectory','addDirectory');
	var $allowedMethods = array();
	
	var $bannedPlugins = array();
	var $allowedPlugins = array();
	
	var $highSecurityMode = false;
	
	function wproAjax () {
		$this->bannedProperties = $this->lowerArray($this->bannedProperties);
		$this->allowedProperties = $this->lowerArray($this->allowedProperties);
		$this->bannedMethods = $this->lowerArray($this->bannedMethods);
		$this->allowedMethods = $this->lowerArray($this->allowedMethods);
		$this->bannedPlugins = $this->lowerArray($this->bannedPlugins);
		$this->allowedPlugins = $this->lowerArray($this->allowedPlugins);
	}
	
	function lowerArray ($arr) {
		foreach ($arr as $k => $v) {
			$arr[$k] = strtolower($v);
		}
		return $arr;
	}
	
	function banProperty ($n) {
		if (is_array($n)) {
			$this->bannedProperties = array_merge($this->bannedProperties, $this->lowerArray($n));
		} else {
			array_push($this->bannedProperties, strtolower($n));
		}
	}
	function allowProperty ($n) {
		$this->highSecurityMode = true;
		if (is_array($n)) {
			$this->allowedProperties = array_merge($this->allowedProperties, $this->lowerArray($n));
		} else {
			array_push($this->allowedProperties, strtolower($n));
		}
	}
	function banMethod ($n) {
		if (is_array($n)) {
			$this->bannedMethods = array_merge($this->bannedMethods,  $this->lowerArray($n));
		} else {
			array_push($this->bannedMethods, strtolower($n));
		}
	}
	function allowMethod ($n) {
		$this->highSecurityMode = true;
		if (is_array($n)) {
			$this->allowedMethods = array_merge($this->allowedMethods,  $this->lowerArray($n));
		} else {
			array_push($this->allowedMethods, strtolower($n));
		}
	}
	function banPlugin ($n) {
		if (is_array($n)) {
			$this->bannedPlugins = array_merge($this->bannedPlugins,  $this->lowerArray($n));
		} else {
			array_push($this->bannedPlugins, strtolower($n));
		}
	}
	function allowPlugin ($n) {
		$this->highSecurityMode = true;
		if (is_array($n)) {
			$this->allowedPlugins = array_merge($this->allowedPlugins,  $this->lowerArray($n));
		} else {
			array_push($this->allowedPlugins, strtolower($n));
		}
	}
	
	function setXajaxObject (&$xajax) {
		$this->xajax = & $xajax;
		$this->usingWpAjax = false;
	}
	
	function setBaseEditor(&$editor) {
		$this->editor = $editor;
	}
	
	function loadXajax() {
		if ($this->xajax == null) {
			require_once(WPRO_DIR.'config.inc.php');
			if (WPRO_PATH_XAJAX == WPRO_DIR.'core/libs/xajax/') {
				require_once(WPRO_PATH_XAJAX."xajax.inc.php");
			} else {
				if (!wpro_class_exists('xajax')) require_once(WPRO_PATH_XAJAX."xajax.inc.php");
			}
			if (wpro_class_exists('wpro_xajax')) {
				$this->xajax = new wpro_xajax();
			} else {
				$this->xajax = new xajax();
			}
			$url = $this->xajax->_detectURI();
			if (preg_match('/^http(|s):\/\/.*?\//si', $url)) {
				$url = preg_replace('/^http(|s):\/\/[^\/]+/si', '', $url);
			}
			$this->xajax->sRequestURI = preg_replace("/action=[a-z]+/si", "action=ajax", $url);
		} 
	}
	
	function processRequests() {
		$this->loadXajax();
		if (!defined('WPRO_AJAX_METHOD')) {
			define('WPRO_AJAX_METHOD', 'POST');
		}
		$xajax_method = XAJAX_POST;
		if (strtolower(WPRO_AJAX_METHOD)=='get') {
			$xajax_method = XAJAX_GET;	
		}
		$this->xajax->registerFunction(array('displayWysiwygPro', &$this, 'displayWysiwygPro'), $xajax_method);
		if ($this->usingWpAjax) {
			@$this->xajax->processRequests();
		}
	}
	
	function addLanguage($lang) {
		if (is_array($lang)) {
			$this->languages = array_merge($this->languages, $lang);
		} else {
			array_push($this->languages, $lang);
		}
	}
	function addTheme($theme) {
		if (is_array($theme)) {
			$this->themes = array_merge($this->themes, $theme);
		} else {
			array_push($this->themes, $theme);
		}
	}
	
	function registerConstructorFunction($func) {
		if (is_array($func)) {
			$this->registeredFunctions[$func[0]] = array($func[1], $func[2]);
		} else {
			$this->registeredFunctions[$func] = $func;
		}
	}
	
	function fetchHeadContent () {
		$this->loadXajax();
		$return = '';
		
		$e = new wysiwygPro();
		if (!empty($this->editorURL)) {
			$e->editorURL = $this->editorURL;
		}
		if (!empty($this->route)) {
			$e->route = $this->route;
		}
		$e->_doBaseConfig();
		$this->editorURL = $e->editorURL;
		$this->route = $e->route;
		$return .= $e->fetchHeadContent();
		unset($e);
		
		//$wproHeadDisplayed = false;
		if (!empty($this->languages)) {
			foreach ($this->languages as $v) {
				$e = new wysiwygPro();
				$e->lang = $v;
				$e->iframeDialogs = true;
				$e->editorURL = $this->editorURL;
				$e->route = $this->route;
				$return .= $e->fetchStyleSheets();
				unset($e);
				//$wproHeadDisplayed = true;
			}
		}
		if (!empty($this->themes)) {
			foreach ($this->themes as $v) {
				$e = new wysiwygPro();
				$e->theme = $v;
				$e->iframeDialogs = true;
				$e->editorURL = $this->editorURL;
				$e->route = $this->route;
				$return .= $e->fetchStyleSheets();
				unset($e);
			}
		}
		/*
		if (!$wproHeadDisplayed) {
		
			$e = new wysiwygPro();
			$e->iframeDialogs = true;
			$e->editorURL = $this->editorURL;
			$e->route = $this->route;
			$return .= $e->fetchStyleSheets();
			//exit(htmlspecialchars($e->fetchHeadContent()));
			unset($e);
		}
		*/
				
		if ($this->usingWpAjax) {
			$return .= $this->xajax->getJavascript($this->editorURL."core/js/", "xajax/xajax.js");
			// adds support for non-activex powered ajax for IE < 7
			$return .= '<!--[if lt IE 7]><script type="text/javascript" src="'.$this->editorURL.'core/js/xajax/activex_off.js"></script><![endif]-->';
		}
		$return .= '<script type="text/javascript">function displayWysiwygPro(one,two,three,four,five){'.$this->xajax->sWrapperPrefix.'displayWysiwygPro(one,two,three,four,five);}</script>';
		$return .= '<script src="'.$this->editorURL.'js/ajaxDisplay.js" type="text/javascript"></script>';
		
		return $return;
	}
	
	function displayHeadContent () {
		echo $this->fetchHeadContent();
	}
	
	function displayWysiwygPro($divName, $properties = array(), $methods=array(), $extra = '', $extraParam = '') {
		if ($this->editor == null) {
			$editor = new wysiwygPro();
		} else {
			$editor = $this->editor;
		}
		if (!empty($this->editorURL)) {
			$editor->editorURL = $this->editorURL;
		}
		if (!empty($this->route)) {
			$editor->route = $this->route;
		}
		if (!is_array($properties)) {
			$properties = array();
		}
		if (!is_array($methods)) {
			$methods = array();
		}
		
		$this->bannedProperties = $this->lowerArray($this->bannedProperties);
		$this->allowedProperties = $this->lowerArray($this->allowedProperties);
		
		$this->bannedMethods = $this->lowerArray($this->bannedMethods);
		$this->allowedMethods = $this->lowerArray($this->allowedMethods);
		
		foreach ($properties as $p => $v) {
			if (in_array(strtolower($p), $this->bannedProperties)) {
				continue;
			}
			if ($this->highSecurityMode) {
				if (!in_array(strtolower($p), $this->allowedProperties)) {
					continue;
				}
			}
			$editor->$p = $v;
		}
		foreach ($methods as $p => $v) {
			if (in_array(strtolower($p), $this->bannedMethods)) {
				continue;
			}
			if ($this->highSecurityMode) {
				if (!in_array(strtolower($p), $this->allowedMethods)) {
					continue;
				}
			}
			if (method_exists($editor, $p)) {
				if (is_array($v)) {
					if (strtolower($p) == 'loadplugin' && in_array(strtolower($v[0]), $this->bannedPlugins)) {
						continue;
					}
					if ($this->highSecurityMode) {
						if (strtolower($p) == 'loadplugin' && !in_array(strtolower($v[0]), $this->allowedPlugins)) {
							continue;
						}
					}
					call_user_func_array (array(&$editor, $p), $v );
				} else {
					if (strtolower($p) == 'loadplugin' && in_array(strtolower($v), $this->bannedPlugins)) {
						continue;
					}
					if ($this->highSecurityMode) {
						if (strtolower($p) == 'loadplugin' && !in_array(strtolower($v), $this->allowedPlugins)) {
							continue;
						}
					}
					call_user_func ( array(&$editor, $p), $v );
				}
			}
		}
		
		if (!empty($extra)) {
			if (isset($this->registeredFunctions[$extra])) {
				if (is_array($this->registeredFunctions[$extra])) {
					call_user_func_array (array(&$this->registeredFunctions[$extra][1], $this->registeredFunctions[$extra][2]), array(&$editor,$extraParam) );
				} else {
					call_user_func_array ($extra, array(&$editor,$extraParam) );
				}
			}
			
			
		}
		
		$editor->subsequent = true;
		$editor->compileJSIncludes = true;
				
		$code = $editor->fetch();
		
		if (WPRO_PATH_XAJAX == WPRO_DIR.'core/libs/xajax/') {
			require_once(WPRO_PATH_XAJAX."xajaxResponse.inc.php");
		} else {
			if (!wpro_class_exists('xajaxResponse')) require_once(WPRO_PATH_XAJAX."xajaxResponse.inc.php");
		}
		if (wpro_class_exists('wpro_xajaxResponse')) {
			$response = new wpro_xajaxResponse($editor->langEngine->get('conf','charset'));
		} else {
			$response = new xajaxResponse($editor->langEngine->get('conf','charset'));
		}
				
		$response->addScriptCall("wproAjaxDisplay", htmlspecialchars($code), $divName, true);
		return $response;
	}
}
?>
