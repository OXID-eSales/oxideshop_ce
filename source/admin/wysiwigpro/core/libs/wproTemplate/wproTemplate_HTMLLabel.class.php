<?php
if (!defined('IN_WPRO')) exit;
class wproTemplate_HTMLLabel {
	
	var $label = '';
	var $for = '';
	var $accessKey = '';
	
	function make () {	
		$h = '<label';
		if (!empty($this->for)) $h.= ' for="'.htmlspecialchars($this->for).'"';
		if (!empty($this->accessKey)) $h.= ' accesskey="'.htmlspecialchars($this->accessKey).'"';
		$h.= '>'.$this->template->underlineAccessKey($this->label).'</label>';
		return $h;
	}
	
	function fetch () {
		return $this->make();
	}
	
	function display() {
		echo $this->make();
	}


}


?>