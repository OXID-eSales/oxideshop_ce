<?php if (!defined('IN_WPRO')) exit; ?>

<!-- For the security aware let me assure you that these permissions don't actually control what a user can do, they are used to enable/disable user interface controls, if a user interface control is displayed it doesn't mean the user will actually be allowed to perform that action!! -->
<input type="hidden" name="permissions[deleteFiles]" id="permissions[deleteFiles]" value="<?php echo $dir->deleteFiles ? '1' : '0' ?>" />
<input type="hidden" name="permissions[deleteFolders]" id="permissions[deleteFolders]" value="<?php echo $dir->deleteFolders ? '1' : '0' ?>" />
<input type="hidden" name="permissions[renameFiles]" id="permissions[renameFiles]" value="<?php echo $dir->renameFiles ? '1' : '0' ?>" />
<input type="hidden" name="permissions[renameFolders]" id="permissions[renameFolders]" value="<?php echo $dir->renameFolders ? '1' : '0' ?>" />
<input type="hidden" name="permissions[upload]" id="permissions[upload]" value="<?php echo $dir->upload ? '1' : '0' ?>" />
<input type="hidden" name="permissions[overwrite]" id="permissions[overwrite]" value="<?php echo $dir->overwrite ? '1' : '0' ?>" />
<input type="hidden" name="permissions[moveFiles]" id="permissions[moveFiles]" value="<?php echo $dir->moveFiles ? '1' : '0' ?>" />
<input type="hidden" name="permissions[moveFolders]" id="permissions[moveFolders]" value="<?php echo $dir->moveFolders ? '1' : '0' ?>" />
<input type="hidden" name="permissions[copyFiles]" id="permissions[copyFiles]" value="<?php echo $dir->copyFiles ? '1' : '0' ?>" />
<input type="hidden" name="permissions[copyFolders]" id="permissions[copyFolders]" value="<?php echo $dir->copyFolders ? '1' : '0' ?>" />
<input type="hidden" name="permissions[createFolders]" id="permissions[createFolders]" value="<?php echo $dir->createFolders ? '1' : '0' ?>" />
<input type="hidden" name="permissions[editImages]" id="permissions[editImages]" value="<?php echo $dir->editImages ? '1' : '0' ?>" />

<input type="hidden" name="folderType" id="folderType" value="<?php echo htmlspecialchars($dir->type) ?>" />
<input type="hidden" name="folderID" id="folderID" value="<?php echo htmlspecialchars($folderID) ?>" />
<input type="hidden" name="folderPath" id="folderPath" value="<?php echo htmlspecialchars($folderPath) ?>" />
<input type="hidden" name="folderURL" id="folderURL" value="<?php echo htmlspecialchars($folderURL) ?>" />
<input type="hidden" name="sortBy" id="sortBy" value="<?php echo htmlspecialchars($sortBy) ?>" />
<input type="hidden" name="sortDir" id="sortDir" value="<?php echo htmlspecialchars($sortDir) ?>" />

<input type="hidden" name="thumbnails" id="thumbnails" value="<?php echo $thumbnails ? '1' : '0'; ?>" />
<input type="hidden" name="view" id="view" value="<?php echo $thumbnails ? 'thumbnails' : 'list'; ?>" />

<input type="hidden" name="page" id="page" value="<?php echo intval($pageCurrent); ?>" />


<?php if ($pageTotal>100) : ?><div class="wproEditor">
<div id="toolbar" class="wproToolbar pageNavTop">

<div class="pageNavText">
<?php echo $EDITOR->varReplace($langEngine->get('wproCore_fileBrowser','pageInfo'),array('pageStart'=>$pageStart,'pageEnd'=>$pageEnd,'pageTotal'=>$pageTotal)); ?>
</div>

<?php if ($pageStart != 0) : ?>
<button type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'first'); ?>" onclick="FB.goPage('1')" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/first.gif'?>');" class="wproReady">&nbsp;</button>

<button type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'previous'); ?>" onclick="FB.goPage('<?php echo $pageCurrent-1 ?>')" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/previous.gif'?>');" class="wproReady">&nbsp;</button>
<?php endif ?>

