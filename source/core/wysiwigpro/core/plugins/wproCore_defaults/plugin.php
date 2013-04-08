<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_defaults {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
			
		/* load all default plugins */
		/*include_once(WPRO_DIR.'core/libs/wproFilesystem.class.php');
		$fs = new wproFilesystem();
		$folders = $fs->getFoldersInDir(WPRO_DIR.'core/plugins/');
		foreach ($folders as $folder) {
			$EDITOR->loadPlugin($folder['name']);	
		}*/
		
		$EDITOR->loadPlugins(array(
			'wproCore_bookmark',
			'wproCore_codeCleanup',
			'wproCore_colorPicker',
			'wproCore_direction',
			'wproCore_emoticons',
			'wproCore_fileBrowser',
			'wproCore_find',
			'wproCore_fullWindow',
			//'wproCore_help',
			'wproCore_insertHTML',
			'wproCore_list',
			'wproCore_ruler',
			'wproCore_snippets',
			'wproCore_specialCharacters',
			'wproCore_spellchecker',
			'wproCore_styleWithCSS',
			'wproCore_table',
			'wproCore_tagEditor',
			'wproCore_v2Compat',
			'wproCore_zoom',
		));
		
		// load user defined default plugins
		require(WPRO_DIR.'conf/defaultPlugins.inc.php');
		$EDITOR->loadPlugins($DEFAULT_PLUGINS);
		
		/* default styles and font menu options */
		$EDITOR->stylesMenu = array('p'=>'##normal##','h1'=>'##heading_1##','h2'=>'##heading_2##','h3'=>'##heading_3##','h4'=>'##heading_4##','h5'=>'##heading_5##','h6'=>'##heading_6##','pre'=>'##pre_formatted##','address'=>'##address##');
		$EDITOR->fontMenu = array('Arial','Courier','Georgia','Times New Roman','Verdana');
		$EDITOR->sizeMenu = array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7');
		
		$EDITOR->contextMenu = array('cut','copy','paste','pastecleanup','separator','edittable','insrowsandcols','autofitcols','distcols','fixedcols','delcol','delrow','deltable', 'mergecells', 'unmergecells','separator','bulletsandnumbering','separator','bookmarkproperties','rulerproperties','imageproperties','mediaproperties','separator','link','unlink','separator','selectall','separator','inserthtml','tageditor','removetag','deletetag'); 
		
		if (!defined('WPRO_V2_MODE')) {
			/* load default toolbar layout */
			/* load default context menu */
			
			$EDITOR->toolbarLayout = array(
				'toolbar1' => array('fullwindow','print','find','spelling','codecleanup','documentproperties','separator1','cut','copy','paste','pastecleanup','separator2','undo','redo','separator3','tablemenu','image','media','emoticon','ruler','link','bookmark','specialchar','snippets','zoom'),
				'toolbar2' => array('styles','font','size','separator4','bold','italic','underline','moretextformatting','separator5','fontcolor','highlight','separator6','left','center','right','full','morealignmentformatting','separator7','numbering','bullets','morelistformatting','separator8','outdent','indent'),
				/*'toolbar3' => array('form','textfield','textbox','dropdownlist','listbox','checkbox','option','optiongroup','button','file','imageinput'),*/
			);
			
		}
		
		// ALL VERSION 2 OPTIONS SHOULD BE DEFINED IN THE wproCore_v2Compat PLUGIN
		
		/* load features that are enabled by default */
		$EDITOR->enableFeatures(array(
			//'tagPath', // the tag path selector
			'target',
			'events',
			'htmldepreciated', // features that cause depreciated markup
			'dialogappearanceoptions',
			'webcolors', // websafe color pallette
			'tageditor', // advanced tag editor
			'nongecko', // features not supported by gecko
			'nonopera', // features not supported by gecko
			'nonsafari', // features not supported by gecko
			'viewtabs', // all tabs for switching views
			'designtab', 
			'sourcetab',
			'previewtab',
			'guidelines',
			'shift+entermessage',
			'dragresize',
		));
	}
	
}
?>