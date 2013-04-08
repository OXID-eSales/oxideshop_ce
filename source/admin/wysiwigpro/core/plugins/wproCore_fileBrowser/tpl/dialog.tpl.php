<?php if (!defined('IN_WPRO')) exit; 
require_once(WPRO_DIR.'conf/defaultValues/wproCore_fileBrowser.inc.php');

$chooserType = isset($chooserType) ? $chooserType : $action;
$chooser = isset($chooser) ? $chooser : false;
?>

<script type="text/javascript">
/*<![CDATA[ */
var strPleaseWait = "<?php echo addslashes($langEngine->get('core', 'pleaseWait')) ?>";
var strFolderNotFound = "<?php echo addslashes($langEngine->get('wproCore_fileBrowser', 'folderNotExistError')) ?>";
var strDeleteWarning = "<?php echo addslashes($langEngine->get('wproCore_fileBrowser', 'JSDeleteWarning')) ?>";
var strEnterNewFolderName = "<?php echo addslashes($langEngine->get('wproCore_fileBrowser', 'enterNewFolderName')) ?>";
var strOK = '<?php echo addslashes($langEngine->get('core', 'ok')); ?>';
var strCancel = '<?php echo addslashes($langEngine->get('core', 'cancel')); ?>';
var strEnterNewName = "<?php echo addslashes($langEngine->get('wproCore_fileBrowser', 'enterNewName')) ?>";


var canGif = <?php echo (!empty($canGD) && !empty($canGif)) ? 'true' : 'false' ?>;
var canGD = <?php echo (!empty($canGD)) ? 'true' : 'false' ?>;
var thumbnails = <?php echo $EDITOR->thumbnails ? 'true' : 'false' ?>;
var browserReady = false;
FB.action = '<?php echo addslashes($action) ?>';
FB.chooser = <?php echo $chooser ? 'true' : 'false' ?>;

FB.linksBrowserURL = '<?php echo addslashes(isset($linksBrowserURL) ? $linksBrowserURL : '') ?>';

/* print all the directory URLs so we can potentially 
load the correct image folder and even select the image */

FB.dirs = [<?php 
$arr = array();
foreach ($dirs as $dir) {
array_push($arr, "{'id':'".$dir->id."','type':'".$dir->type."','URL':'".$dir->URL."'}" );
}; echo implode(',', $arr); ?>];

FB.folderArray = {<?php $n = count($dirs)-1; $i=0; foreach ($dirs as $dir) : ?> "<?php echo addslashes($dir->id) ?>":"<?php if (!empty($dir->name)) { echo addslashes($dir->name); } else if (intval($dir->id) < 3) { echo addslashes($langEngine->get('files', $dir->type.'Folder')); } else { echo addslashes(basename($dir->dir)); } ?>"<?php if ($i<$n) echo ','; $i++; endforeach ?>};

FB.imageExtensions = <?php echo $this->wproJSArray($imageExtensions, 'numeric') ?>;
FB.docExtensions =  <?php echo $this->wproJSArray($docExtensions, 'numeric') ?>;
FB.mediaExtensions =  <?php echo $this->wproJSArray($mediaExtensions, 'numeric') ?>;

/* maximum display dimensions */
FB.maxImageDisplayWidth = <?php echo intval($EDITOR->maxImageDisplayWidth) ?>;
FB.maxImageDisplayHeight = <?php echo intval($EDITOR->maxImageDisplayHeight) ?>;
FB.maxMediaDisplayWidth = <?php echo intval($EDITOR->maxMediaDisplayWidth) ?>;
FB.maxMediaDisplayHeight = <?php echo intval($EDITOR->maxMediaDisplayHeight) ?>;

/* determin if we are editing properties */
FB.propertiesRequired();