<?php
// paging jump menu
$UI = $this->createHTMLSelect();
$options = array();
for ($i=1;$i<$pageNumPages+1;$i++) {
	$options[$i] = $i;
}
$UI->options = $options;
$UI->selected = $pageCurrent-1;
$UI->attributes = array('onchange'=>'FB.goPage(this.value)', 'title'=>$langEngine->get('wproCore_fileBrowser', 'jumpToPage'),'name'=>'pageJumpBottom');
$UI->display();

?>

<?php if ($pageEnd != $pageTotal) : ?>
<button type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'next'); ?>" onclick="FB.goPage('<?php echo $pageCurrent+1 ?>')" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/next.gif'?>');" class="wproReady">&nbsp;</button>

<button type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'last'); ?>" onclick="FB.goPage('<?php echo $pageNumPages ?>')" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/last.gif'?>');" class="wproReady">&nbsp;</button>
<?php endif ?>

</div>
</div>
<?php endif ?>

<?php 
if ($dir->type!='image') {$thumbnails = false;}

$i = 0;

if ($thumbnails) : ?>
<div class="thumbHolder" id="fileTable">
<table class="fileTable" cellpadding="0" cellspacing="0">
<thead>
<tr>
<th nowrap="nowrap" class="outset" scope="col"><input title="<?php echo $langEngine->get('wproCore_fileBrowser', 'selectAll'); ?>" type="checkbox" name="selectAll" id="selectAll" value="true" onclick="FB.toggleSelectAll(this)" /></th>
<th nowrap="nowrap" class="outset<?php if ($sortBy=='name') : ?> sorted<?php endif ?>" scope="col" onclick="FB.sortFilesBy('name')" width="70"><?php echo $langEngine->get('wproCore_fileBrowser', 'nameColumn'); ?> <?php if ($sortBy=='name') : ?><img alt="" src="<?php echo htmlspecialchars($themeURL) ?>misc/sort<?php echo htmlspecialchars($sortDir) ?>.gif" width="8" height="7" /><?php endif ?>&nbsp;</th>
<th nowrap="nowrap" class="outset<?php if ($sortBy=='type') : ?> sorted<?php endif ?>" scope="col" onclick="FB.sortFilesBy('type')" width="60"><?php echo $langEngine->get('wproCore_fileBrowser', 'typeColumn'); ?> <?php if ($sortBy=='type') : ?><img alt="" src="<?php echo htmlspecialchars($themeURL) ?>misc/sort<?php echo htmlspecialchars($sortDir) ?>.gif" width="8" height="7" /><?php endif ?>&nbsp;</th>
<th nowrap="nowrap" class="outset<?php if ($sortBy=='size') : ?> sorted<?php endif ?>" scope="col"onclick="FB.sortFilesBy('size')" width="60"><?php echo $langEngine->get('wproCore_fileBrowser', 'sizeColumn'); ?> <?php if ($sortBy=='size') : ?><img alt="" src="<?php echo htmlspecialchars($themeURL) ?>misc/sort<?php echo htmlspecialchars($sortDir) ?>.gif" width="8" height="7" /><?php endif ?>&nbsp;</th>
<th nowrap="nowrap" class="outset<?php if ($sortBy=='modified') : ?> sorted<?php endif ?>" scope="col" onclick="FB.sortFilesBy('modified')" width="110"><?php echo $langEngine->get('wproCore_fileBrowser', 'modifiedColumn'); ?> <?php if ($sortBy=='modified') : ?><img alt="" src="<?php echo htmlspecialchars($themeURL) ?>misc/sort<?php echo htmlspecialchars($sortDir) ?>.gif" width="8" height="7" /><?php endif ?>&nbsp;</th>
<th class="outset" width="100%">&nbsp;</th>
</tr>
</thead>
</table>

