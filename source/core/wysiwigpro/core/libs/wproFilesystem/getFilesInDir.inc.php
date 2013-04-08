<?php
/* function include file */
if (!defined('IN_WPROFILESYSTEM')) exit;

		$filelist = array();
		$handle = @opendir($directory);
		if (substr($directory, strlen($directory)-1) != '/') {
			$directory.='/';
		}
		$i=0;
		while (false !== ($file = @readdir($handle))) {
			$extension = strrchr(strtolower($file), '.');
			if (is_file($directory.$file) && ($file != ".") && ($file != "..")) {
				if (strstr($file, '_WPROTEMP_')) {
					// cleanup temp files over 48 hours old.
					if (filemtime($directory.$file) < time()-172800) {
						unlink($directory.$file);
					}
					continue;
				}				
				if (!empty($file_types)) {
					if (!$this->extensionOK($extension, $file_types)) {
						continue;
					}
				}
				if (!empty($filters)) {
					if ($this->filterMatch($file, $filters)) continue;
				}
				if (!$this->fileNameOK($file)) continue;
				$file_info = $this->getFileInfo($extension);
				$filelist[$i]['name'] = $file;
				$filelist[$i]['type'] = $file_info['description'];
				$filelist[$i]['modified'] = $this->fileModTime($directory.$file);
				$filelist[$i]['size'] = $this->fileSize($directory.$file);
				$filelist[$i]['info'] = $file_info;
				if ($getDimensions) {
					if (@list ($width, $height) = @getimagesize($directory.$file)) {
						$filelist[$i]['dimensions']['width'] = $width;
						$filelist[$i]['dimensions']['height'] = $height;
						$filelist[$i]['dimensions']['text'] = $width.' x '.$height;
					}
				}
				
				$i ++;
			}
		}
		@closedir($handle);
		// do sorting...
		if ($sortby != 'name' && $sortby != 'type' && $sortby != 'modified' && $sortby != 'size') {
			$sortby='name';
		}
		if (strtolower($sortdir) == 'asc') {
			$sortflag = SORT_ASC;
		} else {
			$sortflag = SORT_DESC;
		}
		
		$filelist = $this->arrayCSort($filelist, $sortby, $sortflag);
		
		return($filelist);

?>