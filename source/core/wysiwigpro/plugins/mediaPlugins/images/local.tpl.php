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
	$s->selected='px';

$t->addRow($langEngine->get('core', 'width'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'width',
)).$s->fetch(), $prefix.'width');

	$s = $this->createHTMLSelect();
	$s->attributes = array('name'=>$prefix.'heightUnits');
	$s->options = array(''=>$langEngine->get('core', 'pixels'),'%'=>$langEngine->get('core', 'percent'));
	$s->selected='px';

$t->addRow($langEngine->get('core', 'height'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'height',
)).$s->fetch(), $prefix.'height');


if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) : 

$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'border'), 'b'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'border',
	'value' => $defaultValues['imageBorder'],
	'accesskey' => 'b',
)).' '.$langEngine->get('core', 'pixels'), $prefix.'border');

endif; 

$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'screenTip'), 't'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '20',
	'name' => $prefix.'screenTip',
	'value' => $defaultValues['mediaScreenTip'],
	'accesskey' => 's',
)),  $prefix.'screenTip');
$t->display();	


?>
</fieldset>

<?php if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) : ?>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_fileBrowser', 'positioning')?></legend>

<?php 
$t = $this->createUI2ColTable();
$t->width = 'small';

$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>$prefix.'align','accesskey'=>'f');
$UI->options = array(
'' =>$langEngine->get('core', 'default'),
'top' => $langEngine->get('wproCore_fileBrowser', 'textTop'),
'middle' => $langEngine->get('wproCore_fileBrowser', 'textMiddle'),
'bottom' => $langEngine->get('wproCore_fileBrowser', 'textBottom'),
'left' => $langEngine->get('wproCore_fileBrowser', 'floatLeft'),
'right' => $langEngine->get('wproCore_fileBrowser', 'floatRight')
);
$UI->selected = $defaultValues['imageAlign'];

$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'textFlow'), 'f'), 
$UI->fetch(), $prefix.'align');

$t->display();

?>

<p><?php echo $langEngine->get('wproCore_fileBrowser', 'distanceToText')?></p>
<?php
$t = $this->createUI2ColTable();
$t->width = 'small';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'top'), 't'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'mtop',
	'value' => $defaultValues['imageMarginTop'],
	'accesskey' => 't',
)).' '.$langEngine->get('core', 'pixels'), $prefix.'mtop');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'bottom'), 'o'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'mbottom',
	'value' => $defaultValues['imageMarginBottom'],
	'accesskey' => 'o',
)).' '.$langEngine->get('core', 'pixels'), $prefix.'mbottom');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'left'), 'l'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'mleft',
	'value' => $defaultValues['imageMarginLeft'],
	'accesskey' => 'l',
)).' '.$langEngine->get('core', 'pixels'), $prefix.'mleft');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'right'), 'r'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'mright',
	'value' => $defaultValues['imageMarginRight'],
	'accesskey' => 'r',
)).' '.$langEngine->get('core', 'pixels'), $prefix.'mright');
$t->display();
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
$UI->attributes = array('name'=>$prefix.'style','accesskey'=>'s');
$UI->options = array_merge(array(''=>$langEngine->get('core', 'default')), $EDITOR->imageStyles);
$UI->selected = $defaultValues['imageStyle'];
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'style'), 's'), $UI->fetch().'<br />'.$strStyleOverrides, 'style');
$t->display();
?>
</fieldset>