<!-- thumbnail folders -->
<?php foreach ($folders as $folder) : if ($i < $pageStart) {$i++;continue;} ?>
<div <?php if (in_array($folder['name'], $highlight)) : echo 'class="selected" '; endif;?>title="<?php echo htmlspecialchars(($folder['name']==$EDITOR->thumbnailFolderName)?$EDITOR->thumbnailFolderDisplayName:$folder['name']) ?>" onclick="FB.selectFile(this,{'name':'<?php echo addslashes(htmlspecialchars($folder['name'])) ?>','type':'folder','mod':'<?php echo addslashes(htmlspecialchars($langEngine->date($folder['modified']))) ?>'})" ondblclick="FB.goToFolder(this)" oncontextmenu="FB.inContext=true;this.onclick();return false;">
<div class="thumb" style="background-image:url('<?php echo htmlspecialchars($themeURL) ?>icons/folder32.gif')"><input type="checkbox" name="folders" value="<?php echo htmlspecialchars($folder['name']) ?>" <?php if (in_array($folder['name'], $highlight)) : echo 'checked="checked" '; endif;?> onclick="FB.checkSelect(this)" /></div>
<label class="name"><?php echo htmlspecialchars($this->truncate(($folder['name']==$EDITOR->thumbnailFolderName)?$EDITOR->thumbnailFolderDisplayName:$folder['name'], 15, '...', true)) ?><br /><span><?php if ($folder['name']==$EDITOR->thumbnailFolderName) : echo $EDITOR->langEngine->get('files', 'thumbFolder'); else :  echo $EDITOR->langEngine->get('files', 'folder'); endif ?></span></label>
</div>

<?php $i++; if ($i >= $pageEnd) break; endforeach ?>

<!-- thumbnail files -->
<?php foreach ($files as $file) : if ($i < $pageStart) {$i++;continue;} ?>

<div <?php if (in_array($file['name'], $highlight)) : echo 'class="selected" '; endif;?>title="<?php echo htmlspecialchars($file['name']) ?>; <?php echo htmlspecialchars($file['size']) ?>; <?php echo htmlspecialchars(isset($file['dimensions']['text'])?$file['dimensions']['text']:'') ?>; <?php echo htmlspecialchars($langEngine->date($file['modified'])) ?>" onclick="FB.selectFile(this,{'name':'<?php echo addslashes(htmlspecialchars($file['name'])) ?>','type':'<?php echo addslashes(htmlspecialchars($this->varReplace($langEngine->get('files', $file['type']), array('file'=>$langEngine->get('files', 'file'))))) ?>','size':'<?php echo addslashes(htmlspecialchars($file['size'])) ?>','mod':'<?php echo addslashes(htmlspecialchars($langEngine->date($file['modified']))) ?>','prev':<?php echo htmlspecialchars($file['info']['preview']) ?>})" ondblclick="this.onclick();setTimeout('formAction();',100)" oncontextmenu="FB.inContext=true;this.onclick();return false;">
<div class="thumb" style="background-image:url('<?php echo htmlspecialchars(str_replace(' ', '%20', $file['thumbURL'])) ?><?php if (in_array($file['name'], $highlight)) : echo (strstr($file['thumbURL'],'?')?'&amp;rand=':'?rand=').rand(); endif;?>')"><input type="checkbox" name="files" value="<?php echo htmlspecialchars($file['name']) ?>" <?php if (in_array($file['name'], $highlight)) : echo 'checked="checked" '; endif;?> onclick="FB.checkSelect(this)" /></div>
<label class="name"><?php echo htmlspecialchars($this->truncate($file['name'], 15, '...', true)) ?><br /><span><?php echo isset($file['dimensions']['text'])?$file['dimensions']['text']:'&nbsp;' ?></span></label>
</div>

<?php $i++; if ($i >= $pageEnd) break; endforeach ?>

<!-- empty folder ? -->
<?php if ($i==0) : ?>
<?php echo $langEngine->get('wproCore_fileBrowser', 'noFiles'); ?>
<?php endif ?>

</div>
<?php else : ?>

