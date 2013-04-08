<?php if (!defined('IN_WPRO')) exit; ?>

<div class="leftColumn">

<?php require_once(WPRO_DIR.'conf/defaultValues/wproCore_table.inc.php');
$tabs = $this->createUITabbed();
$tabs->startTab($this->underlineAccessKey($langEngine->get('core', 'basic'), 'b'), array('accesskey'=>'b'));
?><fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'size')?></legend>

<?php
$t = $this->createUI2ColTable();
$t->width = 'small';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'rows'), 'r'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'value' => $defaultValues['rows'],
	'name' => 'rows',
	'onchange' => 'updatePreview()',
	'accesskey' => 'r',
)), 'rows');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'columns'), 'c'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'value' => $defaultValues['cols'],
	'name' => 'cols',
	'onchange' => 'updatePreview()',
	'accesskey' => 'c',
)), 'cols');

if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) {
	
	$UI = $this->createHTMLSelect();
	$UI->attributes = array('name'=>'widthUnits','onchange'=>'updatePreview()');
	$UI->options = array('%'=>$langEngine->get('core', 'percent'),'px'=>$langEngine->get('core', 'pixels'));
	$UI->selected = $defaultValues['widthUnits'];
	$t->addRow($this->underlineAccessKey($langEngine->get('core', 'width'), 'w'), 
	$this->HTMLInput(array(
		'type' => 'text',
		'size' => '3',
		'value' => $defaultValues['width'],
		'name' => 'width',
		'onchange' => 'previousWidth=this.value;updatePreview()',
		'accesskey' => 'w',
	)).$UI->fetch(), 'width');
}

$t->display();			
?>

</fieldset>
<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'headingCells')?></legend>
<?php $headers = $this->createUIImageRadio(); 
$headers->name = 'headers';
$headers->selected = $defaultValues['headers'];
$headers->addOption($langEngine->get('wproCore_table', 'none'), $themeURL.'misc/headers.none.gif', 'none');
$headers->addOption($langEngine->get('wproCore_table', 'left'), $themeURL.'misc/headers.left.gif', 'left');
$headers->addOption($langEngine->get('wproCore_table', 'top'), $themeURL.'misc/headers.top.gif', 'top');
$headers->addOption($langEngine->get('wproCore_table', 'both'), $themeURL.'misc/headers.both.gif', 'both');
$headers->onChange =  'updatePreview()';
$headers->display();
?>
</fieldset>
<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'columnWidths')?></legend>
<label>
<?php
	echo $this->HTMLInput(array(
	'type' => 'radio',
	'size' => '2',
	'value' => 'percent',
	'checked' => ($defaultValues['columnWidths'] == 'percent') ? 'checked' : '',
	'name' => 'columnWidths',
	'onclick' => 'colWidthChange(this.value)',
	'accesskey' => 'e',
));
?><?php echo $this->underlineAccessKey($langEngine->get('wproCore_table', 'spaceEvenly'), 'e')?></label>
<br />
<label>
<?php
	echo $this->HTMLInput(array(
	'type' => 'radio',
	'size' => '2',
	'value' => 'noWidth',
	'checked' => ($defaultValues['columnWidths'] == 'noWidth') ? 'checked' : '',
	'name' => 'columnWidths',
	'onclick' => 'colWidthChange(this.value)',
	'accesskey' => 'a',
));
?><?php echo $this->underlineAccessKey($langEngine->get('wproCore_table', 'autoFit'), 'a')?></label>
<br />
<label>
<?php
	echo $this->HTMLInput(array(
	'type' => 'radio',
	'size' => '2',
	'value' => 'fixedWidth',
	'checked' => ($defaultValues['columnWidths'] == 'fixedWidth') ? 'checked' : '',
	'name' => 'columnWidths',
	'onclick' => 'colWidthChange(this.value)',
	'accesskey' => 'f',
));
?><?php echo $this->underlineAccessKey($langEngine->get('wproCore_table', 'fixedWidth'), 'f')?></label>
<?php echo ' '.$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'value' => $defaultValues['fixedColumnWidths'],
	'name' => 'fixedColumnWidths',
	'onchange' => 'updatePreview()',
	'disabled' => ($defaultValues['columnWidths'] == 'fixedWidth') ? '' : 'disabled',
)).' '.$langEngine->get('core', 'pixels');
?>
</fieldset>
<fieldset class="singleLine">
<legend><?php echo $langEngine->get('core', 'appearance')?></legend>
<?php
$strStyleOverrides = $EDITOR->featureIsEnabled('dialogappearanceoptions') ? $langEngine->get('core', 'styleOverrides') : '';	
$t = $this->createUI2ColTable();
$t->width = 'small';
$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'style','accesskey'=>'s','onchange'=>'updatePreview()');
$UI->options = array_merge(array(''=>$langEngine->get('core', 'default')), $EDITOR->tableStyles);
$UI->selected = $defaultValues['style'];
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'style'), 's'), $UI->fetch().'<br />'.$strStyleOverrides, 'style');
$t->display();
?>
</fieldset>
<?php 
$tabs->endTab();
$tabs->startTab($this->underlineAccessKey($langEngine->get('core', 'options'), 'o'), array('accesskey'=>'o'));

