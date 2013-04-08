<?php
if (!defined('IN_WPRO')) exit;
if (!defined('WPRO_DIR')) exit;
function wproDirectoryUniqueID() {
	static $id = 3;
	$id ++;
	return $id;	
}
// class for generating directory objects
class wproDirectory {
	var $id = 3;
	function wproDirectory() {
		$this->id = wproDirectoryUniqueID();
	}
	
	/* type */
	var $type = 'image'; // or document, or media
	var $icon = '';
	var $name = ''; // a user identifyable string (display name) (the folder name will be used if this is blank)
	
	/* location */
	var $URL = ''; // web URL of folder
	var $dir = ''; // file directory path to folder
	
	function setPermissions($p) {
		switch ($p) {
			case 'read-only' :
				$this->deleteFiles = false;           // can users delete files
				$this->deleteFolders = false;         // can users delete folders
				$this->renameFiles = false;           // can users rename files
				$this->renameFolders = false;         // can users rename folders
				$this->upload = false;           	// can users upload files
				$this->overwrite = false;             // can users overwrite files/folders when moving or uploading
				$this->moveFiles = false;             // can users move files
				$this->moveFolders = false;           // can users move folders
				$this->copyFiles = false;             // can users copy files
				$this->copyFolders = false;           // can users copy folders
				$this->createFolders = false; 		// can users create new folders
				$this->editImages = false; 					// can users edit files
				break;
			case 'read-write' :
				$this->deleteFiles = false;           // can users delete files
				$this->deleteFolders = false;         // can users delete folders
				$this->renameFiles = false;           // can users rename files
				$this->renameFolders = false;         // can users rename folders
				$this->upload = true;           // can users upload files
				$this->overwrite = false;             // can users overwrite files/folders when moving or uploading
				$this->moveFiles = false;             // can users move files
				$this->moveFolders = false;           // can users move folders
				$this->copyFiles = true;             // can users copy files
				$this->copyFolders = true;           // can users copy folders
				$this->createFolders = true; 		// can users create new folders
				$this->editImages = false; 					// can users edit files
				break;
			case 'read-write-modify' :
				$this->deleteFiles = false;           // can users delete files
				$this->deleteFolders = false;         // can users delete folders
				$this->renameFiles = true;           // can users rename files
				$this->renameFolders = true;         // can users rename folders
				$this->upload = true;           // can users upload files
				$this->overwrite = true;             // can users overwrite files/folders when moving or uploading
				$this->moveFiles = false;             // can users move files
				$this->moveFolders = false;           // can users move folders
				$this->copyFiles = true;             // can users copy files
				$this->copyFolders = true;           // can users copy folders
				$this->createFolders = true; 		// can users create new folders
				$this->editImages = true; 					// can users edit files
				break;
			case 'read-write-modify-delete' :
			case 'everything' :
				$this->deleteFiles = true;           // can users delete files
				$this->deleteFolders = true;         // can users delete folders
				$this->renameFiles = true;           // can users rename files
				$this->renameFolders = true;         // can users rename folders
				$this->upload = true;           // can users upload files
				$this->overwrite = true;             // can users overwrite files/folders when moving or uploading
				$this->moveFiles = true;             // can users move files
				$this->moveFolders = true;           // can users move folders
				$this->copyFiles = true;             // can users copy files
				$this->copyFolders = true;           // can users copy folders
				$this->createFolders = true; 		// can users create new folders
				$this->editImages = true; 					// can users edit files
				break;
		}
	}
	
	/* permissions */
	var $deleteFiles = false;           // can users delete files
	var $deleteFolders = false;         // can users delete folders
	var $renameFiles = false;           // can users rename files
	var $renameFolders = false;         // can users rename folders
	var $upload = false;           		// can users upload files
	var $overwrite = false;             // can users overwrite files/folders when moving or uploading
	var $moveFiles = false;             // can users move files
	var $moveFolders = false;           // can users move folders
	var $copyFiles = false;             // can users copy files
	var $copyFolders = false;           // can users copy folders
	var $createFolders = false; 				// can users create new folders
	var $editImages = false; 					// can users edit files
	
	var $filters = array();
	
	function addFilter($filter) {
		if (!in_array($filter, $this->filters))	array_push($this->filters, $filter);
	}
	
	/* disk quata, specified in MB, 0 for unlimited. */
	var $diskQuota = 0;

}
// class for generating the editor
class wysiwygPro extends wproCore {

	/*
	public variables
	*/
	var $version = '3.2.1.20091130';
	var $productName = 'WysiwygPro';
	var $copyright = '(c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.';
		
	/* permissions setable stuff... */
	/* directories and addresses */
	var $emoticonDir = '';
	var $emoticonURL = '';
	var $imageDir = '';
	var $imageURL = '';
	var $documentDir = '';
	var $documentURL = '';
	var $mediaDir = '';
	var $mediaURL = '';
	
	var $dirFilters = array();
	
	var $_defaultFilters = array(
		// built in filters
		
		// dreamweaver notes
		'/(\/|\\\|^)_notes(\/|\\\|$)/D',
		
		// front page extensions
		'/(\/|\\\|^)_vti_[a-z]+(\/|\\\|$)/Di',
		
		// dot net
		'/(\/|\\\|^)aspnet_client(\/|\\\|$)/D',
		
		// cgi
		'/(\/|\\\|^)cgi-bin(\/|\\\|$)/D',
		
		// unix system files
		'/(\/|\\\|^)\.[^.\/]/'
	);
	
	/* permissions */
	var $deleteFiles = '';            // can users delete files
	var $deleteFolders = '';          // can users delete files
	var $renameFiles = '';            // can users rename files
	var $renameFolders = '';          // can users rename files
	var $upload = '';                 // can users upload files
	var $overwrite = '';              // can users overwrite files
	var $moveFiles = '';              // can users move files
	var $moveFolders = '';            // can users move files
	var $copyFiles = '';              // can users copy files
	var $copyFolders = '';            // can users copy files
	var $createFolders = ''; 				  // can users create new directories
	var $editImages = ''; 				  // can users edit files
	
	var $diskQuota = 0;
	
	var $folderCHMOD = 0; // mode for new directories
	var $fileCHMOD = 0;	// mode for uploaded files
	
	var $defaultImageView = 'thumbnails';
	var $thumbnails = true;
	var $thumbnailFolderName = 'wpThumbnails';
	var $thumbnailFolderDisplayName = ''; // leave blank to load from language pack
			
	/* extensions and file sizes */ 
	var $allowedImageExtensions = '';
	var $allowedDocExtensions = '';
	var $allowedMediaExtensions = '';
	var $allowedMediaPlugins = '';
	var $maxDocSize = '';        // Maximum file size in bytes allowed for upload
	var $maxMediaSize = '';        // Maximum file size in bytes allowed for upload
	var $maxImageSize = '';         // Maximum file size of images in bytes allowed for upload
	var $maxImageWidth = '';          // Maximum width of images in pixels
	var $maxImageHeight = '';         // Maximum height of images in pixels
	
	var $maxImageDisplayWidth = '';          // Default maximum display width of images in pixels
	var $maxImageDisplayHeight = '';         // Default maximum display height of images in pixels
	var $maxMediaDisplayWidth = '';          // Default maximum display width of media in pixels
	var $maxMediaDisplayHeight = '';         // Default maximum display height of media in pixels
	
	var $directories = array();
	
	/* instance dirs, there must be a trusted dir array in conf/dialogConfig.inc.php */
	var $instanceDocDir = '';
	var $instanceImgDir = '';
	var $instanceMediaDir = '';
	
	// allows you to specify some html, maybe a script tag to be displayed above or below the editor
	var $displayAbove = array(); 
	var $displayBelow = array(); 
	
	var $configJS = array();
	
	// allows custom variables to be appended to query strings...
	var $appendToQueryStrings = '';
	var $appendSid = true;
	
	var $sessionId = '';
	
	var $iframeDialogs = false;
	
	var $name = 'htmlCode'; 
	var $lang = 'en-us';
	var $editorCharset = '';
	var $langFolderURL = '';
	var $langFolderDir = '';
	
	/* appearance */
	var $theme = '';
	var $themeFolderURL = '';
	var $themeFolderDir = '';
	var $toolbarHeight = 24;
	var $width = '100%';
	var $height = '400';
	var $startView = 'design'; // or 'source' or 'preview'
	
	var $loadMethod = 'inline'; // or 'onload' or 'disabled'
	
	var $operaSupport = false;
	var $konquerorSupport = false;
	
	var $editorURL = ''; // overrides WPRO_EDITOR_URL in config file
	var $route = '';
	
	var $colorSwatches = array();
	
	var $saveButton = false;
	var $saveButtonLabel = '##save##';
	var $saveButtonURL = '##themeURL##buttons/save.gif';
	var $saveButtonWidth = 22;
	var $saveButtonHeight = 22;
	
	/* code format */
	var $value = ''; // the html to insert
	var $valueIsEscaped = false;
	var $emptyValue = 'auto';
	var $escapeCharacters = false;
	var $escapeCharactersRange = 'charCode>127';
	var $escapeCharactersMappings = array();
	var $baseURL = '';
	var $fullURLs = false;
	var $urlFormat = '';
	var $encodeURLs = true;
	var $htmlLang = '';
	var $htmlDirection = '';
	var $htmlCharset = '';
	var $defaultHTMLCharset = 'UTF-8';
	var $jsBookmarkLinks = false;
	
	var $stylesheets = array();
	var $stylesheet = '';
	var $cssText = ''; // a string of CSS to be added to documents.
	var $defaultFont = '';
	var $defaultBgColor = '';
	var $fragmentCSSText = ''; // a string of CSS to be added to document fragment previews
	var $fragmentStylesheet = ''; // a stylesheet to be added to document fragment previews
	var $bodyClass = '';
	
	// doctypes to choose from for when user fails to set one
	var $xhtmlDoctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	var $xhtmlStrictDoctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
	var $xhtml1_1Doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
	
	var $htmlDoctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	var $htmlStrictDoctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
	
	var $doctype = ''; // this is the doctype that will be used if set.
	
	var $htmlVersion = ''; // HTML | HTML STRICT | XHTML | XHTML STRICT
	var $defaultHTMLVersion = 'XHTML';
	var $lineReturns = 'P'; // or DIV or BR
	var $newCellInners = 'auto';
	
	var $guidelines = true;
		
	//var $sendCacheHeaders = '';
		
	/* menus */
	var $stylesMenu = array();
	var $fontMenu = array();
	var $sizeMenu = array();
		
	/* class lists */
	
	// context menu, items are displayed, or not displayed based on current selection.
	var $contextMenu = array(); 
	
	// snippets (custom inserts)
	var $snippets = array();
	
	// links
	var $links = array();
	var $linksBrowserURL = '';
	
	var $toolbarLayout = array();
		
	/*var $formEditingLayout = array();
	var $strictEditingLayout = array();
	var $textOnlyLayout = array();
	var $limitedSpaceLayout = array();*/
		
