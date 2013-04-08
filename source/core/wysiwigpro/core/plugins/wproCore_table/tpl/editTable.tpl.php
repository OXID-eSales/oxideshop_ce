<?php if (!defined('IN_WPRO')) exit; ?>


<?php 
$tabs = $this->createUITabbed();
$tabs->startTab($this->underlineAccessKey($langEngine->get('wproCore_table', 'table'), 't'), array('accesskey'=>'t'), 'tabTable()');
?>

<!-- table tab -->
<?php if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) : ?>

<?php
$tabs4 = $this->createUITabbed();
$tabs4->startTab($this->underlineAccessKey($langEngine->get('core', 'basic'), 'b'), array('accesskey'=>'b'));
?>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'size')?></legend>
<?php
$t = $this->createUI2ColTable();
$t->width = 'medium';
$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'tableWidthUnits');
$UI->options = array('%'=>$langEngine->get('core', 'percent'),'px'=>$langEngine->get('core', 'pixels'));
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'preferredWidth'), 'w'), $this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'value' => '',
	'name' => 'tableWidth',
	'accesskey' => 'w',
)).$UI->fetch(), 'tableWidth');
$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'tableHeightUnits');
$UI->options = array('%'=>$langEngine->get('core', 'percent'),'px'=>$langEngine->get('core', 'pixels'));
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'preferredHeight'), 'h'), $this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'value' => '',
	'name' => 'tableHeight',
	'accesskey' => 'h',
)).$UI->fetch(), 'tableHeight');
$t->display();
?></fieldset>



<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'alignOnPage')?></legend>
<?php $a = $this->createUIImageRadio(); 
$a->name = 'tableAlign';
//$headers->selected = $defaultValues['headers'];
$a->addOption($langEngine->get('core', 'default'), $themeURL.'misc/table.default.gif', '');
$a->addOption($langEngine->get('core', 'left'), $themeURL.'misc/table.left.gif', 'left');
if ($EDITOR->featureIsEnabled('htmlDepreciated')) : 
$a->addOption($langEngine->get('core', 'center'), $themeURL.'misc/table.center.gif', 'center');
endif;
$a->addOption($langEngine->get('core', 'right'), $themeURL.'misc/table.right.gif', 'right');
//$headers->onChange =  'updatePreview()';
$a->display();
?>
</fieldset>

<?php endif ?>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('core', 'appearance')?></legend>
<?php
$strStyleOverrides = $EDITOR->featureIsEnabled('dialogappearanceoptions') ? $langEngine->get('core', 'styleOverrides') : '';	
$t = $this->createUI2ColTable();
$t->width = 'small';
$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'tableStyle','accesskey'=>'s');
$UI->options = array_merge(array(''=>$langEngine->get('core', 'default')), $EDITOR->tableStyles);
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'style'), 's'), $UI->fetch().'<br />'.$strStyleOverrides, 'tableStyle');
$t->display();
?>
</fieldset>

<?php if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) : ?>

<?php 
$tabs4->endTab();
$tabs4->startTab($this->underlineAccessKey($langEngine->get('core', 'options'), 'o'), array('accesskey'=>'o'));
?>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'bordersAndShading')?></legend>

<?php $t = $this->createUI2ColTable();
$t->width = 'medium';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'borderWidth'), 'w'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'value' => '',
	'name' => 'tableBorder',
	//'onchange' => 'updatePreview()',
	'accesskey' => 'w',
)).' '.$langEngine->get('core', 'pixels'), 'tableBorder');
$c = $this->createUIColorPicker();
$c->name = 'tableBorderColor';
$c->showInput = true;
//$c->onChange='updatePreview()';
//$c->color = $defaultValues['borderColor'];
$c->accessKey = 'd';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'borderColor'), 'd'), $c->fetch(), 'tableBorderColor');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'borderCollapse'), 'l'), 
$this->HTMLInput(array(
	'type' => 'checkbox',
	'value' => '1',
	'name' => 'tableBorderCollapse',
	//'onclick' => 'updatePreview()',
	'onclick' => 'borderCollapseChange(this.checked);',
	'accesskey' => 'l',
)), 'tableBorderCollapse');
$c = $this->createUIColorPicker();
$c->name = 'tableBackgroundColor';
$c->showInput = true;
//$c->onChange='updatePreview()';
//$c->color = $defaultValues['backgroundColor'];
$c->accessKey = 'a';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'backgroundColor'), 'a'), $c->fetch(), 'tableBackgroundColor');

