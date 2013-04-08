<?php if (!defined('IN_WPRO')) exit; ?>
<fieldset class="UIDropDown">
<legend class="UIDropDownSelectHolder"><label for="<?php echo htmlspecialchars($UID) ?>"><?php echo $label ?></label>&nbsp;
<select name="<?php echo htmlspecialchars($UID) ?>" id="<?php echo htmlspecialchars($UID) ?>">
<?php $i=0; foreach($options as $label => $content): ?>     
<option<?php if ($i == $selected) echo ' selected="selected"'; ?> label="<?php echo htmlspecialchars($label) ?>" value="sPane_<?php echo htmlspecialchars($UID) ?>_<?php echo $i ?>"><?php echo $label ?></option>
<?php $i ++; endforeach ?>
</select>
</legend>
<div class="UIDropDownPaneHolder">
<?php $i=0; foreach($options as $label => $content): ?>    
<div id="sPane_<?php echo htmlspecialchars($UID) ?>_<?php echo $i ?>" class="UIDropDownPane">
<?php echo $content ?>
</div>
<?php $i++; endforeach ?>
</div>
</fieldset>

<script type="text/javascript">
/*<![CDATA[ */
	var <?php echo $UID ?> = new wproUISelect ();
	<?php if (!empty($onChange)) : ?>
	<?php echo $UID ?>.onchange = <?php echo $onChange ?>;
	<?php endif ?>
	<?php echo $UID ?>.init('<?php echo $UID ?>');
/* ]]>*/
</script>