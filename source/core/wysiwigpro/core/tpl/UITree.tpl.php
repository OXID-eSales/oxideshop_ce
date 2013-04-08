<?php if (!defined('IN_WPRO')) exit; 
if (!function_exists('buildNodeArray')) {
	function buildNodeArray($nodes) {
		/*
		var caption = '';
		var URL = NULL;
		var target = NULL;
		var folders = array();
		var buttons = array();
		var isFolder = false;
		var expanded = false;
		var caption_onclick = '';
		var caption_onmouseover = '';
		var caption_onmouseout = '';
		var image_onclick = '';
		var image_onmouseover = '';
		var image_onmouseout = '';
		var button_onclick = '';
		var button_onmouseover = '';
		var button_onmouseout = '';
		var $childNodes = array();
		*/
		$arr = array();
		
		foreach ($nodes as $node) {
		
			$a = array();
			array_push($a, (empty($node->id) ? '' : '{id:"'.addslashes($node->id).'"}'));
			array_push($a, (empty($node->caption) ? "''" : '"'.addslashes($node->caption).'"'));
			array_push($a, (empty($node->URL) ? 'null' : '"'.addslashes($node->URL).'"'));
			array_push($a, (empty($node->target) ? 'null' : '"'.addslashes($node->target).'"'));
			
			if (!empty($node->folders) || 
			!empty($node->buttons) ||
			!empty($node->isFolder) ||
			!empty($node->expanded) ||
			!empty($node->caption_onclick) ||
			!empty($node->caption_onmouseover) ||
			!empty($node->caption_onmouseout) ||
			!empty($node->image_onclick) ||
			!empty($node->image_onmouseover) ||
			!empty($node->image_onmouseout) ||
			!empty($node->button_onclick) ||
			!empty($node->button_onmouseover) ||
			!empty($node->button_onmouseout)) {
				$fa = array();
				if (!empty($node->folders)) {
					array_push($fa, 'folders:["'.addslashes($node->folders[0]).'","'.addslashes($node->folders[1]).'","'.addslashes($node->folders[2]).'"]');
				} 
				if (!empty($node->buttons)) {
					array_push($fa, 'buttons:["'.addslashes($node->buttons[0]).'","'.addslashes($node->buttons[1]).'","'.addslashes($node->buttons[2]).'"]');
				} 
				if ($node->isFolder) {
					array_push($fa, 'isFolder:true');
				}
				if ($node->expanded) {
					array_push($fa, 'expanded:true');
				}
				if (!empty($node->caption_onclick)) {
					array_push($fa, 'caption_onclick:'.$node->caption_onclick);
				}
				if (!empty($node->caption_onmouseover)) {
					array_push($fa, 'caption_onmouseover:'.$node->caption_onmouseover);
				}
				if (!empty($node->caption_onmouseout)) {
					array_push($fa, 'caption_onmouseout:'.$node->caption_onmouseout);
				}
				if (!empty($node->image_onclick)) {
					array_push($fa, 'image_onclick:'.$node->image_onclick);
				}
				if (!empty($node->image_onmouseover)) {
					array_push($fa, 'image_onmouseover:'.$node->image_onmouseover);
				}
				if (!empty($node->image_onmouseout)) {
					array_push($fa, 'image_onmouseout:'.$node->image_onmouseout);
				}
				if (!empty($node->button_onclick)) {
					array_push($fa, 'button_onclick:'.$node->button_onclick);
				}
				if (!empty($node->button_onmouseover)) {
					array_push($fa, 'button_onmouseover:'.$node->button_onmouseover);
				}
				if (!empty($node->button_onmouseout)) {
					array_push($fa, 'button_onmouseout:'.$node->button_onmouseout);
				}
				array_push($a, '{format:{'.implode(',', $fa).'}}');
			}
			if (!empty($node->childNodes)) {
				 array_push($a, buildNodeArray($node->childNodes));
			}
			array_push($arr, '['.implode(',', $a).']');
		}
		return implode(',', $arr);
	}
}
?><div id="<?php echo $UID; ?>_treeHolder"><script type="text/javascript">
/*<![CDATA[ */
<?php echo $UID; ?>_FORMAT = TREE_FORMAT;
/* button images: collapsed state, expanded state, blank image */
<?php echo $UID; ?>_FORMAT[3] = ["<?php echo addslashes($themeURL).'misc/tree.collapsed.gif'; ?>", "<?php echo addslashes($themeURL).'misc/tree.expanded.gif'; ?>", "core/images/spacer.gif"];
/* icon images: closed folder, opened folder, document */
<?php echo $UID; ?>_FORMAT[6] = ["<?php echo addslashes($themeURL).'icons/folder.gif'; ?>", "<?php echo addslashes($themeURL).'icons/folderopen.gif'; ?>", "<?php echo addslashes($themeURL).'icons/unknown.gif'; ?>"];
<?php if ($width!=0||$height!=0) : ?>
/* 19. initial space for the relatively positioned tree: width, height */
<?php echo $UID; ?>_FORMAT[19] = [<?php echo $width ? $width : '200' ?>, <?php echo $height ? $height : '50' ?>],
/* 20. resize container of the relatively positioned tree */
<?php echo $UID; ?>_FORMAT[20] = false;
<?php endif; ?>
<?php echo $UID; ?>_NODES = [<?php echo buildNodeArray($nodes); ?>];
var <?php echo $UID; ?> = new COOLjsTreePRO("<?php echo $UID; ?>", <?php echo $UID; ?>_NODES, <?php echo $UID; ?>_FORMAT);
<?php echo $UID; ?>.init();
<?php if ($width!=0||$height!=0) : ?>
document.getElementById('<?php echo $UID; ?>_treeHolder').getElementsByTagName('DIV').item(0).style.overflow = 'auto';
<?php endif; ?>
/* ]]>*/
</script></div>