	/* buttons are also menu items and other such features */
	var $_buttonDefinitions = 
	// 'idname' => array('button',$tooltip,$function,$url,$width,$height,$cid),
	array(
		
		'print' => array('button','', 'WPro.##name##.print()', '##buttonURL##print.gif', 22, 22, '' ),
		
		'cut' => array('button','', 'WPro.##name##.callFormatting(\'cut\')', '##buttonURL##cut.gif', 22, 22, 'cut' ),
		'copy' => array('button','', 'WPro.##name##.callFormatting(\'copy\')', '##buttonURL##copy.gif', 22, 22, 'copy' ),
		'paste' => array('button','', 'WPro.##name##.callFormatting(\'paste\')', '##buttonURL##paste.gif', 22, 22, 'paste' ),
				
		'undo' => array('button','', 'WPro.##name##.callFormatting(\'undo\')', '##buttonURL##undo.gif', 22, 22, 'undo'),
		'redo' => array('button','', 'WPro.##name##.callFormatting(\'redo\')', '##buttonURL##redo.gif', 22, 22, 'redo'),
		
		'image' => array('button','', 'WPro.##name##.callFormatting(\'redo\')', '##buttonURL##image.gif', 22, 22, '' ),
				
		'link' => array('button','', 'WPro.##name##.callFormatting(\'redo\')', '##buttonURL##link.gif', 22, 22, '' ),
		
		'document' => array('button','', 'WPro.##name##.callFormatting(\'redo\')', '##buttonURL##document.gif', 22, 22, '' ),
			
		'tageditor' => array('button','', "WPro.##name##.showTagEditor()", '##buttonURL##spacer.gif', 22, 22, '' ),
		'deletetag' => array('button','', "WPro.##name##.deleteNodeClicked()", '##buttonURL##delete.gif', 22, 22, '' ),
		'removetag' => array('button','', "WPro.##name##.removeNodeClicked()", '##buttonURL##spacer.gif', 22, 22, '' ),
				
		'styles' => array('builtinselect','','',array(),82,'format'),
		//'class' => array('select','##cssStyle##','',array(),72,'class'),
		'font' => array('builtinselect','','',array(),82,'font'),
		'size' => array('builtinselect','','',array(),40,'size'),
		
		'bold' => array('button','', 'WPro.##name##.callFormatting(\'bold\')', '##buttonURL##bold.gif', 22, 22, 'bold'),
		'italic' => array('button','', 'WPro.##name##.callFormatting(\'italic\')', '##buttonURL##italic.gif', 22, 22, 'italic' ),
		'underline' => array('button','', 'WPro.##name##.callFormatting(\'underline\')', '##buttonURL##underline.gif', 22, 22, 'underline' ),
		'superscript' => array('button','', 'WPro.##name##.callFormatting(\'superscript\')', '##buttonURL##superscript.gif', 22, 22, 'superscript' ),
		'subscript' => array('button','', 'WPro.##name##.callFormatting(\'subscript\')', '##buttonURL##subscript.gif', 22, 22, 'subscript' ),
		'strikethrough' => array('button','', 'WPro.##name##.callFormatting(\'strikethrough\')', '##buttonURL##strikethrough.gif', 22, 22, 'strikethrough' ),
		
		'left' => array('button','', 'WPro.##name##.callFormatting(\'justifyleft\')', '##buttonURL##left.gif', 22, 22, 'justifyleft' ),
		'center' => array('button','', 'WPro.##name##.callFormatting(\'justifycenter\')', '##buttonURL##center.gif', 22, 22, 'justifycenter' ),
		'right' => array('button','', 'WPro.##name##.callFormatting(\'justifyright\')', '##buttonURL##right.gif', 22, 22, 'justifyright' ),
		'full' => array('button','', 'WPro.##name##.callFormatting(\'justifyfull\')', '##buttonURL##full.gif', 22, 22, 'justifyfull' ),
				
		'numbering' => array('button','', 'WPro.##name##.callFormatting(\'insertorderedlist\')', '##buttonURL##numbering.gif', 22, 22, 'insertorderedlist' ),
		'bullets' => array('button','', 'WPro.##name##.callFormatting(\'insertunorderedlist\')', '##buttonURL##bullets.gif', 22, 22, 'insertunorderedlist' ),
		
		'outdent' => array('button','', 'WPro.##name##.callFormatting(\'outdent\')', '##buttonURL##outdent.gif', 22, 22, 'outdent' ),
		'indent' => array('button','', 'WPro.##name##.callFormatting(\'indent\')', '##buttonURL##indent.gif', 22, 22, 'indent' ),
		
		'fontcolor' => array('button','', 'WPro.##name##.fontColor.pick()', '##buttonURL##fontcolor.gif', 22, 22, 'forecolor' ),
		'highlight' => array('button','', 'WPro.##name##.highlightColor.pick()', '##buttonURL##highlight.gif', 22, 22, 'backcolor' ),
		
		// form editing
		'form' => array('button','', '', '##buttonURL##form.gif', 22, 22, '' ),
		//'editform' => array('button','##highlight##', '', 'editform.gif', 22, 22, 'forecolor' ),
		'textbox' => array('button','', '', '##buttonURL##textbox.gif', 22, 22, '' ),
		'textfield' => array('button','', '', '##buttonURL##textfield.gif', 22, 22, '' ),
		'dropdownlist' => array('button','', '', '##buttonURL##dropdownlist.gif', 22, 22, '' ),
		'listbox' => array('button','', '', '##buttonURL##listbox.gif', 22, 22, '' ),
		'checkbox' => array('button','', '', '##buttonURL##checkbox.gif', 22, 22, '' ),
		'option' => array('button','', '', '##buttonURL##option.gif', 22, 22, '' ), // radio button
		'optiongroup' => array('button','', '', '##buttonURL##optiongroup.gif', 22, 22, '' ), // radio group
		'button' => array('button','', '', '##buttonURL##button.gif', 22, 22, '' ), // radio group
		'file' => array('button','', '', '##buttonURL##file.gif', 22, 22, '' ), // radio group
		'imageinput' => array('button','', '', '##buttonURL##imageinput.gif', 22, 22, '' ), // radio group		
		
		// menu buttons...
		'formediting' => array('menu','', array('form','textbox','textfield','dropdownlist','listbox','checkbox','option','button','file','imageinput'), '##buttonURL##form.gif', 22, 22, '' ),
		
		'moretextformatting' => array('menu','', array('superscript','subscript','strikethrough'), '##buttonURL##more.gif', 16, 22, '' ),
		'morealignmentformatting' => array('menu','', array('dirltr','dirrtl'), '##buttonURL##more.gif', 16, 22, '' ),
		'morelistformatting' => array('menu','', array('bulletsandnumbering'), '##buttonURL##more.gif', 16, 22, '' ),
		
		'moreformatting' => array('menu','', array('superscript','subscript','strikethrough','separator','dirltr','dirrtl'), '##buttonURL##more.gif', 16, 22, '' ),
		
		// select all
		'selectall' => array('button','', 'WPro.callCommand(WPro.##name##.editDocument, \'selectall\', false, null)', '##buttonURL##spacer.gif', 22, 22, 'selectall' ),
		
		// generic spacer
		'spacer' => array('spacer', 22, 22),
		
		// generic separator
		'separator' => array('separator'),
		
		// separators
		'separator1' => array('separator'),
		'separator2' => array('separator'),
		'separator3' => array('separator'),
		'separator4' => array('separator'),
		'separator5' => array('separator'),
		'separator6' => array('separator'),
		'separator7' => array('separator'),
		'separator8' => array('separator'),
		'separator9' => array('separator'),
		'separator10' => array('separator'),
				
		// v2 buttons, nope they need to be done as features!!!
	);
	
	//var $features = array();
	
	var $_featureDefinitions = array ( 
		// [idname] => buttons
		// features that produce depreciated markup
		'htmldepreciated' => array(array('target')),
		'target' => array(),
		'events' => array(), // used to open new windows if target is not allowed
		// visual options in dialog windows (except for the style option)
		'dialogappearanceoptions' => array(),
		// websafe color pallete
		'webcolors' => array(),
		// advanced tag editor
		'tageditor' => array(array('removetag','deletetag','tagpath')),
		// features not supported under gecko
		//'nongecko' => array(/*array('cut','copy','paste','spacer2','separator2')*/),
		'nongecko' => array(array('zoom')),
		// features not supported in opera
		'nonopera' => array(array('zoom')),
		// features not supported in safari
		'nonsafari' => array(array('zoom')),
		// tabs, added V2 mappings
		'viewtabs' => array(),
		'designtab' => array(),
		'sourcetab' => array(),
		'previewtab' => array(),
		// guidelines button
		'guidelines' => array(),
		// the shift+enter message
		'shift+entermessage' => array(),
		// draggable resize button
		'dragresize' => array(),
	);
	
	var $features = array();
	
	/* depreciated */
	
	
	
	/*
	private variables 
	*/
	var $has_expired = true;
	var $_v2Mode = false; // version 2 compatability required?
	var $_removeArray = array();
	var $_originalName = 'htmlCode';
	var $_subsequent = false;
	var $subsequent = false;
	var $_browserType = 'unsupported';
	var $_browserVersion = 0;
	var $_unsupported = true;
	var $_createSession = true;
	var $_headContentDone = false;
	var $_dialogJSDone = false;
	var $_sessRefresh = 360;
	var $_baseConfigDone = false;		
	
	
	/* class constructor */
	function wysiwygPro($cacheId='') {
		if (!empty($cacheId)) {
			$this->sessionId = $cacheId;
		}
		$this->loadPlugin ('wproCore_defaults');
	}
	
