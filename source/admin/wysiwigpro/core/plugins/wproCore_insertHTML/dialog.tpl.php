<?php if (!defined('IN_WPRO')) exit; ?>

<p><label for="strToInsert"><?php echo $langEngine->get('wproCore_insertHTML', 'instructions') ?></label></p>
<textarea class="exampleTextarea" id="strToInsert" name="strToInsert"></textarea>

<script type="text/javascript">
/*<![CDATA[ */
function formAction () {

	var html = document.dialogForm.strToInsert.value;
	
	dialog.editor.insertAtSelection(html);
	
	dialog.close();
	return false;
}
/* ]]>*/
</script>