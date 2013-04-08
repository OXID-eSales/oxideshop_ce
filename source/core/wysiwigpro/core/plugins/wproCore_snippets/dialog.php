<?php
if (!defined('IN_WPRO')) exit;
class wproDialogPlugin_wproCore_snippets {
	function _buildSnippetArray(&$UI, &$pNode, $snippets, $snippetId) {
		$return = '';
		$i=0;
		foreach($snippets as $k => $v) {
			if (!empty($v)) {
				$node = & $UI->createNode();
				$id = '';
				if (isset($pNode->id)) {
					$id .= $pNode->id.'_';
				} else {
					$id = 'snippetTree_';
				}
				$id.=$i;
				$node->id = $id;
				if ($id == $snippetId) {
					$return = $v;
				}
				if (is_array($v) && empty($return)) {
					$return = $this->_buildSnippetArray($UI, $node, $v, $snippetId);
				}
				$pNode->appendChild($node);
				if (!empty($return)) break;
				$i++;
			}
		}
		return $return;
	}
	function getSnippetContent ($snippetId='') {
		global $DIALOG;
				
		$response = $DIALOG->createAjaxResponse();
		
		$snippets = $this->getSnippets();
		
		
		if (!isset($snippetId)||!is_string($snippetId)) {
			return $response;
		}
		
		$UI = $DIALOG->template->createUITree();
		$code = $this->_buildSnippetArray($UI, $UI, $snippets, $snippetId);
		
		if (!is_string($code)) {
			return $response;	
		}	
		$response->addScriptCall('showSnippet', $code, $snippetId);
		return $response;		
	}
	function getSnippets() {
		global $EDITOR;
		$EDITOR->triggerEvent('onBeforeGetSnippets');
		$snippets = $EDITOR->snippets;
		if (!is_array($snippets)) $snippets = array();
		$data = $EDITOR->triggerEvent('onGetSnippets');
		if (!empty($data)) {
			foreach ($data as $v) {
				$snippets = array_merge($snippets, $v);
			}
		}	
		return empty($snippets) ? array() : $snippets;
	}	
	function runAction ($action, $params) {
		global $EDITOR, $DIALOG;
		$DIALOG->registerAjaxFunction(array('getSnippetContent', &$this, 'getSnippetContent'));
		switch ($action) {
			case 'default' :
				$DIALOG->title = str_replace('...', '', $DIALOG->langEngine->get('editor', 'snippets'));
				$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_snippets/dialog.tpl.php';
				$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_snippets/dialog.css" type="text/css" />');
				$DIALOG->headContent->add('<script type="text/javascript" src="core/plugins/wproCore_snippets/dialog_src.js"></script>');
				$snippets = $this->getSnippets();
				$DIALOG->template->bulkAssign( array(
					'snippets' => $snippets,
				));
				$DIALOG->options = array(
					array(
						'type'=>'submit',
						'name'=>'ok',
						'disabled'=>'disabled',
						'value'=>$DIALOG->langEngine->get('core', 'insert'),
					),
					array(
						'onclick' => 'dialog.close()',
						'type'=>'button',
						'name'=>'close',
						'value'=>$DIALOG->langEngine->get('core', 'close'),
					)
				);
				break;
			case 'ajax' :
			
				break;
		}
	}
}

?>