	/* sleep function */
	function __sleep() {
		return array(
			'emoticonDir',
			'emoticonURL',
			
			// directories??
			'imageDir',
			'imageURL',
			'documentDir',
			'documentURL',
			'mediaDir',
			'mediaURL',
				
			'dirFilters',
				
			'deleteFiles',
			'deleteFolders',
			'renameFiles',
			'renameFolders',
			'upload',
			'overwrite',
			'moveFiles',
			'moveFolders',
			'copyFiles',
			'copyFolders',
			'createFolders',
			'editImages',
			
			'diskQuota',
				
			'folderCHMOD',
			'fileCHMOD',
			
			'defaultImageView',
			'thumbnails',
			'thumbnailFolderName',
			'thumbnailFolderDisplayName',
								
			'allowedImageExtensions',
			'allowedDocExtensions',
			'allowedMediaExtensions',
			'allowedMediaPlugins',
			'maxDocSize',
			'maxMediaSize',
			'maxImageSize',
			'maxImageWidth',
			'maxImageHeight',
			
			'maxImageDisplayWidth',
			'maxImageDisplayHeight',
			'maxMediaDisplayWidth',
			'maxMediaDisplayHeight',
				
			'directories',
				
			'instanceDocDir',
			'instanceImgDir',
			'instanceMediaDir',
				
			//'displayAbove', 
			//'displayBelow',
				
			//'configJS',
				
			'appendToQueryStrings',
			'appendSid',
				
			//'sessionId',
				
			'iframeDialogs',
				
			//'name',
			'lang',
			//'editorCharset',
			'langFolderURL',
			'langFolderDir',
				
			'theme',
			'themeFolderURL',
			'themeFolderDir',
			//'toolbarHeight',
			//'width',
			//'height',
			//'startView',
				
			//'loadMethod',
			
			//'operaSupport',
				
			'editorURL',
			'route',
				
			'colorSwatches',
				
			//'saveButton',
			//'saveButtonLabel',
			//'saveButtonURL',
			//'saveButtonWidth',
			//'saveButtonHeight',
				
			//'value',
			//'valueIsEscaped',
			//'escapeCharacters',
			//'escapeCharactersRange',
			//'escapeCharactersMappings',
			'baseURL',
			'fullURLs',
			'urlFormat',
			'encodeURLs',
			'htmlLang',
			'htmlDirection',
			'htmlCharset',
			//'defaultHTMLCharset',
				
			//'stylesheets',
			//'stylesheet',
			//'cssText',
			//'defaultFont',
			//'defaultBgColor',
			//'fragmentCSSText',
			//'fragmentStylesheet',
				
			//'xhtmlDoctype',
			//'xhtmlStrictDoctype',
			//'xhtml1_1Doctype',
				
			//'htmlDoctype',
			//'htmlStrictDoctype',
				
			//'doctype',
				
			'htmlVersion',
			//'defaultHTMLVersion',
			//'lineReturns',
			//'newCellInners',
				
			//'guidelines',
					
			'stylesMenu',
			//'fontMenu',
			//'sizeMenu',
					
			//'contextMenu', 
				
			'snippets',
				
			'links',
			'linksBrowserURL',
				
			//'toolbarLayout',
								
			//'_buttonDefinitions',
				
			//'_featureDefinitions',
			
			'features',
			
			//'has_expired',
			//'_v2Mode',
			//'_removeArray',
			//'_originalName',
			//'_subsequent',
			//'subsequent',
			//'_browserType',
			//'_browserVersion',
			//'_unsupported',
			//'_createSession',
			//'_headContentDone',
			//'_dialogJSDone',
			//'_sessRefresh',
			//'_baseConfigDone',
			
			'plugins',
			//'JSPlugins',
			//'JSHTMLFilters',
			//'JSEditorEvents',
			//'JSBSH',
			//'JSFH',
			//'JSFVH',
	
		);
	}
	
	
	/* plugin API */
	var $plugins = array();
	function loadPlugin ($name) {
		static $fs;
		require_once(WPRO_DIR.'core/libs/wproFilesystemBase.class.php');
		$fs = new wproFilesystemBase();
		$name = $this->makeVarOK($name);
		if (!isset($this->plugins[$name])) {
			if (!wpro_class_exists('wproPlugin_'.$name)) {
				if (substr($name, 0, 9) == 'wproCore_') {
					$dir = WPRO_DIR.'core/plugins/';
				} else {
					$dir = WPRO_DIR.'plugins/';
				}
				if (!$fs->includeFileOnce($name, $dir, '/plugin.php')) {
					return false;
				} else if (!wpro_class_exists('wproPlugin_'.$name)) {
					return false;
				}
			}
			
			@eval ('$this->plugins["'.$name.'"] = new wproPlugin_'.$name.'();');
			
			if (method_exists($this->plugins[$name],'init')) {
				$this->plugins[$name]->init($this);
			}
			
			return true;
		} else {
			return false;
		}
	}
	function loadPlugins ($plugins) {
		$return = array();
		foreach ($plugins as $plugin) {
			$p = $this->loadPlugin($plugin);
			array_push($return, $p);
		}
		return $return;
	}
	function unloadPlugin ($name) {
		$name = $this->makeVarOK($name);
		if ($this->pluginIsLoaded($name)) {
			$p=&$this->plugins[$name];
			if (method_exists($p,'unload')) {
				$p->unload($this);
			}
			unset($this->plugins[$name]);
			return ($this->pluginIsLoaded($name)?false:true);
		} else {
			return false;
		}
	}
	
	function addMediaPlugin($name) {
		$name = $this->makeVarOK($name);
		$arr = explode(',', $this->allowedMediaPlugins);
		array_push($arr, $name);
		$this->allowedMediaPlugins = implode(',',$arr);
	}
	
	function triggerEvent($action='onMakeEditor', $params=NULL) {
		$ret = array();
		foreach ($this->plugins as $k=>$v) {
			if (is_object($this->plugins[$k])) {
				if (method_exists($v,$action)) {
					if ($return = $this->plugins[$k]->$action($this,$params)) {
						array_push($ret, $return);
					}
				}
			}
		}
		return $ret;
	}
	
	function pluginIsLoaded($name) {
		if (isset($this->plugins[$name])) {
			return true;
		} else {
			return false;
		}
	}
	
	/* JS API */
	var $JSPlugins = array();
	function addJSPlugin ($name, $scriptName='') {
		$name = $this->makeVarOK($name);
		if (!isset($this->JSPlugins[$name])) {
			$this->JSPlugins[$name] = $scriptName;
			return true;
		} else {
			return false;
		}
	}
	
	function JSPluginIsLoaded ($name) {
		if (isset($this->JSPlugins[$name])) {
			return true;
		} else {
			return false;
		}
	}
	// html filters
	var $JSHTMLFilters = array();
	function addJSHTMLFilter($filterName, $functionName) {
		array_push($this->JSHTMLFilters, array($filterName,$functionName));
	}
	// JS events
	var $JSEditorEvents = array();
	function addJSEditorEvent($eventName, $functionName) {
		$this->addJSEvent($eventName, $functionName);
	}
	function addJSEvent($eventName, $functionName) {
		array_push($this->JSEditorEvents, array($eventName,$functionName));
	}
	// button state handlers
	var $JSBSH = array();
	function addJSButtonStateHandler($handlerName, $functionName) {
		$this->JSBSH[$handlerName] = $functionName;
	}
	// formatting handlers
	var $JSFH = array();
	function addJSFormattingHandler($handlerName, $functionName) {
		$this->JSFH[$handlerName] = $functionName;
	}
	// formatting value query handlers
	var $JSFVH = array();
	function addJSFormattingValueHandler($handlerName, $functionName) {
		$this->JSFVH[$handlerName] = $functionName;
	}
	//
	
	function _getDisplayAbove () {
		return join('',$this->displayAbove);
	}
	function _getDisplayBelow () {
		return join('',$this->displayBelow);
	}
	function addOutputAbove ($html) {
		array_push($this->displayAbove, $html);
	}
	function addOutputAboveOnce ($html) {
		if (!wproOutputAdded($html)) {
			array_push($this->displayAbove, $html);
			return true;
		} else {
			return false;
		}
	}
	function addOutputBelowOnce ($html) {
		if (!wproOutputAdded($html)) {
			array_push($this->displayBelow, $html);
			return true;
		} else {
			return false;
		}
	}
	function addOutputBelow ($html) {
		array_push($this->displayBelow, $html);
	}
	
	function _getConfigJS () {
		return join('',$this->configJS);
	}
	function addConfigJS ($JS) {
		array_push($this->configJS, $JS);
	}
	
	
	/* public methods */
	
	function addDirectory($o) {
		$this->directories[] = $o;
	}
	
	function getDirectories ($type='') {	
		return include(dirname(__FILE__).'/wysiwygPro/getDirectories.inc.php');
	}
	
	function getDirById ($ID) {
		$dirs = $this->getDirectories ();

		foreach ($dirs as $dir) {
			if ($dir->id == $ID) {
				return $dir;
			}
		}
		return false;
	}
	
	/* register context menu */
	
	/* registers MenuButton (menu button has a list of options) */
	function registerMenuButton ($idname, $tooltip, $options, $url, $width=22, $height=22, $cid = '', $textButton=false) {
		$this->_buttonDefinitions[strtolower($idname)] = array('menu',$tooltip,$options,$url,$width,$height,$cid,$textButton);
	}
	
	/* registers a drop menu (font font-size etc) */
	function registerSelect ($idname,$tooltip,$function,$options,$selected='',$width=82,$cid='') {
		$this->_buttonDefinitions[strtolower($idname)] = array('select',$tooltip,$function,$options,$selected,$width,$cid);
	}
	
	/* adds a new button definition */
	function registerButton ($idname, $tooltip, $function, $url, $width=22, $height=22, $cid = '', $textButton=false) {
		$this->_buttonDefinitions[strtolower($idname)] = array('button',$tooltip,$function,$url,$width,$height,$cid,$textButton);
	}
	
	/* adds a new text button definition */
	function registerTextButton ($idname, $text, $tooltip, $function, $url, $width=22, $height='', $cid = '') {
		$this->_buttonDefinitions[strtolower($idname)] = array('button',$tooltip,$function,$url,$width,$height,$cid,$text);
	}
	
	/* adds a new spacer definition */
	function registerSpacer ($idname, $width=22, $height=22) {
		$this->_buttonDefinitions[strtolower($idname)] = array('spacer',$width,$height);
	}
	
	/* adds a new separator definition */
	function registerSeparator ($idname) {
		$this->_buttonDefinitions[strtolower($idname)] = array('separator');
	}

	/* modifies a registered button */
	function setButtonFunction($idname, $function) {
		if (isset($this->_buttonDefinitions[strtolower($idname)])) {
			$this->_buttonDefinitions[strtolower($idname)][2] = $function;
		}
	}
	
	/* adds a registered button, menubutton, separator or spacer to the toolbar layout */
	function addRegisteredButton ($idname, $location) {
		$relative = '';
		$handle = '';
		$return = false;
		$location = strtolower($location);
		$idname = strtolower($idname);
		if (substr($location, 0, 6) == 'after:') {
			$handle = substr($location, 6);
			$relative = 'button';
			$action = 'after';
		} else if (substr($location, 0, 7) == 'before:') {
			$handle = substr($location, 7);
			$relative = 'button';
			$action = 'before';
		} else if (substr($location, 0, 6) == 'start:') {
			$handle = substr($location, 6);
			if (empty($handle)) $handle = 0;
			$relative = 'toolbar';
			$action = 'start';
		} else if (substr($location, 0, 4) == 'end:') {
			$handle = substr($location, 4);
			if ($handle=='') $handle = (count($this->toolbarLayout)-1);
			$relative = 'toolbar';
			$action = 'end';
		} else if (substr($location, 0, 5) == 'start') {
			$handle = 0;
			$relative = 'toolbar';
			$action = 'start';
		} else if (substr($location, 0, 3) == 'end') {
			$handle = (count($this->toolbarLayout)-1);
			$relative = 'toolbar';
			$action = 'end';
		} else {
			return false;
		}
		switch ($relative) {
			case 'button' :
				// since some buttons have been renamed since version two, we need to check that this button isn't actually now just a feature reference.
				if (isset($this->_featureDefinitions[$handle])) {
					if (count($this->_featureDefinitions[$handle][0])) {
						$handle = $this->_featureDefinitions[$handle][0][0];
					}
				}
				foreach ($this->toolbarLayout as $toolbarId => $buttons) {
					$newArr = array();
					foreach ($buttons as $buttonId) {
						if (strtolower($buttonId) == $handle) {
							if ($action=='before') {
								array_push($newArr, $idname);
								array_push($newArr, $buttonId);
								$return = true;
							} else if ($action=='after') {
								array_push($newArr, $buttonId);
								array_push($newArr, $idname);
								$return = true;
							}
						} else {
							array_push($newArr, $buttonId);
						}
					}
					$this->toolbarLayout[$toolbarId] = $newArr;
				}
				break;
			case 'toolbar' ;
				$i = 0;
				foreach ($this->toolbarLayout as $toolbarId => $buttons) {
					settype($i, "string");
					if (strtolower($toolbarId)==$handle||$handle==$i) {
						if ($action=='start') {
							$this->toolbarLayout[$toolbarId] = array_merge(array($idname), $buttons);
							$return = true;
						} else if ($action=='end') {
							array_push($this->toolbarLayout[$toolbarId], $idname);
							$return = true;
						}
					}
					settype($i, "integer");
					$i++;
				}
				break;
		}
		return $return;
	}
	
	function clearToolbarLayout() {
		$this->toolbarLayout = array();
	}
	
	function addToolbar($idName, $buttons) {
		if (is_array($buttons)) {
			$this->toolbarLayout[$idName] = $buttons;
		}
	}
	
