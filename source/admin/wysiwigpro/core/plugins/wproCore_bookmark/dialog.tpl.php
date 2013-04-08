<?php if (!defined('IN_WPRO')) exit;
$f = $this->createHTMLInput();
$f->attributes = array('accesskey'=>'b','type'=>'text','name'=>'bookmarkName','size'=>'22','onkeyup' => 'checkEnableButtons()',);
$t = $this->createUI2ColTable();
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_bookmark', 'bookmarkName'), 'b'), $f->fetch(), 'bookmarkName');
$t->display();
?>
<fieldset>
<legend><label for="bookmarkSelect"><?php echo $this->underlineAccessKey($langEngine->get('wproCore_bookmark', 'existingBookmarks'),'e')?></label></legend>
<div class="selectBookmark">
<select accesskey="e" name="bookmarkSelect" id="bookmarkSelect" onchange="selectBookmark(this.value)" size="5"></select>
<?php $i = $this->createHTMLInput();
$i->attributes = array('class'=>'button', 'disabled'=>'disabled', 'type'=>'button','name'=>'insertBookmark','onclick' => 'formAction()', 'value'=>$langEngine->get('core', 'insert'));
$i->display();
$i = $this->createHTMLInput();
$i->attributes = array('class'=>'button', 'disabled'=>'disabled', 'type'=>'button','name'=>'updateBookmark','onclick' => 'formAction()', 'value'=>$langEngine->get('core', 'apply'));
$i->display();
$i = $this->createHTMLInput();
$i->attributes = array('class'=>'button', 'disabled'=>'disabled', 'type'=>'button','name'=>'removeBookmark','onclick' => 'clearBookmark()', 'value'=>$langEngine->get('core', 'remove'));
$i->display();
?>
</div></fieldset>
<script type="text/javascript">
/*<![CDATA[ */
	initBookmark();
/* ]]>*/
</script>