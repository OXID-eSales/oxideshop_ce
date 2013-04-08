<?php if (!defined('IN_WPRO')) exit; ?>
<div class="selectCharacter">
<label><?php echo $this->underlineAccessKey($langEngine->get('wproCore_specialCharacters', 'insert'), 'c') ?> 
<input name="selectedCharacter" type="text" size="8" accesskey="c" onchange="displayCharacter(this.value)" /></label>
<span id="charDisplay">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</div>
<?php
$UI = $this->createUISelect();
function sanitizeCharacter($ch) {
	$ch = preg_replace("/[^#a-z0-9]/si", '', $ch);
	return $ch;
}
function specialCharactersTable ($symbols, $id) {
	global $DIALOG;
	$h = '';
	$otr = 1;
	$showWarning = false;
	foreach ($symbols as $k => $v) {
		if (empty($v)) continue;
		if ($v == 'warning') {$showWarning = true;continue;}
		$h .= '<a href="javascript:undefined" class="cl" title="'.'&amp;' . htmlspecialchars(sanitizeCharacter($v)) . ';'.'">'. '&' . htmlspecialchars(sanitizeCharacter($v)) . ';' .'</a>';					
	}	
	$h .= '</div>';
	if ($showWarning) {
		$h.='<div class="smallWarning">'.$DIALOG->langEngine->get('wproCore_specialCharacters', 'browserWarning').'</div>';
		$h = '<div class="inset characterScroll small" id="'.$id.'">'.$h;
	} else {
		$h = '<div class="inset characterScroll" id="'.$id.'">'.$h;
	}
	
	
	return $h;
}
$panes = array();
foreach ($symbols as $k => $v) {
	$UI->addOption($langEngine->get('wproCore_specialCharacters', $k), specialCharactersTable($v, $k));
}
$UI->display();
?><br />
<fieldset id="recentlyUsed" class="singleLine">
<legend><?php echo $langEngine->get('core', 'recentlyUsed') ?></legend>
<?php echo specialCharactersTable($recentlyUsed, 'recent');  ?>
</fieldset>
<script type="text/javascript">
/*<![CDATA[ */
	initSpecialCharacters();
/* ]]>*/
</script>