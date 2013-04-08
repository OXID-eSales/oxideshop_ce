<?php
if (!defined('IN_WPRO')) exit;
/* version 2 compatability plugin */
class wproPlugin_wproCore_v2Compat {
	/* called when the plugin is loaded */
	function init ($EDITOR) {
	
		if (defined('WP_WEB_DIRECTORY') && !defined('WPRO_V2_MODE')) {
			define('WPRO_V2_MODE', true);
			$EDITOR->_v2Mode = true;
		}
	
		/* anything that should also be available in native mode goes here */
		
		$EDITOR->registerAndEnableFeature('tab', array('viewtabs'));
		$EDITOR->registerAndEnableFeature('html', array('sourcetab'));
		$EDITOR->registerAndEnableFeature('design', array('designtab'));
		$EDITOR->registerAndEnableFeature('source', array('sourcetab'));
		$EDITOR->registerAndEnableFeature('preview', array('previewtab'));
		$EDITOR->registerAndEnableFeature('border', array('guidelines')); // guidelines button		
		
		$EDITOR->registerAndEnableFeature('pasteword', array('pastecleanup'));
		
		$EDITOR->registerAndEnableFeature('format', array('styles'));
		$EDITOR->registerAndEnableFeature('class', array('styles'));
		$EDITOR->registerAndEnableFeature('custom', array('snippets'));
		$EDITOR->registerAndEnableFeature('ol', array('numbering'));
		$EDITOR->registerAndEnableFeature('ul', array('bullets'));
		$EDITOR->registerAndEnableFeature('color', array('fontcolor'));
		$EDITOR->registerAndEnableFeature('tbl', array('table'));
		$EDITOR->registerAndEnableFeature('smiley', array('emoticon'));
		
		
		// buttons and separators
		
		// v2 separators
		$EDITOR->registerSeparator('spacer1');
		$EDITOR->registerSeparator('spacer2');
		$EDITOR->registerSeparator('spacer3');
		$EDITOR->registerSeparator('spacer4');
		$EDITOR->registerSeparator('spacer5');
		$EDITOR->registerSeparator('spacer6');
		$EDITOR->registerSeparator('spacer7');
		$EDITOR->registerSeparator('spacer8');
		
		/* version 2 toolbar layout */
		/* load toolbar layout */
		if (defined('WPRO_V2_MODE')) {
			$EDITOR->_v2Mode = true;
			// anything that should only be set in V2 mode goes here 
		
			// do not encode special characters
			//$EDITOR->escapeCharacters = false;
			$EDITOR->defaultHTMLVersion = 'HTML';
			$EDITOR->iframeDialogs = false;
			$EDITOR->lineReturns = 'DIV';

			$EDITOR->toolbarLayout = array(
				'toolbar1' => array('print','find','spacer1','cut','copy','paste','pastecleanup','spacer2','undo','redo','spacer3','instable','edittable','insrowsandcols','delcol','delrow','mergecells','unmergecells','spacer4','image','emoticon','ruler','link','document','bookmark','specialchar','snippets'),
				'toolbar2' => array('styles','font','size','spacer5','bold','italic','underline','spacer6','left','center','right','full','spacer7','numbering','bullets','outdent','indent','spacer8','fontcolor','highlight'),
			);
			$EDITOR->loadMethod = 'onload';
		}
	}	
}
?>