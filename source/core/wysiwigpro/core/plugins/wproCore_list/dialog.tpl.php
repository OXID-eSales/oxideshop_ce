<?php if (!defined('IN_WPRO')) exit;
require_once(WPRO_DIR.'conf/defaultValues/wproCore_list.inc.php');
if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) {
	
	$UI = $this->createUIDropDown();
	$UI->label = $langEngine->get('wproCore_list', 'listType');
	$bulleted = $this->createUIImageRadio(); 
	$bulleted->name = 'bulleted';
	$bulleted->width = '86';
	$bulleted->height = '94';
	$bulleted->addOption($langEngine->get('wproCore_list', 'disc'), $themeURL.'misc/list.disc.gif', 'disc');
	$bulleted->addOption($langEngine->get('wproCore_list', 'circle'), $themeURL.'misc/list.circle.gif', 'circle');
	$bulleted->addOption($langEngine->get('wproCore_list', 'square'), $themeURL.'misc/list.square.gif', 'square');
	$UI->addOption($langEngine->get('wproCore_list', 'bulleted'), $bulleted->fetch());
	
	$numbered = $this->createUIImageRadio(); 
	$numbered->name = 'numbered';
	$numbered->width = '86';
	$numbered->height = '94';
	$numbered->addOption($langEngine->get('wproCore_list', 'decimal'), $themeURL.'misc/list.decimal.gif', 'decimal');
	$numbered->addOption($langEngine->get('wproCore_list', 'lower-roman'), $themeURL.'misc/list.lower-roman.gif', 'lower-roman');
	$numbered->addOption($langEngine->get('wproCore_list', 'upper-roman'), $themeURL.'misc/list.upper-roman.gif', 'upper-roman');
	$numbered->addOption($langEngine->get('wproCore_list', 'lower-alpha'), $themeURL.'misc/list.lower-alpha.gif', 'lower-alpha');
	$numbered->addOption($langEngine->get('wproCore_list', 'upper-alpha'), $themeURL.'misc/list.upper-alpha.gif', 'upper-alpha');
	
	$t = $this->createUI2ColTable();
	if ($EDITOR->featureIsEnabled('htmlDepreciated') || stristr($EDITOR->htmlVersion, ' start')) {
		$start = $this->createHTMLInput();
		//$start->label = $langEngine->get('wproCore_list', 'startFrom');
		$start->attributes = array('type'=>'text','size'=>'4','name'=>'start','value'=>'1','onchange'=>'checkStartField();');
		$t->addRow($langEngine->get('wproCore_list', 'startFrom'),$start->fetch(),'start');
	}
		
	$UI->addOption($langEngine->get('wproCore_list', 'numbered'), $numbered->fetch().$t->fetch());
		
	$UI->display();
	
}
	
$t = $this->createUI2ColTable();
$t->width = 'small';
$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'style','accesskey'=>'s');
if ($EDITOR->featureIsEnabled('dialogappearanceoptions')) {
	$UI->options = array_merge(array(''=>$langEngine->get('core', 'default')), $EDITOR->listStyles);
} else {
	$UI->options = array_merge(array('UL'=>$langEngine->get('wproCore_list', 'bulleted'), 'OL'=>$langEngine->get('wproCore_list', 'numbered')), $EDITOR->listStyles);
}
$strStyleOverrides = $EDITOR->featureIsEnabled('dialogappearanceoptions') ? $langEngine->get('core', 'styleOverrides') : '';
$UI->selected = $defaultValues['style'];
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'style'), 's'), $UI->fetch().' '.$strStyleOverrides, 'style');
$t->display();


?>
<script type="text/javascript">
/*<![CDATA[ */
	initList();
/* ]]>*/
</script>