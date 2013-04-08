<?php
if (!defined('IN_WPRO')&&!defined('WPRO_IN_ROUTE')) exit;
if (!defined('IN_WPROFILESYSTEM')) define('IN_WPROFILESYSTEM', true);
require_once(dirname(__FILE__).'/wproFilesystemBase.class.php');
/* 
functions needed only for displaying the editor have been moved to wproFilesystemBase
wproFilesystemBase is now used by wproSession and wysiwygPro instead (to reduce memory usage)
*/
class wproFilesystem extends wproFilesystemBase {
	
	function wproFilesystem () {
	
	}
		
	// checks if a file exists and if it does it returns a filename with a numeric suffix that doesn't exist
	function resolveDuplicate($file,$dir,$prefix='_copy_',$suffix='') {
		$file = basename($file);
		$i=1;$probeer=$file;
		while(file_exists($dir.$probeer)) {
			$punt=strrpos($file,".");
			if ($punt==false) {
				$test = $file;
			} else {
				$test = substr($file, 0, $punt);
			}
			if (!preg_match("/".quotemeta($prefix)."[0-9]+".quotemeta($suffix)."$/i",$test)) {
				$probeer=$test.$prefix.$i.$suffix;
 			} else {
				$probeer=preg_replace("/".quotemeta($prefix)."[0-9]+".quotemeta($suffix)."$/i",$prefix.$i.$suffix,$test);
			}
			if ($punt!=false) $probeer.=substr($file,($punt),strlen($file)-$punt);
			$i++;
		}
		return $probeer;
	}
	
	// deletes all files in a directory
	function emptyDir ($dir) {
		if(@ ! $opendir = @opendir($dir)) {
			return false;
		}
		while(false !== ($readdir = @readdir($opendir))) {
			if($readdir !== '..' && $readdir !== '.') {
				$readdir = trim($readdir);
				if(is_file($dir.'/'.$readdir)) {
					if(@ ! unlink($dir.'/'.$readdir)) {
						return false;
					}
				} elseif(is_dir($dir.'/'.$readdir)) {
					// Calls itself to clear subdirectories
					if(! $this->emptyDir($dir.'/'.$readdir)) {
						return false;
					}
				}
			}
		}
		@closedir($opendir);
		if(@ ! rmdir($dir)) {
			return false;
		}
		return true;
	
	}
	