if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) :

?>
<fieldset class="singleLine borders">
<legend><?php echo $langEngine->get('wproCore_table', 'bordersAndShading')?></legend>

<?php $t = $this->createUI2ColTable();
$t->width = 'medium';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'borderWidth'), 'w'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'value' => $defaultValues['border'],
	'name' => 'border',
	'onchange' => 'updatePreview()',
	'accesskey' => 'w',
)).' '.$langEngine->get('core', 'pixels'), 'border');
$c = $this->createUIColorPicker();
$c->name = 'borderColor';
$c->showInput = true;
$c->onChange='updatePreview()';
$c->color = $defaultValues['borderColor'];
$c->accessKey = 'o';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'borderColor'), 'o'), $c->fetch(), 'borderColor');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'borderCollapse'), 'l'), 
$this->HTMLInput(array(
	'type' => 'checkbox',
	'value' => '1',
	'name' => 'borderCollapse',
	'checked' => ($defaultValues['borderCollapse'] == 'collapse') ? 'checked' : '',
	'onclick' => 'borderCollapseChange(this.checked);',
	'accesskey' => 'l',
)), 'borderCollapse');
$c = $this->createUIColorPicker();
$c->name = 'backgroundColor';
$c->showInput = true;
$c->onChange='updatePreview()';
$c->color = $defaultValues['backgroundColor'];
$c->accessKey = 'a';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'backgroundColor'), 'a'), $c->fetch(), 'backgroundColor');
$t->display();
?>
</fieldset>
<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'spacingAndPadding')?></legend>

<?php
$t = $this->createUI2ColTable();
$t->width = 'medium';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'cellPadding'), 'm'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => 'cellPadding',
	'value' => $defaultValues['cellPadding'],
	'onchange' => 'updatePreview()',
	'accesskey' => 'm',
)).' '.$langEngine->get('core', 'pixels'), 'cellPadding');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'cellSpacing'), 's'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => 'cellSpacing',
	'value' => $defaultValues['cellSpacing'],
	'disabled' => ($defaultValues['borderCollapse'] == 'collapse') ? 'disabled' : '',
	'onchange' => 'updatePreview()',
	'accesskey' => 's',
)).' '.$langEngine->get('core', 'pixels'), 'cellSpacing');
$t->display();	
?>
</fieldset>
<?php endif ?>
<fieldset class="singleLine description">
<legend><?php echo $langEngine->get('wproCore_table', 'description')?></legend>
<?php
$t = $this->createUI2ColTable();
$t->width = 'small';
if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) {
	$UI = $this->createHTMLSelect();
	$UI->attributes = array('name'=>'captionAlign','onchange'=>'updatePreview()');
	$UI->options = array(''=>$langEngine->get('core', 'default'),'top'=>$langEngine->get('core', 'top'),'bottom'=>$langEngine->get('core', 'bottom'),'left'=>$langEngine->get('core', 'left'),'right'=>$langEngine->get('core', 'right'),);
	$UI->selected = $defaultValues['fixedColumnWidthsUnits'];
}
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'caption'), 'c'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '20',
	'name' => 'caption',
	'value' => $defaultValues['caption'],
	'onchange' => 'updatePreview()',
	'accesskey' => 'c',
)).($EDITOR->featureIsEnabled('dialogappearanceoptions')?$UI->fetch():''), 'caption');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'summary'), 'y'), 
'<textarea name="summary" cols="23" rows="3" accesskey="y">'.$defaultValues['summary'].'</textarea>', 'summary');
$t->display();
?>
</fieldset>
<?php 
$tabs->endTab();
$tabs->display();
?>

</div>


<div id="previewColumn" class="rightColumn">

<br /><br /><?php echo $langEngine->get('wproCore_table', 'preview') ?>
<fieldset class="frameFix">
<iframe id="tablePreview" name="tablePreview" src="core/html/iframeSecurity.htm" class="previewFrame" frameborder="0" width="100%"></iframe></fieldset>

</div>

<script type="text/javascript">
/*<![CDATA[ */
	var strColumnNumber = "<?php echo addslashes($langEngine->get('wproCore_table', 'columnNumber')) ?>";
	var strRowNumber = "<?php echo addslashes($langEngine->get('wproCore_table', 'rowNumber')) ?>";
	initNewTable();
/* ]]>*/
</script>