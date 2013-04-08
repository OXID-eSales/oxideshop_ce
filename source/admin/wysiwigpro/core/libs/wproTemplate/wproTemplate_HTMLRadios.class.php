<?php
if (!defined('IN_WPRO')) exit;
/* displays a group of checkboxes, takes care of selecting active boxes*/
class wproTemplate_HTMLRadios {
	
	var $separator = ' ';
	var $before = '';
	var $after = '';
	var $labelPosition = 'before';
	
	var $selected = null;
	var $options = array();
	var $accessKeys = array();
	
	var $name = '';
	
	function make () {	
		$h = '';
		foreach ($this->options as $k => $v) {
			$h.=$this->before;
			$f = $this->template->createHTMLInput();
			$attributes = array(
				'name'=>$this->name.'[]',
				'value'=>$k,
				'type'=>'radio',
			);
			if ($k == $this->selected) {
				$attributes['checked'] = 'checked';
			}
			$f->attributes = $attributes;
			$f->forceId = false;
			if (isset($accessKeys[$k])) {
				$f->accessKey = $accessKeys[$k];
			}
			$f->label = $v;
			$h.=$f->fetch();
			$h.=$this->after;
			$h.=$this->separator;
		}
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