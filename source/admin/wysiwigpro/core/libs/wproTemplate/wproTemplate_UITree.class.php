<?php
function wproTemplate_UITree_outputfilter ($content) {
	return str_replace('</body>', '<script type="text/javascript">RedrawAllTrees()</script></body>', $content);
}
class wproTemplate_UITree_node {
	var $id = '';
	var $caption = '';
	var $URL = NULL;
	var $target = NULL;
	var $folders = array();
	var $buttons = array();
	var $isFolder = false;
	var $expanded = false;
	var $caption_onclick = '';
	var $caption_onmouseover = '';
	var $caption_onmouseout = '';
	var $image_onclick = '';
	var $image_onmouseover = '';
	var $image_onmouseout = '';
	var $button_onclick = '';
	var $button_onmouseover = '';
	var $button_onmouseout = '';
	var $childNodes = array();
	
	function appendChild(&$node) {
		//static $c = 0;
		//$this->childNodes[$c] = &$node;
		//$c++;
		array_push($this->childNodes, $node);
	}
}
class wproTemplate_UITree {
	
	var $uid = 0;
	var $nodes = array();
	var $width = 0;
	var $height = 0;
	
	function wproTemplate_UITree() {
		$this->rootNode = new wproTemplate_UITree_node();
	}
	
	function createNode() {
		$node = new wproTemplate_UITree_node();
		return $node;
	}
	
	function appendChild(&$node) {
		array_push($this->nodes, $node);
	}
	
	function make () {
		$tpl = new wproTemplate();
		$tpl->templates = $this->template->templates;
		$tpl->bulkAssign(array(
			'nodes' => $this->nodes,
			'UID' => 'treeUI'.$this->uid,
			'editorURL' => $this->template->editorURL,
			'themeURL' => $this->template->themeURL,
			'width' => $this->width,
			'height' => $this->height,
		));
		$output = $tpl->fetch( WPRO_DIR.'core/tpl/UITree.tpl.php' );
		if ($this->uid==1) {
			$this->template->addOutputFilter('wproTemplate_UITree_outputfilter');
			$output = '<script type="text/javascript" src="core/js/COOLjsTreePro/cooltreepro.js"></script>
			<script type="text/javascript" src="core/js/COOLjsTreePro/tree_format.js"></script>'.$output;
		}
		return $output;
	}
	
	function fetch () {
		return $this->make();
	}
	
	function display() {
		echo $this->make();
	}


}

?>