<table class="fileTable" id="fileTable" width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr>
<th nowrap="nowrap" class="outset" scope="col" width="38"><input title="<?php echo $langEngine->get('wproCore_fileBrowser', 'selectAll'); ?>" type="checkbox" name="selectAll" id="selectAll" value="true" onclick="FB.toggleSelectAll(this)" /></th>
<th nowrap="nowrap" class="outset<?php if ($sortBy=='name') : ?> sorted<?php endif ?>" scope="col" onclick="FB.sortFilesBy('name')"><?php echo $langEngine->get('wproCore_fileBrowser', 'nameColumn'); ?> <?php if ($sortBy=='name') : ?><img alt="" src="<?php echo htmlspecialchars($themeURL) ?>misc/sort<?php echo htmlspecialchars($sortDir) ?>.gif" width="8" height="7" /><?php endif ?>&nbsp;</th>
<th nowrap="nowrap" class="outset<?php if ($sortBy=='type') : ?> sorted<?php endif ?>" scope="col" width="95" onclick="FB.sortFilesBy('type')"><?php echo $langEngine->get('wproCore_fileBrowser', 'typeColumn'); ?> <?php if ($sortBy=='type') : ?><img alt="" src="<?php echo htmlspecialchars($themeURL) ?>misc/sort<?php echo htmlspecialchars($sortDir) ?>.gif" width="8" height="7" /><?php endif ?>&nbsp;</th>
<th nowrap="nowrap" class="outset<?php if ($sortBy=='size') : ?> sorted<?php endif ?>" scope="col" width="97" onclick="FB.sortFilesBy('size')"><?php echo $langEngine->get('wproCore_fileBrowser', 'sizeColumn'); ?> <?php if ($sortBy=='size') : ?><img alt="" src="<?php echo htmlspecialchars($themeURL) ?>misc/sort<?php echo htmlspecialchars($sortDir) ?>.gif" width="8" height="7" /><?php endif ?>&nbsp;</th>
<th nowrap="nowrap" class="outset<?php if ($sortBy=='modified') : ?> sorted<?php endif ?>" scope="col" width="140" onclick="FB.sortFilesBy('modified')"><?php echo $langEngine->get('wproCore_fileBrowser', 'modifiedColumn'); ?> <?php if ($sortBy=='modified') : ?><img alt="" src="<?php echo htmlspecialchars($themeURL) ?>misc/sort<?php echo htmlspecialchars($sortDir) ?>.gif" width="8" height="7" /><?php endif ?>&nbsp;</th>
<th class="outset">&nbsp;</th>
</tr>
</thead>
<tbody>

