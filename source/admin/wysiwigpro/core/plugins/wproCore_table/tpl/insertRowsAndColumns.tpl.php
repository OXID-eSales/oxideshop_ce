<?php if (!defined('IN_WPRO')) exit; ?>


<?php
$t = $this->createUI2ColTable();
//$t->width = 'large';
//$UI->selected = '';
$t->addRow($langEngine->get('wproCore_table', 'numrows'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'value' => '1',
	'name' => 'rows',
	'accesskey' => 'r',
)).' <select name="rowPosition" id="rowPosition"><option value="above" label="'.$langEngine->get('wproCore_table', 'positionAbove').'">'.$langEngine->get('wproCore_table', 'positionAbove').'</option><option value="below" label="'.$langEngine->get('wproCore_table', 'positionBelow').'">'.$langEngine->get('wproCore_table', 'positionBelow').'</option></select>', 'cols');

$t->addRow($langEngine->get('wproCore_table', 'numcolumns'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'value' => '0',
	'name' => 'cols',
	'accesskey' => 'c',
)).' <select name="colPosition" id="colPosition"><option value="left" label="'.$langEngine->get('wproCore_table', 'positionLeft').'">'.$langEngine->get('wproCore_table', 'positionLeft').'</option><option value="right" label="'.$langEngine->get('wproCore_table', 'positionRight').'">'.$langEngine->get('wproCore_table', 'positionRight').'</option></select>', 'cols');

$t->display();			

?>

<!--<p><label><input type="checkbox" name="columnWidthEditor" id="columnWidthEditor" value="true" /> <?php echo $langEngine->get('wproCore_table', 'openColumnWidthEditor') ?></label></p>-->

<script type="text/javascript">
/*<![CDATA[ */
	initInsertRowsAndColumns();
/* ]]>*/
</script>