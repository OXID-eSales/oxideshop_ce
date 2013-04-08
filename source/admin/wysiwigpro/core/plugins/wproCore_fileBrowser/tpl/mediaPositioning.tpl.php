<?php if (!defined('IN_WPRO')) exit; 
//include(WPRO_DIR.'conf/defaultValues/wproCore_fileBrowser.inc.php');
?>
<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_fileBrowser', 'properties')?></legend>
<?php

$t = $this->createUI2ColTable();
$t->width = 'small';

if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) : 

$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'border'), 'b'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => 'mediaborder',
	'value' => $defaultValues['mediaBorder'],
	'accesskey' => 'b',
	'onchange' => 'FB.mediaPreview()',
)).' '.$langEngine->get('core', 'pixels'),'mediaborder');

endif; 

$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'screenTip'), 't'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '20',
	'name' => 'mediascreenTip',
	'value' => $defaultValues['mediaScreenTip'],
	'accesskey' => 's',
	'onchange' => 'FB.mediaPreview();',
)),  'mediascreenTip');
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
$UI->attributes = array('name'=>'mediaalign','accesskey'=>'f','onchange'=>'FB.mediaPreview()');
$UI->options = array(
'' =>$langEngine->get('core', 'default'),
'top' => $langEngine->get('wproCore_fileBrowser', 'textTop'),
'middle' => $langEngine->get('wproCore_fileBrowser', 'textMiddle'),
'bottom' => $langEngine->get('wproCore_fileBrowser', 'textBottom'),
'left' => $langEngine->get('wproCore_fileBrowser', 'floatLeft'),
'right' => $langEngine->get('wproCore_fileBrowser', 'floatRight')
);
$UI->selected = $defaultValues['mediaAlign'];

$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'textFlow'), 'f'), 
$UI->fetch(), 'mediaalign');

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
	'name' => 'mediamtop',
	'value' => $defaultValues['mediaMarginTop'],
	'accesskey' => 't',
	'onchange'=>'FB.mediaPreview()'
)).' '.$langEngine->get('core', 'pixels'), 'mediamtop');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'bottom'), 'o'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => 'mediambottom',
	'value' => $defaultValues['mediaMarginBottom'],
	'accesskey' => 'o',
	'onchange'=>'FB.mediaPreview()'
)).' '.$langEngine->get('core', 'pixels'), 'mediambottom');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'left'), 'l'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => 'mediamleft',
	'value' => $defaultValues['mediaMarginLeft'],
	'accesskey' => 'l',
	'onchange'=>'FB.mediaPreview()'
)).' '.$langEngine->get('core', 'pixels'), 'mediamleft');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'right'), 'r'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => 'mediamright',
	'value' => $defaultValues['mediaMarginRight'],
	'accesskey' => 'r',
	'onchange'=>'FB.mediaPreview()'
)).' '.$langEngine->get('core', 'pixels'), 'mediamright');
$t->display();
?>
<br />
<?php echo $langEngine->get('wproCore_fileBrowser', 'positioningPreview'); ?>
<div id="mediastylepreview" class="previewFrame" style="padding:2px; height:70px; overflow:hidden; font-size:8px">
  <div><img id="mediawrappreview" src="<?php echo $themeURL; ?>misc/wrap_preview.gif" width="48" height="48" align="" alt="">Lorem ipsum, Dolor sit amet, consectetuer adipiscing loreum ipsum 
    edipiscing elit, sed diam nonummy nibh euismod tincidunt ut 
    laoreet dolore magna aliquam erat volutpat.Loreum ipsum edipiscing 
    elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore 
    magna aliquam erat volutpat. Ut wisi enim ad minim veniam, 
    quis nostrud exercitation ullamcorper suscipit. Lorem ipsum, 
    Dolor sit amet, consectetuer adipiscing loreum ipsum edipiscing 
    elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore 
    magna aliquam erat volutpat.</div>
</div>
</fieldset>
<?php endif ?>
<fieldset class="singleLine">
<legend><?php echo $langEngine->get('core', 'appearance')?></legend>
<?php
$strStyleOverrides = $EDITOR->featureIsEnabled('dialogappearanceoptions') ? $langEngine->get('core', 'styleOverrides') : '';	
$t = $this->createUI2ColTable();
$t->width = 'small';
$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'mediastyle','accesskey'=>'s','onchange'=>'FB.mediaPreview()');
$UI->options = array_merge(array(''=>$langEngine->get('core', 'default')), $EDITOR->imageStyles);
$UI->selected = $defaultValues['mediaStyle'];
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'style'), 's'), $UI->fetch().'<br />'.$strStyleOverrides, 'style');
$t->display();
?>
</fieldset>
