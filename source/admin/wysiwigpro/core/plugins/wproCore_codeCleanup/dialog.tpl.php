<?php if (!defined('IN_WPRO')) exit;
require_once(WPRO_DIR.'conf/defaultValues/wproCore_codeCleanup.inc.php');
?>
<?php
if ($action=='paste') :
$tabs = $this->createUITabbed();
$tabs->startTab($this->underlineAccessKey($langEngine->get('editor', 'paste'), 'p'), array('accesskey'=>'p'));
?>

<p><?php echo $langEngine->get('wproCore_codeCleanup', 'instructions') ?></p>
<fieldset class="frameFix">
<iframe id="pasteFrame" name="pasteFrame" src="core/html/iframeSecurity.htm" class="previewFrame" frameborder="0" width="100%" height="220"></iframe></fieldset>

<?php
$tabs->endTab();
$tabs->startTab($this->underlineAccessKey($langEngine->get('core', 'options'), 'o'), array('accesskey'=>'o'));
endif
?><p>
<div class="inset scroll">

 <label><input type="checkbox" name="proprietary" id="proprietary"<?php echo $defaultValues['proprietary'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'proprietary') ?></label>
 <br />
  <label><input type="checkbox" name="quotes" id="quotes"<?php echo $defaultValues['fixCharacters'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'fixCharacters') ?></label>
 <br />
  <label><input type="checkbox" name="removeConditional" id="removeConditional"<?php echo $defaultValues['removeConditional'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeConditional') ?></label>
 <br />
 <label><input type="checkbox" name="removeComments" id="removeComments"<?php echo $defaultValues['removeComments'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeComments') ?></label>
 <br />
 <label><input type="checkbox" name="removeEmptyContainers" id="removeEmptyContainers"<?php echo $defaultValues['removeEmptyContainers'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeEmptyContainers') ?></label>
  <br />
 <label><input type="checkbox" name="removeLang" id="removeLang"<?php echo $defaultValues['removeLang'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeLang') ?></label>
 <br />
 <label><input type="checkbox" name="removeDel" id="removeDel"<?php echo $defaultValues['removeDel'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeDel') ?></label>
 <br />
 <label><input type="checkbox" name="removeIns" id="removeIns"<?php echo $defaultValues['removeIns'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeIns') ?></label>
 <br />
  <label><input type="checkbox" name="removeXML" id="removeXML"<?php echo $defaultValues['removeXML'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeXML') ?></label>
 <br />
 <label><input type="checkbox" name="removeScripts" id="removeScripts"<?php echo $defaultValues['removeScripts'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeScripts') ?></label>
 <br />
 <label><input type="checkbox" name="removeObjects" id="removeObjects"<?php echo $defaultValues['removeObjects'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeObjects') ?></label>
 <br />
 <label><input type="checkbox" name="removeImages" id="removeImages"<?php echo $defaultValues['removeImages'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeImages') ?></label>
 <br />
 <label><input type="checkbox" name="removeLinks" id="removeLinks"<?php echo $defaultValues['removeLinks'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeLinks') ?></label>
 <br />
 <label><input type="checkbox" name="removeAnchors" id="removeAnchors"<?php echo $defaultValues['removeAnchors'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeAnchors') ?></label>
 
<br />
<br />

 <label><input type="checkbox" name="removeEmptyP" id="removeEmptyP"<?php echo $defaultValues['removeEmptyP'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeEmptyP') ?></label>
 <br />
<label><input type="checkbox" name="convertP" id="convertP" onclick="if(this.checked) this.form.convertDiv.checked=false;"<?php echo $defaultValues['convertP'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'convertP') ?></label>
<br />
<label>
<input type="checkbox" name="convertDiv" id="convertDiv" onclick="if(this.checked) this.form.convertP.checked=false;"<?php echo $defaultValues['convertDiv'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'convertDiv') ?></label>
  
  <br />
<br />

<label><input type="checkbox" name="removeStyles" id="removeStyles"<?php echo $defaultValues['removeStyles'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeStyles') ?></label>
  <br />
  <label>
  <input type="checkbox" name="removeClasses" id="removeClasses"<?php echo $defaultValues['removeClasses'] ? ' checked="checked"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeClasses') ?></label>
  <br />
  <label>
  <input type="checkbox" name="removeFont" id="removeFont"<?php echo $defaultValues['removeFont'] ? ' checked="checked"' : '' ?> onclick="if (this.checked) {this.form.combineFont.disabled=true;this.form.removeAttributelessFont.disabled=true;}else{this.form.combineFont.disabled=false;this.form.removeAttributelessFont.disabled=false;}" /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeFont') ?></label>
  <br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="checkbox" name="combineFont" id="combineFont" checked="checked"<?php echo $defaultValues['combineFont'] ? ' checked="checked"' : '' ?><?php echo $defaultValues['removeFont'] ? ' disabled="disabled"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'combineFont') ?></label>
<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="checkbox" id="removeAttributelessFont" name="removeAttributelessFont" checked="checked"<?php echo $defaultValues['removeAttributelessFont'] ? ' checked="checked"' : '' ?><?php echo $defaultValues['removeFont'] ? ' disabled="disabled"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeAttributelessFont') ?></label>
<br />
<label><input type="checkbox" name="removeSpan" id="removeSpan"<?php echo $defaultValues['removeSpan'] ? ' checked="checked"' : '' ?> onclick="if (this.checked) {this.form.combineSpan.disabled=true;this.form.removeAttributelessSpan.disabled=true;}else{this.form.combineSpan.disabled=false;this.form.removeAttributelessSpan.disabled=false;}"/><?php echo $langEngine->get('wproCore_codeCleanup', 'removeSpan') ?></label>
<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="checkbox" name="combineSpan" id="combineSpan" checked="checked"<?php echo $defaultValues['combineSpan'] ? ' checked="checked"' : '' ?><?php echo $defaultValues['removeFont'] ? ' disabled="disabled"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'combineSpan') ?></label>
<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="checkbox" id="removeAttributelessSpan" name="removeAttributelessSpan" checked="checked"<?php echo $defaultValues['removeAttributelessSpan'] ? ' checked="checked"' : '' ?><?php echo $defaultValues['removeFont'] ? ' disabled="disabled"' : '' ?> /><?php echo $langEngine->get('wproCore_codeCleanup', 'removeAttributelessSpan') ?></label>
</div></p>
<?php 
if ($action=='paste'):
$tabs->endTab();
$tabs->display();
endif;
?>


<textarea style="display:none" name="html" id="html"><?php if ($mode=='upload') {echo htmlspecialchars($html);} ?></textarea>

<script type="text/javascript">
/*<![CDATA[ */
	dialog.events.addEvent(window, 'load', initCodeCleanup);
	var action = '<?php echo $action ?>';
	var mode = 'normal';
/* ]]>*/
</script>