<!-- display folders -->
<?php foreach ($folders as $folder) : if ($i < $pageStart) {$i++;continue;} ?>
<tr <?php if (in_array($folder['name'], $highlight)) : echo 'class="selected" '; endif;?>onclick="FB.selectFile(this,{'name':'<?php echo addslashes(htmlspecialchars($folder['name'])) ?>','type':'folder','mod':'<?php echo addslashes(htmlspecialchars($langEngine->date($folder['modified']))) ?>'})" ondblclick="FB.goToFolder(this)" title="<?php echo htmlspecialchars($folder['name']) ?>" oncontextmenu="FB.inContext=true;this.onclick();return false;">
<td><input type="checkbox" name="folders" value="<?php echo htmlspecialchars($folder['name']) ?>" <?php if (in_array($folder['name'], $highlight)) : echo 'checked="checked" '; endif;?> onclick="FB.checkSelect(this)" /><img alt="" src="<?php echo htmlspecialchars($themeURL) ?>icons/folder.gif" width="16" height="16" /></td>
<td nowrap="nowrap"><?php echo htmlspecialchars($this->truncate(($folder['name']==$EDITOR->thumbnailFolderName)?$EDITOR->thumbnailFolderDisplayName:$folder['name'], 30, '...', true)) ?></td>
<td nowrap="nowrap"><?php echo $langEngine->get('files', 'folder') ?></td>
<td nowrap="nowrap">&nbsp;</td>
<td nowrap="nowrap"><?php echo $langEngine->date($folder['modified']) ?></td>
</tr>
<?php $i++; if ($i >= $pageEnd) break; endforeach ?>
<!-- display files -->
<?php foreach ($files as $file) : if ($i < $pageStart) {$i++;continue;} ?>
<tr <?php if (in_array($file['name'], $highlight)) : echo 'class="selected" '; endif;?>onclick="FB.selectFile(this,{'name':'<?php echo addslashes(htmlspecialchars($file['name'])) ?>','type':'<?php echo addslashes(htmlspecialchars($this->varReplace($langEngine->get('files', $file['type']), array('file'=>$langEngine->get('files', 'file'))))) ?>','size':'<?php echo addslashes(htmlspecialchars($file['size'])) ?>','mod':'<?php echo addslashes(htmlspecialchars($langEngine->date($file['modified']))) ?>','prev':<?php echo htmlspecialchars($file['info']['preview']) ?>})" title="<?php echo htmlspecialchars($file['name']) ?>" ondblclick="this.onclick();setTimeout('formAction();',100)" oncontextmenu="FB.inContext=true;this.onclick();return false;">
<td><input type="checkbox" name="files" value="<?php echo htmlspecialchars($file['name']) ?>" <?php if (in_array($file['name'], $highlight)) : echo 'checked="checked" '; endif;?> onclick="FB.checkSelect(this)" /><img alt="" src="<?php echo htmlspecialchars($themeURL) ?>icons/<?php echo htmlspecialchars($file['info']['icon']) ?>.gif" width="16" height="16" /></td>
<td nowrap="nowrap"><?php echo htmlspecialchars($this->truncate($file['name'], 30, '...', true)) ?>&nbsp;&nbsp;</td>
<td nowrap="nowrap"><?php echo $this->varReplace($langEngine->get('files', $file['type']), array('file'=>$langEngine->get('files', 'file'))) ?>&nbsp;&nbsp;</td>
<td nowrap="nowrap"><?php echo htmlspecialchars($file['size']) ?>&nbsp;&nbsp;</td>
<td nowrap="nowrap"><?php echo $langEngine->date($file['modified']) ?>&nbsp;</td>
</tr>
<?php $i++; if ($i >= $pageEnd) break; endforeach ?>

<!-- empty folder ? -->
<?php if ($i==0) : ?>
<td colspan="5"><?php echo $langEngine->get('wproCore_fileBrowser', 'noFiles'); ?></td>
<?php endif ?>

</tbody>
</table>

<?php endif ?>


<?php if ($pageTotal>0) : ?><div class="wproEditor">
<div id="toolbar" class="wproToolbar pageNavBottom">

<div class="pageNavText">
<?php echo $EDITOR->varReplace($langEngine->get('wproCore_fileBrowser','pageInfo'),array('pageStart'=>$pageStart,'pageEnd'=>$pageEnd,'pageTotal'=>$pageTotal)); ?>
</div>

<?php if ($pageStart != 0) : ?>
<button type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'first'); ?>" onclick="FB.goPage('1')" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/first.gif?1'?>');" class="wproReady">&nbsp;</button>

<button type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'previous'); ?>" onclick="FB.goPage('<?php echo $pageCurrent-1 ?>')" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/previous.gif?1'?>');" class="wproReady">&nbsp;</button>
<?php endif ?>

<?php
if ($pageTotal>100) {
	// paging jump menu
	$UI = $this->createHTMLSelect();
	$UI->options = $options;
	$UI->selected = $pageCurrent-1;
	$UI->attributes = array('onchange'=>'FB.goPage(this.value)', 'title'=>$langEngine->get('wproCore_fileBrowser', 'jumpToPage'),'name'=>'pageJumpBottom');
	$UI->display();
}
?>

<?php if ($pageEnd != $pageTotal) : ?>
<button type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'next'); ?>" onclick="FB.goPage('<?php echo $pageCurrent+1 ?>')" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/next.gif?1'?>');" class="wproReady">&nbsp;</button>

<button type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'last'); ?>" onclick="FB.goPage('<?php echo $pageNumPages ?>')" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/last.gif?1'?>');" class="wproReady">&nbsp;</button>
<?php endif ?>

</div></div>
<?php endif ?>

