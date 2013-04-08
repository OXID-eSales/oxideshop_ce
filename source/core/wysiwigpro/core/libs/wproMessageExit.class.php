<?php
if (!defined('IN_WPRO')) exit;
require_once(dirname(__FILE__).'/wproCore.class.php');
// outputs nice standardised html messages
// define message codes
define('WPRO_QUESTION', 199);
define('WPRO_INFORMATION', 200);
define('WPRO_WARNING', 201);
define('WPRO_CRITICAL', 202);
class wproMessageExit extends wproCore {
	
	var $action='';
	var $jsAction='';
	var $title='';
	var $icon='';
	var $options=array();
	var $hidden=array();
	var $msg='';
	var $msgCode=0;
	var $chromeless = false;
	
	function getLang($k, $e) {
		global $EDITOR;
		if (empty($EDITOR)) {
			return $e;
		} else {
			return $EDITOR->langEngine->get('core', $k);
		}
	}
	
	function _setDefaults() {
		global $EDITOR;
		
		switch($this->msgCode) {
			case WPRO_QUESTION:
				if (empty($this->title)) {
					$this->title = $this->getLang('question', 'Question');
				}
				if (empty($this->icon)) {
					$this->icon = 'question32';
				}
				if (empty($this->msg)) {
					$this->msg = $this->title;
				}
				if (empty($this->action) && empty($this->jsAction)) {
					$this->action = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
				}
				break;
			
			case WPRO_WARNING:
				if (empty($this->title)) {
					$this->title = $this->getLang('warning', 'Warning');
				}
				if (empty($this->msg)) {
					$this->msg = $this->title;
				}
				if (empty($this->icon)) {
					$this->icon = 'warning32';
				}
				if (empty($this->action) && empty($this->jsAction)) {
					$this->action = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
				}
				break;
	
			case WPRO_CRITICAL:
				if (empty($this->title)) {
					$this->title = $this->getLang('error','Error');
				}
				if (empty($this->msg)) {
					$this->msg = $this->title;
				}
				if (empty($this->icon)) {
					$this->icon = 'critical32';
				}
				if (empty($this->action) && empty($this->jsAction)) {
					$this->jsAction = 'top.window.close();return false;';	
				}
				break;

			case WPRO_INFORMATION:
			default:
				if (empty($this->title)) {
					$this->title = $this->getLang('information','Information');
				}
				if (empty($this->msg)) {
					$this->msg = $this->title;
				}
				if (empty($this->icon)) {
					$this->icon = 'information32';
				}
				if (empty($this->action) && empty($this->jsAction)) {
					$this->action = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
				}
				break;

		}
	}
	
	function jsConfirm ($ok, $cancel) {
		$this->_setDefaults();
echo 
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
</head>
<body><script type="text/javascript">
if (confirm("'.addslashes($this->msg).'") {
	'.$ok.';
} else {
	'.$cancel.';
}
</script>
</body>
</html>';
exit;
	}
	
	function jsAlert($ok) {
		$this->_setDefaults();
echo 
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
</head>
<body><script type="text/javascript">
alert("'.addslashes($this->msg).'");
'.$ok.';
</script>
</body>
</html>';
exit;
	}
	
	function display() {
		
		global $EDITOR, $WPRO_EDITOR_URL;
		$this->_setDefaults();
		
		require_once(WPRO_DIR.'core/libs/wproTemplate.class.php'); 
		
		if (empty($this->options)) {
			$this->options	= array (
				
				array(
					'type'=>'submit',
					'name'=>'ok',
					'value'=>$this->getLang('ok','OK'),
				),
				array(
					'type'=>'submit',
					'name'=>'cancel',
					'value'=>$this->getLang('cancel','Cancel'),
				)
				
			);	
		}
		
		if (empty($EDITOR)) {
			
			//exit ($_SERVER['REQUEST_URI']);
			$iframeDialogs = false;
			$frameID = 0;
			
			if (defined('WPRO_IN_ROUTE')) {
				$editorURL = wproRoute::getEditorURL();
			} else {
				$editorURL = $WPRO_EDITOR_URL;
			}
			
			$themeURL = $this->varReplace(WPRO_THEME_URL, array('EDITOR_URL'=>$WPRO_EDITOR_URL)).WPRO_THEME.'/wysiwygpro';
			$tpl = new wproTemplate();
			$tpl->path = WPRO_DIR.'core/tpl/';
			
			if (isset($_GET['iframe'])) {
				$iframeDialogs = true;
			}
			
			if (isset($_GET['dialogFrameID'])) {
				$iframeDialogs = true;
				$frameID = intval($_GET['dialogFrameID']);
			}
			
			if (isset($_GET['dialogOpenerID'])) {
				$openerID = intval($_GET['dialogOpenerID']);
			} else {
				$openerID = null;
			}
			
		} else {
		
			require_once(WPRO_DIR.'core/libs/wproDialog.class.php');
			
			$DIALOG = new wproDialog();
			
			$tpl = & $DIALOG->template;
			
			$DIALOG->bodyInclude = WPRO_DIR.'core/tpl/messageExitBody.tpl.php';
			
			$DIALOG->title = $this->title;
			
			$DIALOG->options = $this->options;
			
			$DIALOG->chromeless = $this->chromeless;
			
			if (isset($_GET['iframe'])) {
				$iframeDialogs = true;
			}
			
			if (isset($_GET['dialogFrameID'])) {
				$iframeDialogs = true;
				$DIALOG->frameID = intval($_GET['dialogFrameID']);
			}
			if (isset($_GET['dialogOpenerID'])) {
				$DIALOG->openerID = intval($_GET['dialogOpenerID']);
			}
			
			$editorURL = $EDITOR->editorURL;
			$themeURL = $EDITOR->themeFolderURL.$EDITOR->theme.'/wysiwygpro';
			$iframeDialogs = $EDITOR->iframeDialogs;
			$frameID = $DIALOG->frameID;
			$openerID = $DIALOG->openerID;
		}
		
		$tpl->bulkAssign(array(
			'iframeDialogs' => $iframeDialogs,
			'frameID' => $frameID,
			'openerID' => $openerID,
			'editorURL' => $editorURL,
			'themeURL' => $themeURL,
			'hidden' => $this->hidden,
			'jsAction' => $this->jsAction,
			'action' => $this->action,
			'title' => $this->title,
			'icon' => $this->icon,
			'options' => $this->options,
			'msg' => $this->msg,
		));
		
		/**  
		* Echo the results.  
		*/ 
		if (empty($EDITOR)) {
			$tpl->display('messageExit.tpl.php'); 
		} else {
			$DIALOG->display();
		}
		exit;
	
	}
	
	function alert() {
		$this->options = array(
			array(
				'type'=>'button',
				'name'=>'OK',
				'value'=>$this->getLang('ok', 'OK'),
				'onclick'=>'dialog.close()',
			)
		);
		$this->display();
	}


}

?>