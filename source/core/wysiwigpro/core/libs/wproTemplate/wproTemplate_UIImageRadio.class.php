<?php
class wproTemplate_UIImageRadio {
	
	var $options = array();
	var $uid = 0;
	var $selected = null;
	var $name = '';
	var $onChange = '';
	var $width = 48;
	var $height = 48;
	
	function addOption($label, $src, $value) {
		$this->options[$label] = array($src, $value);
	}
	
	function make () {
		global $DIALOG, $EDITOR;
		$tpl = new wproTemplate();
		$tpl->templates = $this->template->templates;
		$tpl->bulkAssign(array(
			'headContent' => &$DIALOG->headContent,
			'EDITOR'=> &$EDITOR,
			'name' => $this->name,
			'UID' => 'irUI'.$this->uid,
			'options' => $this->options,
			'onChange' => $this->onChange,
			'selected' => $this->selected,
			'width' => $this->width,
			'height' => $this->height,
		));
		$output = $tpl->fetch( WPRO_DIR.'core/tpl/UIImageRadio.tpl.php' );
		if ($this->uid==1) {
			$output = '<script type="text/javascript" src="core/js/wproUIImageRadio_src.js"></script>'.$output;
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