$chooser = $this->createUIURLChooser();
$chooser->type='image';
$chooser->attributes = array(
	'type' => 'text',
	'size' => '20',
	'value' => '',
	'name' => 'tableBackgroundImage',
	'accesskey' => 'i',
);

$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'backgroundImage'), 'i'), 

$chooser->fetch()

, 'tableBackgroundImage');


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
	'name' => 'tableCellPadding',
	//'onchange' => 'updatePreview()',
	'accesskey' => 'm',
)).' '.$langEngine->get('core', 'pixels'), 'tableCellPadding');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'cellSpacing'), 's'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => 'tableCellSpacing',
	'value' => '',
	//'onchange' => 'updatePreview()',
	'accesskey' => 's',
)).' '.$langEngine->get('core', 'pixels'), 'tableCellSpacing');
$t->display();	
?>
</fieldset>

<?php endif ?>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'description')?></legend>
<?php
$t = $this->createUI2ColTable();
$t->width = 'small';
if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) {
	$UI = $this->createHTMLSelect();
	$UI->attributes = array('name'=>'tableCaptionAlign'/*,'onchange'=>'updatePreview()'*/);
	$UI->options = array(''=>$langEngine->get('core', 'default'),'top'=>$langEngine->get('core', 'top'),'bottom'=>$langEngine->get('core', 'bottom'),'left'=>$langEngine->get('core', 'left'),'right'=>$langEngine->get('core', 'right'),);
	//$UI->selected = $defaultValues['fixedColumnWidthsUnits'];
}
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'caption'), 'n'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '20',
	'name' => 'tableCaption',
	'value' => '',
	//'onchange' => 'updatePreview()',
	'accesskey' => 'n',
)).($EDITOR->featureIsEnabled('dialogappearanceoptions')?$UI->fetch():''), 'tableCaption');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'summary'), 'y'), 
'<textarea name="tableSummary" cols="23" rows="2" accesskey="y"></textarea>', 'tableSummary');
$t->display();
?>
</fieldset>
<?php if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) : ?>
<?php 
$tabs4->endTab();
$tabs4->display();
?>
<?php endif ?>

<?php 
$tabs->endTab();
$tabs->startTab($this->underlineAccessKey($langEngine->get('wproCore_table', 'row'), 'r'), array('accesskey'=>'r'), 'tabRow()');
?>

<!-- row -->

<div id="rowNumber">&nbsp;</div>

<?php if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) : ?>
<?php
$tabs3 = $this->createUITabbed();
$tabs3->startTab($this->underlineAccessKey($langEngine->get('core', 'basic'), 'b'), array('accesskey'=>'b'));
?>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'verticalAlignment')?></legend>
<?php $a = $this->createUIImageRadio(); 
$a->name = 'rowvAlign';
//$headers->selected = $defaultValues['headers'];
$a->addOption($langEngine->get('core', 'default'), $themeURL.'misc/td.middle.gif', '');
$a->addOption($langEngine->get('core', 'top'), $themeURL.'misc/td.top.gif', 'top');
$a->addOption($langEngine->get('core', 'middle'), $themeURL.'misc/td.middle.gif', 'middle');
$a->addOption($langEngine->get('core', 'bottom'), $themeURL.'misc/td.bottom.gif', 'bottom');
$a->onChange = 'changeCellVAlignDefault()';
//$headers->onChange =  'updatePreview()';
$a->display();
?>
<input type="checkbox" name="overrideCellAlignment" id="overrideCellAlignment" value="true" /> <?php echo $langEngine->get('wproCore_table', 'overrideCellAlignment') ?>

