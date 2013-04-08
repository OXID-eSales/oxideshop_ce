<?php
class wproTemplate_UIURLChooser {
	var $attributes = array();
	var $accessKey = '';
	var $name = '';
	var $forceId = true;
	var $label = '';
	var $labelPosition = 'before';
	
	var $type ='link';
		
	function make () {	
		
		global $EDITOR;
		
		if (!empty($this->name)) {
			$this->attributes['name'] = $this->name;
		} else {
			$this->name = $this->attributes['name'];
		}
		if ($this->forceId) {
			$this->attributes['id'] = $this->name;
		}
		if (!empty($this->accessKey)) {
			$this->attributes['accesskey'] = $this->accessKey;
		}
		
		$input = $this->template->createHTMLInput();
		$input->attributes = $this->attributes;
		$input->forceId = $this->forceId;
		$input->label = $this->label;
		$input->labelPosition = $this->labelPosition;
		
		$str = '';
		
		$str.=$input->fetch();
		
		$function = $EDITOR->triggerEvent('onFileChooserButtonJS', array('type'=>$this->type,'field'=>$this->name));
		
		if (empty($function)) {
			$function = array('dialog.openFileBrowser(\''.$this->type.'\', function(url){document.dialogForm.elements[\''.$this->name.'\'].value=url;}, function(){return document.dialogForm.elements[\''.$this->name.'\'].value;})');
		}
				
		$str.='<button type="button" class="chooserButton" onclick="'.htmlspecialchars(implode(';',$function)).'" style="background-image:url(\''.$EDITOR->themeFolderURL.$EDITOR->theme.'/wysiwygpro/buttons/'.$this->type.'.gif\')">&nbsp;</button>';
		
		
		return $str;
	}
	
	function fetch () {
		return $this->make();
	}
	
	function display() {
		echo $this->make();
	}
}
?>
