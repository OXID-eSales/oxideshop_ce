<?php
if (!defined('IN_WPRO')) exit;
require_once(dirname(__FILE__).'/wproCore.class.php');
require_once(dirname(__FILE__).'/wproTemplate.class.php');

if (!defined('WPRO_AJAX_METHOD')) {
	define('WPRO_AJAX_METHOD', 'POST');
}

class wproHeadContent {
	
	var $content = array();
	
	function add ($data) {
		if (!in_array($data, $this->content)) {
			array_push($this->content, $data);
		}
	}
	
	
	function fetch () {
	
		return implode($this->content,'');
	
	}
	
	function display () {
		echo $this->fetch();
	}

}

class wproDialog extends wproCore {
	
	var $title = '';
	var $options = array();
	var $bodyInclude = '';
	var $bodyContent = '';
	var $formMethod = 'post';
	var $formAction = '';
	var $formOnSubmit = 'return dialog.doFormSubmit();';
	var $formEnctype = '';
	var $plugins = array();
	var $frameID = 0;
	var $openerID = NULL;
	var $formTags = true;
	var $chromeless = false;
	var $embedded = false;
	var $contentType = 'text/html';
	var $ajax = NULL;
	var $closeFunction = 'dialog.close()';
	var $classIsolator = '';
	var $dialogName = '';
	
	var $width = '';
	var $height = '';
	
	function wproDialog() {
		global $EDITOR, $WPRO_SESS;	
		$this->formAction = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
		$this->EDITOR = &$EDITOR;
		$this->headContent = new wproHeadContent();
		$this->sess = &$WPRO_SESS;
		$this->langEngine = & $EDITOR->langEngine;
		$this->template = new wproTemplate();
		$this->template->themeURL = $this->EDITOR->themeFolderURL.$this->EDITOR->theme.'/wysiwygpro/';
		$this->template->editorURL = $this->EDITOR->editorURL;
		
		if (isset($_GET['dWidth'])) {
			$this->width=intval($_GET['dWidth']);
		}
		if (isset($_GET['dHeight'])) {
			$this->height=intval($_GET['dHeight']);
		}
		$this->options = array(
			array(
				'type'=>'submit',
				'name'=>'ok',
				'value'=>$this->langEngine->get('core', 'ok'),
			),
			array(
				'onclick' => 'dialog.close()',
				'type'=>'button',
				'name'=>'cancel',
				'value'=>$this->langEngine->get('core', 'cancel'),
			)
		);
		if (isset($_GET['dialogFrameID'])) {
			$this->frameID = intval($_GET['dialogFrameID']);
		}
		if (isset($_GET['dialogOpenerID'])) {
			$this->openerID = intval($_GET['dialogOpenerID']);
		}
	}
	
	function loadAjax () {
		if (!$this->ajax) {
			
			if (WPRO_PATH_XAJAX == WPRO_DIR.'core/libs/xajax/') {
				require_once(WPRO_PATH_XAJAX."xajax.inc.php");
			} else {
				if (!wpro_class_exists('xajax')) require_once(WPRO_PATH_XAJAX."xajax.inc.php");
			}
			if (wpro_class_exists('wpro_xajax')) {
				$this->ajax = new wpro_xajax();
			} else {
				$this->ajax = new xajax();
			}
			
			$url = $this->ajax->_detectURI();
			if (preg_match('/^http(|s):\/\/.*?\//si', $url)) {
				$url = preg_replace('/^http(|s):\/\/[^\/]+/si', '', $url);
			}
			$this->ajax->sRequestURI = preg_replace("/action=[a-z]+/si", "action=ajax", $url);
			$this->ajax->sWrapperPrefix = 'ajax_';
			$this->ajax->bExitAllowed = false;
			$this->ajax->setCharEncoding($this->EDITOR->langEngine->get('conf','charset'));
			
			// turn these off in production
			//$this->ajax->bErrorHandler = true;
			//$this->ajax->bDebug = true;
		}
	}
	
	function registerAjaxFunction($function, $method=WPRO_AJAX_METHOD) {
		
		if (!$this->ajax) {
			$this->loadAjax();
		}
		
		$xajax_method = XAJAX_POST;
		if (strtolower($method)=='get') {
			$xajax_method = XAJAX_GET;	
		}

		$this->ajax->registerFunction($function, $xajax_method);
		
	}
	