	function removeToolbar($idName) {
		if (isset($this->toolbarLayout[$idName])) {
			unset($this->toolbarLayout[$idName]);
		} else if (is_int($idName)) {
			$i = 0;
			foreach ($this->toolbarLayout as $toolbarId => $buttons) {
				if ($idName==$i) {
					unset($this->toolbarLayout[$toolbarId]);
					break;
				}
				$i++;
			}
		}
	}
	
	/* context menu */
	function clearContextMenu() {
		$this->contextMenu = array();
	}
	
	function addContextMenuItem ($idname, $location) {
		$relative = '';
		$handle = '';
		$return = false;
		$location = strtolower($location);
		$idname = strtolower($idname);
		if (substr($location, 0, 6) == 'after:') {
			$handle = substr($location, 6);
			$relative = 'button';
			$action = 'after';
		} else if (substr($location, 0, 7) == 'before:') {
			$handle = substr($location, 7);
			$relative = 'button';
			$action = 'before';
		} else if ($location == 'start') {
			$relative = 'menu';
			$action = 'start';
		} else if ($location == 'end') {
			$relative = 'menu';
			$action = 'end';
		} else {
			return false;
		}
		switch ($relative) {
			case 'button' :
				// since some buttons have been renamed since version two, we need to check that this button isn't actually now just a feature reference.
				if (isset($this->_featureDefinitions[$handle])) {
					if (count($this->_featureDefinitions[$handle][0])) {
						if ($action=='before') {
							$handle = $this->_featureDefinitions[$handle][0][0];
						} else if ($action=='after') {
							$handle = $this->_featureDefinitions[$handle][0][count($this->_featureDefinitions[$handle][0])-1];
						}
					}
				}
				$newArr = array();
				foreach ($this->contextMenu as $buttonId) {
					if (strtolower($buttonId) == $handle) {
						if ($action=='before') {
							array_push($newArr, $idname);
							array_push($newArr, $buttonId);
							$return = true;
						} else if ($action=='after') {
							array_push($newArr, $buttonId);
							array_push($newArr, $idname);
							$return = true;
						}
					} else {
						array_push($newArr, $buttonId);
					}
				}
				$this->contextMenu = $newArr;
				break;
			case 'menu' ;
				if ($action=='start') {
					$this->contextMenu = array_merge(array($idname), $this->contextMenu);
					$return = true;
				} else if ($action=='end') {
					array_push($this->contextMenu, $idname);
					$return = true;
				}
				break;
		}
		return $return;
	}
	
	function removeContextMenuItem($idname) {
		$newMenu = array();
		foreach ($this->contextMenu as $button) {
			if ($button != $idname) {
				array_push($newMenu, $button);
			}
		}
		$this->contextMenu = $newMenu;
		//sort($this->contextMenu);
	}
	
	
	function clearStylesMenu () {
		$this->stylesMenu = array();
	}
	
	/* adds a style definition to the styles menu */
	function addStyle($tag, $label) {
		$this->stylesMenu[$tag] = $label;
	}
	
	/* removes a style definition from the styles menu */
	function removeStyle($tag) {
		if (isset($this->stylesMenu[$tag])) {
			unset($this->stylesMenu[$tag]);
		}
	}
	
	function clearFontMenu () {
		$this->fontMenu = array();
	}
	/* adds a font definition to the font menu */
	function addFont($fontName) {
		array_push($this->fontMenu, $fontName);
		$this->fontMenu = array_unique($this->fontMenu);
		sort($this->fontMenu);
	}
	/* removes a font definition from the styles menu */
	function removeFont($fontName) {
		//$i = 0;
		foreach ($this->fontMenu as $font) {
			if ($font == $fontName) {
				unset($font);
				break;	
			}
			//$i ++;
		}
		sort($this->fontMenu);
		
	}
	
	function clearSizeMenu () {
		$this->sizeMenu = array();
	}
	/* adds a size definition to the styles menu */
	function addSize($tag, $label='') {
		$this->sizeMenu[$tag] = empty($label) ? $tag : $label;
	}
	/* removes a size definition from the styles menu */
	function removeSize($tag) {
		if (isset($this->sizeMenu[$tag])) {
			unset($this->sizeMenu[$tag]);
		}
	}
	
	function addColorSwatch($swatch) {
		array_push($this->colorSwatches, $swatch);
	}
	
	
	/* adds a snippet */
	function addSnippet($name, $value) {
		$this->snippets[$name] = $value;
	}
	
	/* removes a snippet */
	function removeSnippet($name) {
		unset($this->snippets[$name]);
	}
	
	function addStyleSheet($url) {
		array_push($this->stylesheets, $url);
	}
	
	function addCSSText($text) {
		$this->cssText .= $text;
	}
	
	
	
	/* loads content from a text file */
	function loadValueFromFile ($file) {
		require_once(WPRO_DIR.'core/libs/wproFilesystemBase.class.php');
		$this->value = wproFilesystemBase::getContents($file);
	}
	
	/* loads content from an external web page over HTTP */
	function loadValueFromURL ($url, $authUser='', $authPass='', $proxyURL='', $proxyPort=0, $proxyUser='', $proxyPass='') {
		$this->baseURL = $url;
		require_once(WPRO_DIR.'core/libs/wproWebAgent.class.php');
		$fs = new wproWebAgent();
		$fs->proxyURL = $proxyURL;
		$fs->proxyPort = $proxyPort;
		$fs->proxyUser = $proxyUser;
		$fs->proxyPass = $proxyPass;
		$fs->authUser = $authUser;
		$fs->authPass = $authPass;
		if ($html = $fs->fetch($url)) {
			$this->value = $html;
		}
	}
	
	/* registers a feature */
	function registerFeature ($idname, $buttons=array(), $onenable='', $ondisable='') {
		$this->_featureDefinitions[strtolower($idname)]=array($buttons,$onenable,$ondisable);
	}
	
	/* registers a feature */
	function registerAndEnableFeature ($idname, $buttons=array(), $onenable='', $ondisable='') {
		$this->registerFeature ($idname, $buttons, $onenable,$ondisable);
		$this->enableFeature($idname);
	}
	
	function disableFeature($id) {
		if (!is_array($id)) {
			$id = strtolower($id);
			if (in_array($id, $this->features)) {
				unset($this->features[array_search($id, $this->features)]);
				if (isset($this->_featureDefinitions[$id])) {
					if (!empty($this->_featureDefinitions[$id][2])) {
						if (is_array($this->_featureDefinitions[$id][2])) {
							if (method_exists($this->_featureDefinitions[$id][2][0], $this->_featureDefinitions[$id][2][1])) {
								$this->_featureDefinitions[$id][2][0]->$this->_featureDefinitions[$id][2][1]();
							}
						} else if (function_exists($this->_featureDefinitions[$id][2])) {
							$this->_featureDefinitions[$id][2]($id,$this);
						}
					}
					if (!empty($this->_featureDefinitions[$id][0])) {
						foreach ($this->_featureDefinitions[$id][0] as $button) {
							if ($id != strtolower($button)) {
								$this->_remove(strtolower($button));
								//if (isset($this->features[$button])) unset($this->features[$button]);
								$this->disableFeature(strtolower($button));
							}
						}
					}
				}
			}

			$this->_remove($id);
		} else {
			$this->disableFeatures($id);
		}
	}
	
	// removes features (not just buttons).
	function disableFeatures($ids) {
		if (is_array($ids)) {
			foreach($ids as $id) {
				$this->disableFeature($id);
			}
		} else {
			$this->disableFeature($ids);
		}
	}
	
	function enableFeatures($ids) {
		if (is_array($ids)) {
			foreach ($ids as $id) {
				$this->enableFeature($id);				
			}
		} else {
			$this->enableFeature($id);
		}	
	}
	function enableFeature($id) {
		if (!is_array($id)) {
			$id = strtolower($id);
			if (!in_array($id, $this->features)) {
				array_push($this->features, $id);
				if (isset($this->_featureDefinitions[$id])) {
					if (!empty($this->_featureDefinitions[$id][1])) {
						if (is_array($this->_featureDefinitions[$id][1])) {
							if (method_exists($this->_featureDefinitions[$id][1][0], $this->_featureDefinitions[$id][1][1])) {
								$this->_featureDefinitions[$id][1][0]->$this->_featureDefinitions[$id][1][1]();
							}
						} else if (function_exists($this->_featureDefinitions[$id][1])) {
							$this->_featureDefinitions[$id][1]($id,$this);
						}
					}
				}
			}
		} else {
			$this->enableFeatures($id);
		}
	}
	
	function featureIsEnabled($id) {
		if (in_array(strtolower($id), $this->features)) {
			return true;
		} else {
			return false;
		}
	}
	
	function buttonIsEnabled ($id) {
		$id = strtolower($id);
		if (in_array($id, $this->_removeArray)) {
			return false;
		}
		foreach ($this->toolbarLayout as $k => $v) {
			if (in_array($id, $v)) {
				return true;
			}
			foreach($v as $buttonId) {
				$button = isset($this->_buttonDefinitions[strtolower($buttonId)]) ? $this->_buttonDefinitions[strtolower($buttonId)] : ''; 
				if (!is_array($button)) {continue;}
				if ($button[0] == 'menu') {
					if (in_array($id, $button[2])) {
						return true;
					}
				}
			}		
		}
		if (in_array($id, $this->contextMenu)) {
			return true;
		}		
		return false;
	}
	
	
	/* private methods */
	
	
	// adds button items to the remove array, expects an array of ids to add.
	function _remove($items) {
		if (is_array($items)) {
			$num = count($items);
			for ($i=0;$i<$num;$i++) {
				array_push($this->_removeArray, strtolower($items[$i]));
			}
		} else {
			array_push($this->_removeArray, strtolower($items));
		}
	}
	
