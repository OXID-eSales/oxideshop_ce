<?php
if (!defined('IN_WPRO')) exit;
/* 
functions for loading and processing language files 
*/
//require_once(dirname(__FILE__).'/wproFilesystem.class.php');
require_once(dirname(__FILE__).'/wproCore.class.php');
class wproLangLoader extends wproCore {
	
	var $filename = 'lang.inc.php';
	
	// folder where language folders are stored.
	var $langFolderDir = '';
	
	// URL of folder where language folders are stored.
	var $langFolderURL = '';
	
	// default language sub folder
	var $defaultLang = 'en-us';
	
	// preferred language sub folder
	var $preferredLang = 'en-us';
	
	// actual language sub folder chosen by script if preferred language is not available.
	var $actualLang = 'en-us';
	
	// language code loaded from the language file
	var $langCode = 'en-us';
	
	// charset of language vars
	var $langCharset = 'iso-8859-1';	
	
	// list of available language folders?
	var $availableLanguageFiles = array();
	var $languageFolders = array();
	
	var $preferredVars = array();
	var $defaultVars = array();
	
	// vars will be converted to this charset (from the language file charset)
	var $convertCharset = '';
	
	function wproLangLoader () {
		$this->filename = 'wysiwygpro/lang.inc.php';
	}
	
	function _addTrailingSlash($var) {
		return $this->addTrailingSlash($var);
	}
	
	/* attempts to load the preferred lang 
	if preferred lang is not available it loads the default.
	sets actual lang to the language actually loaded.	
	*/
	function load (&$EDITOR) {
				
		$this->langFolderDir = $this->_addTrailingSlash($this->langFolderDir);
		$this->langFolderURL = $this->_addTrailingSlash($this->langFolderURL);
		
		$this->preferredLang = preg_replace('/[^a-z0-9\-_]/si', '', $this->preferredLang);
		$this->defaultLang = preg_replace('/[^a-z0-9\-_]/si', '', $this->defaultLang);
		
		$this->_loadDefault ();
		if (is_file($this->langFolderDir.$this->preferredLang.'/'.$this->filename)) {
			$this->actualLang = $this->preferredLang;
			//$fs->includeFile($this->preferredLang, $this->langFolderDir, '/'.$this->filename);
			include($this->langFolderDir.$this->preferredLang.'/'.$this->filename);
		} else if (is_file($this->langFolderDir.$this->defaultLang.'/'.$this->filename)) {
			$this->actualLang = $this->defaultLang;
			//$fs->includeFile($this->defaultLang, $this->langFolderDir, '/'.$this->filename);
			include($this->langFolderDir.$this->defaultLang.'/'.$this->filename);
		} else {
			return false;
		}
		if (isset($lang)) {
			if (is_array($lang)) {
				$this->preferredVars = $lang;
				$this->langCode = htmlspecialchars($lang['conf']['code']);
				$this->langCharset = htmlspecialchars($lang['conf']['charset']);
			}
		}
		return true;
	}
	
	function _loadDefault () {
		//$fs = new wproFilesystem();
		//echo $this->langFolderDir.$this->defaultLang.'/'.$this->filename;
		if (is_file($this->langFolderDir.$this->defaultLang.'/'.$this->filename)) {
			//$fs->includeFile($this->defaultLang, $this->langFolderDir, '/'.$this->filename);
			include($this->langFolderDir.$this->defaultLang.'/'.$this->filename);
		}
		if (!isset($lang)) {
			return false;
		}
		if (is_array($lang)) {
			$this->defaultVars = $lang;
		}
		return true;
	}
	
	/* 
	adds an aditional folder to look in for language files. 
	*/
	function loadFolder($dir, $default='en-us') {

		$dir = $this->_addTrailingSlash($dir);
		$this->preferredLang = preg_replace('/[^a-z0-9\-_]/si', '', $this->preferredLang);
		$this->defaultLang = preg_replace('/[^a-z0-9\-_]/si', '', $this->defaultLang);
		
		if (is_file($dir.$this->preferredLang.'.php')) {
			include($dir.$this->preferredLang.'.php');
		} else if (is_file($dir.$this->defaultLang.'.php')) {
			include($dir.$this->defaultLang.'.php');
		} else if (is_file($dir.$default.'.php')) {
			include($dir.$default.'.php');
		} else {
			return false;
		}
		if (isset($lang)) {
			if (is_array($lang)) {
				if (isset($lang['conf']['charset'])) {
					if (strtolower($lang['conf']['charset']) != strtolower($this->preferredVars['conf']['charset'])) {
						foreach ($lang as $k => $v) {
							if (is_array($lang[$k])) {
								foreach ($lang[$k] as $k2 => $v2) {
									$lang[$k][$k2] = $this->confCharset($lang['conf']['charset'], $this->preferredVars['conf']['charset'], $lang[$k][$k2]);
								}
								@reset($lang[$k]);
							} else {
								$lang[$k] = $this->confCharset($lang['conf']['charset'], $this->preferredVars['conf']['charset'], $lang[$k]);
							}
						}
						@reset($lang);
					}
				}			
				$this->preferredVars = array_merge($this->preferredVars, $lang);
			}
		}
		return true;
	}
	
