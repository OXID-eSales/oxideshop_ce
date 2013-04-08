<?php if (!defined('IN_WPRO')) exit; 
$array=array();
?><div class="UIImageRadio" id="UIImageRadio_<?php echo $UID ?>">
<?php $value = ''; $i=0; foreach($options as $label => $content): ?>
<a class="outset<?php if ($selected === $i || $selected == $content[1]) {$value = $content[1]; echo ' selected'; } ?>" onclick="<?php echo $UID ?>.select(this, '<?php echo htmlspecialchars($content[1]) ?>');" href="javascript:undefined" style="width:<?php echo (intval($width)+10) ?>px" title="<?php echo htmlspecialchars($label) ?>">
<img src="<?php echo htmlspecialchars($content[0]) ?>" width="<?php echo intval($width) ?>" height="<?php echo intval($height) ?>" alt="<?php echo htmlspecialchars($label) ?>" />
<br /><?php echo $label ?>
</a>
<?php array_push($array, addslashes($content[1])); $i ++; endforeach ?>
<div class="clear">&nbsp;</div>
<input type="hidden" name="<?php echo htmlspecialchars($name) ?>" value="<?php echo htmlspecialchars($value) ?>" />
</div>
<script type="text/javascript">
/*<![CDATA[ */
	var <?php echo $UID ?> = new wproUIImageRadio ();
	<?php echo $UID ?>.options = ['<?php echo implode("','", $array)?>'];
	<?php echo $UID ?>.init('<?php echo $UID ?>');
	<?php if (!empty($onChange)) : ?>
	<?php echo $UID ?>.onChange = function () { <?php echo $onChange ?> };
	<?php endif ?>
/* ]]>*/
</script>