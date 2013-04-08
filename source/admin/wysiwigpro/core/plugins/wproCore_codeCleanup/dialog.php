<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_codeCleanup {
	function init (&$DIALOG) {
		$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_codeCleanup/dialog_src.js"></script>');
		$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_codeCleanup/dialog.css" type="text/css" />');
		//$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'bookmark'));
		
	}
	function runAction ($action, $params) {
		global $DIALOG;
		
		
		
		// show upload screen
		if (isset($_POST['html'])) {
			// check if there are files to upload
			$html = $_POST['html'];
			
			$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_codeCleanup/upload.tpl.php';
						
			$DIALOG->template->assign('mode', 'upload');
			
			// find the local files and assign them to the template
			$files=array();
			
			preg_match_all('/<[a-z]+[^>]*[a-z]+="file:\/\/([^"]+)/i', $html, $files);

			$DIALOG->template->assign('files', array_unique($files[1]));
							
			$DIALOG->template->assign('html', $html);
			
			switch (strtolower($action)) {
				case 'paste' :
					$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'pastecleanup'));
					$DIALOG->template->assign('action', 'paste');
					break;
				case '':
				default:
					$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'codecleanup'));
					$DIALOG->template->assign('action', 'clean');
				break;
			}
			
			// check if th
			
			$action = 'showupload';
			
		} else {
			$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_codeCleanup/dialog.tpl.php';
			$DIALOG->template->assign('mode', 'normal');
			
		}
		
		$DIALOG->formOnSubmit = 'dialog.showLoadMessage();'.$DIALOG->formOnSubmit;
		
		// finsih up
		switch (strtolower($action)) {
			case 'showupload' :
				$DIALOG->options = array(
					array(
						'type'=>'submit',
						'name'=>'ok',
						'value'=>$DIALOG->langEngine->get('core', 'ok'),
					),
					array(
						'type'=>'button',
						'name'=>'dontUpload',
						'onclick' => 'skip()',
						'value'=>$DIALOG->langEngine->get('wproCore_codeCleanup', 'skip'),
					),
					array(
						'onclick' => 'dialog.close()',
						'type'=>'button',
						'name'=>'close',
						'value'=>$DIALOG->langEngine->get('core', 'cancel'),
					),
				);
				break;				
			case 'paste' :
				$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'pastecleanup'));
				$DIALOG->template->assign('action', 'paste');
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
			case '':
			default:
				$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'codecleanup'));
				$DIALOG->template->assign('action', 'clean');
				$DIALOG->options = array(
					array(
						'type'=>'submit',
						'name'=>'ok',
						'value'=>$DIALOG->langEngine->get('core', 'apply'),
						//'onclick' => 'dialog.showLoadMessage();'
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