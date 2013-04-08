<?php if (!defined('IN_WPRO')) exit; ?>
<input type="hidden" name="srcFolderID" value="<?php echo htmlspecialchars($srcFolderID) ?>" />
<input type="hidden" name="srcFolderPath" value="<?php echo htmlspecialchars($srcFolderPath) ?>" />

<input type="hidden" name="destFolderID" value="<?php echo htmlspecialchars($destFolderID) ?>" />
<input type="hidden" name="destFolderPath" value="<?php echo htmlspecialchars($destFolderPath) ?>" />

<input type="hidden" name="requiredPermissions" value="<?php echo htmlspecialchars($requiredPermissions) ?>" />

<input type="hidden" name="files" value="" />

<input type="hidden" name="moveCopyID" value="<?php echo htmlspecialchars($moveCopyID) ?>" />

<script type="text/javascript">
initMoveCopy();
</script>

<p><?php echo $langEngine->get('wproCore_fileBrowser', 'selectDestinationFolder') ?></p>

<!-- outlook bar -->
<div class="leftColumn">

<iframe class="outlookBar inset" id="outlookFrame" name="outlookFrame" src="<?php echo htmlspecialchars($EDITOR->editorLink('dialog.php?dialog=wproCore_fileBrowser&action=outlook&filesOnly=true&requiredPermissions='.$requiredPermissions.'&current='.$destFolderID.'&mode='.$srcFolderType.($EDITOR->appendToQueryStrings ? '&'.$EDITOR->appendToQueryStrings : '').'&'.$wpsname.'='.$wpsid.($EDITOR->appendSid ? strip_tags(defined('SID') ? '&'.SID : '') : '').'#'.$srcFolderID )); ?>" frameborder="0"></iframe>
</div>
<!-- end outlook bar -->

<div class="rightColumn insetWhite">

<?php 

function buildFolderArray(&$UI, &$pNode, $folders) {
	global $EDITOR;
	$i=0;
	foreach($folders as $folder) {
		$node = & $UI->createNode();

		$node->id = $folder['path'];
		$node->caption = ($folder['name']==$EDITOR->thumbnailFolderName)?$EDITOR->thumbnailFolderDisplayName:$folder['name'];
		$node->isFolder = true;
		$node->caption_onclick = 'function (node) {selectFolder(\''.addslashes($folder['path']).'\');}';
		if (!empty($folder['children'])) {
			buildFolderArray($UI, $node, $folder['children']);
		}
		$pNode->appendChild($node);
		$i++;
	}
}

$UI = $this->createUITree();
$UI->width = 327;
$UI->height = 280;


buildFolderArray($UI, $UI, $folders);


$UI->display();

?>

</div>

<label><input type="checkbox" name="overwrite" value="true"<?php if ($overwrite) :?> checked="checked"<?php endif ?> /> <?php echo $langEngine->get('wproCore_fileBrowser', 'overwrite') ?></label><br />

<label><input type="checkbox" name="goToDest" value="true"<?php if ($goToDest) :?> checked="checked"<?php endif ?> /> <?php echo $langEngine->get('wproCore_fileBrowser', 'goToDestination') ?></label>