/*dialog.hideLoadMessage();*/
/* ]]>*/
if (FB.properties) {

document.write('<div class="leftColumn"><iframe class="outlookBar inset" id="outlookFrame" name="outlookFrame" src="<?php echo htmlspecialchars($EDITOR->editorLink('dialog.php?dialog=wproCore_fileBrowser&action=outlook&current='.$chooserType.'&mode='.$chooserType.($EDITOR->appendToQueryStrings ? '&'.$EDITOR->appendToQueryStrings : '').'&'.$wpsname.'='.$wpsid.($EDITOR->appendSid ? strip_tags(defined('SID') ? '&'.SID : '') : '').'&properties=true#'.$action )); ?>" frameborder="0"></iframe></div>');


} else {

document.write('<div class="leftColumn"><iframe class="outlookBar inset" id="outlookFrame" name="outlookFrame" src="<?php echo htmlspecialchars($EDITOR->editorLink('dialog.php?dialog=wproCore_fileBrowser&action=outlook&current='.$chooserType.'&mode='.$chooserType.($EDITOR->appendToQueryStrings ? '&'.$EDITOR->appendToQueryStrings : '').'&'.$wpsname.'='.$wpsid.($EDITOR->appendSid ? strip_tags(defined('SID') ? '&'.SID : '') : '').($chooser ? '&chooser=true' :'').'#'.$action )); ?>" frameborder="0"></iframe></div>');

}

</script>

<!-- start panes -->

<?php if (($action == 'link' || $action == 'document') && !$chooser) : ?>
<!-- shared hyperlink stuff -->
<div class="topOptions">
<?php
$t = $this->createUI2ColTable();
$t->width = 'small';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'linkText'), 't'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '43',
	'style' => 'width:440px',
	'name' => 'linkText',
	'value' => $defaultValues['linkText'],
	'accesskey' => 't',
	'onchange'=>'FB.flinkTextChanged();',
)), 'linkText');
$t->display();
?>
<div class="fourCol">
<?php
$t = $this->createUI2ColTable();
$t->width = 'small';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'screenTip'), 's'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '20',
	'style' => 'width:180px',
	'name' => 'screenTip',
	'value' => $defaultValues['linkScreenTip'],
	'accesskey' => 's',
)), 'screenTip');

$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>'style','accesskey'=>'s');
$UI->options = array_merge(array(''=>$langEngine->get('core', 'default')), $EDITOR->linkStyles);
$UI->selected = $defaultValues['linkStyle'];
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'style'), 's'), $UI->fetch(), 'style');
$t->display();		
?>
</div>
</div>
<?php endif ?>

<!-- end shard hyperlink stuff-->

<div class="centerPanes">

<!-- link panes -->
<?php if ($action == 'link') : ?>
<?php if (!empty($EDITOR->links)||!empty($EDITOR->linksBrowserURL)) :?>
<!-- Links on this site -->
<div id="site" style="display:none">
<div id="siteFrameHolder" name="siteFrameHolder"><iframe id="siteFrame" class="inset" src="core/html/iframeSecurity.htm" frameborder="0"></iframe></div>
<div id="sitePreviewHolder"><iframe class="previewFrame" id="sitePreview" name="sitePreview" src="<?php echo htmlspecialchars($EDITOR->editorLink('dialog.php?dialog=wproCore_fileBrowser&action=preview'.($EDITOR->appendToQueryStrings ? '&'.$EDITOR->appendToQueryStrings : '').'&'.$wpsname.'='.$wpsid.($EDITOR->appendSid ? strip_tags(defined('SID') ? '&'.SID : '') : '') )); ?>" frameborder="0"></iframe>
<div><a href="javascript:undefined" onclick="FB.previewInNewWindow()"><?php echo $langEngine->get('wproCore_fileBrowser', 'previewInNewWindow'); ?></a></div>
</div>
</div>
<!-- end links on this site -->
<?php endif ?>
<?php if ($EDITOR->featureIsEnabled('bookmarkmanager')) : ?>
<!-- bookmarks -->
<div id="doc" style="display:none">
<p><img src="<?php echo $themeURL ?>/buttons/bookmark.gif" alt="" align="top" /> <?php echo $langEngine->get('wproCore_fileBrowser', 'bookmarks'); ?></p>
<select accesskey="e" name="bookmarkSelect" id="bookmarkSelect" onchange="this.form.URL.value = '#'+this.value" size="5">
<option value=""><?php echo $langEngine->get('wproCore_fileBrowser', 'topOfPage'); ?></option>
</select>
</div>
<!-- end bookmarks -->
<?php endif ?>

