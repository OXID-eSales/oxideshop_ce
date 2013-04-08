<?php

if (!defined('IN_WPRO')&&!defined('WPRO_IN_ROUTE')) exit;
if (!defined('IN_WPROFILESYSTEM')) define('IN_WPROFILESYSTEM', true);
require_once(dirname(__FILE__).'/wproCore.class.php');

class wproFilesystemBase extends wproCore {

	// folders and files containing these strings are considered invalid.
	// these values override the accepted properties below
	var $badFolderChars = array('./','.\\','?','&','%','#','~',':','^','<','>','[',']','(',')','*','+','@','"',"'",'`',',','|',"\r","\n","\t");
	var $badFileChars =     array('/','\\','?','&','%','#','~',':','^','<','>','[',']','(',')','*','+','@','"',"'",'`',',','|',"\r","\n","\t");
	
	// if the below are set then files and folders containing anything other than these characters are considered invalid.
	// the following must either be an empty value or a valid regular expression
	//var $acceptedFolderChars = "#[^A-Za-z0-9_\-./\\ ]#i";
	//var $acceptedFileChars = "#[^A-Za-z0-9_\-. ]#i";
	var $acceptedFolderChars = "";
	var $acceptedFileChars = "";
		
	function wproFilesystemBase () {

	}
	
	// includes a PHP file..
	function includeFileOnce($file, $base='', $extra='') {
		if ($this->fileNameOk($file)) {
			$file = $this->fileName($base.$file.$extra);
			if (is_file($file)) {
				if ( include_once($file) ) { 
					return true;
				}
			}
		}
		return false;
	}
	
	function includeFile($file, $base='', $extra='') {
		if ($this->fileNameOk($file)) {
			$file = $this->fileName($base.$file.$extra);
			if (is_file($file)) {
				if ( include($file) ) { 
					return true;
				}
			}
		}
		return false;
	}
	
	function makeFileNameOK($file) {
		// names cannot have illegal characters
		$file = trim(str_replace($this->badFileChars, '', basename($file)));
		if (!empty($this->acceptedFileChars)) $file = preg_replace($this->acceptedFileChars, '', $file);
		// names cannot have multiple dots
		$file = preg_replace("/\.+/si", ".", $file);
		// names cannot have multiple spaces
		$file = preg_replace("/\s+/si", " ", $file);
		// names cannot start with a . or -
		$file = preg_replace("/^(\.|-)+/si", '', $file);
		
		if (empty($file)) {
			return false;
		}
		
		return $file;
	}
	
	/*function makeFolderNameOK($file) {
		// names cannot start with a .
		$file = preg_replace("/^\.+/si", '', $file);
		// names cannot have multiple spaces
		$file = preg_replace("/(\s)\s+/si", "$1", $file);
		
		return str_replace($this->badFolderChars, '', $file);
	}*/
	
	function folderNameOK($file) {
		return $this->dirNameOK($file);
	}
	
	/* returns true if file name is OK */
	function fileNameOK($name = '') {
		$values = $this->badFileChars;
		$num = sizeof($values);
		$match = false;
		if (!is_string($name)) {
			return false;
		}
		// names cannot start with a .
		if (substr($name, 0, 1) == '.') {
			$match = true;
		}
		// names cannot start with a -
		if (substr($name, 0, 1) == '-') {
			$match = true;
		}
		// names cannot have multiple spaces
		if (preg_match("/\s\s/si", $name)) {
			$match = true;
		}
		// names cannot have multiple dots
		if (preg_match("/\.\./si", $name)) {
			$match = true;
		}
		// name cannot be empty
		if (empty($name)) {
			$match = true;
		}
		// name cannot have illegal characters
		for ($i=0; $i<$num; $i++) { 
			if (stristr($name,$values[$i])) {
				$match = true;
				break;
			}
		}
		if (!empty($this->acceptedFileChars)) {
			if (preg_match($this->acceptedFileChars, $name)) $match = true;
		}
		if ($match) {
			return false;
		} else {
			return true;
		}
	}

	/* returns true if dir name is OK */
	function dirNameOK($name = '') {
		$values = $this->badFolderChars;
		$num = sizeof($values);
		$match = false;
		if (!is_string($name)) {
			return false;
		}
		// names cannot start with a .
		if (substr($name, 0, 1) == '.') {
			$match = true;
		}
		// names cannot start with a -
		if (substr($name, 0, 1) == '-') {
			$match = true;
		}
		// names cannot have multiple spaces
		if (preg_match("/\s\s/si", $name)) {
			$match = true;
		}
		// names cannot have multiple dots
		if (preg_match("/\.\./si", $name)) {
			$match = true;
		}
		// name cannot be empty
		if (empty($name)) {
			$match = true;
		}
		// name cannot have illegal characters
		for ($i=0; $i<$num; $i++) { 
			if (stristr($name,$values[$i])) {
				$match = true;
				break;
			}
		}
		if (!empty($this->acceptedFolderChars)) {
			if (preg_match($this->acceptedFolderChars, $name)) $match = true;
		}
		if ($match) {
			return false;
		} else {
			return true;
		}
	}

	/* checks if an extension is OK */
	function extensionOK($extension, $accept_array) {
		
		if (!is_array($accept_array)) {
			$accept_array = explode(',', str_replace(' ', '', strtolower($accept_array)));
		} else {
			for ($i=0;$i<count($accept_array);$i++) {
				$accept_array[$i] = trim(strtolower($accept_array[$i]));
			}
		}
		if (empty($accept_array)) {
			return false;
		}
		if (in_array(strtolower($extension), $accept_array)) {
			return true;
		} else {
			return false;
		}
	}
	
	// chmods a file
	function chmod ($filename, $mode) {
		$mode = intval($mode, 8);
		if ($mode) {
			return eval ("return @chmod(stripslashes('".addslashes($filename)."'), 0".decoct($mode).");");
		}
		//return @chmod($filename, octdec($mode));
		return false;
	}
	
	// deletes a file or directory
	function delete($file) {
		if ((file_exists ($file)) && (!is_file($file))) { 
			if ($this->emptyDir($file)) {
				return true;
			} else {
				return false;
			}
		} elseif ((file_exists ($file)) && (is_file($file))) {
			if (@unlink($file)) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	
	// fixes slash inconsistencies
	function fileName($file) {
		return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $file);
	}
	
	// writes data to a file
	function writeFile($file, $data) {
		if (empty ($file)) { return false; }
		$retVal = false;
		$fp = @fopen ($file, 'w');
		@flock ($fp, 2);
		$retVal = @fwrite ($fp, $data);
		@flock ($fp, 3);
		@fclose ($fp);
		return $retVal;
	}
	
	function fileModTime($file) {
		if (! is_file ($file)) {
			return false;
		}
		return @filemtime ($file);
	}
	
	function fileExists($file) {
		if (is_file ($file)) {
			return true;
		} else {
			return false;
		}
	}

	function getContents($file) {
		//return implode("", @file($file)) === false;
		$code = @implode('', @file ($file));
		if (get_magic_quotes_runtime ()) {
			$code = stripslashes($code);
		}
		return $code;
	}
	
}

?>