	// fixes the code to be edited.
	function _fixHtml($code='') {
		//if (!empty($code)) {
			// strip out any PHP echo'd XML declaration because this can confuse Internet Explorer
			$code = preg_replace('/<\?php echo "<\?xml version=\"[0-9]\.[0-9]\" encoding=\"(.*?)\"\?"\.">"; \?>/si',  "", $code);
			// attempt to auto-detect the doctype
			if (empty($this->htmlVersion)) {
				$arr = array();
				// first try to base it on the specified document type
				if (preg_match('/\/\/DTD [X]*HTML [0-9.]*[ A-Z]*\/\//i', $this->doctype, $arr)) {
					$this->htmlVersion = strtoupper(preg_replace("/\/\/DTD ([X]*HTML [0-9.]*[ A-Z]*)\/\//i", "$1", $arr[0]));
				// else look for a doctype declaration in the code
				} else if (preg_match('/<\!DOCTYPE HTML PUBLIC "-\/\/W3C\/\/DTD [X]*HTML[^>]+>/i', $code, $arr)) {
					$this->doctype = $arr[0];
					$this->htmlVersion = strtoupper(preg_replace('/<\!DOCTYPE HTML PUBLIC "-\/\/W3C\/\/DTD ([X]*HTML [0-9.]*[ A-Z]*)\/\/[^>]+>/i', "$1", $arr[0]));
					// attempt to auto detect the xml declaration
					if (preg_match('/<\?xml version=\"[^>]+>/i', $code, $arr)) {
						$this->doctype = $arr[0]."\n".$this->xhtmlDoctype;
					}
				// else look for xhtml syntax
				} else if (preg_match('/<[^(\/>)]+\/>/i', $code)) {
					$this->htmlVersion = 'XHTML';
				// else use default document type
				} else {
					$this->htmlVersion = $this->defaultHTMLVersion;
				}
			}
			
			// set the doctype declaration
			$search = strtolower($this->htmlVersion);
			if (empty($this->doctype)) {
				if (substr($search, 0, 5)=='xhtml') {
					if (strstr($search, 'strict')) {
						$this->doctype = $this->xhtmlStrictDoctype;
					} elseif (strstr($search, 'xhtml 1.1')) {
						$this->doctype = $this->xhtml1_1Doctype;
					} else {
						$this->doctype = $this->xhtmlDoctype;
					}
				} else {
					if (strstr($search, 'strict')) {
						$this->doctype = $this->htmlStrictDoctype;
					} else {
						$this->doctype = $this->htmlDoctype;
					}
				}
			}
			
			// attempt to auto-detect the charset
			if (empty($this->htmlCharset)) {
				$arr = array();
				if (preg_match('/<meta http-equiv="Content-Type" content="text\/html; charset=[^"]+"[^>]*>/i', $code, $arr)) {
					$this->htmlCharset = preg_replace('/<meta http-equiv="Content-Type" content="text\/html; charset=([^"]+)"[^>]*>/i', "$1", $arr[0]);
				} else if (preg_match('/<\?xml version=\"[^"]+" encoding="[^"]+"\?>/i', $code, $arr)) {
					$this->htmlCharset = preg_replace('/<\?xml version=\"[^"]+" encoding="([^"]+)"\?>/i', "$1", $arr[0]);
				} else {
					$this->htmlCharset = $this->defaultHTMLCharset;
				}
			}
			
			// attempt to auto-detect the htmlLang
			if (empty($this->htmlLang)) {
				$arr = array();
				if (preg_match('/<html [^>]*lang="[^"]+"[^>]*>/i', $code, $arr)) {
					$this->htmlLang = preg_replace('/<html [^>]*lang="([^"]+)"[^>]*>/i', "$1", $arr[0]);
				}
			}
			
			// attempt to auto-detect the htmlDirection
			if (empty($this->htmlDirection)) {
				$arr = array();
				if (preg_match('/<html [^>]*dir="[^"]+"[^>]*>/i', $code, $arr)) {
					$this->htmlDirection = preg_replace('/<html [^>]*dir="([^"]+)"[^>]*>/i', "$1", $arr[0]);
				}
			}
			
			// attempt to auto-detect the baseURL
			$arr = array();
			if (preg_match('/<base [^>]*href="[^"]+"[^>]*>/i', $code, $arr)) {
				if (empty($this->baseURL)) {
					$this->baseURL = preg_replace('/<base [^>]*href="([^"]+)"[^>]*>/i', "$1", $arr[0]);
				}
				$this->jsBookmarkLinks = true;
			}
				
			// convert to htmlentities to make it safe to paste in a textarea	
			if (!$this->valueIsEscaped) {
				$code = htmlspecialchars($code, ENT_NOQUOTES);
			}
			
			return $code;
		//} else {
		
		//}
	}
	
	// perfroms browser detection and sets the internal browser variables.
	function _browserDetection() {
		
		$browser_string = strtolower($_SERVER["HTTP_USER_AGENT"]);
		
		$is_mac = strstr($browser_string, 'mac');
		$is_safari = strstr($browser_string, 'applewebkit');
		$is_opera = strstr($browser_string, 'opera');
		$is_konq = strstr($browser_string, 'khtml');
		$is_camino = strstr($browser_string, 'camino');
		$is_ie = strstr($browser_string, 'msie');
		$is_gecko = strstr($browser_string, 'gecko');
		if ($is_gecko && $is_camino) {
			$camino_version = preg_replace('/.*camino\//sm', '', $browser_string);
			$camino_version = substr ($camino_version, 0,3);
		}
		
		if ($is_ie && !$is_mac && !$is_opera && !$is_konq && !$is_gecko) {
			// ie detection
			$this->_browserType = 'msie';
			$ie_version = preg_replace('/.*msie/sm', '', $browser_string);
			$ie_version = substr ($ie_version, 0,4);
			$this->_browserVersion = $ie_version;
			
			$this->_unsupported = false;
		
		} else if ($is_gecko && (!$is_camino || ($is_camino && $camino_version >= 0.9)) && !$is_opera && !$is_konq && !$is_ie) {
			// gecko detection
			$this->_browserType = 'gecko';
			$gecko_version = preg_replace('/.*rv:/sm', '', $browser_string);
			if ($gecko_version == $browser_string) {
				$gecko_version = preg_replace('/.*rv/sm', '', $browser_string);
			}
			$gecko_version = substr ($gecko_version, 0,3);
			$this->_browserVersion = $gecko_version;
			
			$this->_unsupported = false;
			
		} else if ($is_safari && !$is_opera && !$is_ie) {
			// safari 
			$this->_browserType = 'safari';
			$safari_version = preg_replace('/.*applewebkit\//sm', '', $browser_string);
			$safari_version = substr ($safari_version, 0,3);
			$this->_browserVersion = $safari_version;
			
			$this->_unsupported = false;
			
		} else if ($is_konq && !$is_opera && !$is_ie) {
			// konqueror same as Safari?, not!
			$this->_browserType = 'konqueror';
			$konqueror_version = preg_replace('/.*khtml\//sm', '', $browser_string);
			$konqueror_version = substr ($konqueror_version, 0,3);
			$this->_browserVersion = $konqueror_version;
			
			if ($this->konquerorSupport) {
				$this->_unsupported = false;
			}
			
		} else if ($is_opera) {
			// Opera
			$this->_browserType = 'opera';
			$opera_version = preg_replace('/.*opera\//sm', '', $browser_string);
			$opera_version = substr ($opera_version, 0, 1);
			$this->_browserVersion = $opera_version;
			
			if ($this->operaSupport) {
				$this->_unsupported = false;
			}
		
		} else {
			// other crap
			$this->_browserType = 'unsupported';
			$this->_browserVersion = 0;
		}
		
	}
	
	function _jsEncode($string) {
		$return = '';
		for ($x=0; $x < strlen($string); $x++) {
			$return .= '%' . bin2hex($string[$x]);
		}
		return $return;
	}
	
	// returns the JS name to be used by an editor of the name $str
	function getJSName ($str='') {
		if (empty($str)) {
			$str = $this->name;
		}
		$str = $this->makeVarOK($str);
		if (is_numeric(substr($str, 0, 1)) || preg_match('/^(default)$/',$str)) {
			$str = 'wp'.$str;
		}
		return $str;
	}
	
	function getEditorURL() {
		if (empty($this->editorURL)) {
			require_once(WPRO_DIR.'config.inc.php');
			$EDITOR_URL = WPRO_EDITOR_URL;
			/* Routine for auto setting WPRO_EDITOR_URL, if this routine fails you will need to set it manually in the config file */
			if (empty($EDITOR_URL)) {	
				$wp_script_filename = isset($_SERVER["SCRIPT_FILENAME"]) ? str_replace(array('\\','\\\\','//'),'/',$_SERVER["SCRIPT_FILENAME"]) : str_replace(array('\\','\\\\','//'),'/',$_SERVER["PATH_TRANSLATED"]);
				$wp_script_name = $_SERVER["SCRIPT_NAME"];
				$wp_dir_name = str_replace(array('\\','\\\\','//'),'/',WPRO_DIR);
				$EDITOR_URL = preg_replace( '/^(.*?)'.str_replace('/','\/',quotemeta(preg_replace( '/'.str_replace('/','\/',quotemeta($wp_script_name)).'/i', '', $wp_script_filename))).'/i' , '', $wp_dir_name);
				if (strtolower($EDITOR_URL) == strtolower($wp_dir_name)) {die('<div><b>WysiwygPro config error</b>: Could not auto-detect URL or wysiwygPro folder. You need to set the WPRO_EDITOR_URL constant in config.inc.php, or set the editorURL property when constructing the editor.</div>');}
			}
		} else {
			$EDITOR_URL = $this->editorURL;
		}
		
		$EDITOR_URL = $this->addTrailingSlash($EDITOR_URL);
		// web URL (strip out domain etc)
		if (preg_match('/^http(|s):\/\/.*?\//si', $EDITOR_URL )) {
			$EDITOR_URL = preg_replace('/^http(|s):\/\/[^\/]+/si', '', $EDITOR_URL);
		}
		
		return $EDITOR_URL;
	}
	
	function editorLink($url) {
		if (!empty($this->route)) {
			$a = explode('?',$url);
			$u = $a[0];
			$q = (!isset($a[1])) ? '' : $a[1];
			return $this->route . (strstr($this->route, '?') ? '&' : '?') . 'wproroutelink=' . rawurlencode(str_replace(array('/','.php'),array('-',''),$u)) . '&'.$q;		
		} else {
			$inDialog = defined('WPRO_IN_DIALOG') ? true : false;
			return ($inDialog ? '' : $this->editorURL) . $url;	
		}
	}
	