<?php if ($EDITOR->featureIsEnabled('email')) : ?>
<!-- email -->
<div id="email" style="display:none">
<br /><br /><br /><br />
<?php
$t = $this->createUI2ColTable();
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'emailAddress'), 'a'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '43',
	'name' => 'emailAddress',
	'accesskey' => 'a',
)), 'emailAddress');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'emailSubject'), 's'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '43',
	'name' => 'emailSubject',
	'accesskey' => 's',
)), 'emailSubject');
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'emailMessage'), 'm'), 
'<textarea name="emailMessage" id="emailMessage" cols="43" rows="5" accesskey="y"></textarea>', 'emailMessage');
$t->display();			
?>
</div>
<!-- end email -->
<?php endif ?>
<?php endif ?>
<!-- end links panes -->

<!-- web location -->
<div id="web" style="display:none">

<?php if ($action == 'link'||$action == 'document') : ?>
<fieldset class="singleLine" style="width:97%">
<legend>Preview</legend>
<iframe class="previewFrame" id="webPreview" name="webPreview" src="<?php echo htmlspecialchars($EDITOR->editorLink('dialog.php?dialog=wproCore_fileBrowser&action=preview'.($EDITOR->appendToQueryStrings ? '&'.$EDITOR->appendToQueryStrings : '').'&'.$wpsname.'='.$wpsid.($EDITOR->appendSid ? strip_tags(defined('SID') ? '&'.SID : '') : '') )); ?>" frameborder="0"></iframe>
<div><a href="javascript:undefined" onclick="FB.preview('webPreview')"><?php echo $langEngine->get('wproCore_fileBrowser', 'loadPreview'); ?></a> | <a href="javascript:undefined" onclick="FB.previewInNewWindow()"><?php echo $langEngine->get('wproCore_fileBrowser', 'previewInNewWindow'); ?></a></div>
</fieldset>
<?php else: ?>

<div class="mediaOptionsCenter">
<?php 

$UI = $this->createUIDropDown();
$UI->label = $langEngine->get('wproCore_fileBrowser', 'chooseFileType');
$UI->onChange = 'FB.onRemotePluginChange';

$prefixes = array();
$DIALOG->plugins['wproCore_fileBrowser']->loadEmbedPlugins();
$i = 0;
foreach ($DIALOG->plugins['wproCore_fileBrowser']->embedPlugins as $name => $plugin) {
	if ($plugin->remote) {
		$continue = false;
		if (!empty($plugin->extensions)) {
			foreach ($plugin->extensions as $extension) {
				if (in_array($extension, $allowedExtensions)) {
					$continue = true;
					break;
				}
			}
		} else {
			$continue = ($action=='media')?true:false;
		}
		if ($continue) {
			$UI->addOption($plugin->description, $plugin->displayRemoteOptions('remoteEmbed'.$i) );
			$prefixes[$name] = 'remoteEmbed'.$i;
			$i++;
		}
	}
}
$UI->display();
?>


<script type="text/javascript">
<?php $i=0; foreach ($prefixes as $k => $v) : 
	if ($i==0) echo 'FB.currentRemotePlugin = "'.addslashes($k).'";';
	if ($i==0) echo 'FB.currentRemotePrefix = "'.addslashes($v).'";';
?>
FB.remoteEmbedPrefixes['<?php echo addslashes($k) ?>'] = '<?php echo addslashes($v) ?>';
<?php $i++; endforeach ?>
<?php foreach ($DIALOG->plugins['wproCore_fileBrowser']->embedPlugins as $name => $plugin) : ?>
FB.loadEmbedPlugin('<?php echo addslashes($name) ?>');
<?php endforeach ?>
</script>
</div>

