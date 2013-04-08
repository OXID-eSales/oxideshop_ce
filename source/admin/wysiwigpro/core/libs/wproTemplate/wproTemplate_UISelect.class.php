<?php
class wproTemplate_UISelect {
	
	var $options = array();
	var $uid = 0;
	var $size = 10;
	var $selected = 0;
	var $editorURL = '';
	var $onChange = '';
	
	function addOption($label, $content) {
		$this->options[$label] = $content;
	}
	
	function make () {
		$tpl = new wproTemplate();
		$tpl->templates = $this->template->templates;
		$tpl->bulkAssign(array(
			'selected' => $this->selected,
			'size' => $this->size,
			'options' => $this->options,
			'UID' => 'sUI'.$this->uid,
			'onChange' => $this->onChange,
		));
		$output = $tpl->fetch( WPRO_DIR.'core/tpl/UISelect.tpl.php' );
		if ($this->uid==1) {
			$output = '<script type="text/javascript" src="core/js/wproUISelect_src.js"></script>'.$output;
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