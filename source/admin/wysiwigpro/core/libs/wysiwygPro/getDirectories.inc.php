<?php
/* function include file */
if (!defined('IN_WPRO')) exit;


		$return = array();
		if (empty($type)||$type=='link') {
			$type = array('image','document','media');
		} elseif (!is_array($type)) {
			$type = array($type);
		}
		if (defined('WPRO_SERIALIZED_DIRECTORIES')) {
			$directories = unserialize(WPRO_SERIALIZED_DIRECTORIES);
		} else {
			$directories = $this->directories;
		}
		if (!defined('WPRO_SERIALIZED_DIRECTORIES')) {
			foreach($type as $typ) {
				// add the default directory
				switch ($typ) {
					case 'image':
						if (isset($this->imageDir) && isset($this->imageURL) && (!empty($this->imageDir) || !empty($this->imageURL))&&$this->featureIsEnabled('imagemanager')) {
							$dir = new wproDirectory;
							$dir->id = 0;
							$dir->type = 'image';
							$dir->dir = $this->imageDir;
							$dir->URL = $this->imageURL;
							$dir->deleteFiles = $this->deleteFiles;
							$dir->deleteFolders = $this->deleteFolders;
							$dir->renameFiles = $this->renameFiles;
							$dir->renameFolders = $this->renameFolders;
							$dir->upload = $this->upload;
							$dir->overwrite = $this->overwrite;
							$dir->moveFiles = $this->moveFiles;
							$dir->moveFolders = $this->moveFolders;
							$dir->copyFiles = $this->copyFiles;
							$dir->copyFolders = $this->copyFolders;
							$dir->createFolders = $this->createFolders;
							$dir->editImages = $this->editImages;
							$dir->filters = $this->dirFilters;
							$dir->diskQuota = $this->diskQuota;
							$directories = array_merge(array($dir), $directories);
						}				
						break;
					case 'document':
						if (isset($this->documentDir) && isset($this->documentURL) && (!empty($this->documentDir) || !empty($this->documentURL))&&$this->featureIsEnabled('documentmanager')) {
							$dir2 = new wproDirectory;
							$dir2->id = 1;
							$dir2->type = 'document';
							$dir2->dir = $this->documentDir;
							$dir2->URL = $this->documentURL;
							$dir2->deleteFiles = $this->deleteFiles;
							$dir2->deleteFolders = $this->deleteFolders;
							$dir2->renameFiles = $this->renameFiles;
							$dir2->renameFolders = $this->renameFolders;
							$dir2->upload = $this->upload;
							$dir2->overwrite = $this->overwrite;
							$dir2->moveFiles = $this->moveFiles;
							$dir2->moveFolders = $this->moveFolders;
							$dir2->copyFiles = $this->copyFiles;
							$dir2->copyFolders = $this->copyFolders;
							$dir2->createFolders = $this->createFolders;
							$dir2->editImages = $this->editImages;
							$dir2->filters = $this->dirFilters;
							$dir2->diskQuota = $this->diskQuota;
							$directories = array_merge(array($dir2), $directories);
						}				
						break;
					case 'media':
						if (isset($this->mediaDir) && isset($this->mediaURL) && (!empty($this->mediaDir) || !empty($this->mediaURL))&&$this->featureIsEnabled('mediamanager')) {
							$dir3 = new wproDirectory;
							$dir3->id = 2;
							$dir3->type = 'media';
							$dir3->dir = $this->mediaDir;
							$dir3->URL = $this->mediaURL;
							$dir3->deleteFiles = $this->deleteFiles;
							$dir3->deleteFolders = $this->deleteFolders;
							$dir3->renameFiles = $this->renameFiles;
							$dir3->renameFolders = $this->renameFolders;
							$dir3->upload = $this->upload;
							$dir3->overwrite = $this->overwrite;
							$dir3->moveFiles = $this->moveFiles;
							$dir3->moveFolders = $this->moveFolders;
							$dir3->copyFiles = $this->copyFiles;
							$dir3->copyFolders = $this->copyFolders;
							$dir3->createFolders = $this->createFolders;
							$dir3->editImages = $this->editImages;
							$dir3->filters = $this->dirFilters;
							$dir3->diskQuota = $this->diskQuota;
							$directories = array_merge(array($dir3), $directories);
						}				
						break;
				}
			}
		}
		
		// add the directory objects
		foreach($type as $typ) {
			if (!$this->featureIsEnabled($typ.'manager')) continue;
			foreach($directories as $obj) {
				if (is_array($obj->type)) {
					if (in_array($typ, $obj->type)) {
						// check upload ability, if unavailable then set uploads allowed to false
						if (!ini_get('file_uploads')) {
							$obj->upload = false;
						}
						$obj->dir = $this->addTrailingSlash($obj->dir);
						$obj->URL = $this->addTrailingSlash($obj->URL);
						// add default filters to obj
						foreach ($this->_defaultFilters as $filter) {
							if (!in_array($filter, $obj->filters)) {
								$obj->addFilter($filter);
							}
						}						
						array_push($return, $obj);
					}
				} else {
					if ($obj->type == $typ) {
						if (!ini_get('file_uploads')) {
							$obj->upload = false;
						}
						$obj->dir = $this->addTrailingSlash($obj->dir);
						$obj->URL = $this->addTrailingSlash($obj->URL);
						// add default filters to obj
						foreach ($this->_defaultFilters as $filter) {
							if (!in_array($filter, $obj->filters)) {
								$obj->addFilter($filter);
							}
						}
						array_push($return, $obj);
					}
				}
			}
		}
			
		return $return;

?>