<div class="mediaPositioningHolder">
<?php require(WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/mediaPositioning.tpl.php') ?>
</div>

<?php endif ?>

</div>
<!-- end web location -->


</div>

<!-- full on file browser -->
<!-- right column -->
<div id="fileBrowser" style="display:none">

<!-- center column -->

<div id="fileBrowserCenter" class="fileBrowserCenter">

<div class="lookIn">
<select id="lookInSelect" name="lookInSelect" value="" onchange="FB.lookInSelectChange(this);" style="width:340px">
</select>
</div>

<div class="wproEditor">
<div class="wproToolbarHolder">
<div id="toolbar" class="wproToolbar">

<button id="back" name="back" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'back'); ?>" onclick="FB.goBack(this)" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/back.gif'?>');" class="wproReady">&nbsp;</button>
<button id="up" name="up" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'up'); ?>" onclick="FB.upOneLevel(this);" style="background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/up.gif'?>');" class="wproReady">&nbsp;</button>

<img alt="" id="editSeparator" src="core/images/spacer.gif" class="wproSeparator" />

<button id="move" name="move" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'moveTo'); ?>" onclick="FB.showMove(this);" style="display:none;background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/movefilesto.gif'?>');" class="wproReady">&nbsp;</button>
<button id="copy" name="copy" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'copyTo'); ?>" onclick="FB.showCopy(this)" style="display:none;background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/copyfilesto.gif'?>');" class="wproReady">&nbsp;</button>

<button id="rename" name="rename" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'rename'); ?>" onclick="FB.showRename(this);" style="display:none;background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/renamefiles.gif'?>');" class="wproReady">&nbsp;</button>

<button id="editImages" name="edit" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'editImage'); ?>" onclick="FB.showImageEditor(this)" style="display:none;background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/editimage.gif'?>');" class="wproReady">&nbsp;</button>

<button id="delete" name="delete" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'delete'); ?>" onclick="FB.deleteFiles(this)" style="display:none;background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/deletefiles.gif'?>');" class="wproReady">&nbsp;</button>

<img alt="" id="uploadSeparator" style="display:none;" src="core/images/spacer.gif" class="wproSeparator" />

<button id="upload" name="upload" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'uploadFiles'); ?>" onclick="FB.showUpload(this);" style="display:none;background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/uploadfiles.gif'?>');" class="wproTextButtonReady wproReady"><span><?php echo $langEngine->get('wproCore_fileBrowser', 'upload'); ?></span></button>

<button id="createFolders" name="create" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'newFolder'); ?>" onclick="FB.showNewFolder(this);" style="display:none;background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/newfolder.gif'?>');" class="wproReady">&nbsp;</button>

<img alt="" id="viewSeparator" src="core/images/spacer.gif" class="wproSeparator" />

<button id="thumbnailsView" name="thumbnailView" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'thumbnailView'); ?>" onclick="FB.changeView('thumbnails');" style="display:none;background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/thumbnailsview.gif'?>');" class="wproReady">&nbsp;</button>

<button id="listView" name="listView" type="button" title="<?php echo $langEngine->get('wproCore_fileBrowser', 'listView'); ?>" onclick="FB.changeView('list');" style="display:none;background-image:url('<?php echo htmlspecialchars($themeURL).'buttons/listview.gif'?>');" class="wproReady">&nbsp;</button>

</div>
</div>
</div>

<div class="fileHolder inset" id="folderFrame"></div>

</div>

<!-- right column -->

<div class="hideDetails">
<a href="javascript:undefined" onclick="FB.toggleDetails(this)">&gt;&gt; <?php echo $langEngine->get('wproCore_fileBrowser', 'hide'); ?></a>
</div>

<div id="detailsHolder" class="detailsHolder">

<?php
$tabs = $this->createUITabbed();
$tabs->startTab($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'details'), 'd'), array('accesskey'=>'d'));
?>
<div class="details">

<div style="display:none" id="multiplePane">
<?php echo $langEngine->get('wproCore_fileBrowser', 'multipleItemsSelected'); ?>
</div>

<div style="display:none" id="nothingPane">
<?php echo $langEngine->get('wproCore_fileBrowser', 'noItemSelected'); ?>
</div>

