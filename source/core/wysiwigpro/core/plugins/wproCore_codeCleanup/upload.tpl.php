<?php if (!defined('IN_WPRO')) exit; ?>

<?php if (isset($files)) : ?>

<div class="smallWarning"><image src="<?php echo $EDITOR->themeFolderURL.$EDITOR->theme ?>/wysiwygpro/misc/warning16.gif" width="16" height="16" alt="" /> <?php echo $langEngine->get('wproCore_codeCleanup', 'fileWarning') ?></div>
<br />
<div class="inset" style="height:270px;overflow:auto">

<ol>
<?php $i=0;foreach($files as $file) : ?>
<li><?php echo $langEngine->get('wproCore_codeCleanup', 'fileFound') ?><br />
<b><?php echo htmlspecialchars(urldecode($file)) ?></b><br />
<?php echo $langEngine->get('wproCore_codeCleanup', 'fileInstructions') ?><br />
<input type="text" size="30" name="files_<?php echo $i ?>" id="files_<?php echo $i ?>" value="" /><button type="button" class="chooserButton" onclick="dialog.openFileBrowser('link', function(url){document.dialogForm.elements['files_<?php echo $i ?>'].value=url;})" style="background-image:url('<?php echo $EDITOR->themeFolderURL.$EDITOR->theme.'/wysiwygpro/buttons/link.gif' ?>')">&nbsp;</button><br />
<br /><br /></li>
<?php $i++; endforeach ?>
</ol>

</div>

<?php endif ?>

<textarea style="display:none" name="html" id="html"><?php echo htmlspecialchars($html); ?></textarea>
<script type="text/javascript">
/*<![CDATA[ */
	initCodeCleanup();
	var action = '<?php echo $action ?>';
	var mode = '<?php echo $mode ?>';
	var files = [];
	<?php $i=0;foreach($files as $file) : ?>
	files[<?php echo $i ?>] = '<?php echo addslashes($file) ?>';
	<?php $i++; endforeach ?>
/* ]]>*/
</script>