</fieldset>

<?php endif ?>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'headingCells')?></legend>
<?php
$t = $this->createUI2ColTable();
$t->width = 'medium';

$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'convertCells');
$UI->options = array('normal'=>$langEngine->get('wproCore_table', 'normalCell'),'heading'=>$langEngine->get('wproCore_table', 'headingCell'),'col'=>$langEngine->get('wproCore_table', 'columnHeading'),'row'=>$langEngine->get('wproCore_table', 'rowHeading'));

$t->addRow('<input type="checkbox" name="doConvertCells" id="doConvertCells" value="true" /> '.$this->underlineAccessKey($langEngine->get('wproCore_table', 'convertCells'), 't'), $UI->fetch(), 'convertCells');
$t->display();

?>
</fieldset>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('core', 'appearance')?></legend>
<?php
$strStyleOverrides = $EDITOR->featureIsEnabled('dialogappearanceoptions') ? $langEngine->get('core', 'styleOverrides') : '';	
$t = $this->createUI2ColTable();
$t->width = 'small';
$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'rowStyle','accesskey'=>'s');
$UI->options = array_merge(array(''=>$langEngine->get('core', 'default')), $EDITOR->rowStyles);
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'style'), 's'), $UI->fetch().'<br />'.$strStyleOverrides, 'rowStyle');
$t->display();
?>
</fieldset>

<?php if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) : ?>
<?php 
$tabs3->endTab();
$tabs3->startTab($this->underlineAccessKey($langEngine->get('core', 'options'), 'o'), array('accesskey'=>'o'));
?>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'bordersAndShading')?></legend>
<?php $t = $this->createUI2ColTable();
$t->width = 'medium';
$c = $this->createUIColorPicker();
$c->name = 'rowBackgroundColor';
$c->showInput = true;
//$c->onChange='updatePreview()';
//$c->color = $defaultValues['backgroundColor'];
$c->accessKey = 'a';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'backgroundColor'), 'a'), $c->fetch(), 'rowBackgroundColor');
$t->display();
?>
<input type="checkbox" name="overrideCellBackground" id="overrideCellBackground" value="true" /> <?php echo $langEngine->get('wproCore_table', 'overrideCellBackground') ?>

</fieldset>

<?php
$tabs3->endTab();
$tabs3->display();
?>
<?php endif ?>

<div class="rowCellChange">
<button class="largeButton" type="button" onclick="previousRow()"><img src="<?php echo $themeURL.'misc/arrow_up.gif' ?>" alt="" /> <?php echo $langEngine->get('wproCore_table', 'previousRow')?></button>
<button class="largeButton" type="button" onclick="nextRow()"><img src="<?php echo $themeURL.'misc/arrow_down.gif' ?>" alt="" /> <?php echo $langEngine->get('wproCore_table', 'nextRow')?></button>
</div>

<?php 
$tabs->endTab();
$tabs->startTab($this->underlineAccessKey($langEngine->get('wproCore_table', 'cell'), 'c'), array('accesskey'=>'c'), 'tabCell()');
?>


<!-- cell -->

<div id="cellNumber">&nbsp;</div>
<?php if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) : ?>
<?php
$tabs2 = $this->createUITabbed();
$tabs2->startTab($this->underlineAccessKey($langEngine->get('core', 'basic'), 'b'), array('accesskey'=>'b'));
?>