<div style="display:none" id="folderPane">
<div class="displayDetails">
<div><a href="javascript:undefined" onclick="FB.openSelectedFolder()"><?php echo $langEngine->get('wproCore_fileBrowser', 'openFolder'); ?></a></div>
<br />
<div><strong><?php echo $langEngine->get('wproCore_fileBrowser', 'name'); ?></strong><span id="displayFolderName"></span></div>
<div id="displayFolderSize"></div>
</div>
</div>

<div style="display:none" id="filePane">
<iframe id="filePanePreview" name="filePanePreview" class="previewFrame" src="<?php echo htmlspecialchars($EDITOR->editorLink('dialog.php?dialog=wproCore_fileBrowser&action=preview'.($EDITOR->appendToQueryStrings ? '&'.$EDITOR->appendToQueryStrings : '').'&'.$wpsname.'='.$wpsid.($EDITOR->appendSid ? strip_tags(defined('SID') ? '&'.SID : '') : '') )); ?>" frameborder="0"></iframe>
<span id="loadPreview"><a href="javascript:undefined" onclick="FB.preview()"><?php echo $langEngine->get('wproCore_fileBrowser', 'loadPreview'); ?></a> | </span><a href="javascript:undefined" onclick="FB.previewInNewWindow()"><?php echo $langEngine->get('wproCore_fileBrowser', 'previewInNewWindow'); ?></a>
<hr />
<div class="displayDetails">
<div><strong><?php echo $langEngine->get('wproCore_fileBrowser', 'name'); ?></strong><span id="displayName"></span></div>
<div><strong><?php echo $langEngine->get('wproCore_fileBrowser', 'type'); ?></strong><span id="displayType"></span></div>
<div><strong><?php echo $langEngine->get('wproCore_fileBrowser', 'size'); ?></strong><span id="displaySize"></span></div>
<div><strong><?php echo $langEngine->get('wproCore_fileBrowser', 'modified'); ?></strong><span id="displayModified"></span></div>
<div id="displayExtra"></div>
</div>
</div>
</div>
<?php
$tabs->endTab();
if (!$chooser) :
$tabs->startTab($this->underlineAccessKey($langEngine->get('core', 'options'), 'o'), array('accesskey'=>'o'));
?>
<div id="optionsLoadMessage"></div>
<?php if ($action == 'link'||$action == 'document') : ?>

<label><input type="checkbox" name="prefixFileIcon" value=""<?php if ($defaultValues['prefixFileIcon']==true):?> checked="checked"<?php endif ?> /> <?php echo $langEngine->get('wproCore_fileBrowser', 'prefixIcon'); ?></label><br />
<label><input type="checkbox" name="appendFileType" value=""<?php if ($defaultValues['appendFileType']==true):?> checked="checked"<?php endif ?> /> <?php echo $langEngine->get('wproCore_fileBrowser', 'appendTypeSize'); ?></label><br />

<?php else : ?>

<?php 

$prefixes = array();
$DIALOG->plugins['wproCore_fileBrowser']->loadEmbedPlugins();
$i = 0;
foreach ($DIALOG->plugins['wproCore_fileBrowser']->embedPlugins as $name => $plugin) {
	if ($plugin->local) {
		$continue = false;
		foreach ($plugin->extensions as $extension) {
			if (in_array($extension, $allowedExtensions)) {
				$continue = true;
				break;
			}
		}
		if ($continue) {
			echo '<div id="localEmbed'.$i.'" style="display:none">';
			echo $plugin->displayLocalOptions('localEmbed'.$i);
			$prefixes[$name] = 'localEmbed'.$i;
			echo '</div>';
			$i++;
		}
	}
}
?>
<script type="text/javascript">
<?php foreach ($prefixes as $k => $v) : ?>
FB.localEmbedPrefixes['<?php echo addslashes($k) ?>'] = '<?php echo addslashes($v) ?>';
<?php endforeach ?>
</script>
<?php endif ?>

<?php
$tabs->endTab();
endif;
$tabs->display();
?>

</div>


</div>
<!-- end file browser -->


<div id="messageBox" class="wproFloatingDialog">&nbsp;</div>

