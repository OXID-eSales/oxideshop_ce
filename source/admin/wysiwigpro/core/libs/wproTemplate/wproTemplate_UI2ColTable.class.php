<?php
class wproTemplate_UI2ColTable {

	var $width = 'medium';
	var $rows = array();
	
	function addRow($col1, $col2='', $labelFor='') {
		
		$col1Str = '';
		$col2Str = '';
		
		$for = '';
		if (!empty($labelFor)) {
			$col1Str = '<label class="ltd" for="'.$labelFor.'">'.$col1.'</label>';
		} else {
			$col1Str = '<div class="ltd">'.$col1.'</div>';
		}
		$col2Str = '<div class="rtd">'.$col2.'</div>';
		array_push($this->rows, '<div class="row">'.$col1Str.$col2Str.'</div>');
	}
	
	function make () {	
		$str = '<div class="UI2ColTable '.$this->width.'">';
		foreach ($this->rows as $val) {
			$str .= $val;
		}
		$str .= '</div>';
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