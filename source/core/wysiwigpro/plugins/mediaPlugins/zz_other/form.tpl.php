<?php if (!defined('IN_WPRO')) exit; 
require(WPRO_DIR.'conf/defaultValues/wproCore_fileBrowser.inc.php');
?>



<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_fileBrowser', 'properties')?></legend>
<?php 

$t = $this->createUI2ColTable();
$t->width = 'small';
	$s = $this->createHTMLSelect();
	$s->attributes = array('name'=>$prefix.'widthUnits');
	$s->options = array(''=>$langEngine->get('core', 'pixels'),'%'=>$langEngine->get('core', 'percent'));
	$s->selected=$defaultValues['otherWidthUnits'];
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'width'), 'w'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'width',
	'value' => $defaultValues['otherWidth'],
	'accesskey' => 'w',
)).$s->fetch(), $prefix.'width');

	$s = $this->createHTMLSelect();
	$s->attributes = array('name'=>$prefix.'heightUnits');
	$s->options = array(''=>$langEngine->get('core', 'pixels'),'%'=>$langEngine->get('core', 'percent'));
	$s->selected=$defaultValues['otherHeightUnits'];

$t->addRow($this->underlineAccessKey($langEngine->get('core', 'height'), 'h'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'height',
	'accesskey' => 'h',
	'value' => $defaultValues['otherHeight'],
)).$s->fetch(), $prefix.'height');
$t->display();

?>
</fieldset>
<fieldset>
<legend><input type="checkbox" name="<?php echo $prefix ?>useObjectTag" value="1" onclick="if(this.checked){document.getElementById('<?php echo $prefix ?>objectTag').style.display='block';}else{document.getElementById('<?php echo $prefix ?>objectTag').style.display='none';}" checked="checked" /> <?php echo $langEngine->get('wproCore_fileBrowser', 'objectTag')?></legend>

<div id="<?php echo $prefix ?>objectTag">
<?php

$t = $this->createUI2ColTable();
$t->width = 'small';

$chooser = $this->createUIURLChooser();
$chooser->type='link';
$chooser->attributes = array(
	'type' => 'text',
	'size' => '16',
	'name' => $prefix.'classid',
	'value' => '',
);
$t->addRow($langEngine->get('wproCore_fileBrowser', 'classid'), $chooser->fetch(), $prefix.'classid');

$chooser = $this->createUIURLChooser();
$chooser->type='link';
$chooser->attributes = array(
	'type' => 'text',
	'size' => '16',
	'name' => $prefix.'codebase',
	'value' => '',
);
$t->addRow($langEngine->get('wproCore_fileBrowser', 'codebase'), $chooser->fetch(), $prefix.'classid');

$chooser = $this->createUIURLChooser();
$chooser->type='media';
$chooser->attributes = array(
	'type' => 'text',
	'size' => '16',
	'name' => $prefix.'data',
	'value' => '',
);
$t->addRow($langEngine->get('wproCore_fileBrowser', 'data'), $chooser->fetch(), $prefix.'data');

$t->addRow($langEngine->get('wproCore_fileBrowser', 'codetype'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '18',
	'name' => $prefix.'codetype',
	'value' => '',
)), $prefix.'codetype');

$t->addRow($langEngine->get('wproCore_fileBrowser', 'type'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '18',
	'name' => $prefix.'type',
	'value' => '',
)), $prefix.'type');

$t->addRow($langEngine->get('wproCore_fileBrowser', 'archive'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '18',
	'name' => $prefix.'archive',
	'value' => '',
)), $prefix.'archive');

$t->addRow($langEngine->get('wproCore_fileBrowser', 'standby'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '18',
	'name' => $prefix.'standby',
	'value' => '',
)), $prefix.'standby');

$t->display();

