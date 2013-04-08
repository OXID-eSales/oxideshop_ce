
<div class="outlookList" id="outlookList">

<?php if (!$filesOnly) : ?>

<?php if ($mode == 'link') : ?>
<?php
$EDITOR->triggerEvent('onBeforeGetLinks');
?>
<?php if (!empty($EDITOR->links)||!empty($EDITOR->linksBrowserURL)) :?>
<a name="site" id="site" class="fl" href="javascript:undefined" onclick="outlookSelect(this.id);parent.switchPane(this.id)">
<img alt="" src="<?php echo htmlspecialchars($themeURL) ?>buttons/pageonthissite.gif" /><br />
<?php echo $langEngine->get('wproCore_fileBrowser', 'pageOnThisSite') ?>
</a>
<?php endif ?>
<?php endif ?>

<?php if ($properties) : ?>
<a name="web" id="web" class="fl" href="javascript:undefined" onclick="outlookSelect(this.id);parent.switchPane(this.id)">
<img alt="" src="<?php echo htmlspecialchars($themeURL) ?>buttons/<?php echo $mode=='image'?'image':($mode=='media'?'media':'weblocation')?>.gif" /><br />
<?php echo $langEngine->get('wproCore_fileBrowser', 'properties') ?>
</a>
<?php else : ?>
<?php if ($EDITOR->featureIsEnabled('weblocation')) : ?>
<a name="web" id="web" class="fl" href="javascript:undefined" onclick="outlookSelect(this.id);parent.switchPane(this.id)">
<img alt="" src="<?php echo htmlspecialchars($themeURL) ?>buttons/weblocation.gif" /><br />
<?php echo $langEngine->get('wproCore_fileBrowser', 'webLocation') ?>
</a>
<?php endif ?>
<?php endif ?>

<?php if ($mode == 'link') : ?>
<?php if ($EDITOR->featureIsEnabled('bookmarkmanager') && !$chooser) : ?>
<a name="doc" id="doc" class="fl" href="javascript:undefined" onclick="outlookSelect(this.id);parent.switchPane(this.id)">
<img alt="" src="<?php echo htmlspecialchars($themeURL) ?>buttons/placeonthispage.gif" /><br />
<?php echo $langEngine->get('wproCore_fileBrowser', 'placeInThisDocument') ?>
</a>
<?php endif ?>
<?php if ($EDITOR->featureIsEnabled('email')) : ?>
<a name="email" id="email" class="fl" href="javascript:undefined" onclick="outlookSelect(this.id);parent.switchPane(this.id)">
<img alt="" src="<?php echo htmlspecialchars($themeURL) ?>buttons/emailaddress.gif" /><br />
<?php echo $langEngine->get('wproCore_fileBrowser', 'emailAddress2') ?>
</a>
<?php endif ?>
<?php endif ?>

<?php endif ?>

<?php foreach ($dirs as $dir) : ?>

<a <?php /*if ($current == $dir->id) { echo 'class="selected" '; } else { echo 'class="fl" '; }*/ ?>class="fl" name="<?php echo htmlspecialchars($dir->id) ?>" href="javascript:undefined" id="<?php echo htmlspecialchars($dir->id) ?>" onclick="outlookSelect(this.id);parent.switchPane('fileBrowser');parent.hideMessageBox();parent.FB.loadFolder(this.id)">
<img alt="" src="<?php if (!empty($dir->icon)) { echo htmlspecialchars($dir->icon); } else { echo htmlspecialchars($themeURL).'icons/'.$dir->type.'folder.gif'; } ?>" width="16" height="16" /><br />
<?php if (!empty($dir->name)) { echo htmlspecialchars($dir->name); } else if (intval($dir->id) < 3) { echo $langEngine->get('files', $dir->type.'Folder'); } else { echo htmlspecialchars(basename($dir->dir)); } ?>
</a>

<?php endforeach ?>

</div>

<script type="text/javascript">
/*<![CDATA[ */
<?php if ($properties) : ?>
var defaultFolderId = '';
<?php else : ?>
var defaultFolderId = '<?php echo addslashes($current) ?>';
<?php endif ?>
function initOutlook () {
	if (parent.browserReady == false) {
		setTimeout('initOutlook()', 500);
		return;
	} else {
		if (!parent._initiated) {
			if (defaultFolderId=='fileBrowser') {
				var links = document.getElementsByTagName('A');
				var n = links.length;
				for (var i=0; i<n; i++) {
					if (!isNaN(links[i].id)) {
						defaultFolderId = links[i].id;
						break;
					} 
				}
			}
			if (!document.getElementById(defaultFolderId)) {
				document.getElementById('outlookList').getElementsByTagName('A').item(0).onclick();
			} else {
				document.getElementById(defaultFolderId).onclick();
			}
		}
		setTimeout('parent.dialog.hideLoadMessage();', 500);
	}
}

function outlookSelect(id, click) {
	
	if (id=='fileBrowser') {
		var links = document.getElementsByTagName('A');
		var n = links.length;
		for (var i=0; i<n; i++) {
			if (!isNaN(links[i].id)) {
				id = links[i].id;
				break;
			} 
		}
	}
	
	
	var links = document.getElementsByTagName('A');
	var n = links.length;
	for (var i=0; i<n; i++) {
		if (links[i].id==id) {
			links[i].className = 'selected';
			if (click) {
				links[i].onclick();
			}
		} else {
			links[i].className = 'fl';
		}
	}
}

initOutlook();
/* ]]>*/
</script>