	// creates a directory
	function makeDir($dir, $chmod=0) {
		if (!file_exists ($dir)) {
			if (@mkdir ($dir)) {
				if (!empty($chmod)) {
					//@chmod($dir, octdec($chmod));
					$this->chmod($dir, $chmod);
					// make group the same as folder if possible
					$parent = $dir;
					if (substr($parent, strlen($parent)-1)=='/' || substr($parent, strlen($parent)-1)=='\\') {
						$parent = substr($parent, 0, strlen($parent)-1);
					}
					$parent = dirname($parent);
					//echo $parent;
					if (@filegroup($parent) != @filegroup($dir)) {
						if (@filegroup($parent)) {
							@chgrp ( $dir, @filegroup($parent) );
						}
					}
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	// copy a file or directory
	// just use rename to move a file/folder
	function copy ($oldname, $newname) {
		if (is_file($oldname)){
			return @copy($oldname, $newname);
		} else if (is_dir($oldname)){
			return $this->dirCopy($oldname, $newname);
		} else {
			return false;
		}
	}
	function dirCopy($oldname, $newname) {
		if (!is_dir($newname)) {
			@mkdir($newname);
			//if (!empty($chmod)) {
				@chmod($newname, fileperms($oldname));
				// make group the same as folder if possible
				if (@filegroup($oldname) != @filegroup($newname)) {
					if (@filegroup($oldname)) {
						@chgrp ( $newname, @filegroup($oldname) );
					}
				}
			//}
		}
		$dir = @opendir($oldname);
		//while($file = readdir($dir)){
		while (false !== ($file = @readdir($dir))) {
			if ($file == "." || $file == "..") continue;
			$this->copy($oldname.'/'.$file, $newname.'/'.$file);
		}
		@closedir($dir);
		return true;
	}

	function rename ($oldname, $newname) {
		return @rename($oldname, $newname);
	}
	
	// appends data to a file
	function appendContents($filename, $data) {
		if (($existing = implode("", @file($filename))) === false) {
			return false;
		}
		if (($h = @fopen($filename, 'w')) === false) {
			return false;
		}
		if (($bytes = @fwrite($h, $existing.$data)) === false) {
			return false;
		}
		@fclose($h);
		return $bytes;
	}
		
	function isFile ($file) {
		if (is_file($file)) {
			return true;
		} else {
			return false;
		}
	}
	
	function fileSize($file) {
		// First check if the file exists.
		if(!is_file($file)) return '';
		
		// Get the file size in bytes.
		$size = @filesize($file);
		
		return $this->convertByteSize($size);
	}
	
	function dirSize($dir) {
		$dirSize=0;
		if ($handle=@opendir($dir)) {
			while (false !== ($file = @readdir($handle))) {
				if (($file!=".")&&($file!="..")) {
					if (is_dir($dir."/".$file)) {
						$tmp=$this->dirSize($dir."/".$file);
						if ($tmp!==false) $dirSize+=$tmp;
					} else {
						$dirSize+=filesize($dir."/".$file);
					}
				}
			}
			@closedir($handle);
		} else {
			return false;
		}
		
		return $dirSize;
	}

	// converts bytes 
	function convertByteSize($size) {
		// Setup some common file size measurements.
		$kb = 1024;         // Kilobyte
		$mb = 1024 * $kb;   // Megabyte
		$gb = 1024 * $mb;   // Gigabyte
		$tb = 1024 * $gb;   // Terabyte
		/* If it's less than a kb we just return the size, otherwise we keep going until
		the size is in the appropriate measurement range. */
		if($size < $kb) {
			return $size." B";
		} else if($size < $mb) {
			return round($size/$kb,2)." KB";
		} else if($size < $gb) {
			return round($size/$mb,2)." MB";
		} else if($size < $tb) {
			return round($size/$gb,2)." GB";
		} else {
			return round($size/$tb,2)." TB";
		}
	}
	
	// when passed a string in this format [0-9]+ [A-Za-z]+ it returns it as bytes
	function returnBytes($val) {
	   if (!empty($val)) {
		   $val = trim($val);
		   $last = strtolower(preg_replace("/^[0-9]+\s*([A-Za-z]+)$/si", "$1", $val));//strtolower($val{strlen($val)-1});
		   $val = preg_replace("/[^0-9]/si", "", $val);
		   switch($last) {
			   // The 'G' modifier is available since PHP 5.1.0
			   case 't':
			   case 'tb':
					$val *= 1024;
			   case 'g':
			   case 'gb':
				   $val *= 1024;
			   case 'm':
			   case 'mb':
				   $val *= 1024;
			   case 'k':
			   case 'kb':
				   $val *= 1024;
		   }
		}
	   return $val;
	}

	function getFileInfo($extension) {
		// to add more filetypes save an icon image to the images folder and add a description to your language file, then describe how the function should handle the file below:
		require(WPRO_DIR.'conf/fileDefinitions.inc.php');
		return $info;
	}
	
	// function for checking filter matches (filters are used to filter out directories and files that developers don't want displayed)
	function filterMatch($filename, $filters) {
		foreach($filters as $filter) {
			if (@preg_match($filter, $filename)) {
				return true;
			}
		}
		return false;
	}
	
	function getFoldersInDir ($directory, $sortby='name', $sortdir='asc', $filters=array(), $defaultFilters=true ) {
		return include(dirname(__FILE__).'/wproFilesystem/getFoldersInDir.inc.php');
	}
	
	// returns files in a directory
	function getFilesInDir ($directory, $sortby='name', $sortdir='asc', $file_types=array(), $filters=array(), $getDimensions=false) {
		return include(dirname(__FILE__).'/wproFilesystem/getFilesInDir.inc.php');
	}
	
	
	/* uploads multiple files (if there are files uploaded)
	if stopOnError returns the error and stops, else returns an array of errors	
	*/
	function uploadFiles($field, $folder, $extensions, $filters=array(), $sizeLimit=1024, $overwrite=false, $chkimgwidth=true, $maxwidth=500, $maxheight=500, $chmod=0, $changeGroup=true, $stopOnError=false) {
		return include(dirname(__FILE__).'/wproFilesystem/uploadFiles.inc.php');
	}
}

?>