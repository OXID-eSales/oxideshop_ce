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
	$s->selected=$defaultValues['flvplayerWidthUnits'];
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'width'), 'w'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'width',
	'value' => $defaultValues['flvplayerWidth'],
)).$s->fetch(), $prefix.'width');

	$s = $this->createHTMLSelect();
	$s->attributes = array('name'=>$prefix.'heightUnits');
	$s->options = array(''=>$langEngine->get('core', 'pixels'),'%'=>$langEngine->get('core', 'percent'));
	$s->selected=$defaultValues['flvplayerHeightUnits'];

$t->addRow($this->underlineAccessKey($langEngine->get('core', 'height'), 'h'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'height',
	'value' => empty($defaultValues['flvplayerControllerHeight'])?$defaultValues['flvplayerHeightUnits']:intval($defaultValues['flvplayerControllerHeight'])+intval($defaultValues['flvplayerHeight']),
)).$s->fetch(), $prefix.'height');

$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'autoplay'), 'a'), '<input type="checkbox" name="'.$prefix.'autoplay" value="true" '.($defaultValues['flvplayerAutoplay']?' checked="checked"':'').' />', 'autoplay');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'loop'), 'l'), '<input type="checkbox" name="'.$prefix.'loop" value="true" '.($defaultValues['flvplayerLoop']?' checked="checked"':'').' />', 'loop');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'showPlaylist'), 'p'), '<input type="checkbox" name="'.$prefix.'playlist" value="true" onclick="FB.embedPlugins[\'flvplayer\'].updateWidth(\''.$prefix.'\', this.checked);" />', 'playlist');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'controller'), 'c'), '<input type="checkbox" name="'.$prefix.'controller" value="true" '.($defaultValues['flvplayerController']?' checked="checked"':'').' onclick="FB.embedPlugins[\'flvplayer\'].updateHeight(\''.$prefix.'\', this.checked);" />', 'controller');


$t->display();

?>
<input type="hidden" name="<?php echo $prefix ?>controllerHeight" value="<?php echo intval($defaultValues['flvplayerControllerHeight']) ?>" />
<input type="hidden" name="<?php echo $prefix ?>playlistWidth" value="<?php echo intval($defaultValues['flvplayerPlaylistWidth']) ?>" />
</fieldset>

<!--<fieldset class="singleLine">
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
</fieldset>-->