<?php if (($action == 'link' || $action == 'document') && ($EDITOR->featureIsEnabled('target')||$EDITOR->featureIsEnabled('events')) && !$chooser) : ?>

<div id="windowOptionsBox" class="wproFloatingDialog"><div class="bodyHolder">

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_fileBrowser', 'properties') ?></legend>

<?php
$t = $this->createUI2ColTable();
$t->width = 'medium';

$t->addRow($langEngine->get('wproCore_fileBrowser', 'windowName'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '25',
	'name' => 'windowName',
	'value' => $defaultValues['windowName'],
	'accesskey' => 'w',
)), 'windowName');

$t->display();
?>

</fieldset>

<fieldset class="singleLine">
<legend><?php echo $langEngine->get('core', 'appearance') ?></legend>
<?php 

$t = $this->createUI2ColTable();
$t->width = 'medium';

$t->addRow($langEngine->get('wproCore_fileBrowser', 'windowDefaultAppearance'), '<input type="checkbox" name="windowDefaultAppearance" value="true" '.($defaultValues['windowDefaultAppearance']?' checked="checked"':'').' onclick="if(this.checked){document.getElementById(\'windowAppearance\').style.display=\'none\';}else{document.getElementById(\'windowAppearance\').style.display=\'\';}" />', 'windowDefaultAppearance');

$t->display();
?>
<div id="windowAppearance"<?php echo $defaultValues['windowDefaultAppearance']?' style="display:none"':'' ?>>
<?php
$t = $this->createUI2ColTable();
$t->width = 'medium';
$t->addRow($langEngine->get('core', 'width'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => 'windowWidth',
	'value' => $defaultValues['windowWidth'],
	'accesskey' => 'w',
)).' '.$langEngine->get('core', 'pixels'), 'windowWidth');

$t->addRow($langEngine->get('core', 'height'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => 'windowHeight',
	'accesskey' => 'h',
	'value' => $defaultValues['windowHeight'],
)).' '.$langEngine->get('core', 'pixels'), 'windowHeight');

$t->addRow($langEngine->get('wproCore_fileBrowser', 'showLocationBar'), '<input type="checkbox" name="windowLocationBar" value="true" '.($defaultValues['windowLocationBar']?' checked="checked"':'').' />', 'windowLocationBar');

$t->addRow($langEngine->get('wproCore_fileBrowser', 'showMenuBar'), '<input type="checkbox" name="windowMenuBar" value="true" '.($defaultValues['windowMenuBar']?' checked="checked"':'').' />', 'windowMenuBar');

$t->addRow($langEngine->get('wproCore_fileBrowser', 'showToolBar'), '<input type="checkbox" name="windowToolBar" value="true" '.($defaultValues['windowToolBar']?' checked="checked"':'').' />', 'windowToolBar');

$t->addRow($langEngine->get('wproCore_fileBrowser', 'showStatusBar'), '<input type="checkbox" name="windowStatusBar" value="true" '.($defaultValues['windowStatusBar']?' checked="checked"':'').' />', 'windowStatusBar');

$t->addRow($langEngine->get('wproCore_fileBrowser', 'showScrollbars'), '<input type="checkbox" name="windowScrollbars" value="true" '.($defaultValues['windowScrollbars']?' checked="checked"':'').' />', 'windowScrollbars');

$t->addRow($langEngine->get('wproCore_fileBrowser', 'resizable'), '<input type="checkbox" name="windowResizable" value="true" '.($defaultValues['windowResizable']?' checked="checked"':'').' />', 'windowResizable');

$t->display();

?>
</div>

</fieldset>

<div id="windowOptionButtons"></div>

</div></div>

<?php endif ?>

<?php if (!$DIALOG->chromeless) : ?>
<div class="bottomOptions">

<!-- standard bottom options for the file browser -->
<div class="fileBrowserBottomOptions" id="bottomOptions">
<?php 

$altChooserButton = '';
$function = $EDITOR->triggerEvent('onFileChooserButtonJS', array('type'=>$action,'field'=>'URL'));
if (!empty($function)) {		
	$altChooserButton='<button type="button" class="chooserButton" onclick="'.htmlspecialchars(implode(';',$function)).'" style="background-image:url(\''.$EDITOR->themeFolderURL.$EDITOR->theme.'/wysiwygpro/icons/folder.gif\')">&nbsp;</button>';
}