	function loadFile($filename) {
		
		$filename = preg_replace('/[^a-z0-9\-_.\/]/si', '', $filename);
		$this->preferredLang = preg_replace('/[^a-z0-9\-_]/si', '', $this->preferredLang);
		$this->defaultLang = preg_replace('/[^a-z0-9\-_]/si', '', $this->defaultLang);
		
		if (is_file($this->langFolderDir.$this->preferredLang.'/'.$filename)) {
			$from = 'preferred';
			include($this->langFolderDir.$this->preferredLang.'/'.$filename);
		} else if (is_file($this->langFolderDir.$this->defaultLang.'/'.$filename)) {
			$from = 'default';
			include($this->langFolderDir.$this->defaultLang.'/'.$filename);
		} else {
			return false;
		}
		if (!isset($lang)) {
			return false;
		}
		if (is_array($lang)) {
			switch ($from) {
				case 'preferred' :
					$this->preferredVars = array_merge($this->preferredVars, $lang);	
					break;
				case 'default' :
					$this->defaultVars = array_merge($this->defaultVars, $lang);
					break;
			}
		}
		return true;
	}
	
	function confCharset($srcCharset, $destCharset, $str) {
		if (@function_exists('iconv')) {
			if ($ret = iconv($srcCharset, $destCharset, $str)) {
				return $ret;
			}
		} else if (@function_exists('libiconv')) {
			if ($ret = libiconv($srcCharset, $destCharset, $str)) {
				return $ret;
			}
		} elseif (@function_exists('recode_string')) {
			if ($ret = recode_string($srcCharset . '..'  . $destCharset, $str)) {
				return $ret;
			}
		}
		return $str;
	}
	
	function date ($t) {
		if (isset($this->preferredVars['conf']['dateFormat']) && !empty($t)) {
			return @date($this->preferredVars['conf']['dateFormat'], $t);
		} else {
			return $t;
		}
	}
	
	function getVar($group, $var) {
		return $this->get($group, $var);
	}
	
	function get($group, $var) {
		// checks for a var, returns from the default file if not available in the preferred file.
		$ret = $var;
		$from = '';
		if (isset($this->preferredVars[$group] )) {
			if (isset($this->preferredVars[$group][$var] )) {
				$ret = $this->preferredVars[$group][$var];
				$from = 'preferred';
			} else if (isset($this->defaultVars[$group] )) {
				if (isset($this->defaultVars[$group][$var] )) {
					$ret = $this->defaultVars[$group][$var];
					$from = 'default';
				} else {
					$ret = $var;
					$from = 'unknown';
				}
			}
		} else if (isset($this->defaultVars[$group] )) {
			if (isset($this->defaultVars[$group][$var] )) {
				$ret = $this->defaultVars[$group][$var];
				$from = 'default';
			} else {
				$ret = $var;
				$from = 'unknown';
			}
		} else {
			$ret = $var;
			$from = 'unknown';
		}
		//if (is_string($ret)) {
		//	$ret = str_replace(array('"', '>', '<'), array('&quot;', '&gt;', '&lt;'), $ret);
		//}
		if (!empty($this->convertCharset)) {
			switch($from) {
				case 'default':
					if (strtolower($this->defaultVars['conf']['charset']) != strtolower($this->convertCharset)) {
						$ret = $this->confCharset($this->defaultVars['conf']['charset'], $this->convertCharset, $ret);
					}
					break;
				case 'preferred':
					if (strtolower($this->preferredVars['conf']['charset']) != strtolower($this->convertCharset)) {
						$ret = $this->confCharset($this->preferredVars['conf']['charset'], $this->convertCharset, $ret);
					}
					break;
			}
		} else if ($from == 'default' && $this->defaultVars['conf']['charset'] != $this->preferredVars['conf']['charset']) {
			$ret = $this->confCharset($this->defaultVars['conf']['charset'], $this->preferredVars['conf']['charset'], $ret);
		}
		return $ret;
	}
	
}

?>