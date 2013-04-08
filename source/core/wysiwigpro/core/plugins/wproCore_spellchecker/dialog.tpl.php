<?php if (!defined('IN_WPRO')) exit; 
$rand = rand(0, 100);
?>
<!--<input type="hidden" name="headHTML" id="headHTML" value="" />-->
<input type="hidden" name="bodyHTML" id="bodyHTML" value="" />
<input type="hidden" name="referrer" id="referrer" value="" />
<input type="hidden" name="waitMessage" id="waitMessage" value="<?php echo $langEngine->get('wproCore_spellchecker', 'retrievingResults')?>" />
<div id="rightCol" class="rightCol" style="margin-top:125px">
<input class="largeButton" tabindex="3" type="button" name="ignore" id="ignore" value="<?php echo $langEngine->get('wproCore_spellchecker', 'ignoreOnce')?>" onclick="spellchecker.ignore();" /><br />
<input class="largeButton" tabindex="4" type="button" name="ignoreAll" id="ignoreAll" value="<?php echo $langEngine->get('wproCore_spellchecker', 'ignoreAll')?>" onclick="spellchecker.ignoreAll();" /><br />
<input class="largeButton" tabindex="5" type="button" name="learn" id="learn" value="<?php echo $langEngine->get('wproCore_spellchecker', 'learn')?>" onclick="spellchecker.learn();" />
<hr />
<input class="largeButton" tabindex="6" type="button" name="replace" id="replace" value="<?php echo $langEngine->get('wproCore_spellchecker', 'replaceOnce')?>" onclick="spellchecker.replace();" /><br />
<input class="largeButton" tabindex="7" type="button" name="replaceAll" id="replaceAll" value="<?php echo $langEngine->get('wproCore_spellchecker', 'replaceAll')?>" onclick="spellchecker.replaceAll();" />
</div>
<div class="leftCol"><fieldset class="frameFix">
<?php echo $langEngine->get('wproCore_spellchecker', 'notInDictionary')?><br />
<iframe src="<?php echo htmlspecialchars($EDITOR->editorLink('core/plugins/wproCore_spellchecker/blank.php?'.$wpsname.'='.$sid.($EDITOR->appendToQueryStrings ? '&'.$EDITOR->appendToQueryStrings : '').($EDITOR->appendSid ? strip_tags(defined('SID') ? '&'.SID : '') : '') )); ?>" name="spellcheckerResultsFrame_<?php echo htmlspecialchars($rand); ?>" id="spellcheckerResultsFrame_<?php echo htmlspecialchars($rand); ?>"></iframe>
</fieldset>
<div id="foundMessage">&nbsp;</div>
<?php echo $langEngine->get('wproCore_spellchecker', 'changeTo')?><br />
<input tabindex="1" type="text" name="changeTo" id="changeTo" value="" /><br />
<?php echo $langEngine->get('wproCore_spellchecker', 'suggestions')?><br />
<select tabindex="2" id="suggestions" name="suggestions" size="10" onclick="if(this.value.length>0){this.form.changeTo.value=this.value;}" ondblclick="if(this.value.length>0){this.form.changeTo.value=this.value;};spellchecker.replace();">

</select>
</div>
<div class="dictionaryChooser"><?php echo $langEngine->get('wproCore_spellchecker', 'dictionary')?> 
 <select id="dictionary" name="dictionary" onchange="spellchecker.init()">
<option value="<?php echo $dictionary ?>"><?php echo $dictionary ?></option>
</select>
</div>
<script type="text/javascript">
/*<![CDATA[ */
	dialog.hideLoadMessageOnLoad = false;
	RAND = <?php echo intval($rand) ?>;
	strComplete = '<?php echo addslashes($langEngine->get('wproCore_spellchecker', 'JSComplete'))?>';
	strNoneFound = '<?php echo addslashes($langEngine->get('wproCore_spellchecker', 'JSNoneFound'))?>';
	strNoSuggestions  = '<?php echo addslashes($langEngine->get('wproCore_spellchecker', 'noSuggestions'))?>';
	spellchecker.url = '<?php echo addslashes($spellcheckerURL) ?>';
	spellchecker.init();
/* ]]>*/
</script>