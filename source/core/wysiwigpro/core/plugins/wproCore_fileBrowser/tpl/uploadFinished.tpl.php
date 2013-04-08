<?php if (!defined('IN_WPRO')) exit; ?>
<script type="text/javascript">
/*<![CDATA[ */
var uploadID = '<?php echo addslashes($uploadID); ?>';

var overwriteDone = false;
function initUpload () {
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
			parentWindow.FB.uploadFinished([], uploadID, dialog);
		} else {
			parentWindow.FB.uploadFinished([], uploadID);
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
		parentWindow.FB.uploadFinished(overwrite, uploadID, dialog);
	} else {
		parentWindow.FB.uploadFinished([], uploadID, dialog);
		dialog.close();
	}
	return false;
}
/* ]]>*/
</script>
<div id="errors">
<?php
$fs = new wproFilesystem();
if (!empty($errors['fatal'])) {
	echo '<div class="smallWarning"><image src="'.$EDITOR->themeFolderURL.$EDITOR->theme.'/wysiwygpro/misc/warning16.gif" width="16" height="16" alt="" /> '.$langEngine->get('wproCore_fileBrowser', 'uploadErrorsOccurred').'<br /><br /><ul>';
	foreach ($errors['fatal'] as $file => $reason) {
		$extension = strrchr(strtolower($file), '.');
		if (!$extension) {
			$icon = 'folder';
		} else {
			$file_info = $fs->getFileInfo($extension);
			$icon = $file_info['icon'];
		}
		switch ($reason) {
			case 'badDimensions' :
				if ($canGD) {
					echo '<li><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars($file).'</em>: '.$this->varReplace($langEngine->get('wproCore_fileBrowser', 'dimensionsTooLarge'), array('maxwidth'=>$maxWidth, 'maxheight'=>$maxHeight)).'<br /><br /></li>';
				} else {
					echo '<li><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars($file).'</em>: '.$this->varReplace($langEngine->get('wproCore_fileBrowser', 'dimensionsTooLargeNoGD'), array('maxwidth'=>$maxWidth, 'maxheight'=>$maxHeight)).'<br /><br /></li>';
				}
			break;
			case 'badExtension' :
				echo '<li><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars($file).'</em>: '.$this->varReplace($langEngine->get('wproCore_fileBrowser', 'badExtension'), array('extensions'=>$extensions)).'<br /><br /></li>';
			break;
			case 'badSize' :
				echo '<li><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars($file).'</em>: '.$this->varReplace($langEngine->get('wproCore_fileBrowser', 'tooLarge'), array('maxsize'=>$fs->convertByteSize($sizeLimit))).'<br /><br /></li>';
			break;
			case 'duplicate' :
				echo '<li><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars(isset($errors['rename'][$file]) ? $errors['rename'][$file] : $file).'</em>: '.$langEngine->get('wproCore_fileBrowser', 'fileExistsError').'<br /><br /></li>';
			break;
			case 'reserved' :
				echo '<li><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars(isset($errors['rename'][$file]) ? $errors['rename'][$file] : $file).'</em>: '.$langEngine->get('wproCore_fileBrowser', 'moveReservedNameError').'<br /><br /></li>';
			break;
			// php errors
			case 1 :
				echo '<li><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars($file).'</em>: '.$this->varReplace($langEngine->get('wproCore_fileBrowser', 'combinedSize'), array('maxsize'=>$maxTotalSize)).'<br /><br /></li>';	
			break;
			// other unknown errors
			case 'unknown' :
			default:
				echo '<li><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars($file).'</em>: '.$langEngine->get('wproCore_fileBrowser', 'uploadUnknownError').'<br /><br /></li>';
			break;
		
		}	
	}
	echo '</ul>';
	
	echo '</div>';
}
if (!empty($errors['overwrite'])) :
	echo '<div class="smallWarning"><image src="'.$EDITOR->themeFolderURL.$EDITOR->theme.'/wysiwygpro/misc/warning16.gif" width="16" height="16" alt="" /> '.$langEngine->get('wproCore_fileBrowser', 'chooseFilesToOverwrite').'<br /><br />';
	if (count($errors['overwrite']) > 1) { echo '<a href="javascript:selectAll()">'.$langEngine->get('wproCore_fileBrowser', 'selectAll').'</a> | <a href="javascript:unselectAll()">'.$langEngine->get('wproCore_fileBrowser', 'deselectAll').'</a><br /><br />'; };
	echo '<table class="overwriteTable" width="100%" border="0" cellspacing="0" cellpadding="3"><tr><th>'.$langEngine->get('wproCore_fileBrowser', 'name').'</th><th>'.$langEngine->get('wproCore_fileBrowser', 'modified').'</th></tr>';
	foreach ($errors['overwrite'] as $file => $temp) {
		$extension = strrchr(strtolower($file), '.');
		if (!$extension) {
			$icon = 'folder';
		} else {
			$file_info = $fs->getFileInfo($extension);
			$icon = $file_info['icon'];
		}
		echo '<tr><td><label><input type="checkbox" name="files" value="'.htmlspecialchars($file).'" /><img align="middle" alt="" src="'.htmlspecialchars($themeURL).'icons/'.htmlspecialchars($icon).'.gif" width="16" height="16" /> <em>'.htmlspecialchars(isset($errors['rename'][$file]) ? $errors['rename'][$file] : $file).'&nbsp;&nbsp;&nbsp;&nbsp;</em></label></td>
		<td>'.$langEngine->date(filemtime($directory.$file)).'</td></tr>';
	}
	echo '</table>';
	
	echo '</div>';
endif;


if (!empty($errors['succeeded']) && (!empty($errors['overwrite']) || !empty($errors['fatal']))) {
	echo '<div class="smallInformation"><image src="'.$EDITOR->themeFolderURL.$EDITOR->theme.'/wysiwygpro/misc/information16.gif" width="16" height="16" alt="" /> '.$langEngine->get('wproCore_fileBrowser', 'allOtherFilesUploadedSuccessfully').'</div>';
}
?>



</div>
<script type="text/javascript">
/*<![CDATA[ */
	<?php if (empty($errors['fatal']) && empty($errors['overwrite'])) : ?>
	dialog.hideLoadMessageOnLoad = false;
	overwriteDone = true;
	parentWindow.FB.uploadFinished([], uploadID, dialog);
	<?php endif; ?>
/* ]]>*/
</script>