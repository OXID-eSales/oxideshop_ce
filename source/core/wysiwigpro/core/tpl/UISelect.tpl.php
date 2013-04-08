<?php if (!defined('IN_WPRO')) exit;?><div class="UISelectLeftCol">
<select name="<?php echo htmlspecialchars($UID) ?>" id="<?php echo htmlspecialchars($UID) ?>" size="<?php echo intval($size) ?>">
<?php $i=0; foreach($options as $label => $content): ?>     
<option<?php if ($i == $selected) echo ' selected="selected"'; ?> label="<?php echo $label ?>" title="<?php echo $label ?>" value="sPane_<?php echo htmlspecialchars($UID) ?>_<?php echo $i ?>"><?php echo $label ?></option>
<?php $i ++; endforeach ?>
</select>
</div>
<div class="UISelectRightCol">
<?php $i=0; foreach($options as $label => $content): ?>    
<div id="sPane_<?php echo htmlspecialchars($UID) ?>_<?php echo $i ?>" class="UISelectPane">
<h2><?php echo $label ?></h2>
<?php echo $content ?>
</div>
<?php $i++; endforeach ?>
</div>
<script type="text/javascript">
/*<![CDATA[ */
	var <?php echo $UID ?> = new wproUISelect ();
	<?php echo $UID ?>.init('<?php echo $UID ?>');
/* ]]>*/
</script>