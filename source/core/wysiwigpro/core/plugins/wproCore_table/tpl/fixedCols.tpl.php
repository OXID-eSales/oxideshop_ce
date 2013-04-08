<?php if (!defined('IN_WPRO')) exit; ?>


<?php
$t = $this->createUI2ColTable();
//$t->width = 'small';
$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'widthUnits');
$UI->options = array('%'=>$langEngine->get('core', 'percent'),'px'=>$langEngine->get('core', 'pixels'));
//$UI->selected = '';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'tableWidth'), 'w'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'value' => '',
	'name' => 'width',
	'accesskey' => 'w',
)).$UI->fetch(), 'width');
$t->display();			

?>
<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'columnWidths') ?></legend>
<div class="inset" id="scroll">
<div class="UI2ColTable medium" id="cols">
    
  </div>
</div>
</fieldset>

<script type="text/javascript">
/*<![CDATA[ */
	var strColumnNumber = "<?php echo addslashes($langEngine->get('wproCore_table', 'columnNumber')) ?>";
	var strPixels = "<?php echo addslashes($langEngine->get('core', 'pixels')) ?>";
	var strPercent = "<?php echo addslashes($langEngine->get('core', 'percent')) ?>";
	initFixedCols();
/* ]]>*/
</script>