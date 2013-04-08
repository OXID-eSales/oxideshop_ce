<?php
if (!defined('IN_WPRO')) exit;
// extract editor settings to local namespace
extract(get_object_vars($EDITOR));
// modify editor settings for display where required
$themeURL = $themeFolderURL.$theme.'/wysiwygpro/';
$langURL = $langFolderURL.$langEngine->actualLang.'/wysiwygpro/';
$browserType = $_browserType;
$browserVersion = $_browserVersion;
$startView = strtolower($startView);
$loadMethod = strtolower($loadMethod);
$lineReturns = strtolower($lineReturns);
$iframeSecurity = isset($_SERVER['HTTPS']) ? (($_SERVER['HTTPS']=='on') ? 'src="'.htmlspecialchars($editorURL).'core/html/iframeSecurity.htm" ' : '') : '';
//$iframeSecurity = 'src="'.$editorURL.'core/html/iframeSecurity.htm" ';
$displayAbove = $EDITOR->_getDisplayAbove();
$displayBelow = $EDITOR->_getDisplayBelow();
$configJS = $EDITOR->_getConfigJS();
// some HTML safe versions of variables
$asEditorURL = htmlspecialchars($editorURL);
$asThemeURL = htmlspecialchars($themeURL);
$asLangURL = htmlspecialchars($langURL);
// some JS var safe versions
$jsEditorURL = addslashes($editorURL);
$jsThemeURL = addslashes($themeURL);
$jsLangURL = addslashes($langURL);
// output HTML
$STR = '';

// REMEMBER: scripts containing document.write must be isolated else the wproAjax helper will die!!

// editor container opening tag, has the theme class name to isolate the theme rules from the rest of the page
$STR .= '<div id="##name##_container" style="width:'.htmlspecialchars($width).';" class="'.htmlspecialchars($theme).'" dir="'.$langEngine->get('conf','dir').'" lang="'.$langEngine->langCode.'">';

$STR .= $EDITOR->fetchStyleSheets();

// second opening tag, sole purpose is to set the wproEditor and wproDialogEditorShared theme selectors
$STR .= '<div class="wproEditor wproDialogEditorShared">

<div id="##name##_displayAbove">'.$displayAbove.'</div>

<div id="##name##_loadMessage" class="wproLoadMessageHolder" style="width:'.htmlspecialchars($width).';height:'.htmlspecialchars($height).';display:none"><div class="wproLoadMessage" style="margin-top:'.(intval(preg_replace("/[px %]/si", '', $height))/2 - 40).'px"><img src="'.addslashes($themeURL).'misc/loader.gif" alt="" /> '.htmlspecialchars($langEngine->get('core', 'pleaseWait')).'</div></div>' ;

$STR .= $EDITOR->fetchSharedJS();

