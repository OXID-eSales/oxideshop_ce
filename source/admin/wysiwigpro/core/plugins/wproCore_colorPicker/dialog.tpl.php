<?php if (!defined('IN_WPRO')) exit; 
function sanitize_color($color) {
	$color = preg_replace("/[^#a-z0-9]/si", '', $color);
	return $color;
}
?>
<div class="cpColHolder">
<div class="cpLeftCol">
<div id="mOverColorDisplay">&nbsp;</div>
</div>
<div class="cpRightCol">
<div id="selectedColorDisplay"<?php if (!empty($selectedColor)) echo ' style="background-color: '.htmlspecialchars(sanitize_color($selectedColor)).'"'; ?>>&nbsp;</div>
<?php echo $this->HTMLInput(array('name' => 'selectedColor', 'value' => htmlspecialchars(sanitize_color($selectedColor)), 'size'=>'6', 'onchange'=>'selectColorAction (this.value)')) ?>
</div>
<div class="cpCenterCol">
<div id="mOverColorText">&nbsp;</div>
</div>
</div>
<div class="clear">&nbsp;</div>
<?php
$UI = $this->createUIDropDown();
function colorsTable ($colors, $sd=false) {
	$h = '<div class="colorTable">';
	if ($sd) {
		$h .= '<a class="defaultColor" href="javascript:undefined" title="">Default</a>';
	}
	$otr = 1;
	foreach ($colors as $k => $v) {
		if (empty($v)) continue;
		$h .= '<a href="javascript:undefined" title="'. htmlspecialchars(sanitize_color($v)) . '">&nbsp;</a>';					
	}
	$h .= '</div>';
	return $h;
}
$panes = array();
foreach ($colors as $k => $v) {
	$UI->addOption($langEngine->get('wproCore_colorPicker', $k), colorsTable($v,true));
}
ksort($UI->options);
reset($UI->options);
$UI->label = $langEngine->get('wproCore_colorPicker', 'colorPalette');
$UI->display();
?>
<fieldset id="recentlyUsed" class="singleLine">
<legend><?php echo $langEngine->get('core', 'recentlyUsed')?></legend>
<div class="recentlyUsed">
<?php echo colorsTable($recentlyUsed);  ?>
</div>
</fieldset>
<script type="text/javascript">
/*<![CDATA[ */
	initColorPicker();
/* ]]>*/
</script>