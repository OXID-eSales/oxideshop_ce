<?php
if (!defined('IN_WPRO')) exit;
if (!isset($wpro_inDialog)) exit;

// Loads the version 2 config file if V2 compatability required....

// include version 2 config file
if (@include_once(WPRO_DIR.'config.php')) {
	if (!defined('WPRO_V2_MODE')) define('WPRO_V2_MODE', true);
	if ($wpro_inDialog) {
		// load the vars into a version 3 compatible space
		$WPRO_IMAGE_DIR = defined('IMAGE_FILE_DIRECTORY') ? IMAGE_FILE_DIRECTORY : $WPRO_IMAGE_DIR;
		$WPRO_IMAGE_URL = defined('IMAGE_WEB_DIRECTORY') ? IMAGE_WEB_DIRECTORY : $WPRO_IMAGE_URL;
		$WPRO_DOC_DIR = defined('DOCUMENT_FILE_DIRECTORY') ? DOCUMENT_FILE_DIRECTORY : $WPRO_DOC_DIR;
		$WPRO_DOC_URL = defined('DOCUMENT_WEB_DIRECTORY') ? DOCUMENT_WEB_DIRECTORY : $WPRO_DOC_URL;
		
		$WPRO_FOLDER_CHMOD = defined('CHMOD_MODE') ? CHMOD_MODE : $WPRO_FOLDER_CHMOD;
		$WPRO_FILE_CHMOD = defined('FILE_CHMOD_MODE') ? FILE_CHMOD_MODE : $WPRO_FILE_CHMOD; 
		
		$WPRO_DELETE_FILES = isset($delete_files) ? $delete_files : $WPRO_DELETE_FILES;
		$WPRO_DELETE_FOLDERS = isset($delete_directories) ? $delete_directories : $WPRO_DELETE_FOLDERS;
		$WPRO_RENAME_FILES = isset($rename_files) ? $rename_files : $WPRO_RENAME_FILES;
		$WPRO_RENAME_FOLDERS = isset($rename_directories) ? $rename_directories : $WPRO_RENAME_FOLDERS;
		$WPRO_UPLOAD = isset($upload_files) ? $upload_files : $WPRO_UPLOAD;
		$WPRO_OVERWRITE = isset($overwrite) ? $overwrite : $WPRO_OVERWRITE;
		$WPRO_CREATE_FOLDERS = isset($create_directories) ? $create_directories : $WPRO_CREATE_FOLDERS;
		$WPRO_MOVE_FILES = isset($rename_files) ? $rename_files : $WPRO_MOVE_FILES;
		$WPRO_MOVE_FOLDERS = isset($rename_directories) ? $rename_directories : $WPRO_MOVE_FOLDERS;
		$WPRO_COPY_FILES = isset($rename_files) ? $rename_files : $WPRO_COPY_FILES;
		$WPRO_COPY_FOLDERS = isset($rename_directories) ? $rename_directories : $WPRO_COPY_FOLDERS;
		
		$WPRO_ALLOWED_DOC_EXTENSIONS = isset($document_types) ? $document_types : $WPRO_ALLOWED_DOC_EXTENSIONS;
		$WPRO_ALLOWED_IMAGE_EXTENSIONS = isset($image_types) ? $image_types : $WPRO_ALLOWED_IMAGE_EXTENSIONS;
		
		$WPRO_MAX_DOC_SIZE = isset($max_documentfile_size) ? $max_documentfile_size : $WPRO_MAX_DOC_SIZE;
		$WPRO_MAX_IMAGE_SIZE = isset($max_file_size) ? $max_file_size : $WPRO_MAX_IMAGE_SIZE;
		$WPRO_MAX_IMAGE_WIDTH = isset($max_image_width) ? $max_image_width :  $WPRO_MAX_IMAGE_WIDTH;
		$WPRO_MAX_IMAGE_HEIGHT = isset($max_image_height) ? $max_image_height : $WPRO_MAX_IMAGE_HEIGHT;
	
	} else {
	
		$LANG = defined('DEFAULT_LANG') ? str_replace('.php', '', DEFAULT_LANG) : WPRO_LANG;
		
	}
}
?>