	function _doBaseConfig ($checkSubsequent=true) {
		
		if (!$this->_baseConfigDone) {
			$this->_baseConfigDone = true;
			
			// load and store session
			// create a new WP session to store this data for the dialogs
			if ($this->_createSession) {
				require_once(WPRO_DIR.'core/libs/wproSession.class.php');
				$this->sess = new wproSession();
				if ($this->sess->usePHPEngine&&empty($this->sessionId)) {
					// no session id has been set so create one. 
					// We want to ensure that if a user refreshes the same editor they don't end up creating multiple sessions,
					// resulting in enormous session files and possibly out of memory problems.					
					/*$this->sessionId = (isset($_SERVER['SCRIPT_NAME']) ?$_SERVER['SCRIPT_NAME'] : (isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:'')).'-'.$this->name.'-'.serialize($_GET);*/
					$this->sessionId = serialize($this);
				}
				$this->sess->create ($this->sessionId);
				$this->sess->editor = &$this;
			}
			
			$this->sessionId  = isset($this->sess) ? $this->sess->sessionId : '';
						
			// common display stuff
			
			/* load template engine */
			require_once(WPRO_DIR.'core/libs/wproTemplate.class.php');  
			
			/**  
			* Create a template object for the outer template and set its variables.  
			*/  
			$this->template = new wproTemplate();
			$this->template->stringMode = true;
			$this->template->path = WPRO_DIR.'core/tpl/';
			
			
			// load defaults if user has not defined
			require_once(WPRO_DIR.'config.inc.php');

			// addresses and directories
			$this->editorURL = $this->getEditorURL();
			
			$LANG = WPRO_LANG;
			if ($this->_v2Mode == true) {
				$wpro_inDialog = false;
				require_once(WPRO_DIR.'core/libs/v2ConfigLoader.inc.php');
			}
			if (empty($this->emoticonDir)) {
				$this->emoticonDir = $this->varReplace(WPRO_EMOTICON_DIR, array('EDITOR_URL'=>$this->editorURL));
			}
			$this->emoticonDir = $this->addTrailingSlash($this->emoticonDir);
			
			if (empty($this->emoticonURL)) {
				$this->emoticonURL = $this->varReplace(WPRO_EMOTICON_URL, array('EDITOR_URL'=>$this->editorURL));
			}
			$this->emoticonURL = $this->addTrailingSlash($this->emoticonURL);
			
			if (empty($this->langFolderDir)) {
				$this->langFolderDir = $this->varReplace(WPRO_LANG_DIR, array('EDITOR_URL'=>$this->editorURL));
			}
			$this->langFolderDir = $this->addTrailingSlash($this->langFolderDir);
			
			if (empty($this->langFolderURL)) {
				$this->langFolderURL = $this->varReplace(WPRO_LANG_URL, array('EDITOR_URL'=>$this->editorURL));
			}
			$this->langFolderURL = $this->addTrailingSlash($this->langFolderURL);
			
			if (empty($this->themeFolderURL)) {
				$this->themeFolderURL = $this->varReplace(WPRO_THEME_URL, array('EDITOR_URL'=>$this->editorURL));
			}
			$this->themeFolderURL = $this->addTrailingSlash($this->themeFolderURL);
			if (empty($this->themeFolderDir)) {
				$this->themeFolderDir = $this->varReplace(WPRO_THEME_DIR, array('EDITOR_URL'=>$this->editorURL));
			}
			$this->themeFolderDir = $this->addTrailingSlash($this->themeFolderDir);
			if (empty($this->lang)) {
				$this->lang = $LANG;
			}
			if (empty($this->theme)) {
				$this->theme = WPRO_THEME;
			}
			$this->lang = preg_replace('/[^a-z0-9\-_]/si', '', $this->lang);
			$this->theme = preg_replace('/[^a-z0-9\-_]/si', '', $this->theme);
			
			if (!WPRO_USE_JS_SOURCE) {
				$this->template->addOutputFilter('wproTemplateJSSourceFilter');
			}
			
			$this->_sessRefresh = intval(WPRO_SESS_REFRESH);
						
			// check and fix variables
			
			// fix baseURL, baseURL should always start with the domain and end in a /
			if (!empty($this->baseURL)) {
				// append domain to start of base url if it has no domain
				if (substr($this->baseURL, 0, 1) == '/' ) {
					$this->baseURL = strtolower(substr($_SERVER['SERVER_PROTOCOL'],0,strpos($_SERVER['SERVER_PROTOCOL'],'/')) . (isset($_SERVER['HTTPS']) ? ($_SERVER['HTTPS'] == "on" ? 's://' : '://') : '://' ) . $_SERVER['SERVER_NAME'] ).(isset($_SERVER['SERVER_PORT'])?':'.$_SERVER['SERVER_PORT']:'').$this->baseURL;
				}
				// if baseurl does not end in a slash, make it so:
				if (substr($this->baseURL, strlen($this->baseURL) - 1) != '/' ) {
					if (preg_match('/^http(|s):\/\/[^\/]*?$/si', $this->baseURL )) {
						$this->baseURL.='/';
					} else {
						$this->baseURL = preg_replace('/^(.*\/)[^\/]*?$/si', "$1", $this->baseURL);
					}
				}
			}
			
			// load the theme file
			if (!file_exists($this->themeFolderDir.$this->theme.'/wysiwygpro/editor.css')) {
				$this->theme = WPRO_THEME;
				$this->theme = preg_replace('/[^a-z0-9\-_]/si', '', $this->theme);
			}
			$this->themeURL = $this->themeFolderURL.$this->theme.'/wysiwygpro/';
			if (file_exists($this->themeFolderDir.$this->theme.'/wysiwygpro/theme.inc.php')) {
				require_once($this->themeFolderDir.$this->theme.'/wysiwygpro/theme.inc.php');
				$this->loadPlugin($this->theme.'Theme');
			}
			
			// name
			//$this->_originalName = $this->name;
			//$this->name = $this->getJSName();
			
			// url Format
			if (empty($this->urlFormat)) {
				$this->urlFormat = ($this->fullURLs?'absolute':'nodomain');
			} else if ($this->urlFormat=='absolute') {
				$this->fullURLs=true;
			}
			
			// is this editor subsequent?
			if ($checkSubsequent) {
				if ($this->subsequent == false && $this->_subsequent == false) {
					if (wproIsSubsequent()) {
						$this->_subsequent = true;
						$this->subsequent = true;
					}
				} else {
					$this->_subsequent = true;
					$this->subsequent = true;
				}
			}
			
			// detect browser version
			$this->_browserDetection();
			
			// load language Engine
			require_once(WPRO_DIR.'core/libs/wproLangLoader.class.php');
			$l = new wproLangLoader();
			$l->defaultLang = $LANG;
			$l->preferredLang = $this->lang;
			$l->langFolderDir = $this->langFolderDir;
			$l->langFolderURL = $this->langFolderURL;
			if (!empty($this->editorCharset)) {
				$l->convertCharset = $this->editorCharset;
			}
			$l->load($this);
			$this->langEngine = &$l;
						
			// load plugin language files
			foreach ($this->plugins as $k=>$v) {
				if (substr($k, 0, 9)=='wproCore_') continue;
				$this->langEngine->loadFile('wysiwygpro/includes/'.$k.'.inc.php');
			}
			
			// langauge specific
			if (empty($this->thumbnailFolderDisplayName)) {
				$this->thumbnailFolderDisplayName = $this->langEngine->get('core', 'thumbnailFolderDisplayName');
			}
		
		}
	
	}
	
	// makes the editor code
	function _makeEditor ($templateFile='') {
		
		// initiate html code to return.
		$code = '';
		
		$this->triggerEvent('onBeforeMakeEditorProcess');
		
		$this->_doBaseConfig ();
		
		// name
		$this->_originalName = $this->name;
		$this->name = $this->getJSName();
		
		// fix width and height.
		if (!preg_match('/^[0-9]+(\%|px|em|pt)$/', $this->width)) {
			if (intval($this->width) != 0) {
				$this->width = intval($this->width).'px';
			} else if ($this->width != 'auto' && $this->width != 'inherit') {
				$this->width = '100%';
			}
		}
		if (intval($this->height) != 0) {
			$this->height = intval($this->height).'px';
		} else {
			$this->height = '400px';
		}
		
		if ($this->_browserType == 'gecko') {
			$this->disableFeatures(array('nongecko'));
		}
		if ($this->_browserType == 'opera') {
			$this->disableFeatures(array('nonopera'));
		}
		if ($this->_browserType == 'safari' || $this->_browserType == 'konqueror') {
			$this->disableFeatures(array('nonsafari'));
		}
		
		// check stylesheets
		if (!empty($this->stylesheet)) {
			array_push($this->stylesheets, $this->stylesheet);
		}
		
		// append default font and background color
		if (!empty($this->defaultFont)) {
			$this->cssText.='body { font-face: '.$this->defaultFont.' }';
		}
		if (!empty($this->defaultBgColor)) {
			$this->cssText.='body { background-color: '.$this->defaultBgColor.' }';
		}
		
		// add theme stylesheet
		//array_push($this->stylesheets, $this->themeFolderURL.$this->theme.'/wysiwygpro/document.css');
		
		// add base editing styles
		//array_push($this->stylesheets, $this->editorURL.'core/css/document.css');
		
		// if strict DTD, remove depreciated features
		// moved to stylewithcss plugin
		
		if (empty($this->snippets)) {
			$this->_remove('snippets');
		}
		
		if (empty($this->fontMenu)) {
			$this->_remove('font');
		}
		if (empty($this->sizeMenu)) {
			$this->_remove('size');
		}
		
		if (empty($this->stylesMenu)) {
			$this->_remove('styles');
		}
		
		// fix dirs
				
		// replace styles menu vars
		foreach ($this->stylesMenu as $tag=>$label) {
			$tag = str_replace(array('<','>'),'',trim($tag)); $label = trim($label);
			if ($this->langEngine->get('editor',str_replace('#','',$label)) != $label) {
				$this->stylesMenu[$tag] = $this->langEngine->get('editor',str_replace('#','',$label));
			}		
		}
		foreach ($this->fontMenu as $tag=>$label) {
			$tag = trim($tag); $label = trim($label);
			if ($this->langEngine->get('editor',str_replace('#','',$label)) != $label) {
				$this->fontMenu[$tag] = $this->langEngine->get('editor',str_replace('#','',$label));
			}		
		}
		foreach ($this->sizeMenu as $tag=>$label) {
			$tag = trim($tag); $label = trim($label);
			if ($this->langEngine->get('editor',str_replace('#','',$label)) != $label) {
				$this->sizeMenu[$tag] = $this->langEngine->get('editor',str_replace('#','',$label));
			}	
		}
		
		// create various styles menu options
		$this->paragraphStyles = array();
		$this->textStyles = array();
		$this->linkStyles = array();
		$this->rulerStyles = array();
		$this->imageStyles = array();
		$this->tableStyles = array();
		$this->rowStyles = array();
		$this->cellStyles = array();
		$this->listStyles = array();
		$this->listItemStyles = array();
		
		$this->selectStyles = array();
		$this->textareaStyles = array();
		$this->textInputStyles = array();
		$this->buttonInputStyles = array();
		$this->radioInputStyles = array();
		$this->checkboxInputStyles = array();
		$this->imageInputStyles = array();
		$this->fileInputStyles = array();
		$this->inputStyles = array();
		
		foreach ($this->stylesMenu as $tag=>$label) {
			$tagName = strtolower(preg_replace("/ [^>]+/si", '', $tag));
			if (preg_match("/^(\*block\*|div|p|h1|h2|h3|h4|h5|h6|address|pre|blockquote)$/si",$tagName)) {
				$this->paragraphStyles[$tag] = $label;
			} else if (preg_match("/^(abbr|acronym|b|bdo|big|cite|code|dfn|em|font|i|kbd|label|nobr|q|s|samp|small|span|strike|strong|sub|sup|tt|u|var)$/si",$tagName)) {
				$this->textStyles[$tag] = $label;
			} else if ($tagName == 'a') {
				$this->linkStyles[$tag] = $label;
			} else if ($tagName == 'hr') {
				$this->rulerStyles[$tag] = $label;
			} else if ($tagName == 'img') {
				$this->imageStyles[$tag] = $label;
			} else if ($tagName == 'object'||$tagName == 'embed') {
				$tag = preg_replace("/^(object|embed)/si", 'img', $tag);
				$this->imageStyles[$tag] = $label;
			} else if ($tagName == 'table') {
				$this->tableStyles[$tag] = $label;
			} else if ($tagName == 'tr') {
				$this->rowStyles[$tag] = $label;
			} else if ($tagName == 'td' || $tagName == 'th') {
				$this->cellStyles[$tag] = $label;
			} else if ($tagName == 'ol' || $tagName == 'ul') {
				$this->listStyles[$tag] = $label;
			} else if ($tagName == 'li') {
				$this->listItemStyles[$tag] = $label;
			} else if ($tagName == 'input') {
				if (stristr($tag, 'type="button"') || stristr($tag, 'type="submit"') || stristr($tag, 'type="reset"')) {
					$this->buttonInputStyles[$tag] = $label;
				} else if (stristr($tag, 'type="text"') || stristr($tag, 'type="password"')) {
					$this->textInputStyles[$tag] = $label;
				} else if (stristr($tag, 'type="checkbox"')) {
					$this->checkboxInputStyles[$tag] = $label;
				} else if (stristr($tag, 'type="radio"')) {
					$this->radioInputStyles[$tag] = $label;
				} else if (stristr($tag, 'type="image"')) {
					$this->imageInputStyles[$tag] = $label;
				} else if (stristr($tag, 'type="file"')) {
					$this->fileInputStyles[$tag] = $label;
				} else {
					$this->inputStyles[$tag] = $label;
				}
			} 
		}
		
		asort($this->paragraphStyles);
		asort($this->textStyles);
		asort($this->linkStyles);
		asort($this->imageStyles);
		asort($this->tableStyles);
		asort($this->rowStyles);
		asort($this->cellStyles);
		asort($this->listStyles);
		asort($this->listItemStyles);
		
		asort($this->textareaStyles);
		asort($this->textInputStyles);
		asort($this->selectStyles);
		asort($this->buttonInputStyles);
		asort($this->radioInputStyles);
		asort($this->checkboxInputStyles);
		asort($this->imageInputStyles);
		asort($this->fileInputStyles);
		asort($this->inputStyles);
		
		$this->fontMenu = array_unique($this->fontMenu);
		sort($this->fontMenu);
		$this->sizeMenu = array_unique($this->sizeMenu);
		asort($this->sizeMenu);	
		
		
		// remove buttons in the remove array
		if (!empty($this->_removeArray)) {
			$newArray = array();
			foreach ($this->toolbarLayout as $k => $v) {
				if (in_array(strtolower($k), $this->_removeArray)) {
					continue;
				} else {
					$a2 = array();
					foreach ($v as $buttonId) {
						if (in_array(strtolower($buttonId), $this->_removeArray)) {
							continue;
						} else {
							// check menu button items
							$button = isset($this->_buttonDefinitions[$buttonId]) ? $this->_buttonDefinitions[$buttonId] : ''; 
							if (is_array($button)) {
								if ($button[0] == 'menu') {
									$newButtonMenuArr = array();
									foreach($button[2] as $s) {
										if (in_array(strtolower($s), $this->_removeArray)) {
											continue;
										} else {
											array_push($newButtonMenuArr, $s);
										}
									}
									$this->_buttonDefinitions[$buttonId][2] = $newButtonMenuArr;
								}
							}
							array_push($a2, $buttonId);
						}
					}
					$newArray[$k] = $a2;
				}			
			}		
			$this->toolbarLayout = $newArray;
			// remove context items
			$a2 = array();
			$num = count($this->contextMenu);
			for ($i=0;$i<$num;$i++) {
				if (in_array(strtolower($this->contextMenu[$i]), $this->_removeArray)) {
					continue;
				} else {
					array_push($a2, $this->contextMenu[$i]);
				}
			}
			$this->contextMenu = $a2;
		}
				
		/* assign language vars */
		foreach ($this->_buttonDefinitions as $k => $v) {
			if (empty($v[1])) {
				$this->_buttonDefinitions[$k][1] = $this->langEngine->get('editor',$k);
			} else {
				$this->_buttonDefinitions[$k][1] = $this->langEngine->get('editor',$v[1]);
			}
		}
		
		/* fix html */
		$this->value = $this->_fixHtml($this->value);
		
		$this->triggerEvent('onAfterFixHTML');
		
		$this->template->bulkAssign(array(
			
			'sid' => $this->_jsEncode((isset($this->sess) ? $this->sess->sessionName : 'wprosid').'='.(isset($this->sess) ? $this->sess->sessionId : '') ),
			'sessRefresh' => $this->_sessRefresh,
			
		));
		
		$this->template->assignByRef('EDITOR', $this);
		
		/* loop through styles assigning vars as needed */
		
		/* run plugins */
		$this->triggerEvent('onBeforeMakeEditor');
		
		/**  
		* Echo the results.  
		*/
		if (empty($templateFile)) {
			if (($this->_browserType == 'msie' && $this->_browserVersion >= 6) 
			|| ($this->_browserType == 'gecko' && $this->_browserVersion >= 1.3)
			|| ($this->_browserType == 'safari' && $this->_browserVersion >= 522 && !$this->_unsupported)
			|| ($this->_browserType == 'konqueror' && $this->_browserVersion >= 4.0 && !$this->_unsupported)
			|| ($this->_browserType == 'opera' && $this->_browserVersion >= 9 && !$this->_unsupported)) {
				if ($this->_browserType == 'konqueror') $this->_browserType = 'safari';
				$templateFile = 'editor.tpl.php'; 
			} else {
				$templateFile = 'textarea.tpl.php'; 
			} 
		} 
		$code = $this->template->fetch($templateFile);
		
		// placeholders, back from version 2
		$code = $this->varReplace($code, 
		array(
			// do language stuff.
			// language and some variables are pasted in after template processing so people can use things like ##varname## and have them replaced.
			
			'save' => $this->langEngine->get('editor', 'save'),
			'send' => $this->langEngine->get('editor', 'send'),
			'post' => 	$this->langEngine->get('editor', 'post'),		
			
			// other stuff
			'name' => $this->name,
			'_originalName' => $this->_originalName,
			'originalName' => $this->_originalName,
			'value' => $this->value,
			'address' => $this->editorURL,
			'directory' => $this->editorURL,
			'editorURL' => $this->editorURL,
			'EDITOR_URL' => $this->editorURL,
			'charset' => $this->htmlCharset,
			'themeURL' => $this->themeFolderURL.$this->theme.'/wysiwygpro/',
			'buttonURL' => $this->themeFolderURL.$this->theme.'/wysiwygpro/buttons/',
			
			'wpsid' => (isset($this->sess) ? $this->sess->sessionName : 'wprosid').'='.(isset($this->sess) ? $this->sess->sessionId : ''),
			'phpsid' => $this->appendSid ? strip_tags(defined('SID') ? SID : '') : '',
			
			
		));
		
		if (isset($this->sess)) {
			$this->sess->save($this);
		}
		
		// return html
		//if (!$this->_subsequent) {
		$brandMessage = '<!-- 
 ****************************************************************
 * '.$this->productName.' '.$this->version.' '.$this->copyright.' 
 * Available from: www.WysiwygPro.com. 
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code or related graphics is a violation of copyright law.
 ****************************************************************
-->';
		if (!wproOutputAdded($brandMessage)) {
			$code = $brandMessage.$code;
		} 
				
		$this->triggerEvent('onAfterMakeEditor');
		
		return $code;
		
	}
	