?>
<script type="text/javascript">
function <?php echo $prefix ?>removeParam(name) {
	this.parentNode.parentNode.removeChild(this.parentNode);
}
function <?php echo $prefix ?>createInput(name, value) {
	var i = document.createElement('INPUT');
	i.setAttribute('name', name);
	i.setAttribute('size', '9');
	if (value) {
		i.value = value;
	}
	return i;
}
function <?php echo $prefix ?>addParam(name, div, nameValue, valueValue) {
	var l1 = document.createElement('LABEL');
	l1.style.whiteSpace = 'nowrap';
	var l2 = document.createElement('LABEL');
	l2.style.whiteSpace = 'nowrap';
	
	var nt = document.createTextNode(strName);
	var vt = document.createTextNode(strValue);
	
	var s = document.createTextNode(' ');
	var s2 = document.createTextNode(' ');
	
	var n = <?php echo $prefix ?>createInput(name+'_name', nameValue);
	var v = <?php echo $prefix ?>createInput(name+'_value', valueValue);
	
	l1.appendChild(nt);
	l1.appendChild(n);
	
	l2.appendChild(vt);
	l2.appendChild(v);
	
	var d = document.createElement('DIV');
	d.appendChild(l1);
	d.appendChild(s2);
	d.appendChild(l2);
	d.appendChild(s);
	
	var r = document.createElement('INPUT');
	r.setAttribute('type', 'button');
	r.setAttribute('value', '-');
	r.onclick = <?php echo $prefix ?>removeParam;
	d.appendChild(r);
	
	var h = document.getElementById(div);
	h.appendChild(d);
	
	try{n.focus();}catch(e){}
}
var strName = '<?php echo $langEngine->get('wproCore_fileBrowser', 'name')?>';
var strValue = '<?php echo $langEngine->get('wproCore_fileBrowser', 'value')?>'
</script>
<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_fileBrowser', 'parameters')?></legend>

<input type="button" value="+" name="<?php echo $prefix ?>addObjectParam" onclick="<?php echo $prefix ?>addParam('<?php echo $prefix ?>objectParams', '<?php echo $prefix ?>objectparamholder')" />
<div id="<?php echo $prefix ?>objectparamholder"></div>

</fieldset>
</div>
</fieldset>
<fieldset>
<legend><input type="checkbox" name="<?php echo $prefix ?>useEmbedTag" value="1" onclick="if(this.checked){document.getElementById('<?php echo $prefix ?>embedTag').style.display='block';}else{document.getElementById('<?php echo $prefix ?>embedTag').style.display='none';}" /> <?php echo $langEngine->get('wproCore_fileBrowser', 'embedTag')?></legend>
<div id="<?php echo $prefix ?>embedTag" style="display:none">

<?php

$t = $this->createUI2ColTable();
$t->width = 'small';

$chooser = $this->createUIURLChooser();
$chooser->type='media';
$chooser->attributes = array(
	'type' => 'text',
	'size' => '16',
	'name' => $prefix.'embedsrc',
	'value' => '',
);
$t->addRow($langEngine->get('wproCore_fileBrowser', 'src'), $chooser->fetch(), $prefix.'embedsrc');

$chooser = $this->createUIURLChooser();
$chooser->type='link';
$chooser->attributes = array(
	'type' => 'text',
	'size' => '16',
	'name' => $prefix.'embedpluginspage',
	'value' => '',
);
$t->addRow($langEngine->get('wproCore_fileBrowser', 'pluginspage'), $chooser->fetch(), $prefix.'embedpluginspage');

$t->addRow($langEngine->get('wproCore_fileBrowser', 'type'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '18',
	'name' => $prefix.'embedtype',
	'value' => '',
)), $prefix.'embedtype');

$t->display();

?>


<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_fileBrowser', 'parameters')?></legend>

<input type="button" value="+" name="<?php echo $prefix ?>addEmbedParam" onclick="<?php echo $prefix ?>addParam('<?php echo $prefix ?>embedParams', '<?php echo $prefix ?>embedparamholder')" />
<div id="<?php echo $prefix ?>embedparamholder"></div>

</fieldset>
</div>
</fieldset>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_fileBrowser', 'alternateContent')?></legend>
<textarea name="<?php echo $prefix ?>alternateContent" value="" style="width:95%" cols="18" rows="10"></textarea>
</fieldset>