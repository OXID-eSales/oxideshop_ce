<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_fileBrowser {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		$EDITOR->registerButton('link', '', 'WPro.##name##.openDialogPlugin(\'wproCore_fileBrowser&action=link\',760,480)', '##buttonURL##link.gif', 22, 22);
		
		$EDITOR->registerButton('unlink', '', 'WPro.##name##.callFormatting(\'unlink\')', '##buttonURL##unlink.gif', 22, 22, 'unlink');
		
		$EDITOR->registerButton('document', '', 'WPro.##name##.openDialogPlugin(\'wproCore_fileBrowser&action=document\',760,480)', '##buttonURL##document.gif', 22, 22);
				
		$EDITOR->registerButton('image', '', 'WPro.##name##.openDialogPlugin(\'wproCore_fileBrowser&action=image\',760,480)', '##buttonURL##image.gif', 22, 22);
		$EDITOR->registerButton('media', '', 'WPro.##name##.openDialogPlugin(\'wproCore_fileBrowser&action=media\',760,480)', '##buttonURL##media.gif', 22, 22);
		
		// context menu
		$EDITOR->registerButton('mediaproperties', '', 'WPro.##name##.openDialogPlugin(\'wproCore_fileBrowser&action=media\',760,480)', '##buttonURL##media.gif', 22, 22, 'mediaproperties' );
		$EDITOR->registerButton('imageproperties', '', 'WPro.##name##.openDialogPlugin(\'wproCore_fileBrowser&action=image\',760,480)', '##buttonURL##image.gif', 22, 22, 'imageproperties' );
		
		
		// the full link interface
		$EDITOR->registerAndEnableFeature('linkmanager');
		// the image file browser
		$EDITOR->registerAndEnableFeature('imagemanager');
		$EDITOR->registerAndEnableFeature('mediamanager');
		$EDITOR->registerAndEnableFeature('documentmanager');
		$EDITOR->registerAndEnableFeature('weblocation');
		// the bookmark selector on the hyperlink window
		$EDITOR->registerAndEnableFeature('bookmarkmanager');
		// email options in link manager
		$EDITOR->registerAndEnableFeature('email');
		
		$EDITOR->registerAndEnableFeature('link', array('link','document'));
		$EDITOR->registerAndEnableFeature('image', array('image','imageproperties'));
		$EDITOR->registerAndEnableFeature('media', array('media','mediaproperties'));
		
	}
	
	function onBeforeMakeEditor(&$EDITOR) {
		// check disabled features
		//if ($EDITOR->buttonIsEnabled('media')) {
			$EDITOR->addJSPlugin('wproCore_fileBrowser', 'plugin_src.js');
		//}
		
		if (!$EDITOR->featureIsEnabled('linkmanager')) {
			$EDITOR->setButtonFunction('link', 'WPro.##name##.openDialogPlugin(\'wproCore_fileBrowser&action=basiclink\',570,130)');
		}
		
		
	}	
}
?>