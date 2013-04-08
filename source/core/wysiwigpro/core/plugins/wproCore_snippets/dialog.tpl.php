<?php if (!defined('IN_WPRO')) exit; ?>
<?php
function buildSnippetArray(&$UI, &$pNode, $snippets) {
	$i=0;
	foreach($snippets as $k => $v) {
		if (!empty($v)) {
			$node = & $UI->createNode();
			$id = '';
			if (isset($pNode->id)) {
				$id .= $pNode->id.'_';
			} else {
				$id = 'snippetTree_';
			}
			$id.=$i;
			$node->id = $id;
			$node->caption = $k;
			if (is_array($v)) {
				buildSnippetArray($UI, $node, $v);
			} else {
				$node->caption_onclick = 'function (node) {getSnippetContent(\''.addslashes($id).'\');}';
			}
			$pNode->appendChild($node);
			$i++;
		}
	}
}
$UI = $this->createUITree();
$UI->width = 165;
$UI->height = 327;
buildSnippetArray($UI, $UI, $snippets);
?>
<div class="UISelectLeftCol inset">
<?php $UI->display(); ?>
</div>
<div class="UISelectRightCol">
<div class="UISelectPane" style="display:block">
<iframe id="snippetFrame" name="snippetFrame" src="core/html/iframeSecurity.htm" class="previewFrame" frameborder="0"></iframe>
</div>
</div>
<div id="snippetCache"></div>
<script type="text/javascript">
/*<![CDATA[ */
	var pleaseSelectStr = '<?php echo addslashes($langEngine->get('wproCore_snippets', 'pleaseSelect')) ?>';
	initSnippets();
/* ]]>*/
</script>