<?php if (!defined('IN_WPRO')) exit; ?>


<?php
$t = $this->createUI2ColTable();
$t->width = 'large';
//$UI->selected = '';
$t->addRow($langEngine->get('wproCore_table', 'numcolumns'), 
'<select name="cols" id="cols" onchange="highlightAffectedCells();this.focus()"><option value="" label="'.$langEngine->get('core', 'none').'">'.$langEngine->get('core', 'none').'</option></select>', 'cols');
$t->addRow($langEngine->get('wproCore_table', 'numrows'), 
'<select name="rows" id="rows" onchange="highlightAffectedCells();this.focus()"><option value="" label="'.$langEngine->get('core', 'none').'">'.$langEngine->get('core', 'none').'</option></select>', 'rows');
$t->display();			

?>

<script type="text/javascript">
/*<![CDATA[ */
	initMergeCells();
/* ]]>*/
</script>