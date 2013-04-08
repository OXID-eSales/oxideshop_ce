<?php if (!defined('IN_WPRO')) exit; ?>
<script type="text/javascript">
/*<![CDATA[ */
var moveCopyID = '<?php echo addslashes($moveCopyID); ?>';

var overwriteDone = false;
function initMoveCopy () {
	dialog.hideLoadMessage();
}
function selectAll() {
	var s = document.getElementsByTagName('INPUT');
	var n = s.length;
	for (var i=0;i<n;i++) {
		if (s[i].getAttribute('type')=='checkbox') {
			s[i].checked = true;
		}
	}
}
function unselectAll() {
	var s = document.getElementsByTagName('INPUT');
	var n = s.length;
	for (var i=0;i<n;i++) {
		if (s[i].getAttribute('type')=='checkbox') {
			s[i].checked = false;
		}
	}
}

function unloadDialog(d) {
	if (document.dialogForm.files && !overwriteDone) {
		overwriteDone = true;
		if (d) {
			parentWindow.FB.moveCopyFinished([], moveCopyID, dialog);
		} else {
			parentWindow.FB.moveCopyFinished([], moveCopyID);
		}
	} else {
		dialog.close();
	}
}
function formAction () {
	dialog.showLoadMessage();
	var forms = new wproForms();
	if (document.dialogForm.files) {
		overwriteDone = true;
		var overwrite = forms.getSelectedCheckboxValue(document.dialogForm.files);
		parentWindow.FB.moveCopyFinished(overwrite, moveCopyID, dialog);
	} else {
		dialog.close();
	}
	return false;
}
/* ]]>*/
</script>
<div id="errors">
<?php
$fs = new wproFilesystem();
if (!empty($failed)) {
	echo '<div class="smallWarning"><image src="'.$EDITOR->themeFolderURL.$EDITOR->theme.'/wysiwygpro/misc/warning16.gif" width="16" height="16" alt="" /> '.$langEngine->get('wproCore_fileBrowser', 'moveErrorsOccurred').'<br /><br /><ul>';
	foreach ($failed as $file => $reason) {
		$extension = strrchr(strtolower($file), '.');
		if (!$extension) {
			$icon = 'folder';
		} else {
			$file_info = $fs->getFileInfo($extension);
			$icon = $file_info['icon'];
		}
		
		switch ($reason) {
			case 'notExist' :
				echo '<li><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars($file).'</em>: '.$langEngine->get('wproCore_fileBrowser', 'fileNotExistError').'<br /><br /></li>';
			break;
			case 'destInsideSrc' :
				echo '<li><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars($file).'</em>: '.$langEngine->get('wproCore_fileBrowser', 'insideItselfError').'<br /><br /></li>';
			break;
			case 'duplicate' :
				echo '<li><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars($file).'</em>: '.$langEngine->get('wproCore_fileBrowser', 'fileExistsError').'<br /><br /></li>';
			break;
			case 'reserved' :
				echo '<li><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars($file).'</em>: '.$langEngine->get('wproCore_fileBrowser', 'moveReservedNameError').'<br /><br /></li>';
			break;
			case 'unknown' :
			default :
				echo '<li><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars($file).'</em>: '.$langEngine->get('wproCore_fileBrowser', 'unknownError').'<br /><br /></li>';
		}	
	}
	echo '</ul>';
	
	echo '</div>';
}
if (!empty($overwrite)) :
	echo '<div class="smallWarning"><image src="'.$EDITOR->themeFolderURL.$EDITOR->theme.'/wysiwygpro/misc/warning16.gif" width="16" height="16" alt="" /> '.$langEngine->get('wproCore_fileBrowser', 'chooseFilesToOverwrite').'<br /><br />';
	if (count($overwrite) > 1) { echo '<a href="javascript:selectAll()">'.$langEngine->get('wproCore_fileBrowser', 'selectAll').'</a> | <a href="javascript:unselectAll()">'.$langEngine->get('wproCore_fileBrowser', 'deselectAll').'</a><br /><br />'; };
	echo '<table class="overwriteTable" width="100%" border="0" cellspacing="0" cellpadding="3"><tr><th>'.$langEngine->get('wproCore_fileBrowser', 'name').'</th><th>'.$langEngine->get('wproCore_fileBrowser', 'modified').'</th></tr>';
	foreach ($overwrite as $file) {
		$extension = strrchr(strtolower($file), '.');
		if (!$extension) {
			$icon = 'folder';
		} else {
			$file_info = $fs->getFileInfo($extension);
			$icon = $file_info['icon'];
		}
		echo '<tr><td><label><input type="checkbox" name="files" value="'.htmlspecialchars($file).'" /><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars($file).'</em></label></td><td>'.$langEngine->date(filemtime($destDirectory.$file)).'</td></tr>';
	}
	echo '</table>';
	
	echo '</div>';
endif;


if (!empty($succeeded) && (!empty($overwrite) || !empty($failed))) {
	echo '<div class="smallInformation"><image src="'.$EDITOR->themeFolderURL.$EDITOR->theme.'/wysiwygpro/misc/information16.gif" width="16" height="16" alt="" /> '.$langEngine->get('wproCore_fileBrowser', 'allOtherFilesMovedSuccessfully').'</div>';
}
?>



</div>
<script type="text/javascript">
/*<![CDATA[ */
	<?php if (empty($failed) && empty($overwrite)) : ?>
	dialog.hideLoadMessageOnLoad = false;
	overwriteDone = true;
	parentWindow.FB.moveCopyFinished([], moveCopyID, dialog);
	<?php endif; ?>
/* ]]>*/
</script>