	function displayHeadContent() {
		echo $this->fetchHeadContent();
	}
	
	function fetchHeadContent() {
		$this->_doBaseConfig ();
		
		$code = '';
		if ($this->_browserType != 'unsupported' && !$this->_unsupported) {
		
			$code .= $this->fetchStyleSheets();
			
			// display common editor JavaScript...
			$code .= $this->fetchSharedJS();
			
			$code .= $this->fetchCoreEditorJS();
			
			$this->_subsequent = true;
			if (!WPRO_USE_JS_SOURCE) {
				$code = wproTemplateJSSourceFilter($code);
			}
		}
		return $code;
	}
	
	// outputs stylesheets
	function fetchStyleSheets() {
		$this->_doBaseConfig ();
	
		$themeURL = $this->themeFolderURL.$this->theme.'/wysiwygpro/';
		$langURL = $this->langFolderURL.$this->langEngine->actualLang.'/wysiwygpro/';
		
		$code = '';
		
		// display common editor CSS...
		$editorStyles = '<link rel="stylesheet" type="text/css" href="'.$this->editorURL.'core/css/editor.css?v='.$this->version.'" />';
		$themeStyles = '<link rel="stylesheet" type="text/css" href="'.$themeURL.'editor.css?v='.$this->version.'" />';
		$langStyles = '<link rel="stylesheet" type="text/css" href="'.$langURL.'editor.css?v='.$this->version.'" />';
		
		if (!wproOutputAdded($editorStyles)) {
			$code .= $editorStyles;
		}
		if (!wproOutputAdded($themeStyles)) {
			$code .= $themeStyles;
		}
		if (!wproOutputAdded($langStyles)) {
			$code .= $langStyles;
		}
		
		/*
		// fix for error in IE 6 ???
		if ($this->_browserType=='msie' && $this->_browserVersion < 7) {
			$ie_compatibility = '<!--[if lt IE 7]><link rel="stylesheet" type="text/css" href="'.$this->editorURL.'core/css/editor_ie6.css?v='.$this->version.'" /><![endif]-->';
			if (!wproOutputAdded($ie_compatibility)) {
				$code .= $ie_compatibility;
			}
		}
		*/
	
		return $code;
	}
	
	// outputs shared js content
	function fetchSharedJS() {
		require_once(WPRO_DIR.'config.inc.php');
		$STR = '';
		//if (!$this->_subsequent&&!$this->_dialogJSDone) {
		if (!wproOutputAdded('_wpsharedjs_')) {
			if (WPRO_COMPILE_JS_INCLUDES == true) {
				$STR .= '<script src="'.htmlspecialchars($this->editorLink('core/compileSharedJS.php?iframeDialogs=1&v='.$this->version)).'" type="text/javascript"></script>';
			} else {
				$STR .= '<script src="'.$this->editorURL.'js/dialogEditorShared_src.js?v='.$this->version.''.'" type="text/javascript"></script>';
				$STR .= '<script src="'.$this->editorURL.'core/js/wproPMenu_src.js?v='.$this->version.''.'" type="text/javascript"></script>';
				if ($this->iframeDialogs) {
					$STR .= '<script src="'.$this->editorURL.'core/js/dragiframe_src.js?v='.$this->version.''.'" type="text/javascript"></script>';
				};
			};
			if (!WPRO_USE_JS_SOURCE) {
				$STR = wproTemplateJSSourceFilter($STR);
			}
		}
		//};
		return $STR;
	}
	
	function fetchCoreEditorJS () {
		require_once(WPRO_DIR.'config.inc.php');
		$commonJS = '';
		if (!wproOutputAdded('_wpcoreeditorjs_')) {
			if (WPRO_COMPILE_JS_INCLUDES == true) {
				$commonJS .= '<script src="'.htmlspecialchars($this->editorLink('core/compileCoreEditorJS.php?browserType='.$this->_browserType.'&v='.$this->version)).'" type="text/javascript"></script>';
			} else {
				if ($this->_browserType == 'msie') {
					$commonJS .= '<script src="'.$this->editorURL.'core/js/ieSpecific_src.js?v='.$this->version.'" type="text/javascript"></script>';
				} elseif ($this->_browserType == 'gecko') {
					$commonJS .= '<script src="'.$this->editorURL.'core/js/mozSpecific_src.js?v='.$this->version.'" type="text/javascript"></script>';
				} elseif  ($this->_browserType == 'safari') {
					$commonJS .= '<script src="'.$this->editorURL.'core/js/safSpecific_src.js?v='.$this->version.'" type="text/javascript"></script>';
				} elseif  ($this->_browserType == 'opera') {
					$commonJS .= '<script src="'.$this->editorURL.'core/js/operaSpecific_src.js?v='.$this->version.'" type="text/javascript"></script>';
				}
				$commonJS .= '<script src="'.$this->editorURL.'core/js/editor_src.js?v='.$this->version.'" type="text/javascript"></script>';
			}
			$commonJS .= "<script type=\"text/javascript\">WPro.version='".addslashes($this->version)."';WPro.URL='".addslashes($this->editorURL)."';WPro.route='".addslashes($this->route)."';WPro.browserType='".addslashes($this->_browserType)."';WPro.browserVersion=".preg_replace("/[^0-9.]/si", '', $this->_browserVersion).";WPro._setBrowserTypeStrings();WPro.phpsid='".addslashes($this->appendSid ? strip_tags(defined('SID') ? SID : '') : '')."';</script>";
			if (!WPRO_USE_JS_SOURCE) {
				$commonJS = wproTemplateJSSourceFilter($commonJS);
			}
		}
		return $commonJS;
	}

	
	// displays the editor
	function display ($width=0, $height=0) {
		if ($width!=0) {
			$this->width = $width;
		}
		if ($height!=0) {
			$this->height = $height;
		}
		echo $this->_makeEditor ();
	}
	
