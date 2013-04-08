<?php if (!defined('IN_WPRO')) exit; ?><div class="UITabbedTabHolder" id="<?php echo $UID ?>">
<?php $i=0; $num = count($options); foreach($options as $label => $content): ?> 
<a href="javascript:undefined" class="outset <?php if ($i == 0) : echo 'firstTab'; elseif ($i == $num-1): echo 'lastTab'; else : echo 'centerTab'; endif ?><?php if ($i == $selected): echo ' selected'; endif ?>" onClick="<?php echo $UID ?>.swapTab(<?php echo $i ?>);<?php echo (isset($onswap[$label]) ? $onswap[$label] : '')?>"<?php echo (isset($attributes[$label]) ? $attributes[$label] : '')?>><?php echo $label ?></a>
<?php $i ++; endforeach ?>
</div>
<div class="UITabbedPaneHolder outset" id="t_<?php echo $UID ?>_paneHolder">
<?php $i=0; foreach($options as $label => $content): ?> 
<div id="tPane_<?php echo $UID ?>_<?php echo $i ?>" class="UITabbedPane"><!--<a name="#tPane_<?php echo $UID ?>_<?php echo $i ?>"></a>--><?php echo $content ?></div>
<?php $i ++; endforeach ?>
</div>
<script type="text/javascript">
/*<![CDATA[ */
	var <?php echo $UID ?> = new wproUITabbed ();
	<?php echo $UID ?>.init('<?php echo $UID ?>');
/* ]]>*/
</script>