$t = $this->createUI2ColTable();
$t->width = 'small';

$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'url'), 'u'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '43',
	'name' => 'URL',
	'id' => 'URL',
	'style' => 'width:400px',
	'accesskey' => 'u',
)).'<button class="chooserButton" style="background-image:url(&quot;'.$themeURL.'buttons/view.gif&quot;);display:none" name="previewButton" id="previewButton" type="button" onclick="document.dialogForm.URL.onchange()" title="'.$langEngine->get('wproCore_fileBrowser','loadPreview').'">&nbsp;</button>'.$altChooserButton, 'URL');

if (($action == 'link' || $action == 'document') && ($EDITOR->featureIsEnabled('target')||$EDITOR->featureIsEnabled('events')) && !$chooser) :

$row = '<select name="targetOptions" onchange="FB.targetChanged(this, true)">
<option value="_self">'.$langEngine->get('wproCore_fileBrowser', 'sameWindow').'</option>
<option value="_blank">'.$langEngine->get('wproCore_fileBrowser', 'newWindow').'</option>';

if ($EDITOR->featureIsEnabled('target')) :
$row.='<option value="_parent">'.$langEngine->get('wproCore_fileBrowser', 'parentWindow').'</option>
<option value="_top">'.$langEngine->get('wproCore_fileBrowser', 'topWindow').'</option>
<option value="">'.$langEngine->get('wproCore_fileBrowser', 'namedWindow').'</option>';
endif;

$row .= '</select>';

if ($EDITOR->featureIsEnabled('target')) :
$row.=$this->HTMLInput(array(
	'type' => 'text',
	'size' => '43',
	'name' => 'target',
	'value' => $defaultValues['target'],
	'style' => 'width:150px;display:none;',
	'accesskey' => 'o',
	'onchange' => 'if(/^(_self|_blank|_parent|_top)$/i.test(this.value)){this.form.targetOptions.value=this.value;}else{this.form.targetOptions.value=\'\';};FB.targetChanged(this.form.targetOptions);',
));		
endif;

if ($EDITOR->featureIsEnabled('events')) :

$row .= '<button style="display:none" id="windowOptions" class="largeButton" type="button" onclick="FB.showWindowOptions();">'.$langEngine->get('wproCore_fileBrowser', 'windowOptions').'</button>
<input type="hidden" name="onclick" value="" />';

endif;

$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'target'), 'o'), $row, 'targetOptions');

endif;

$t->display();	

 ?>
</div>



</div>

<?php if (($action == 'link' || $action == 'document') && !$chooser) : ?>
<div id="removeLink" style="display: none">
<?php
echo $this->HTMLInput(array(
	'class'=>'largeButton',
	'onclick' => 'FB.removeLink()',
	'type'=>'button',
	'name'=>'unlink',
	'value'=>$DIALOG->langEngine->get('editor', 'unlink'),
));
?>
</div>
<?php endif ?>

<div class="fbButtons">
<?php echo $this->HTMLInput($fbOptions[0]); ?><br />
<?php echo $this->HTMLInput($fbOptions[1]); ?>
</div>


<?php endif ?>



<script type="text/javascript">
/*<![CDATA[ */
	dialog.hideLoadMessageOnLoad = false;
	strApply = '<?php echo addslashes($langEngine->get('wproCore_fileBrowser', 'JSApply')) ?>';
	strInsert = '<?php echo addslashes($langEngine->get('wproCore_fileBrowser', 'JSInsert')) ?>';
	
	strOpenFolder = '<?php echo addslashes($langEngine->get('wproCore_fileBrowser', 'openFolder')); ?>';
	
	initFileBrowser();
	setTimeout('browserReady = true;', 500);
	
	if (document.dialogForm.elements['target']) {
		if (document.dialogForm.elements['target'].value != '') {
			document.dialogForm.elements['target'].onchange();
		}
	}
/* ]]>*/
</script>