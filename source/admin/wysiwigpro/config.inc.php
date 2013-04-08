<?php
if (!defined('IN_WPRO')) exit();
if (defined('WPRO_EDITOR_URL')) return;
/* 
* WysiwygPro 3.2.1, 30 November 2009.
* (c) Copyright 2007 and forever thereafter Chris Bolt and ViziMetrics Inc.
*/

/*
 * WysiwygPro 3.x global configuration file.
 * 
 * Installation of this software means that you agree to be bound by the terms of the enclosed license agreement.
 * Make sure that you have read and understand the license before installing.
 * 
*/

/* 
* Change WPRO_EDITOR_URL to the URL of your wysiwygPro folder.
* You only need to set this if WysiwygPro will be accessed from a virtual directory or from an Apache rewrite 
* rule or any other situation where the URL is not directly related to the underlying file system. 
* If you're not sure set it anyway :-)
*/

define('WPRO_EDITOR_URL', '');


/* No need to change anything below here! :-) 
 * If this is your first time installing WysiwygPro 3 then please get it up and running first 
 * before attempting to change anything below here.
 * ---------------------------------------------------------

/* Folder locations which cannot be changed at run time: */

// File path of this folder 
// Note: there is no need to set this, the below code automatically sets it for you!

if (!defined('WPRO_DIR')) define('WPRO_DIR', dirname(__FILE__) . '/');  

// File path of temp directory for session and cache storage:
// Note: If this directory is unwritable WysiwygPro will use your server's tmp dir.
// This feature is only used with the 'WP' session option. See WPRO_SESSION_ENGINE.

define('WPRO_TEMP_DIR', WPRO_DIR.'temp/');                                         


/* These are default settings and can be changed at run time: */

// URL of your themes folder 
// (##EDITOR_URL## will automatically be replaced by the value of WPRO_EDITOR_URL):

define('WPRO_THEME_URL', '##EDITOR_URL##themes/');

// Full physical file path of theme folder:      
                  		
define('WPRO_THEME_DIR', WPRO_DIR.'themes/');

// URL of your lang folder where language packs are stored 
// (##EDITOR_URL## will automatically be replaced by the value of WPRO_EDITOR_URL):

define('WPRO_LANG_URL', '##EDITOR_URL##lang/');

// File path for your lang folder where language packs are stored:

define('WPRO_LANG_DIR', WPRO_DIR.'lang/');  

define('WPRO_LANG', 'en-us');        // default language pack to use
define('WPRO_THEME', 'default');     // default theme to use

// URL of your emoticon folder 
// (##EDITOR_URL## will automatically be replaced by the value of WPRO_EDITOR_URL):
           
define('WPRO_EMOTICON_URL', '##EDITOR_URL##images/smileys/');  

// File path of your emoticon folder:		
						
define('WPRO_EMOTICON_DIR', WPRO_DIR.'images/smileys/');


/* session engine settings, these cannot be changed at run time. */

// Session engine to use, values are 'WP' for the built in engine, 
// or 'PHP' for PHP's session engine:
// If you chosse 'PHP' and your application uses custom session handler funtions, 
// or a custom session name then you will need to set these up in conf/customSessHandlers.inc.php

define('WPRO_SESSION_ENGINE', 'PHP');

// If true editor configuration will not be passed to dialog windows. You will need to manually configure 
// dialog windows in conf/dialogConfig.inc.php.

define('WPRO_REDUCED_SESSION', false);

// Session timeout in seconds. (this value can be small so long as the WPRO_SESS_REFRESH is smaller):	
				
define('WPRO_SESS_LIFETIME', 900); 

// Time between keep-alive calls to the session engine, so that session doesn't time-out while user is editing 
// (should be smaller than WPRO_SESS_LIFETIME) 
// To disable this feature simply make the value larger than your session lifetime.

define('WPRO_SESS_REFRESH', 300);

// If set to false direct access to dialog windows will not be allowed (users without a valid session will be denied)
// For your security don't change this value.

define('WPRO_ANONYMOUS_ACCESS', false);	

// maximum number of editor sessions that a single user can have open at any one time

define('WPRO_MAX_SESSIONS', 20);


/* development options, these cannot be changed at run time. */

// If set to true then the system will use the JavaScript source files *_src.js instead of the compressed versions. 
// Note: if the source scripts are missing the editor will die.

define('WPRO_USE_JS_SOURCE', false);

// If set to true JavaScript files will be compiled together into one file to reduce server requests.

define('WPRO_COMPILE_JS_INCLUDES', true);										

// If true AND WPRO_COMPILE_JS_INCLUDES is true then JavaScript output will be gzip encoded to reduce bandwidth

define('WPRO_GZIP_JS', true);													

// If true HTML output will be gzip encoded to reduce bandwidth

define('WPRO_GZIP', true);	

// Method for sending AJAX requests. Valid values are POST or GET
// Using GET will cause errors when manipulating a large number of files simultaneously in the file manager.
// (because the amount of data that can be sent via GET is restricted)
// However using GET may resolve 'No XML response' issues on some platforms

define('WPRO_AJAX_METHOD', 'POST');													


/* language vars, these cannot be changed at run time */
/* these security related messages occur when session data is not available and the language is unknown */
define('WPRO_STR_SESSION_TIMEOUT', '<strong>Your editing session has expired.</strong> <br />This is usually caused by a long period of inactivity. Please save your document and re-load the page.');
define('WPRO_STR_JS_SESSION_TIMEOUT', 'Your editing session has expired. This is usually caused by a long period of inactivity. Please save your document and re-load the page.');
define('WPRO_STR_UNAUTHORIZED', 'You are not authorized to access this resource.');
define('WPRO_STR_JS_UNAUTHORIZED', 'You are not authorized to access this resource.');

/* include paths */
/* If your application already includes these libraries then you may change the paths below and delete these 
 * libraries from wysiwygPro */
define('WPRO_PATH_GETID3', WPRO_DIR.'core/libs/getid3/');
define('WPRO_PATH_DOMIT', WPRO_DIR.'core/libs/domit/');
define('WPRO_PATH_XAJAX', WPRO_DIR.'core/libs/xajax/');

/* WysiwygPro is a modular application, many modules have their own configuration files in 
 * the conf folder... */
?>