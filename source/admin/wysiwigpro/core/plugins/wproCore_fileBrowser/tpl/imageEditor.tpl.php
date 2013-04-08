<?php if (!defined('IN_WPRO')) exit; 
require_once(WPRO_DIR.'conf/defaultValues/wproCore_fileBrowser.inc.php');

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

if ((375 - $height)/2 > 0) {
	$margin = ((375 - $height)/2);
} else {
	$margin = 0;
}

?>
<script type="text/javascript">
/*<![CDATA[ */
var baseURL = dialog.appendBaseToURL('<?php echo addslashes($dir->URL.$folderPath) ?>');
var folderPath = '<?php echo addslashes($folderPath) ?>';
var folderID = '<?php echo addslashes($folderID) ?>';
var image = '<?php echo addslashes($image) ?>';
var width = <?php echo intval($width) ?>;
var height = <?php echo intval($height) ?>;

var editorID = '<?php echo isset($editorID) ? addslashes($editorID) : ''; ?>';
var tempFile = '<?php echo isset($tempFile) ? addslashes($tempFile) : ''; ?>';
/* ]]>*/
</script>


<div class="inset" id="imageBackground">

<img id="theImage" style="margin-top: <?php echo intval($margin) ?>px" src="core/images/spacer.gif" alt="" width="<?php echo intval($width) ?>" height="<?php echo intval($height) ?>" /></div>

</div>


<div id="toolbar" class="wproEditor <?php echo htmlspecialchars($EDITOR->theme) ?>">
<div class="wproToolbarHolder">
<div class="wproToolbar" style="text-align:center">

<button id="" name="back" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'previousImage'); ?>" onclick="previousImage()" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/previousfile.gif'?>');" class="wproReady" onmouseover="this.className='wproOver'" onmouseout="this.className='wproReady'">&nbsp;</button>
<button id="" name="back" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'nextImage'); ?>" onclick="nextImage()" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/nextfile.gif'?>');" class="wproReady" onmouseover="this.className='wproOver'" onmouseout="this.className='wproReady'">&nbsp;</button>

<img alt="" src="core/images/spacer.gif" class="wproSeparator" />

<button id="" name="back" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'rotateRight'); ?>" onclick="rotateRight()" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/rotate90.gif'?>');" class="wproReady" onmouseover="this.className='wproOver'" onmouseout="this.className='wproReady'">&nbsp;</button>
<button id="" name="back" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'rotateLeft'); ?>" onclick="rotateLeft()" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/rotate270.gif'?>');" class="wproReady" onmouseover="this.className='wproOver'" onmouseout="this.className='wproReady'">&nbsp;</button>
<img alt="" src="core/images/spacer.gif" class="wproSeparator" />

&nbsp;<?php echo $langEngine->get('wproCore_fileBrowser', 'resizeToFitWithin'); ?> <select name="resize" id="resize" onchange="sizeChanged(this.value)"><option value="<?php echo $EDITOR->maxImageWidth ?>x<?php echo $EDITOR->maxImageHeight ?>"><?php echo $EDITOR->maxImageWidth ?> x <?php echo $EDITOR->maxImageHeight ?></option><?php displaySizeOptions($defaultValues['resizeOptions']); ?><option value="custom"><?php echo $langEngine->get('wproCore_fileBrowser', 'custom'); ?></option></select>
 <span style="display:none" id="hiddenResize">&nbsp;&nbsp;<input type="text" size="4" name="maxWidth" id="maxWidth" value="<?php echo $EDITOR->maxImageWidth ?>" /> x <input type="text" size="4" name="maxHeight" id="maxHeight" value="<?php echo $EDITOR->maxImageHeight ?>" /> </span> <input type="button" name="go" value="<?php echo $langEngine->get('wproCore_fileBrowser', 'go'); ?>" onclick="imageResize()" />



</div>


</div>
</div>

<div id="details">
<strong><?php echo $langEngine->get('wproCore_fileBrowser', 'name'); ?></strong> <span id="imageName"><?php echo htmlspecialchars($image) ?></span> | <strong><?php echo $langEngine->get('wproCore_fileBrowser', 'dimensions'); ?></strong> <span id="imageWidth"><?php echo htmlspecialchars($width) ?></span> x <span id="imageHeight"><?php echo htmlspecialchars($height) ?></span>
</div>


<div id="messageBox" class="wproFloatingDialog">&nbsp;</div>
<script type="text/javascript">
/*<![CDATA[ */
	var strSaveAs = '<?php echo addslashes($langEngine->get('wproCore_fileBrowser', 'saveAs')); ?>';
	var strOK = '<?php echo addslashes($langEngine->get('core', 'ok')); ?>';
	var strCancel = '<?php echo addslashes($langEngine->get('core', 'cancel')); ?>';
	var strRotateWarning = '<?php echo addslashes($langEngine->get('wproCore_fileBrowser', 'JSRotateWarning')); ?>';
	var strSaveChanges = '<?php echo addslashes($langEngine->get('wproCore_fileBrowser', 'JSSaveChanges')); ?>';
	editFinished('<?php echo addslashes($image) ?>', '', <?php echo intval($width) ?>, <?php echo intval($height) ?>, false);
	dialog.hideLoadMessage();
/* ]]>*/
</script>