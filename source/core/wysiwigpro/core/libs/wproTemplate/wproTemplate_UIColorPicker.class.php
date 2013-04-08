<?php
class wproTemplate_UIColorPicker {
	
	var $options = array();
	var $uid = 0;
	var $selected = 0;
	var $color = '';
	var $onChange = '';
	var $showInput = false;
	var $editorURL = '';
	var $accessKey = '';
	
	function make () {
		$tpl = new wproTemplate();
		$tpl->templates = $this->template->templates;
		$tpl->bulkAssign(array(
			'name' => $this->name,
			'color' => $this->color,
			'UID' => 'cpUI'.$this->uid,
			'onChange' => $this->onChange,
			'showInput' => $this->showInput,
			'accessKey' =>  $this->accessKey
		));
		$output = $tpl->fetch( WPRO_DIR.'core/tpl/UIColorPicker.tpl.php' );
		if ($this->uid==1) {
			$output = '<script type="text/javascript" src="core/js/wproUIColorPicker_src.js"></script>'.$output;
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