$STR .= '
<div id="##name##_textareaHider">
<script type="text/javascript">
/*<![CDATA[ */
if (document.getElementById(\'##name##_loadMessage\').parentNode.offsetWidth) document.getElementById(\'##name##_loadMessage\').style.width = document.getElementById(\'##name##_loadMessage\').parentNode.offsetWidth+\'px\';
var ##name##_doWP = true;';
if ($_subsequent) :
$STR .= 'if (typeof(WPro.##name##) != \'undefined\') {
alert(\'WysiwygPro fatal error: There cannot be more than one instance of WysiwygPro with the same name value. A regular textarea will be displayed instead.\');
##name##_doWP = false;
document.getElementById(\'##name##_loadMessage\').style.display=\'none\';
}';
endif;
$STR .= 'if (##name##_doWP) {
document.getElementById(\'##name##_loadMessage\').style.display=\'block\';
document.getElementById(\'##name##_textareaHider\').style.display=\'none\';
}
/* ]]>*/
</script>
<textarea class="wproHTML" style="width:'.htmlspecialchars($width?$width:'100%').';height:'.htmlspecialchars($height).';" name="##_originalName##" id="##_originalName##" rows="10" cols="10">##value##</textarea>
</div>

<div id="##name##_border" class="wproBorder" style="display:none;">

<!-- editing tab -->
<div id="##name##_designTab" class="'; if ($startView == 'design'): $STR .= 'wproVisibleTab'; elseif ($browserType == 'msie') : $STR .= 'wproHiddenTab'; else: $STR .= 'wproHiddenDesignTab'; endif; $STR .= '">
<div id="##name##_designToolbar" class="wproToolbarHolder" style="'.(($startView == 'design') ? 'display:block' : 'display:none').'">';
$i=0; foreach($toolbarLayout as $toolbar => $buttons):
if (!empty($buttons)) :
$STR .= '<div class="wproToolbar"><span class="wproToolGroup">';
if ($saveButton && $i == 0) :
$STR .= '<button type="submit" name="##name##_designSave" class="wproReady" style="background-image:url(\''.htmlspecialchars($saveButtonURL).'\');width:'.htmlspecialchars($saveButtonWidth).'px;height:'.htmlspecialchars($saveButtonHeight).'px" title="'.$saveButtonLabel.'">&nbsp;</button>';
endif;
foreach($buttons as $buttonId):
$button = isset($_buttonDefinitions[strtolower($buttonId)]) ? $_buttonDefinitions[strtolower($buttonId)] : ''; if (!is_array($button)) {continue;}
if ($button[0] == 'separator') :
$STR .= '</span>
<img alt="" src="'.$asEditorURL.'core/images/spacer.gif'.'" class="wproSeparator" />
<span class="wproToolGroup">';
elseif ($button[0] == 'spacer') :
$STR .= '<img alt="" src="'.$asEditorURL.'core/images/spacer.gif'.'" width="'.htmlspecialchars($button[1]).'" class="wproSpacer" />';
elseif ($button[0] == 'button') :

$STR .= '<button type="button" title="'.$button[1].'" onclick="'.htmlspecialchars($button[2]).'" style="background-image:url(\''.htmlspecialchars($button[3]).'\');'.(($button[4]!=22&&(empty($button[7])||!empty($button[4]))) ? 'width:'.htmlspecialchars($button[4]).'px;' : '').(($button[5]!=22)?'height:'.htmlspecialchars($button[5]).'px':'').'" class="'.(!empty($button[7]) ? 'wproTextButtonReady ':'').'wproReady"'.((!empty($button[6])) ? ' _wp_cid="'.$button[6].'"' : '').'>'.((!empty($button[7])) ? '<span>'.(($button[7]===true) ? $button[1] : $button[7]).'</span>' : '&nbsp;').'</button>';

//$STR .= '<button type="button" title="'.$button[1].'" onclick="'.htmlspecialchars($button[2]).'" style="'.(($button[4]!=22) ? 'width:'.htmlspecialchars($button[4]).'px;' : '').(($button[5]!=22)?'height:'.htmlspecialchars($button[5]).'px':'').'" class="wproReady"'.((!empty($button[6])) ? ' _wp_cid="'.$button[6].'"' : '').'><img src="'.htmlspecialchars($button[3]).'" alt="" />&nbsp;</button>';

elseif ($button[0] == 'menu'&&!empty($button[2])) :
$STR .= '<button type="button" title="'.$button[1].'" onmousedown="WPro.##name##.showButtonMenu(this, [ ';
foreach($button[2] as $mButtonId): 
$mButton = isset($_buttonDefinitions[strtolower($mButtonId)]) ? $_buttonDefinitions[strtolower($mButtonId)] : ''; 
if (!is_array($mButton)) {continue;}
if ($mButton[0] == 'separator') :
$STR .= '[\'separator\'],';
elseif ($mButton[0] == 'button') :
$STR .= '[&quot;'.$mButtonId.'&quot;,&quot;'.$this->wproJSAttrSafe($mButton[1]).'&quot;,&quot;'.$this->wproJSAttrSafe($mButton[2]).'&quot;,&quot;'.$this->wproJSAttrSafe($mButton[3]).'&quot;,&quot;'.$this->wproJSAttrSafe($mButton[4]).'&quot;,&quot;'.$this->wproJSAttrSafe($mButton[5]).'&quot;,&quot;'.(empty($mButton[6]) ? '' : $this->wproJSAttrSafe($mButton[6])).'&quot;],';
endif;
endforeach;
$STR .= ' ]);" onkeypress="this.onmousedown()" style="background-image:url(\''.htmlspecialchars($button[3]).'\');width:'.htmlspecialchars($button[4]).'px;height:'.htmlspecialchars($button[5]).'px" class="wproReady"'.((!empty($button[6])) ? ' _wp_cid="'.$button[6].'"':'').'>&nbsp;</button>';
//$STR .= ']);" onkeypress="this.onmousedown()" style="width:'.htmlspecialchars($button[4]).'px;height:'.htmlspecialchars($button[5]).'px" class="wproReady"'.((!empty($button[6])) ? ' _wp_cid="'.$button[6].'"':'').'><img src="'.htmlspecialchars($button[3]).'" alt="" />&nbsp;</button>';
elseif ($button[0] == 'builtinselect') :

//$STR .= '<button type="button" id="##name##_'.htmlspecialchars($buttonId).'" value="'.htmlspecialchars($button[1]).'" style="width:'.htmlspecialchars($button[4]).'px;" onmouseup="this.className=\'wproOver wproDropDown\';" onmousedown="WPro.##name##.showListMenu(this, \''.$this->wproJSAttrSafe($buttonId).'\')" onmouseover="this.className=\'wproOver wproDropDown\'" onmouseout="this.className=\'wproReady wproDropDown\'" class="wproReady wproDropDown" title="'.$button[1].'">'.str_replace(' ','&nbsp;', $button[1]).'</button><img class="wproSpacer" alt="" src="'.$asEditorURL.'core/images/spacer.gif" width="1" />';

$STR .= '<button type="button" id="##name##_'.htmlspecialchars($buttonId).'" style="width:'.htmlspecialchars($button[4]).'px;" onmousedown="WPro.##name##.showListMenu(this, \''.$this->wproJSAttrSafe($buttonId).'\')" class="wproDropDown" title="'.$button[1].'"><span style="width:'.htmlspecialchars($button[4]-15).'px;">'.str_replace(' ','&nbsp;', $button[1]).'</span></button><img class="wproSpacer" alt="" src="'.$asEditorURL.'core/images/spacer.gif" width="1" />';


elseif ($button[0] == 'select') :
$STR .= '<select onchange="'.$button[2].'" style="width:'.htmlspecialchars($button[5]).'px;" title="'.htmlspecialchars($button[1]).'">';
foreach($button[3] as $value=>$label) :
$STR .= '<option label="'.htmlspecialchars($label).'" value="'.htmlspecialchars($value).'"'.(($value==$button[4]) ? ' selected="selected"' : '').'>'.htmlspecialchars($label).'</option>';
endforeach;
$STR .= '</select>';
endif;
endforeach;
$STR .= '</span></div>';
endif;
$i++; endforeach;
$STR .= '</div>
<!-- html source tab -->
<div id="##name##_sourceToolbar" class="wproToolbarHolder" style="'.(($startView == 'source') ? 'display:block' : 'display:none').'">
<div class="wproToolbar">';
if ($saveButton) :
$STR .= '<button type="submit" name="##name##_sourceSave" class="wproReady" style="background-image:url(\''.htmlspecialchars($saveButtonURL).'\');width:'.htmlspecialchars($saveButtonWidth).'px;height:'.htmlspecialchars($saveButtonHeight).'px" title="'.$saveButtonLabel.'">&nbsp;</button>';
endif;
if ($EDITOR->buttonIsEnabled('fullwindow')) :
$STR .= '<button type="button" title="'.$_buttonDefinitions['fullwindow'][1].'" onclick="'.$_buttonDefinitions['fullwindow'][2].'" style="background-image:url(\''.htmlspecialchars($_buttonDefinitions['fullwindow'][3]).'\');width:'.htmlspecialchars($_buttonDefinitions['fullwindow'][4]).'px;height:'.htmlspecialchars($_buttonDefinitions['fullwindow'][5]).'px" class="wproReady"'.((!empty($_buttonDefinitions['fullwindow'][6]))?' _wp_cid="'.$_buttonDefinitions['fullwindow'][6].'"':'').'>&nbsp;</button>';
endif;
if (isset($_buttonDefinitions['print'])) :
$STR .= '<button type="button" title="'.$_buttonDefinitions['print'][1].'" onclick="'.$_buttonDefinitions['print'][2].'" style="background-image:url(\''.htmlspecialchars($_buttonDefinitions['print'][3]).'\');width:'.htmlspecialchars($_buttonDefinitions['print'][4]).'px;height:'.htmlspecialchars($_buttonDefinitions['print'][5]).'px" class="wproReady"'.((!empty($_buttonDefinitions['print'][6]))?' _wp_cid="'.$_buttonDefinitions['print'][6].'"':'').'>&nbsp;</button>';
endif;
if (isset($_buttonDefinitions['find'])) :
$STR .= '<button type="button" title="'.$_buttonDefinitions['find'][1].'" onclick="'.$_buttonDefinitions['find'][2].'" style="background-image:url(\''.htmlspecialchars($_buttonDefinitions['find'][3]).'\');width:'.htmlspecialchars($_buttonDefinitions['find'][4]).'px;height:'.htmlspecialchars($_buttonDefinitions['find'][5]).'px" class="wproReady"'.((!empty($_buttonDefinitions['find'][6]))?' _wp_cid="'.$_buttonDefinitions['find'][6].'"':'').'>&nbsp;</button>';
endif;
if ($EDITOR->buttonIsEnabled('cut')) :
$STR .= '<img alt="" class="wproSeparator" src="'.$asEditorURL.'core/images/spacer.gif'.'" />
<button type="button" title="'.$langEngine->get('editor','cut').'" onclick="WPro.##name##.callFormatting(\'cut\')" style="background-image:url(\''.$asThemeURL.'buttons/cut.gif'.'\');" class="wproReady" _wp_cid="cut">&nbsp;</button>';
endif;
if ($EDITOR->buttonIsEnabled('copy')) :
$STR .= '<button type="button" title="'.$langEngine->get('editor','copy').'" onclick="WPro.##name##.callFormatting(\'copy\')" style="background-image:url(\''.$asThemeURL.'buttons/copy.gif'.'\');" class="wproReady" _wp_cid="copy">&nbsp;</button>';
endif;
if ($EDITOR->buttonIsEnabled('paste')) :
$STR .= '<button type="button" title="'.$langEngine->get('editor','paste').'" onclick="WPro.##name##.callFormatting(\'paste\')" style="background-image:url(\''.$asThemeURL.'buttons/paste.gif\');" class="wproReady" _wp_cid="paste">&nbsp;</button>';
endif;
$STR .= '<img alt="" class="wproSeparator" src="'.$asEditorURL.'core/images/spacer.gif'.'" />
<button type="button" title="'.$langEngine->get('editor','undo').'" onclick="WPro.##name##.callFormatting(\'undo\')" style="background-image:url(\''.$asThemeURL.'buttons/undo.gif'.'\');" class="wproReady">&nbsp;</button>
<button type="button" title="'.$langEngine->get('editor','redo').'" onclick="WPro.##name##.callFormatting(\'redo\')" style="background-image:url(\''.$asThemeURL.'buttons/redo.gif'.'\');" class="wproReady">&nbsp;</button>
<img alt="" class="wproSeparator" src="'.$asEditorURL.'core/images/spacer.gif" />
<button type="button" title="'.$langEngine->get('editor','syntaxHighlight').'" onclick="WPro.##name##.syntaxHighlightClicked();" style="background-image:url(\''.$asThemeURL.'buttons/syntaxhighlight.gif'.'\');" class="wproReady">&nbsp;</button>
<button type="button" id="##name##_wordWrapButton" title="'.$langEngine->get('editor','wordWrap').'" onclick="WPro.##name##.toggleWordWrap();" style="background-image:url(\''.$asThemeURL.'buttons/wordwrap.gif'.'\');" class="wproReady">&nbsp;</button>
</div>
</div>
<fieldset class="wproFrameFix">
<iframe '.$iframeSecurity.'id="##name##_editFrame" name="##name##_editFrame" class="wproEditFrame" frameborder="0"></iframe>
</fieldset>
</div>
<!-- preview tab -->
<div id="##name##_previewTab" class="'.(($startView == 'preview') ? 'wproVisibleTab' : 'wproHiddenTab').'">
<div id="##name##_previewToolbar" class="wproToolbarHolder">
<div class="wproToolbar">';
if ($saveButton) :
$STR .= '<button type="submit" name="##name##_previewSave" class="wproReady" style="background-image:url(\''.htmlspecialchars($saveButtonURL).'\');width:'.htmlspecialchars($saveButtonWidth).'px;height:'.htmlspecialchars($saveButtonHeight).'px" title="'.$saveButtonLabel.'">&nbsp;</button>';
endif;
if ($EDITOR->buttonIsEnabled('fullwindow')) :
$STR .= '<button type="button" title="'.$_buttonDefinitions['fullwindow'][1].'" onclick="'.$_buttonDefinitions['fullwindow'][2].'" style="background-image:url(\''.htmlspecialchars($_buttonDefinitions['fullwindow'][3]).'\');width:'.htmlspecialchars($_buttonDefinitions['fullwindow'][4]).'px;height:'.htmlspecialchars($_buttonDefinitions['fullwindow'][5]).'px" class="wproReady"'.((!empty($_buttonDefinitions['fullwindow'][6])) ? ' _wp_cid="'.$_buttonDefinitions['fullwindow'][6].'"' : '').'>&nbsp;</button>';
endif;
if (isset($_buttonDefinitions['print'])) :
$STR .= '<button type="button" title="'.$_buttonDefinitions['print'][1].'" onclick="'.$_buttonDefinitions['print'][2].'" style="background-image:url(\''.htmlspecialchars($_buttonDefinitions['print'][3]).'\');width:'.htmlspecialchars($_buttonDefinitions['print'][4]).'px;height:'.htmlspecialchars($_buttonDefinitions['print'][5]).'px" class="wproReady"'.((!empty($_buttonDefinitions['print'][6])) ? ' _wp_cid="'.$_buttonDefinitions['print'][6].'"' : '').'>&nbsp;</button>';
endif;
if ($EDITOR->buttonIsEnabled('zoom')) :
$STR .= '<select onchange="'.$_buttonDefinitions['zoom'][2].'" style="width:'.htmlspecialchars($_buttonDefinitions['zoom'][5]).'px;" title="'.htmlspecialchars($_buttonDefinitions['zoom'][1]).'">';
foreach($_buttonDefinitions['zoom'][3] as $value=>$label) :
$STR .= '<option label="'.htmlspecialchars($label).'" value="'.htmlspecialchars($value).'"'.(($value==$_buttonDefinitions['zoom'][4]) ? ' selected="selected"' : '').'>'.htmlspecialchars($label).'</option>';
endforeach;
$STR .= '</select>';endif;
$STR .= '</div>
</div>
<fieldset class="wproFrameFix">
<iframe '.$iframeSecurity.'id="##name##_previewFrame" name="##name##_previewFrame" class="wproPreviewFrame" frameborder="0"></iframe>
</fieldset>
</div>';

if ($EDITOR->featureIsEnabled('tagPath')) :
$STR .= '<div class="wproTagPath" id="##name##_tagPath">&nbsp;</div>';
endif;
$STR .= '<!-- view tabs -->
<div id="##name##_tabHolder" class="wproTabHolder">';
if ($EDITOR->featureIsEnabled('viewTabs')) :
$STR .= '<div class="wproTabNoTab"><img src="'.$asEditorURL.'core/images/spacer.gif'.'" width="5" height="10" alt="" /></div>';
$STR .= '<span class="wproTabs">';
if ($EDITOR->featureIsEnabled('designTab')) :
$STR .= '<button type="button" id="##name##_designTabButton" onmouseup="WPro.##name##.showDesign();" onmousedown="WPro.tabDown(this);" class="'.(($startView == 'design')?'wproTButtonUp':'wproTButtonDown').'" title="Design"><img src="'.$asThemeURL.'buttons/tab.design.gif'.'" width="14" height="10" alt="" /> '.$langEngine->get('editor', 'design').'</button>';
endif;
if ($EDITOR->featureIsEnabled('sourceTab')) :
$STR .= '<button type="button" id="##name##_sourceTabButton" onmouseup="WPro.##name##.showSource();" onmousedown="WPro.tabDown(this);" class="'.(($startView == 'source')?'wproTButtonUp':'wproTButtonDown').'" title="Source"><img src="'.$asThemeURL.'buttons/tab.source.gif'.'" width="14" height="10" alt="" /> '.$langEngine->get('editor', 'source').'</button>';
endif;
if ($EDITOR->featureIsEnabled('previewTab')) :
$STR .= '<button type="button" id="##name##_previewTabButton" onmouseup="WPro.##name##.showPreview();" onmousedown="WPro.tabDown(this);" class="'.(($startView == 'preview')?'wproTButtonUp':'wproTButtonDown').'" title="Preview"><img src="'.$asThemeURL.'buttons/tab.preview.gif'.'" width="14" height="10" alt="" /> '.$langEngine->get('editor', 'preview').'</button>';
endif;
$STR .= '</span>';
endif;

$STR .= '<div class="wproMessages">'.(($lineReturns != 'br' && $EDITOR->featureIsEnabled('shift+enterMessage')) ? $langEngine->get('editor', 'shift+entermessage').'&nbsp;&nbsp;&nbsp;':'');
if ($EDITOR->featureIsEnabled('guidelines')) :
$STR .= '<button type="button" id="##name##_guidelinesButton" title="'.$langEngine->get('editor', 'toggleGuidelines').'" onclick="WPro.##name##.toggleGuidelines()" style="background-image:url(\''.$asThemeURL.'buttons/guidelines.gif'.'\');width:18px;height:18px" class="'.($guidelines?'wproLatched':'wproReady').'" onmouseover="WPro.##name##._mOver(this)" onmouseout="WPro.##name##._mOut(this)" onmousedown="WPro.##name##._mDown(this)" onmouseup="WPro.##name##._mUp(this)" _wp_cid="guidelines">&nbsp;</button>';
endif;
if ($EDITOR->featureIsEnabled('dragresize')) :
$STR .= '<button type="button" id="##name##_dragresizeButton" class="wproResizeCorner" style="background-image:url(\''.$asThemeURL.'buttons/resizecorner.gif'.'\');">&nbsp;</button>';
endif;
$STR .= '</div>


</div>

</div>';

$STR .= $EDITOR->fetchCoreEditorJS();

// plugin JS includes, included as a single compressed include file

if (WPRO_COMPILE_JS_INCLUDES) {
	$string = array();
	$string[0] = '';
	foreach ($JSPlugins as $id => $url) {
		if (!empty($url)) {
			if (!wproJSPluginAdded($id.'/'.$url)) {
				//echo count($string).' ';
				$s = ','.$id.'/'.$url;
				if (strlen($string[count($string)-1].$s) < 600) {
					$string[count($string)-1].=$s;
				} else {
					array_push($string, $s);
				}
			}
		}
	}
	//print_r($string);
	foreach ($string as $s) {
		if (!empty($s)) {
			$STR .= '<script src="'.htmlspecialchars($EDITOR->editorLink('core/compileJSPlugins.php?browserType='.$browserType.'&plugins='.base64_encode($s).'&v='.$version)).'" type="text/javascript"></script>';
		}
	}
} else {
foreach ($JSPlugins as $id => $url) :
	if (!empty($url)) :
		if (!wproJSPluginAdded($id.'/'.$url)) { 
			if (substr($id, 0, 9)=='wproCore_') {
				$dir = 'core/plugins/';
			} else {
				$dir = 'plugins/';
			}
			$STR .= '<script src="'.$asEditorURL.$dir.$id.'/'.$url.'" type="text/javascript"></script>';
		}
	endif;
endforeach;
}


$STR .= '<script type="text/javascript">
/*<![CDATA[ */
/* all the config stuff... */';
$STR .= 'if (!WPro.##name##) {
WPro.newEditor(\'##name##\', \'##_originalName##\');';
if ($_v2Mode == true) {
$STR .= 'var ##name## = WPro.##name##;';
}

foreach ($JSPlugins as $id => $url) :
if (!empty($id)) :
$STR .= 'WPro.##name##.pluginsToLoad.push(\''.addslashes($id).'\');';
endif;
endforeach;
for ($i=0;$i<count($JSEditorEvents);$i++) :
if (!empty($JSEditorEvents[$i][0])) :
$STR .= 'WPro.##name##.eventsToLoad.push([\''.addslashes($JSEditorEvents[$i][0]).'\', '.$JSEditorEvents[$i][1].']);';
endif;
endfor;
for ($i=0;$i<count($JSHTMLFilters);$i++) :
if (!empty($JSHTMLFilters[$i][0])) :
$STR .= 'WPro.##name##.filtersToLoad.push([\''.addslashes($JSHTMLFilters[$i][0]).'\', '.$JSHTMLFilters[$i][1].']);';
endif;
endfor;
foreach ($JSBSH as $id => $func) :
if (!empty($id)) :
$STR .= 'WPro.##name##.bshToLoad.push([\''.addslashes($id).'\', '.$func.']);';
endif;
endforeach;
foreach ($JSFH as $id => $func) :
if (!empty($id)) :
$STR .= 'WPro.##name##.fhToLoad.push([\''.addslashes($id).'\', '.$func.']);';
endif;
endforeach;
foreach ($JSFVH as $id => $func) :
if (!empty($id)) :
$STR .= 'WPro.##name##.fvhToLoad.push([\''.addslashes($id).'\', '.$func.']);';
endif;
endforeach;

$STR .= 'WPro.##name##.toolbarHeight='.intval($toolbarHeight).';';

$STR .= 'WPro.##name##.lng=Array();

WPro.##name##.sid = unescape(\''.addslashes($sid).'\');
WPro.##name##.sessRefresh = '.addslashes($sessRefresh).';

WPro.##name##.themeURL = \''.$jsThemeURL.'\';
WPro.##name##.langURL = \''.$jsLangURL.'\';

WPro.##name##.themeName = \''.addslashes($theme).'\';
WPro.##name##.langName = \''.addslashes($lang).'\';

WPro.##name##.specifiedHeight = '.intval($height).';
WPro.##name##.specifiedWidth = \''.addslashes($width).'\';

WPro.##name##.charset = \''.addslashes($htmlCharset).'\';
WPro.##name##.htmlLang = \''.addslashes($htmlLang).'\';
WPro.##name##.htmlDirection = \''.addslashes($htmlDirection).'\';
WPro.##name##.useXHTML = '.(strstr(strtolower($htmlVersion), 'xhtml') ? 'true' : 'false').';
WPro.##name##.strict = '.(strstr(strtolower($htmlVersion), 'strict') ? 'true' : 'false').';

WPro.##name##.baseURL = \''.addslashes($baseURL).'\';

WPro.##name##.lineReturns = \''.addslashes($lineReturns).'\';
WPro.##name##.newCellInners = \''.addslashes($newCellInners).'\';
WPro.##name##.emptyValue = \''.addslashes($emptyValue).'\';

WPro.##name##.escapeCharacters = '.($escapeCharacters ? 'true' : 'false').';
WPro.##name##.escapeCharactersRange = \''.addslashes($escapeCharactersRange).'\';
WPro.##name##.escapeCharactersMappings = 
{'; $num = count($escapeCharactersMappings)-1; $i=0; foreach($escapeCharactersMappings as $code => $str):
$STR .= '"'.addslashes($code).'":"'.addslashes($str).'"';if ($i < $num) { $STR .= ',';$i++;}
endforeach;$STR .= '};

WPro.##name##.subsequent = '.($_subsequent ? 'true' : 'false').';

WPro.##name##._guidelines = '.($guidelines ? 'true' : 'false').';

WPro.##name##.doctype = \''.$doctype.'\';
WPro.##name##.fullURLs =  '.($fullURLs ? 'true' : 'false').';
WPro.##name##.urlFormat =  \''.addslashes($urlFormat).'\';
WPro.##name##.encodeURLs =  '.($encodeURLs ? 'true' : 'false').';
WPro.##name##.jsBookmarkLinks = '.($jsBookmarkLinks ? 'true' : 'false').';

WPro.##name##.iframeDialogs = '.($iframeDialogs ? 'true' : 'false').';

WPro.##name##.appendToQueryStrings = \''.addslashes($appendToQueryStrings).'\';

WPro.##name##.startView = \''.$startView.'\';
WPro.##name##.stylesheets = [';
$num = count($stylesheets)-1; $i=0; foreach ($stylesheets as $s) :
$STR .= "'".addslashes($s)."'";
if ($i < $num) { $STR .= ',';$i++;}
endforeach;
$STR .= '];

WPro.##name##.defaultCSS = \''.addslashes($cssText).'\';
WPro.##name##.fragmentCSS = \''.addslashes($fragmentCSSText).'\';
WPro.##name##.fragmentStylesheet = \''.addslashes($fragmentStylesheet).'\';
WPro.##name##.bodyClass = \''.addslashes($bodyClass).'\';

WPro.##name##.preserveAttributes = '.(($EDITOR->featureIsEnabled('dialogappearanceoptions')) ? 'true' : 'false').';

WPro.##name##.contextMenu = 
[ ';foreach($contextMenu as $buttonId): $button = isset($_buttonDefinitions[$buttonId]) ? $_buttonDefinitions[$buttonId] : '';
if (!is_array($button)) {continue;}
if ($button[0] == 'separator') :
$STR .= '[\'separator\'],';
elseif ($button[0] == 'button') :
$STR .= '["'.addslashes($buttonId).'","'.addslashes($button[1]).'","'.addslashes($button[2]).'","'.addslashes($button[3]).'","'.addslashes($button[4]).'","'.addslashes($button[5]).'","'.(empty($button[6]) ? '' : $button[6]).'"],';
endif;
endforeach; $STR .= ' ];';

if (!empty($paragraphStyles)) :
$STR .= 'WPro.##name##.lng[\'paragraphStyles\'] = "'.addslashes($langEngine->get('editor', 'paragraphStyles')).'";
WPro.##name##.paragraphStyles = '.$this->wproJSArray($paragraphStyles).';';
endif;

if (!empty($textStyles)) :
$STR .= 'WPro.##name##.lng[\'textStyles\'] = "'.addslashes($langEngine->get('editor', 'textStyles')).'";
WPro.##name##.textStyles = '.$this->wproJSArray($textStyles).';';
endif;

if (!empty($linkStyles)) :
$STR .= 'WPro.##name##.lng[\'linkStyles\'] = "'.addslashes($langEngine->get('editor', 'linkStyles')).'";
WPro.##name##.linkStyles = '.$this->wproJSArray($linkStyles).';';
endif;

if (!empty($rulerStyles)) :
$STR .= 'WPro.##name##.lng[\'rulerStyles\'] = "'.addslashes($langEngine->get('editor', 'rulerStyles')).'";
WPro.##name##.rulerStyles = '.$this->wproJSArray($rulerStyles).';';
endif;

if (!empty($imageStyles)) :
$STR .= 'WPro.##name##.lng[\'imageStyles\'] = "'.addslashes($langEngine->get('editor', 'imageStyles')).'";
WPro.##name##.imageStyles = '.$this->wproJSArray($imageStyles).';';
endif;

if (!empty($tableStyles)) :
$STR .= 'WPro.##name##.lng[\'tableStyles\'] = "'.addslashes($langEngine->get('editor', 'tableStyles')).'";
WPro.##name##.tableStyles = '.$this->wproJSArray($tableStyles).';';
endif;

if (!empty($rowStyles)) :
$STR .= 'WPro.##name##.lng[\'rowStyles\'] = "'.addslashes($langEngine->get('editor', 'rowStyles')).'";
WPro.##name##.rowStyles = '.$this->wproJSArray($rowStyles).';';
endif;

if (!empty($cellStyles)) :
$STR .= 'WPro.##name##.lng[\'cellStyles\'] = "'.addslashes($langEngine->get('editor', 'cellStyles')).'";
WPro.##name##.cellStyles = '.$this->wproJSArray($cellStyles).';';
endif;

if (!empty($listStyles)) :
$STR .= 'WPro.##name##.lng[\'listStyles\'] = "'.addslashes($langEngine->get('editor', 'listStyles')).'";
WPro.##name##.listStyles = '.$this->wproJSArray($listStyles).';';
endif;

if (!empty($listItemStyles)) :
$STR .= 'WPro.##name##.lng[\'listItemStyles\'] = "'.addslashes($langEngine->get('editor', 'listItemStyles')).'";
WPro.##name##.listItemStyles = '.$this->wproJSArray($listItemStyles).';';
endif;

if (!empty($textareaStyles)) :
$STR .= 'WPro.##name##.lng[\'textareaStyles\'] = "'.addslashes($langEngine->get('editor', 'textareaStyles')).'";
WPro.##name##.textareaStyles = '.$this->wproJSArray($textareaStyles).';';
endif;

if (!empty($textInputStyles)) :
$STR = 'WPro.##name##.lng[\'textFieldStyles\'] = "'.addslashes($langEngine->get('editor', 'textFieldStyles')).'";
WPro.##name##.textInputStyles = '.$this->wproJSArray($textInputStyles).';';
endif;

if (!empty($listBoxStyles)) :
$STR .= 'WPro.##name##.lng[\'listBoxStyles\'] = "'.addslashes($langEngine->get('editor', 'listBoxStyles')).'";
WPro.##name##.listBoxStyles = '.$this->wproJSArray($listBoxStyles).';';
endif;

if (!empty($buttonInputStyles)) :
$STR .= 'WPro.##name##.lng[\'buttonInputStyles\'] = "'.addslashes($langEngine->get('editor', 'buttonInputStyles')).'";
WPro.##name##.buttonInputStyles = '.$this->wproJSArray($buttonInputStyles).';';
endif;

if (!empty($radioInputStyles)) :
$STR .= 'WPro.##name##.lng[\'radioInputStyles\'] = "'.addslashes($langEngine->get('editor', 'radioInputStyles')).'";
WPro.##name##.radioInputStyles = '.$this->wproJSArray($radioInputStyles).';';
endif;

if (!empty($checkboxInputStyles)) :
$STR .= 'WPro.##name##.lng[\'checkboxInputStyles\'] = "'.addslashes($langEngine->get('editor', 'checkboxInputStyles')).'";
WPro.##name##.checkboxInputStyles = '.$this->wproJSArray($checkboxInputStyles).';';
endif;

if (!empty($imageInputStyles)) :
$STR .= 'WPro.##name##.lng[\'imageInputStyles\'] = "'.addslashes($langEngine->get('editor', 'imageInputStyles')).'";
WPro.##name##.imageInputStyles = '.$this->wproJSArray($imageInputStyles).';';
endif;

if (!empty($fileInputStyles)) :
$STR .= 'WPro.##name##.lng[\'fileInputStyles\'] = "'.addslashes($langEngine->get('editor', 'fileInputStyles')).'";
WPro.##name##.fileInputStyles = '.$this->wproJSArray($fileInputStyles).';';
endif;

if (!empty($inputStyles)) :
$STR .= 'WPro.##name##.lng[\'inputStyles\'] = "'.addslashes($langEngine->get('editor', 'inputStyles')).'";
WPro.##name##.inputStyles = '.$this->wproJSArray($inputStyles).';';
endif;

$STR .= 'WPro.##name##.fontMenu = [';
$num = count($fontMenu)-1; $i=0; foreach ($fontMenu as $font) :
$STR .= "'".addslashes($font)."'";
if ($i < $num) { $STR .= ',';$i++;}
endforeach;
$STR .= '];';

$STR .= 'WPro.##name##.sizeMenu = 
{';$num = count($sizeMenu)-1; $i=0; foreach($sizeMenu as $size => $label):
$STR .= '"'.addslashes($size).'":"'.addslashes($label).'"';if ($i < $num) { $STR .= ',';$i++;}
endforeach;$STR .= '};

/* language vars */
WPro.##name##.lng[\'default\'] = "'.addslashes($langEngine->get('core', 'default')).'";
WPro.##name##.lng[\'clearFormatting\'] = "'.addslashes($langEngine->get('editor', 'clearFormatting')).'";
WPro.##name##.lng[\'previewMode\'] = "'.addslashes($langEngine->get('editor', 'previewMode')).'";
WPro.##name##.lng[\'pleaseWait\'] = "'.addslashes($langEngine->get('core', 'pleaseWait')).'";';

$STR .= 'WPro.##name##.lng[\'selecttag\'] = "'.addslashes($langEngine->get('editor', 'selecttag')).'";
WPro.##name##.lng[\'tageditor\'] = "'.addslashes($langEngine->get('editor', 'tageditor')).'";
WPro.##name##.lng[\'removetag\'] = "'.addslashes($langEngine->get('editor', 'removetag')).'";
WPro.##name##.lng[\'deletetag\'] = "'.addslashes($langEngine->get('editor', 'deletetag')).'";';

$STR .= $configJS;

$STR .= '}
/*]]>*/
</script>';
if ($iframeDialogs) :
$STR .= '<div>
<iframe '.$iframeSecurity.'class="wproFloatingDialog" id="##name##_dialogFrame" name="##name##_dialogFrame" frameborder="0" scrolling="no" bgcolor="#ffffff"></iframe></div>';
endif;
$STR .= '<div id="##name##_displayBelow">'.$displayBelow.'</div>
<script type="text/javascript">
/*<![CDATA[ */
';
/* start the editor */
if ($loadMethod=='inline') :
$STR .= 'WPro.##name##.start();';
elseif ($loadMethod=='onload') :
$STR .= 'WPro.events.addEvent(window, \'load\', function(){WPro.##name##.start()});';
endif;
$STR .= '
/*]]>*/
</script>
</div>
</div>';

$contents = $STR;

?>