<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_table {
	function init(&$DIALOG) {
		$DIALOG->headContent->add('<script type="text/javascript" src="core/js/wproForms_src.js"></script>');
		$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_table/js/common_src.js"></script>');
	}
	function runAction ($action, $params) {
		global $EDITOR, $DIALOG;
		
		switch (strtolower($action)) {
			case 'insertrowsandcolumns' :
				$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'insrowsandcols'));
				$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_table/js/insertRowsAndColumns_src.js"></script>');
				$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_table/tpl/insertRowsAndColumns.tpl.php';
				$DIALOG->options = array(
					array(
						'type'=>'submit',
						'name'=>'ok',
						'value'=>$DIALOG->langEngine->get('core', 'insert'),
					),
					array(
						'onclick' => 'dialog.close()',
						'type'=>'button',
						'name'=>'close',
						'value'=>$DIALOG->langEngine->get('core', 'cancel'),
					),
				);
				break;
			case 'unmergecells' :
				$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'unmergecells'));
				$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_table/js/unmergeCells_src.js"></script>');
				$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_table/tpl/unmergeCells.tpl.php';
				$DIALOG->options = array(
					array(
						'type'=>'submit',
						'name'=>'ok',
						'value'=>$DIALOG->langEngine->get('core', 'apply'),
					),
					array(
						'onclick' => 'dialog.close()',
						'type'=>'button',
						'name'=>'close',
						'value'=>$DIALOG->langEngine->get('core', 'cancel'),
					),
				);
				break;
			case 'mergecells' :
				$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'mergecells'));
				$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_table/js/mergeCells_src.js"></script>');
				$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_table/tpl/mergeCells.tpl.php';
				$DIALOG->options = array(
					array(
						'type'=>'submit',
						'name'=>'ok',
						'value'=>$DIALOG->langEngine->get('core', 'apply'),
					),
					array(
						'onclick' => 'dialog.close()',
						'type'=>'button',
						'name'=>'close',
						'value'=>$DIALOG->langEngine->get('core', 'cancel'),
					),
				);
				break;
			case 'fixedcols' :
				$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'fixedcols'));
				$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_table/js/fixedCols_src.js"></script>');
				$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_table/css/fixedCols.css" type="text/css" />');
				$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_table/tpl/fixedCols.tpl.php';
				$DIALOG->options = array(
					array(
						'type'=>'submit',
						'name'=>'ok',
						'value'=>$DIALOG->langEngine->get('core', 'apply'),
					),
					array(
						'onclick' => 'dialog.close()',
						'type'=>'button',
						'name'=>'close',
						'value'=>$DIALOG->langEngine->get('core', 'cancel'),
					),
				);
				break;
			case 'edittable' :
				$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'edittable'));
				$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_table/js/editTable_src.js"></script>');
				$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_table/css/editTable.css" type="text/css" />');
				if (!$EDITOR->featureIsEnabled('dialogappearanceoptions')) {
					$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_table/css/editTableDA.css" type="text/css" />');
				}
				$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_table/tpl/editTable.tpl.php';
				$DIALOG->options = array(
					array(
						'type'=>'submit',
						'name'=>'ok',
						'value'=>$DIALOG->langEngine->get('core', 'apply'),
					),
					array(
						'onclick' => 'dialog.close()',
						'type'=>'button',
						'name'=>'close',
						'value'=>$DIALOG->langEngine->get('core', 'close'),
					)
				);
				break;
			default:
				$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'instable'));
				$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_table/js/newTable_src.js"></script>');
				$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_table/css/newTable.css" type="text/css" />');
				$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_table/tpl/newTable.tpl.php';
				$DIALOG->options = array(
					/*array(
						'onclick' => 'togglePreviewColumn()',
						'type'=>'button',
						'name'=>'togglePreview',
						'style'=> 'width:12em',
						'value'=>str_replace('&gt;', '>', $DIALOG->langEngine->get('core', 'Show Preview >>')),
					),*/
					array(
						'type'=>'submit',
						'name'=>'ok',
						'value'=>$DIALOG->langEngine->get('core', 'insert'),
					),
					array(
						'onclick' => 'dialog.close()',
						'type'=>'button',
						'name'=>'close',
						'value'=>$DIALOG->langEngine->get('core', 'cancel'),
					),
				);
				break;
		}
	}
}

?>