<?php
if (!defined('IN_WPRO')) exit();
/*
* WysiwygPro default file permissions and configuration for dialog windows.
* This file sets the default locations of your image, document and media directories and sets default file management permissions.
* 
* This file gets included into all dialog windows but nothing else.
* You could add your applications authentication routine to customSessHandlers.inc.php to ensure the security of dialog windows...
* And then you could load user data and set the options in this file based on your user permissions and application configuration settings.
* 
* A reference to the current editor ($EDITOR) is available for use in this file.
* This allows you to set any PHP API options right in this file.
*
* Note: If REDUCED_SESSION has NOT been set in config.inc.php then all the variables below can be overwritten at run-time using the PHP API, 
* in which case there is no need to set anything in this file.
* But settings applied directly to the $EDITOR object will orderride everything.
*
* You should NEVER hard-code the editing permissions to true without adding some kind of user authentication!
*
*/


/* all the follow variables set default options only */

/* default directory locations */
$WPRO_IMAGE_URL = '';             	 // web URL of your images folder
$WPRO_IMAGE_DIR = '';                 // file path of the folder above
$WPRO_DOC_URL = '';              	 // web URL of your downloadable documents folder
$WPRO_DOC_DIR = '';                   // file path of the folder above
$WPRO_MEDIA_URL = '';            	 // web URL of your embedded media folder
$WPRO_MEDIA_DIR = '';                 // file path of the folder above

/* default directory editing permissions - set these using a session, hard coding these values to true will leave your server wide open!!! */
$WPRO_DELETE_FILES = false;           // can users delete files?
$WPRO_DELETE_FOLDERS = false;         // can users delete folders?
$WPRO_RENAME_FILES = false;           // can users rename files?
$WPRO_RENAME_FOLDERS = false;         // can users rename folders?
$WPRO_UPLOAD = false;                 // can users upload files?
$WPRO_OVERWRITE = false;              // can users overwrite files?
$WPRO_MOVE_FILES = false;             // can users move files?
$WPRO_MOVE_FOLDERS = false;           // can users move folders
$WPRO_COPY_FILES = false;             // can users copy files?
$WPRO_COPY_FOLDERS = false;           // can users copy folders?
$WPRO_CREATE_FOLDERS = false;         // can users create sub directories?
$WPRO_EDIT_IMAGES = false;            // can users edit images?

/* default directory objects */
$WPRO_DIRECTORIES = array();			// an array of directory objects

/* default allowed file types */
$WPRO_ALLOWED_DOC_EXTENSIONS = '.html, .htm, .pdf, .doc, .docx, .rtf, .txt, .xls, .xlsx, .ppt, pptx, .pps, .ppsx, .zip, .tar, .gzip, .bzip, .sit, .dmg'; // allowed document extensions for upload
$WPRO_ALLOWED_MEDIA_EXTENSIONS = '.asf, .asx, .flv, .h264, .mov, .mp3, .mp4, .swf, .wax, .wma, .wmv, .wvx, .wpl, .xspf'; // allowed media extensions for upload
$WPRO_ALLOWED_IMAGE_EXTENSIONS = '.jpg, .jpeg, .gif, .png'; // allowed image extensions for upload

/* allowed media plugins */
$WPRO_ALLOWED_MEDIA_PLUGINS = 'flash, flvplayer, quicktime, windowsMedia, youtube';

/* default allowed file sizes */
$WPRO_MAX_MEDIA_SIZE = '2 MB';       // Maximum media file size allowed for upload
$WPRO_MAX_DOC_SIZE = '2 MB';         // Maximum document file size allowed for upload
$WPRO_MAX_IMAGE_SIZE = '140 KB';       // Maximum file size of images allowed for upload
$WPRO_MAX_IMAGE_WIDTH = 500;          // Maximum width of images in pixels
$WPRO_MAX_IMAGE_HEIGHT = 500;         // Maximum height of images in pixels

$WPRO_FOLDER_CHMOD = 0777;			// chmod new folders to, leave as 0 or an empty value for no chmod. 
$WPRO_FILE_CHMOD = 0666;				// chmod new files to, leave as 0 or an empty value for no chmod.
/* NOTE: CHMOD settings MUST be an octal value prefixed with 0, e.g. use 0775 NOT 775. Do not use decimal values. */

/* $trusted directories, same is in version 2.x but now depreciated */
$trusted_directories = array();

?>