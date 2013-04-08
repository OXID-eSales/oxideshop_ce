<?php if (!defined('IN_WPRO')) exit; ?>
<script type="text/javascript">
/*<![CDATA[ */
function fbObj() {}
/* ]]>*/
</script>
<script type="text/javascript" src="core/plugins/wproCore_fileBrowser/js/links_src.js"></script>
<script type="text/javascript">
/*<![CDATA[ */
var FB = new fbObj();
function initBasicLink () {
	FB.initLink();
}
function formAction() {
	var form = document.dialogForm;
	FB.insertLink(form.URL.value, '');
	dialog.close();
	return false;
}

/* ]]>*/
</script>
<?php
$f = $this->createHTMLInput();
$f->attributes = array('accesskey'=>'u','type'=>'text','name'=>'URL','size'=>'100','style' => 'width:450px;',);
$t = $this->createUI2ColTable();
$t->width = 'small';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'url'), 'u'), $f->fetch(), 'URL');
$t->display();
?>
<script type="text/javascript">
/*<![CDATA[ */
initBasicLink();
/* ]]>*/
</script>