	function createAjaxResponse() {
			
		if (WPRO_PATH_XAJAX == WPRO_DIR.'core/libs/xajax/') {
			require_once(WPRO_PATH_XAJAX."xajaxResponse.inc.php");
		} else {
			if (!wpro_class_exists('xajaxResponse')) require_once(WPRO_PATH_XAJAX."xajaxResponse.inc.php");
		}
		if (wpro_class_exists('wpro_xajaxResponse')) {
			$response = new wpro_xajaxResponse($this->EDITOR->langEngine->get('conf','charset'));
		} else {
			$response = new xajaxResponse($this->EDITOR->langEngine->get('conf','charset'));
		}
		
		return $response;
	}
	
	function loadPlugin($name, $default=false) {
		require_once(WPRO_DIR.'core/libs/wproFilesystem.class.php');
		$files = new wproFilesystem();
		$name = $this->makeVarOk($name);
		if (!isset($this->plugins[$name])) {
			$baseDir = WPRO_DIR.'/plugins/';
			if (substr($name, 0, 9) == 'wproCore_') {
				$baseDir = WPRO_DIR.'core/plugins/';
			} else {
				$baseDir = WPRO_DIR.'plugins/';
			}
			// load componant file
			if (!wpro_class_exists("wproDialogPlugin_{$name}")) {
				if ($files->includeFileOnce($name, $baseDir, '/dialog.php')) {
					$this->EDITOR->langEngine->loadFile('wysiwygpro/includes/'.$name.'.inc.php');
					//
				} else {
					return false;
				}
			}
			// create componant object
			if (wpro_class_exists("wproDialogPlugin_{$name}")) {
				@eval ('$this->plugins["'.$name.'"] = & new wproDialogPlugin_'.$name.'();');
				if (method_exists($this->plugins[$name], 'init')) {
					$this->plugins[$name]->init($this);
				}
				if ($default) {
					$this->dialogName = $name;
				}
				return true;
			}
		} else {
			if ($default) {
				$this->dialogName = $name;
			}
			return true;
		}
		if ($default) {
			// do exit
			require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
			$msg = new wproMessageExit();
			$msg->msgCode = WPRO_CRITICAL;
			$msg->msg = 'No dialog or incorrect dialog specified.';
			$msg->alert();
			exit;
		} else {
			return false;
		}
	}
	
	function reloadInFrame () {
		if (!isset($_GET['inframe']) && !isset($_POST['inframe']) && !$this->EDITOR->iframeDialogs ) {
			$this->baseTemplate = WPRO_DIR.'core/tpl/dialogFrame.tpl.php';
			$this->display();
			exit;
		}
	}
	
	function runPluginAction($plugin, $action='default', $params=array()) {
		$plugin = $this->makeVarOk($plugin);
		$action = preg_replace("/[^a-z0-9_,\-]/si", '', $action);
		if (isset($this->plugins[$plugin])) {
			if (method_exists($this->plugins[$plugin], 'runAction')) {
				$this->plugins[$plugin]->runAction($action, $params);
			}
		}
	}
	
	var $eventFunctions = array();
	function addEvent($trigger, $func) {
		$trigger = strtolower($trigger);
		if (!isset($this->eventFunctions[$trigger])) {
			$this->eventFunctions[$trigger] = array();
		}	
		array_push($this->eventFunctions[$trigger], $func);
	}
	
	function triggerEvent($trigger, $vars=array()) {
		$trigger = strtolower($trigger);
		if (isset($this->eventFunctions[$trigger])) {
			foreach($this->eventFunctions[$trigger] as $f) {
				if (is_array($f)) {
					if (is_object($f[0])) {
						if (method_exists($f[0], $f[1])) {
							$f[0]->$f[1]($this, $vars);
						}
					}
				} else if (function_exists($f)) {
					$f($this, $vars);
				}
			}
		}
	}
	
	function assign($name, $var) {
		$this->template->assign($name, $var);
	}
	
	function assignByRef($name, &$var) {
		$this->template->assignByRef($name, $var);
	}
	
	function bulkAssign($arr) {
		$this->template->bulkAssign($arr);
	}
	
	function addOutputFilter($func) {
		$this->template->addOutputFilter($func);
	}
	
