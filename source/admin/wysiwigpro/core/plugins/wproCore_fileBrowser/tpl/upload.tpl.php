<?php if (!defined('IN_WPRO')) exit;
require_once(WPRO_DIR.'conf/defaultValues/wproCore_fileBrowser.inc.php'); ?>
<?php if($showUploadError) : ?>
<script type="text/javascript">
/*<![CDATA[ */
alert('<?php echo addslashes($this->varReplace($langEngine->get('wproCore_fileBrowser', 'JSUploadFailed'), array('maxsize'=>$maxTotalSize))) ?>');
/* ]]>*/
</script>
<?php endif ?>
<?php 

function displaySizeOptions($arr) {
	global $EDITOR;
	$str = '';
	foreach ($arr as $item) {
		if ($item[0] >= $EDITOR->maxImageWidth || $item[1] >= $EDITOR->maxImageHeight) {
			continue;
		}
		$str.= '<option value="'.$item[0].'x'.$item[1].'">'.$item[0].' x '.$item[1].'</option>';
	}
	echo $str;
}

?>
<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_fileBrowser', 'chooseFiles') ?></legend>

<div align="center" id="uploadMessage" class="wproLoadMessage" style="display:none;position:absolute;">
<?php echo $langEngine->get('wproCore_fileBrowser', 'uploadInProgress') ?><br /><br />
<img src="<?php echo htmlspecialchars($themeURL) ?>misc/loader.gif" alt="" /><br /><br />
<input type="button" value="<?php echo $langEngine->get('core', 'cancel') ?>" name="cancelUpload" onclick="if(window.stop){window.stop();}else{try{document.execCommand('stop');}catch(e){document.location.reload();};}document.getElementById('uploadMessage').style.display='none';" />
</div>

<ul>
<li><?php echo $this->varReplace($langEngine->get('wproCore_fileBrowser', 'filesMustBe'), array('extensions'=>$extensions)) ?></li>
<li><?php echo $this->varReplace($langEngine->get('wproCore_fileBrowser', 'smallerThan'), array('maxsize'=>$maxFileSize)) ?></li>
<?php if (!$canGD&&$dir->type=='image') : ?>
<li><?php echo $this->varReplace($langEngine->get('wproCore_fileBrowser', 'imageDimensions'), array('maxwidth'=>$EDITOR->maxImageWidth, 'maxheight'=>$EDITOR->maxImageHeight)) ?></li>
<?php endif ?>
</ul>


<div><?php echo $langEngine->get('wproCore_fileBrowser', 'noToUpload') ?> <select name="amount" id="amount" onchange="amountChanged(this.value)"><option value="1">1</option><option value="5"><?php echo $langEngine->get('wproCore_fileBrowser', 'upTo5') ?></option><option value="10"><?php echo $langEngine->get('wproCore_fileBrowser', 'upTo10') ?></option><option value="15"><?php echo $langEngine->get('wproCore_fileBrowser', 'upTo15') ?></option><option value="20"><?php echo $langEngine->get('wproCore_fileBrowser', 'upTo20') ?></option></select><br /><br /></div>

<ul id="combinedMessage" style="display:none">
<li><strong><?php echo $this->varReplace($langEngine->get('wproCore_fileBrowser', 'combinedSize'), array('maxsize'=>$maxTotalSize)) ?></strong></li>
</ul>

<input type="hidden" id="uploadID" name="uploadID" value="<?php echo htmlspecialchars($uploadID) ?>" />
<form name="uploadForm" action="" method="post" enctype="multipart/form-data" onsubmit="return formAction()">
<div id="files" class="inset">
<input name="files[]" id="files[]" type="file" size="40">
</div>
</form>

</fieldset>
<?php if ($dir->overwrite || $dir->type=='image') : ?>
<fieldset class="singleLine">
<legend>Options</legend>
<?php if ($dir->overwrite) : ?>
<div><label><input type="checkbox" name="overwrite" id="overwrite" value="true" /> <?php echo $langEngine->get('wproCore_fileBrowser', 'overwrite') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<br /><br /></div>
<?php endif ?>
<?php if ($dir->type=='image'&&$canGD) : ?>
<div><?php echo $langEngine->get('wproCore_fileBrowser', 'resizeLargerThan') ?> <select name="resize" id="resize" onchange="sizeChanged(this.value)"><option value="<?php echo $EDITOR->maxImageWidth ?>x<?php echo $EDITOR->maxImageHeight ?>"><?php echo $EDITOR->maxImageWidth ?> x <?php echo $EDITOR->maxImageHeight ?></option><?php displaySizeOptions($defaultValues['resizeOptions']); ?><option value="custom"><?php echo $langEngine->get('wproCore_fileBrowser', 'custom') ?></option></select>
 <span style="display:none" id="hiddenResize">&nbsp;&nbsp;<input type="text" size="4" name="maxWidth" id="maxWidth" value="<?php echo intval($EDITOR->maxImageWidth) ?>" /> x <input type="text" size="4" name="maxHeight" id="maxHeight" value="<?php echo intval($EDITOR->maxImageHeight) ?>" /></span> <?php echo $langEngine->get('core', 'pixels') ?></div>
<?php endif ?>
</fieldset>
<?php endif ?>
<script type="text/javascript">
/*<![CDATA[ */
initUpload ();
/* ]]>*/
</script>