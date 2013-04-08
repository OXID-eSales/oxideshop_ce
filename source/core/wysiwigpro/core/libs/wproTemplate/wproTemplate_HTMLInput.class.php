<?php
if (!defined('IN_WPRO')) exit;
class wproTemplate_HTMLInput {
	
	var $attributes = array();
	var $forceId = true;
	var $label = '';
	var $labelPosition = 'before';
	
	function make () {	
		if (!empty($this->attributes)) {
			if ($this->forceId && (isset($this->attributes['name']) && !isset($this->attributes['id']))) {
				$this->attributes['id'] = $this->attributes['name'];
			}
			if ((isset($this->attributes['accesskey']) || !empty($this->accessKey)) && !empty($this->label)) {
				$this->label = $this->template->underlineAccessKey($this->label, (isset($this->attributes['accesskey']) ? $this->attributes['accesskey'] : $this->accessKey));
			}
			if (isset($this->attributes['checked'])) {
				if (empty($this->attributes['checked'])) {
					unset($this->attributes['checked']);
				} else {
					$this->attributes['checked'] = 'checked';
				}
			}
			if (isset($this->attributes['readonly'])) {
				if (empty($this->attributes['readonly'])) {
					unset($this->attributes['readonly']);
				} else {
					$this->attributes['readonly'] = 'readonly';
				}
			}
			if (isset($this->attributes['disabled'])) {
				if (empty($this->attributes['disabled'])) {
					unset($this->attributes['disabled']);
				} else {
					$this->attributes['disabled'] = 'disabled';
				}
			}
			$h = '';
			if (!empty($this->label)) {
				$h .= '<label';
				if (!empty($this->accessKey)) {
					$h .= ' accesskey="'.htmlspecialchars($this->accessKey).'"';
				}
				$h .= '>';
				if ($this->labelPosition == 'before') $h .= $this->label.' ';
			}
			$h .= '<input';
			foreach ($this->attributes as $k => $v) {
				$h .= ' '.$k.'="'.htmlspecialchars($v).'"';
			}
			$h .= ' />';
			if (!empty($this->label)) {
				if ($this->labelPosition == 'after') $h .= $this->label.' ';
				$h .= '</label>';
			}
			return $h;
		}
	}
	
	function fetch () {
		return $this->make();
	}
	
	function display() {
		echo $this->make();
	}


}


?>