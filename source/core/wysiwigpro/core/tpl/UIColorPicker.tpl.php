<?php if (!defined('IN_WPRO')) exit; ?><input <?php if($showInput && !empty($accessKey)) :?>accesskey="<?php echo $accessKey ?>" <?php endif?>type="<?php if($showInput):echo'text';else:echo'hidden';endif ?>" name="<?php echo htmlspecialchars($name) ?>" id="<?php echo htmlspecialchars($name) ?>" size="7" value="<?php if(!empty($color)):echo htmlspecialchars($color); endif ?>" onchange="<?php echo $UID ?>.setColor(this.value);" />
<button <?php if(!$showInput && !empty($accessKey)) :?>accesskey="<?php echo $accessKey ?>" <?php endif?>class="UIColorPicker" id="UIColorPicker_<?php echo $UID ?>" type="button" onclick="return <?php echo $UID ?>.onClick()"><div>&nbsp;</div></button>
<script type="text/javascript">
/*<![CDATA[ */
	var <?php echo $UID ?> = new wproUIColorPicker ();
	<?php echo $UID ?>.init('<?php echo $UID ?>');
	<?php if (!empty($onChange)) : ?>
	<?php echo $UID ?>.onChange = function () { <?php echo $onChange ?> };
	<?php endif ?>
	<?php if (!empty($color)) : ?>
	<?php echo $UID ?>.setColor('<?php echo addslashes($color) ?>', false);
	<?php endif ?>
/* ]]>*/
</script>