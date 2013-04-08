<?php if (!defined('IN_WPRO')) exit;
require_once(WPRO_DIR.'conf/defaultValues/wproCore_ruler.inc.php');
$t = $this->createUI2ColTable();
$t->width='small';

if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) {
	
	if ($EDITOR->featureIsEnabled('htmlDepreciated')) {
		$s = $this->createHTMLSelect();
		$s->attributes = array('accesskey'=>'a','name'=>'rulerAlign');
		$s->options = array(''=>$langEngine->get('core', 'default'),'left'=>$langEngine->get('core', 'left'),'center'=>$langEngine->get('core', 'center'),'right'=>$langEngine->get('core', 'right'));
		$s->selected=$defaultValues['rulerAlign'];
		$t->addRow($this->underlineAccessKey($langEngine->get('core', 'align'), 'a'), $s->fetch(), 'rulerAlign');
	}
	
	$f = $this->createHTMLInput();
	$f->attributes = array('accesskey'=>'w','type'=>'text','name'=>'rulerWidth','value'=>$defaultValues['rulerWidth'],'size'=>'4');
	$s = $this->createHTMLSelect();
	$s->attributes = array('name'=>'widthUnits');
	$s->options = array('%'=>$langEngine->get('core', 'percent'),'px'=>$langEngine->get('core', 'pixels'));
	$s->selected=$defaultValues['widthUnits'];
	$t->addRow($this->underlineAccessKey($langEngine->get('core', 'width'),'w'), $f->fetch().$s->fetch(), 'rulerHeight');
	
	$f = $this->createHTMLInput();
	$f->attributes = array('accesskey'=>'h','type'=>'text','name'=>'rulerHeight','value'=>$defaultValues['rulerHeight'],'size'=>'4');
	$t->addRow($this->underlineAccessKey($langEngine->get('core', 'height'),'h'), $f->fetch().' '.$langEngine->get('core', 'pixels'), 'rulerHeight');
	
	$c = $this->createUIColorPicker();
	$c->name = 'rulerColor';
	$c->showInput = true;
	$c->accessKey = 'c';
	$c->color = $defaultValues['rulerColor'];
	$t->addRow($this->underlineAccessKey($langEngine->get('core', 'color'),'c'), $c->fetch().'<br /><br />', 'rulerColor');
}
$strStyleOverrides = $EDITOR->featureIsEnabled('dialogappearanceoptions') ? $langEngine->get('core', 'styleOverrides') : '';
$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'style','accesskey'=>'s');
$UI->options = array_merge(array(''=>$langEngine->get('core', 'default')),  $EDITOR->rulerStyles);
$UI->selected = $defaultValues['style'];
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'style'), 's'), $UI->fetch().'<br />'.$strStyleOverrides, 'style');

$t->display();
?>
<script type="text/javascript">
/*<![CDATA[ */
	var strApply = "<?php echo addslashes($langEngine->get('core', 'apply'))?>";
	initRuler();
/* ]]>*/
</script>