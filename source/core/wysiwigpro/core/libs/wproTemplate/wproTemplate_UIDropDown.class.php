<?php
class wproTemplate_UIDropDown {
	
	var $options = array();
	var $uid = 0;
	var $selected = 0;
	var $label = '';
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
			'options' => $this->options,
			'label' => $this->label,
			'UID' => 'ddUI'.$this->uid,
			'onChange' => $this->onChange,
		));
		$output = $tpl->fetch( WPRO_DIR.'core/tpl/UIDropDown.tpl.php' );
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