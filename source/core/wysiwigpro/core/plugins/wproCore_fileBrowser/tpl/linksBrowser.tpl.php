<?php if (!defined('IN_WPRO')) exit; ?>
<?php
//print_r($EDITOR->links);
function buildLinksMenu(&$UI, &$pNode, $links) {
	//print_r($links);
	for ($i=0; $i<count($links); $i++) {
		if (empty($links[$i]['title'])) continue;
		$node = & $UI->createNode();
		$id = '';
		if (isset($pNode->id)) {
			$id .= $pNode->id.'_';
		} else {
			$id = 'linksTree_';
		}
		$id.=$i;
		$node->id = $id;
		$node->caption = $links[$i]['title'];
		if (!empty($links[$i]['URL'])) {
			if ($links[$i]['URL']=='folder') {
				$node->isFolder = true;
			} else {
				$node->caption_onclick = 'function (node) {parent.localLink(\''.addslashes($links[$i]['URL']).'\', \''.addslashes($links[$i]['title']).'\');}';
			}
		}
		if (!empty($links[$i]['children'])) buildLinksMenu($UI, $node, $links[$i]['children']);
		
		$pNode->appendChild($node);
	}
}
$UI = $this->createUITree();
//$UI->width = 280;
//$UI->height = 327;

buildLinksMenu($UI, $UI, $EDITOR->links);
$UI->display(); ?>