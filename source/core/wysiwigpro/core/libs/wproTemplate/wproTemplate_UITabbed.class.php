<?php
class wproTemplate_UITabbed {
	
	var $options = array();
	var $uid = 0;
	var $selected = 0;
	var $editorURL = '';
	var $attributes;
	var $onswap;
	
	function addOption($label, $content, $attrs='', $onswap='') {
		$this->options[$label] = $content;
		$attrStr = '';
		foreach ($attrs as $k => $v) {
			$attrStr .= ' '.$k.'="'.htmlspecialchars($v).'"';
		}
		$this->attributes[$label] = $attrStr;
		$this->onswap[$label] = $onswap;
	}
	
	function startTab($label, $attrs='', $onswap='') {
		$this->addOption($label, '##capture##', $attrs, $onswap);
		ob_start();
	}
	
	function endTab () {
		$o = array_reverse($this->options);
		foreach ($o as $k=>$v) {
			if ($v == '##capture##') {
				$this->options[$k] = ob_get_contents();
				ob_end_clean();
				break;
			}
		}
	}
	
	function make () {
		// close any unclosed tabs
		/*$o = array_reverse($this->options);
		foreach ($o as $k=>$v) {
			if ($v == '##capture##') {
				$this->options[$label] = ob_get_contents();
				ob_end_clean();
			}
		}*/
		
		$tpl = new wproTemplate();
		$tpl->templates = $this->template->templates;
		$tpl->bulkAssign(array(
			'selected' => $this->selected,
			'options' => $this->options,
			'attributes' => $this->attributes,
			'onswap' => $this->onswap,
			'UID' => 'tUI'.$this->uid,
		));
		$output = $tpl->fetch( WPRO_DIR.'core/tpl/UITabbed.tpl.php' );
		if ($this->uid==1) {
			$output = '<script type="text/javascript" src="core/js/wproUITabbed_src.js"></script>'.$output;
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