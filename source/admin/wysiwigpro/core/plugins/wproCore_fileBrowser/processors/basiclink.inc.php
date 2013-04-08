<?php
if (!defined('IN_WPRO')) exit;
				//$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_fileBrowser/css/b.css" type="text/css" />');
				//$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_fileBrowser/css/link.css" type="text/css" />');
				$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'link'));
				$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/basiclink.tpl.php';
				$DIALOG->options = array(
					array(
						'type'=>'submit',
						'name'=>'ok',
						'value'=>$DIALOG->langEngine->get('core', 'ok'),
					),
					array(
						'onclick' => 'dialog.close()',
						'type'=>'button',
						'name'=>'close',
						'value'=>$DIALOG->langEngine->get('core', 'cancel'),
					),
				);
				return true;
?>