	function assignCommonVarsToTemplate (&$tpl) {
		$tpl->themeURL = $this->EDITOR->themeFolderURL.$this->EDITOR->theme.'/wysiwygpro/';
		$tpl->editorURL = $this->EDITOR->editorURL;
		
		if (empty($this->classIsolator)) {
			$this->classIsolator = $this->dialogName;
		}
		
		$tpl->bulkAssign(array(
			'classIsolator' => $this->classIsolator,
			'frameID' => $this->frameID,
			'openerID' => $this->openerID,
			'themeURL' => $this->EDITOR->themeFolderURL.$this->EDITOR->theme.'/wysiwygpro/',
			'editorURL' => $this->EDITOR->editorURL,
			'langURL' => $this->EDITOR->langFolderURL.$this->EDITOR->langEngine->actualLang.'/wysiwygpro/',
			'bodyInclude' => $this->bodyInclude,
			'bodyContent' => $this->bodyContent,
			'title' => $this->title,
			'options' => $this->options,
			'formMethod' => (strtolower($this->formMethod) == 'get') ? 'get' : 'post',
			'formAction' => $this->formAction,
			'formOnSubmit' => $this->formOnSubmit,
			'formEnctype' => $this->formEnctype,
		));
		
		$tpl->assignByRef('EDITOR', $this->EDITOR);
		$tpl->assignByRef('DIALOG', $this);
		$tpl->assignByRef('langEngine', $this->EDITOR->langEngine);
		$tpl->assignByRef('headContent', $this->headContent);
	}
	
	function display() {
		global $EDITOR,$WPRO_SESS;
		$EDITOR->triggerEvent('onBeforeDisplayDialog');
		$this->triggerEvent('beforeDisplayProcess');
		
		if ($this->ajax) {
			if ($this->ajax->canProcessRequests()) {
				// clear buffer to prevent any possible problems.
				ob_end_clean();
				// check output buffering
				if (WPRO_GZIP) {
					$wpro_doGzip = false;
					if (!isset($_GET['gzip'])) {
						if (!@ini_get( 'zlib.output_compression' )) {
							if (@ini_get('output_handler') != 'ob_gzhandler') {
								$wpro_doGzip = true;
							}
						}
					}
					if ($wpro_doGzip) {
						ob_start( 'ob_gzhandler' );
					} else {
						ob_start();
					}
					unset($wpro_doGzip);
				} else {
					ob_start();
				}
				$this->ajax->processRequests();
				$WPRO_SESS->shutdown($EDITOR);
				exit;
			}
		}
		
		// ajax..
		if ($this->ajax) {
			$this->headContent->add($this->ajax->getJavascript("core/js/", "xajax/xajax.js"));
			// adds support for non-activex powered ajax for IE < 7
			$this->headContent->add('<!--[if lt IE 7]><script type="text/javascript" src="core/js/xajax/activex_off.js"></script><![endif]-->');
		}
		
		if (!empty($this->contentType)) {
			if ($this->contentType=='text/html') {
				header('Content-Type: '.$this->contentType.'; charset='.$this->EDITOR->langEngine->get('conf','charset'));
			} else {
				header('Content-Type: '.$this->contentType );
			}
		}
		
		/*$this->template->bulkAssign(array(
			'frameID' => $this->frameID,
			'openerID' => $this->openerID,
			'themeURL' => $this->EDITOR->themeFolderURL.$this->EDITOR->theme.'/',
			'editorURL' => $this->EDITOR->editorURL,
			'langURL' => $this->EDITOR->langFolderURL.$this->EDITOR->langEngine->actualLang.'/',
			'bodyInclude' => $this->bodyInclude,
			'bodyContent' => $this->bodyContent,
			'title' => $this->title,
			'options' => $this->options,
			'formMethod' => (strtolower($this->formMethod) == 'get') ? 'get' : 'post',
			'formAction' => $this->formAction,
			'formOnSubmit' => $this->formOnSubmit,
			'formEnctype' => $this->formEnctype,
		));
		
		$this->template->assignByRef('EDITOR', $this->EDITOR);
		$this->template->assignByRef('DIALOG', $this);
		$this->template->assignByRef('langEngine', $this->EDITOR->langEngine);
		$this->template->assignByRef('headContent', $this->headContent);*/
		
		$this->assignCommonVarsToTemplate($this->template);
		
		if (!WPRO_USE_JS_SOURCE) {
			$this->template->addOutputFilter('wproTemplateJSSourceFilter');
		}
		$this->template->addOutputFilter('wproTemplateVersionAppend');
		$this->triggerEvent('beforeDisplay');
		$this->template->display( isset($this->baseTemplate) ? $this->baseTemplate : WPRO_DIR.'core/tpl/dialog.tpl.php' );
		$this->triggerEvent('afterDisplay');
		$EDITOR->triggerEvent('onAfterDisplayDialog');
		$WPRO_SESS->shutdown($EDITOR);
	}

}


?>