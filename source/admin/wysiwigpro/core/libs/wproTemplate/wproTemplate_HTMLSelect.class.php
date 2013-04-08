<?php
if (!defined('IN_WPRO')) exit;
class wproTemplate_HTMLSelect {
	
	var $attributes = array();
	var $forceId = true;
	var $label = '';
	var $labelPosition = 'before';
	var $options = array();
	var $optGroups = array();
	var $selected = null;
	var $accessKey = '';
	
	function addOptGroup($label, $options) {
		$optGroups[$label] = $options;
	}
	
	function makeOptions ($options, $selected, $multiple=false) {
		if (is_array($options)) {
			$h = '';
			$i = 0;
			foreach ($options as $k => $v) {
				$h .= '<option label="'.htmlspecialchars($v).'" title="'.htmlspecialchars($v).'" value="'.htmlspecialchars($k).'"';
				if (is_array($selected) && $multiple) {
					if (in_array($k, $selected)) {
						$h.= ' selected="selected"';
					}
				} elseif ($selected === $i || $selected === $k) {
					$h.= ' selected="selected"';
				}
				$h.='>'.htmlspecialchars($v).'</option>';
				$i ++;
			}
			return $h;
		}
	}
	
	function make () {
		if (!empty($this->attributes)) {	
			if (is_array($this->selected) && !isset($this->attributes['multiple'])) {
				$this->selected = $this->selected[0];
			} else if (!is_array($this->selected) && isset($this->attributes['multiple'])) {
				$this->selected = array($this->selected);
			}
			if ($this->forceId && (isset($this->attributes['name']) && !isset($this->attributes['id']))) {
				$this->attributes['id'] = $this->attributes['name'];
			}
			if ((isset($this->attributes['accesskey']) || !empty($this->accessKey)) && !empty($this->label)) {
				$this->label = $this->template->underlineAccessKey($this->label, (isset($this->attributes['accesskey']) ? $this->attributes['accesskey'] : $this->accessKey));
			}
			$h = '';
			if (!empty($this->label)) {
				$h .= '<label';
				if (!empty($this->accessKey)) {
					$h .= ' accesskey="'.htmlspecialchars($this->accessKey).'"';
				}
				$h .= '>';
				if ($labelPosition == 'before') $h .= $this->label.' ';
			}
			$h .= '<select';
			foreach ($this->attributes as $k => $v) {
				$h .= ' '.$k.'="'.htmlspecialchars($v).'"';
			}
			$h .= '>';
			if (!empty($this->options)) $h.= $this->makeOptions($this->options, $this->selected);
			if (!empty($this->optGroups)) {
				foreach($this->optGroups as $k=>$v) {
					$h.='<optgroup label="'.htmlspecialchars($k).'">';
					$h.=$this->makeOptions($v, $this->selected);
					$h.='</optgroup>';
				}			
			}				
			$h.='</select>';
			if (!empty($this->label)) {
				if ($labelPosition == 'after') $h .= $this->label.' ';
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