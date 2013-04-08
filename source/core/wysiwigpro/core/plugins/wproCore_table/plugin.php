<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_table {
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		// table editing
		$EDITOR->registerButton('instable', '', 'WPro.##name##.openDialogPlugin(\'wproCore_table&action=newTable\',650,470)', '##buttonURL##instable.gif', 22, 22, '' );
		$EDITOR->registerButton('edittable', '', 'WPro.##name##.openDialogPlugin(\'wproCore_table&action=editTable\',400,475)', '##buttonURL##edittable.gif', 22, 22, 'edittable' );
		
		$EDITOR->registerButton('insrowabove','', 'WPro.##name##.tableEditor.insertRow(\'above\')', '##buttonURL##insrowabove.gif', 22, 22, 'insrowabove' );
		$EDITOR->registerButton('insrowbelow','', 'WPro.##name##.tableEditor.insertRow(\'below\')', '##buttonURL##insrowbelow.gif', 22, 22, 'insrowbelow' );
		$EDITOR->registerButton('inscolleft','', 'WPro.##name##.tableEditor.insertColumn(\'left\')', '##buttonURL##inscolleft.gif', 22, 22, 'inscolleft' );
		$EDITOR->registerButton('inscolright','', 'WPro.##name##.tableEditor.insertColumn(\'right\')', '##buttonURL##inscolright.gif', 22, 22, 'inscolright' );
		
		$EDITOR->registerButton('insrowsandcols','', 'WPro.##name##.openDialogPlugin(\'wproCore_table&action=insertRowsAndColumns\',400,100)', '##buttonURL##insrowsandcols.gif', 22, 22, 'insrowsandcols' );
		
		$EDITOR->registerButton('autofitcols','', 'WPro.##name##.tableEditor.autoFitCols()', '##buttonURL##autofitcols.gif', 22, 22, 'edittable' );
		$EDITOR->registerButton('distcols','', 'WPro.##name##.tableEditor.distCols()', '##buttonURL##distcols.gif', 22, 22, 'edittable' );
		$EDITOR->registerButton('fixedcols','', 'WPro.##name##.openDialogPlugin(\'wproCore_table&action=fixedCols\',350,200)', '##buttonURL##fixedcols.gif', 22, 22, 'edittable' );
		
		$EDITOR->registerButton('delcol','', 'WPro.##name##.tableEditor.deleteColumn()', '##buttonURL##delcol.gif', 22, 22, 'delcol' );
		$EDITOR->registerButton('delrow','', 'WPro.##name##.tableEditor.deleteRow()', '##buttonURL##delrow.gif', 22, 22, 'delrow' );
		$EDITOR->registerButton('deltable', '', 'WPro.##name##.tableEditor.deleteTable()', '##buttonURL##deltable.gif', 22, 22, 'deltable' );
		
		$EDITOR->registerButton('mergecells','', 'WPro.##name##.openDialogPlugin(\'wproCore_table&action=mergeCells\',300,100)', '##buttonURL##mergecells.gif', 22, 22, 'mergecells' );
		$EDITOR->registerButton('unmergecells','', 'WPro.##name##.openDialogPlugin(\'wproCore_table&action=unmergeCells\',300,100)', '##buttonURL##unmergecells.gif', 22, 22, 'unmergecells' );
		
		$EDITOR->registerMenuButton('tablemenu', '', array('instable','separator','edittable','separator','insrowabove','insrowbelow','inscolleft','inscolright','insrowsandcols','separator','autofitcols','distcols','fixedcols','separator','delcol','delrow','deltable','separator','mergecells','unmergecells'), '##buttonURL##tablemenu.gif', 32, 22, '' );

		$EDITOR->registerAndEnableFeature('table', array('instable','edittable','tablemenu'));
		$EDITOR->registerAndEnableFeature('edittable', array('insrowabove','insrowbelow','inscolleft','inscolright','insrowsandcols','autofitcols','distcols','fixedcols','delcol','delrow','deltable','mergecells','unmergecells'));
	}
	
	function onBeforeMakeEditor(&$EDITOR) {
		if ($EDITOR->featureIsEnabled('table')) {
			$EDITOR->addJSPlugin('wproCore_table', 'plugin_src.js');
			if (!$EDITOR->featureIsEnabled('dialogappearanceoptions')) {
				$EDITOR->setButtonFunction('edittable', "WPro.##name##.openDialogPlugin('wproCore_table&action=editTable',400,300)");
			}
		}
	}	
}
?>