<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'size')?></legend>
<?php
$t = $this->createUI2ColTable();
$t->width = 'medium';
$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'cellWidthUnits');
$UI->options = array('%'=>$langEngine->get('core', 'percent'),'px'=>$langEngine->get('core', 'pixels'));
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'preferredWidth'), 'w'), $this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'value' => '',
	'name' => 'cellWidth',
	'accesskey' => 'w',
)).$UI->fetch(), 'cellWidth');
$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'cellHeightUnits');
$UI->options = array('%'=>$langEngine->get('core', 'percent'),'px'=>$langEngine->get('core', 'pixels'));
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'preferredHeight'), 'h'), $this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'value' => '',
	'name' => 'cellHeight',
	'accesskey' => 'h',
)).$UI->fetch(), 'cellHeight');
$t->display();
?>
</fieldset>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'verticalAlignment')?></legend>
<?php $a = $this->createUIImageRadio(); 
$a->name = 'cellvAlign';
$a->addOption($langEngine->get('core', 'default'), $themeURL.'misc/td.middle.gif', '');
$a->addOption($langEngine->get('core', 'top'), $themeURL.'misc/td.top.gif', 'top');
$a->addOption($langEngine->get('core', 'middle'), $themeURL.'misc/td.middle.gif', 'middle');
$a->addOption($langEngine->get('core', 'bottom'), $themeURL.'misc/td.bottom.gif', 'bottom');
$a->display();
?>

</fieldset>

<?php endif ?>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'headingCells')?></legend>
<?php
$t = $this->createUI2ColTable();
$t->width = 'medium';

$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'cellType');
$UI->options = array('normal'=>$langEngine->get('wproCore_table', 'normalCell'),'heading'=>$langEngine->get('wproCore_table', 'headingCell'),'col'=>$langEngine->get('wproCore_table', 'columnHeading'),'row'=>$langEngine->get('wproCore_table', 'rowHeading'));

$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'cellType'), 't'), $UI->fetch(), 'cellType');
$t->display();

?>
</fieldset>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('core', 'appearance')?></legend>
<?php
$strStyleOverrides = $EDITOR->featureIsEnabled('dialogappearanceoptions') ? $langEngine->get('core', 'styleOverrides') : '';	
$t = $this->createUI2ColTable();
$t->width = 'small';
$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'cellStyle','accesskey'=>'s');
$UI->options = array_merge(array(''=>$langEngine->get('core', 'default')), $EDITOR->cellStyles);
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'style'), 's'), $UI->fetch().'<br />'.$strStyleOverrides, 'cellStyle');
$t->display();
?>
</fieldset>

<?php if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) : ?>
<?php 
$tabs2->endTab();
$tabs2->startTab($this->underlineAccessKey($langEngine->get('core', 'options'), 'o'), array('accesskey'=>'o'));
?>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_table', 'bordersAndShading')?></legend>

<?php $t = $this->createUI2ColTable();
$t->width = 'medium';
$c = $this->createUIColorPicker();
$c->name = 'cellBackgroundColor';
$c->showInput = true;
//$c->onChange='updatePreview()';
//$c->color = $defaultValues['backgroundColor'];
$c->accessKey = 'a';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'backgroundColor'), 'a'), $c->fetch(), 'cellBackgroundColor');

$chooser = $this->createUIURLChooser();
$chooser->type = 'image';
$chooser->attributes = array(
	'type' => 'text',
	'size' => '20',
	'value' => '',
	'name' => 'cellBackgroundImage',
	'accesskey' => 'i',
);

$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_table', 'backgroundImage'), 'i'), 

$chooser->fetch()

, 'cellBackgroundImage');
$t->display();
?>
</fieldset>



<?php
$tabs2->endTab();
$tabs2->display();
?>
<?php endif ?>

<div class="rowCellChange">
<button class="largeButton" type="button" onclick="previousCell()"><img src="<?php echo $themeURL.'misc/arrow_left.gif' ?>" alt="" /> <?php echo $langEngine->get('wproCore_table', 'previousCell')?></button>
<button class="largeButton" type="button" onclick="nextCell()"><img src="<?php echo $themeURL.'misc/arrow_right.gif' ?>" alt="" /> <?php echo $langEngine->get('wproCore_table', 'nextCell')?></button>
</div>

<?php 
$tabs->endTab();
$tabs->display();
?>

<script type="text/javascript">
/*<![CDATA[ */
	var strCellNumber = "<?php echo addslashes($langEngine->get('wproCore_table', 'cellNumber')) ?>";
	var strRowNumber = "<?php echo addslashes($langEngine->get('wproCore_table', 'rowNumber')) ?>";
	initEditTable();
/* ]]>*/
</script>