	// returns the html for the editor
	function fetch ($width=0, $height=0) {
		if ($width!=0) {
			$this->width = $width;
		}
		if ($height!=0) {
			$this->height = $height;
		}
		return $this->_makeEditor ();
	}
	
	
	/* Image Selector */
	function _makeFileBrowserJS ($functionName='OpenFileBrowser', $inEditor=false) {
		$this->_doBaseConfig (false);
		
		$functionName = $this->getJSName($functionName);
		
		$themeURL = $this->themeFolderURL.$this->theme.'/wysiwygpro/';
		$langURL = $this->langFolderURL.$this->langEngine->actualLang.'/wysiwygpro/';
		
		$code = $this->fetchSharedJS();	
		
		$url = 	addslashes($this->editorURL);
		$sid = 'unescape(\''.addslashes($this->_jsEncode((isset($this->sess) ? $this->sess->sessionName : 'wprosid').'='.(isset($this->sess) ? $this->sess->sessionId : '') )).'\')';
		$phpsid = addslashes($this->appendSid ? strip_tags(defined('SID') ? SID : '') : '');
		$append = addslashes(!empty($this->appendToQueryStrings) ? $this->appendToQueryStrings : '');
		$route = addslashes($this->route);
		
		$this->_dialogJSDone = true;
		//baseurl, sid, iframe, phpsid, appendToQueryStrings
		$code .= '<script type="text/javascript">
/*<![CDATA[ */
function '.$functionName.' (type, returnFunction, getFunction) {
	wproOpenFileBrowser(type, returnFunction, getFunction, \''.$url.'\', '.$sid.', '.(/*$this->iframeDialogs?'true':*/'false').', \''.$phpsid.'\', \''.addslashes($this->appendToQueryStrings).'\', \''.$route.'\');
}
';
if (!$inEditor) {
	$code .= 'wpro_sessTimeout("WPRO_FB_'.addslashes($functionName).'", '.$sid.', \''.$phpsid.'\',  \''.$url.'\',  \''.$append.'\', '.intval(WPRO_SESS_REFRESH).', \''.$route.'\');
';
}
$code .= '/* ]]>*/	
</script>';

		/*if ($this->iframeDialogs) {
			$code .= '<div>
		<iframe '.(isset($_SERVER['HTTPS']) ? (($_SERVER['HTTPS']=='on') ? 'src="'.htmlspecialchars($this->editorURL).'core/html/iframeSecurity.htm" ' : '') : '').'class="wproFloatingDialog" id="wpfileBrowser_dialogFrame" name="wpfileBrowser_dialogFrame" frameborder="0" scrolling="no" bgcolor="#ffffff"></iframe></div>';
		}*/
		
		if (isset($this->sess)&&!$inEditor) {
			$this->sess->save($this);
		}

		return $code;
	}
	
	function displayFileBrowserJS ($functionName='OpenFileBrowser', $inEditor=false) {
		echo $this->_makeFileBrowserJS ($functionName, $inEditor);
	}
	
	function fetchFileBrowserJS ($functionName='OpenFileBrowser', $inEditor=false) {
		return $this->_makeFileBrowserJS ($functionName, $inEditor);
	}


	/* old vertsion 2 functions */
	
	function set_name($name='htmlCode') {
		if (!empty($name)) {
			$this->name = $name;
		}
	}
	
	function set_code($code='') {
		if (!empty($code)) {
			// don't use stripslashes to avoid issues with null characters.
			$code = str_replace(array("\\\\", "\\\"", "\\\'", "\r\n", "\r"), array("\\", "\"", "\'", "\n", "\n"), $code);
			$this->value = $code;
		}
	}

	function set_xhtml_lang($lang="en") {
		if (!empty($lang)) {
			$this->htmlLang = $lang;
		}
	}
	
	function set_encoding($encoding="iso-8859-1") {
		if (!empty($encoding)) {
			$this->htmlCharset = $encoding;
		}
	}
	
	function set_doctype($doctype='') {
		if (!empty($doctype)) {
			$doctype = str_replace(array("'","\r\n","\r","\n"),array("\'",'\n','\n','\n'),$doctype);
			$this->doctype = $doctype;
		}
	}
	
	function set_charset($charset='iso-8859-1') {
		if (!empty($charset)) {
			$this->htmlCharset = $charset;
		}
	}
	
	function usexhtml($usexhtml=true, $encoding="", $lang="") {
		if ($usexhtml) {
			$this->htmlVersion = 'XHTML';
			if (!empty($encoding)) {
				$this->htmlCharset = $encoding;
			}
			if (!empty($lang)) {
				$this->htmlLang($lang);
			}
		}
	}
	
	function usep($usep=true) {
		if ($usep) {
			$this->lineReturns = 'P';
		} else {
			$this->lineReturns = 'DIV';
		}
	}
	
	function set_baseurl($baseURL='') {
		if (!empty($baseURL)) {
			$this->baseURL = $baseURL;
		}
	}
	
	
	function set_stylesheet($stylesheet='') {
		if (!empty($stylesheet)) {
			array_push($this->stylesheets, $stylesheet);
		}
	}
	
	
	function set_instance_lang($lang_file="en-us.php") {
		if (!empty($lang_file)) {
			$this->lang = str_replace('.php', '', $lang_file);
		}
	}
	
	
	function guidelines_visible($show = true) {
		$this->guidelines = $show ? true : false;
	}
	
	
	function subsequent($subsequent=true) {
		$this->_subsequent = $subsequent ? true : false;
	}
	
	
	function removeButtons($removearray='') {
		if (!empty($removearray)) {
			if (is_array($removearray)) {
				$arr = $removearray;
			} else {
				$arr = explode(',', $removearray);
			}
			$this->disableFeatures($arr);
		}
	}
	
	// registers a button AND adds it to the current toolbarLayout in one go
	
	function addbutton($title, $location, $function, $url, $width=22, $height=22, $cid='forecolor') {
		$this->registerButton($title, $title, $function, $url, $width, $height, $cid);
		$this->addRegisteredButton($title, $location);
	}
	
	function addSpacer($title, $location, $width=22, $height=22) {
		if (defined('WPRO_V2_MODE')) {
			$this->registerSeparator($title);
		} else {
			$this->registerSpacer($title, $width, $height);
		}
		$this->addRegisteredButton($title, $location);
	}
	
	function addSeparator($title, $location) {
		$this->registerSeparator($title);
		$this->addRegisteredButton($title, $location);
	}
	
	function useFullURLs($dontremoveserver=true) {
		$this->fullURLs = $dontremoveserver ? true : false;
	}
	
	function set_classmenu($classes) {
		if (is_array($classes)) {
			$this->textClasses = $classes;
		}
	}
	
	function set_fontmenu($fonts) {
		if (!empty($fonts)) {
			$fonts_array = explode (';', $fonts);
			$this->fontMenu = $fonts_array;
		}
	}
	
	
	function set_formatmenu($formats) {
		if (is_array($formats)) {
			$this->formatMenu = $formats;
		}
	}
	
	function set_sizemenu($sizes) {
		if (is_array($sizes)) {
			$this->sizeMenu = $sizes;
		}
	}
	
	
	function set_color_swatches($colors) {
		if (!empty($colors)) {
			$colors = str_replace(' ', '', $colors);
			$this->colorSwatches = explode(',', $colors);
		}
	}
	
	function disableThumbnails($disable=true) {
		if ($disable) {
			$this->thumbnails = false;
		}
	}
	
	function disableImgMngr($disable=true) {
		if ($disable) {
			$this->disableFeatures(array('imageManager'));
		}
	}

	
	function disableLinkMngr($disable=true) {
		if ($disable) {
			$this->disableFeatures(array('linkManager'));
		}
	}
	
	
	function disableBookmarkMngr($disable=true) {
		if ($disable) {
			$this->disableFeatures(array('bookmarkManager'));
		}
	}
	
	function set_inserts($inserts) {
		if (is_array($inserts)) {
			$this->snippets = $inserts;
		}
	}
	
	/***************************************************************************
	 set_links
	 Public: generates a list of selectable links to appear in the hyperlink window.
	 $links: an auto indexed 2d array with three values in each row: indentdepth, url, name, 
	 e.g. array( array(0, '/myfolder/page1.htm', 'Page One'),	array(0, 'folder', 'Section 1'),	array(1, '/myfolder/page2.htm', 'Page two') );
	 
	*/
			
	function _set_links_children($links, &$parent) {
		for($i=0; $i<count($links); $i++) {
			$f = array();
			$depth = $links[$i][0];
			$url = $links[$i][1];
			$name = $links[$i][2];

			$f['title'] = $name;
			$f['URL'] = $url;
			$f['children'] = array();
			
			if (isset($links[$i+1])) {
				if ($links[$i+1][0]>$depth) {
					// we have gone in one folder
					$children = array();
					for($j=$i+1; $j<count($links); $j++) {
						if ($links[$j][0]<=$depth) {
							// we have left the folder
							break;
						} else {
							array_push($children, $links[$j]);
							$i++;
						}
					}
					$this->_set_links_children($children, $f['children']);
				}
			}
			if (is_array($parent)) array_push($parent, $f);
			//$parent[] = $f;
		}
	}
	
	function set_links($links) {
		$return = array();

		$this->_set_links_children($links, $return);
		
		$this->links = array_merge($this->links, $return);
	}

	
	function set_savebutton($name, $url=NULL, $width=NULL, $height=NULL) {
		if (strtolower($name) == 'save') {
			$name = '##save##';
			if (empty($url)) $url = '##themeURL##buttons/save.gif';
			if (empty($width)) $width = 22;
			if (empty($height)) $height = 22;
		} else if (strtolower($name) == 'send') {
			$name = '##send##';
			if (empty($url)) $url = '##themeURL##buttons/send.gif';
			if (empty($width)) $width = 50;
			if (empty($height)) $height = 22;
		} else if (strtolower($name) == 'post') {
			$name = '##post##';
			if (empty($url)) $url = '##themeURL##buttons/post.gif';
			if (empty($width)) $width = 50;
			if (empty($height)) $height = 22;
		} 
		$this->saveButtonLabel = $name;
		$this->saveButtonURL = $url;
		if (empty($width)) {
			$this->saveButtonWidth = 22;
		} else {
			$this->saveButtonWidth = $width;
		}
		if (empty($height)) {
			$this->saveButtonHeight = 22;
		} else {
			$this->saveButtonHeight = $height;
		}
		$this->saveButton = true;
	}
	
	
	function set_img_dir($name) {
		if (!empty($name)) {
			$this->instanceImgDir = $name;
		}
	}
	
	function set_doc_dir($name) {
		if (!empty($name)) {
			$this->instanceDocDir = $name;
		}
	}
	
	function set_media_dir($name) {
		if (!empty($name)) {
			$this->instanceMediaDir = $name;
		}
	}
	
	function loadMethod($method='onload') {
		$this->loadMethod = $method;
	}
	
	function print_editor($width=0, $height=0) {
		if ($width!=0) {
			$this->width = $width;
		}
		if ($height!=0) {
			$this->height = $height;
		}
		$this->display();
	}
	
	function return_editor($width=0, $height=0) {
		if ($width!=0) {
			$this->width = $width;
		}
		if ($height!=0) {
			$this->height = $height;
		}
		return $this->fetch();
	}


}
?>
