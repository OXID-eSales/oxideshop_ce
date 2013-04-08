<?php
/* function include file */
if (!defined('IN_WPROFILESYSTEM')) exit;

		$folderlist = array();
		$bhandle = @opendir($directory);
		if (substr($directory, strlen($directory)-1) != '/') {
			$directory.='/';
		}
		$i = 0;
		while (false !== ($folder = @readdir($bhandle))) {
			if (file_exists($directory.$folder) 
			&& (!is_file($directory.$folder)) 
			&& ($folder != ".") 
			&& ($folder != "..") 
			&& (!$defaultFilters || ($defaultFilters && (
				($folder != "_vti_cnf") 
				&& ($folder != "aspnet_client") 
				&& ($folder != "_notes")
			)))
			&& (!strstr($folder, '_WPROTEMP_'))) {
				if (!$this->fileNameOK($folder)) continue;
				if (!empty($filters)) {
					if ($this->filterMatch($folder, $filters)) continue;
				}
				$folderlist[$i]['name'] = $folder;
				$folderlist[$i]['modified'] = $this->fileModTime($directory.$folder);
				$i ++;
			}
		}
		@closedir($bhandle);
		
		// do sorting...
		// (other types of sorting may be available in future versions if your wondering why the sortby variable is here.)
		if ($sortby != 'name'&&$sortby!='modified') {
			$sortby='name';
		}
		if (strtolower($sortdir) == 'asc') {
			$sortdir = SORT_ASC;
		} else {
			$sortdir = SORT_DESC;
		}
		$folderlist = $this->arrayCSort($folderlist, $sortby, $sortdir);
		
		return $folderlist;

?>