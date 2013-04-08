<?php if (!defined('IN_WPRO')) exit;
$f = $this->createUI2ColTable();
$f->addRow($this->underlineAccessKey($langEngine->get('wproCore_find','findWhat'), 'n'), $this->HTMLInput(array(
	'type' => 'text',
	'size' => '40',
	'name' => 'strSearch',
	'onkeyup' => 'checkEnableButtons()',
	//'onchange' => 'newSearch()',
	'accesskey' => 'n',
)), 'strSearch');
$str = '<br />';
$g = $this->createHTMLInput();
$g->attributes = array(
	'type' => 'checkbox',
	'name' => 'matchCase',
	//'onchange' => 'newSearch()',
	'accesskey' => 'h',
);
$g->label = $langEngine->get('wproCore_find','matchCase');
$g->labelPosition='after';
$str .= $g->fetch();
$str .=  '<br />';
$g = $this->createHTMLInput();
$g->attributes = array(
	'type' => 'checkbox',
	'name' => 'wholeWords',
	//'onchange' => 'newSearch()',
	'accesskey' => 'y',
);
$g->label = $langEngine->get('wproCore_find','matchWholeWords');
$g->labelPosition='after';
$str .= $g->fetch();
$str .= '<br />';
$f->addRow($this->underlineAccessKey($langEngine->get('wproCore_find','replaceWith'), 'i'), $this->HTMLInput(array(
	'type' => 'text',
	'size' => '40',
	'name' => 'strReplace',
	//'onchange' => 'newSearch()',
	'accesskey' => 'i',
)).$str, 'strReplace');
$f->display();	
?>
<script type="text/javascript">
/*<![CDATA[ */
	var strFinishedSearching = '<?php echo addslashes($langEngine->get('wproCore_find','JSFinishedSearching'))?>';
	var strReplacements = '<?php echo addslashes($langEngine->get('wproCore_find','JSReplacements'))?>';
	initFind();
/* ]]>*/
</script>