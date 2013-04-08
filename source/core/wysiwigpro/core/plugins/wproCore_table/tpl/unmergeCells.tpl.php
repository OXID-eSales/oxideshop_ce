<?php if (!defined('IN_WPRO')) exit; ?>


<?php
$t = $this->createUI2ColTable();
$t->width = 'large';
//$UI->selected = '';
$t->addRow($langEngine->get('wproCore_table', 'numcolumns'), 
'<select name="cols" id="cols"></select>', 'cols');
$t->addRow($langEngine->get('wproCore_table', 'numrows'), 
'<select name="rows" id="rows"></select>', 'rows');
$t->display();			

?>

<script type="text/javascript">
/*<![CDATA[ */
	initUnmergeCells();
/* ]]>*/
</script>