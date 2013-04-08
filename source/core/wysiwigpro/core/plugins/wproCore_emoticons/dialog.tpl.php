<?php if (!defined('IN_WPRO')) exit; ?>
<?php if ($custom) : ?>
<div id="emoticons" class="inset scroll custom">
<?php foreach ($files as $k => $v) : 
list($width,$height) = getimagesize($emoticonDir.$v['name']);
if ($width>=48 || $height>=48) continue;
?>
<a href="javascript:undefined" class="el"><img src="<?php echo $emoticonURL.$v['name']?>" width="<?php echo $width?>" height="<?php echo $height?>" alt="" /></a>
<?php endforeach ?>
</div>
<?php else : ?>
<div id="emoticons" class="inset scroll default">
<?php foreach ($emoticons as $k => $v) : ?>
<a href="javascript:undefined" class="el" title="<?php echo $langEngine->get('wproCore_emoticons', $v['label'])?>"><img src="<?php echo $k?>" width="<?php echo $v['width']?>" height="<?php echo $v['height']?>" alt="" /> <?php echo $langEngine->get('wproCore_emoticons', $v['label'])?></a>
<?php endforeach ?>
</div>
<?php endif ?>
<script type="text/javascript">
/*<![CDATA[ */
	initEmoticons();
/* ]]>*/
</script>