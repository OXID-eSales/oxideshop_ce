<?php if (!defined('IN_WPRO')) exit;?>
<?php if (isset($tagInfo[$tagName]['helpLink'])) : ?>
<div class="helpBar"><a href="<?php echo $tagInfo[$tagName]['helpLink'] ?>" target="_blank" title="<?php echo $EDITOR->varReplace($langEngine->get('wproCore_tagEditor','viewDefinition'), array('tagName'=>$tagName)) ?>"><?php echo $EDITOR->varReplace($langEngine->get('wproCore_tagEditor','tagInfo'), array('tagName'=>$tagName)) ?></a></div>
<?php endif ?>
<?php
$UI = $this->createUISelect();
$tagEditorAttributes = 'var tagEditorAttributes = [';
foreach ($attributes as $k => $v) {
	$table = $this->createUI2ColTable();
	foreach ($v as $k2 => $v2) {
		// ignore if this tage doesn't have this attribute
		$row = '';
		if (isset($v2['tags'])) {
			if (is_array($v2['tags'])) {
				if (!in_array($tagName, $v2['tags'])) {
					continue;
				}
			}
		}
		if (isset($v2['!tags'])) {
			if (is_array($v2['!tags'])) {
				if (in_array($tagName, $v2['!tags'])) {
					continue;
				}
			}
		}
		if (isset($v2['dtd'])) {
			if (!$EDITOR->featureIsEnabled('htmlDepreciated')) {
				continue;
			}
		}
		// add input to col 2
		if (isset($v2['values'])) {
			$invals = array(
				'type' => 'text',
				'name' => $v2['name'],
				'id' => $v2['name'],
				'title' => $v2['name'],
			);
			if ($tagName == 'INPUT' && $v2['name'] == 'type') {
				$invals['onchange'] = 'inputTypeChange(this.value);TYPE_CHANGED = true;';
				$k2 = '<strong>'.$k2.'</strong>';
			}
			$s = $this->createHTMLSelect();
			$s->attributes = $invals;
			$s->options = array_merge(array(''=>$langEngine->get('core','default')), $v2['values']);
			$row = $s->fetch();
		} elseif ($v2['type'] == 'longtext') {
			$row = '<textarea name="'.htmlspecialchars($v2['name']).'" id="'.htmlspecialchars($v2['name']).'" title="'.htmlspecialchars($v2['name']).'" cols="20" rows="3"></textarea>';
		} elseif ($v2['type'] == 'color') {
			$c = $this->createUIColorPicker();
			$c->name = $v2['name'];
			$c->showInput = true;
			$row = $c->fetch();
		} else if ($v2['type'] == 'file'||$v2['type'] == 'image'||$v2['type'] == 'document'||$v2['type'] == 'media'||$v2['type'] == 'link') {
			$chooser = $this->createUIURLChooser();
			$chooser->name = $v2['name'];
			$chooser->forceId = true;
			switch ($v2['type']) {
				case 'image':
					$chooser->type = 'image';
					break;
				case 'media':
					$chooser->type = 'media';
					break;
				case 'document':
					$chooser->type = 'document';
					break;
				case 'link':case'file':
					$chooser->type = 'link';
					break;
			}	
			$row = $chooser->fetch();	
		} else {
			//if (!isset($v2['type'])) echo $v2['name'];
			if ($v2['type'] == 'numeral') {
				$width = '5';
			} else {
				$width = '20';
			}
			if ($v2['type'] == 'boolean') {
				$type = 'checkbox';
			} else {
				$type = 'text';
			}
			$row = $this->HTMLInput(array(
				'type' => $type,
				'size' => $width,
				'name' => $v2['name'],
				'id' => $v2['name'],
				'title' => $v2['name'],
			));
		}
		if ($tagName == 'INPUT' && $v2['name'] == 'type') {
			$row .= '<br /><br />';
		}
		$table->addRow($k2, $row,  $v2['name']);
		$tagEditorAttributes .= "'".addslashes($v2['name'])."',";
	}
	if (count($table->rows)) {
		$UI->addOption($langEngine->get('wproCore_tagEditor', $k), $table->fetch(), $v2['name']);
	}
}
$tagEditorAttributes .= ']';
if (isset($tagInfo[$tagName]['canHaveChildren'])) {
	if ($tagInfo[$tagName]['canHaveChildren']) {
		$UI->addOption($langEngine->get('wproCore_tagEditor', 'content'), '<textarea name="innerHTML" id="innerHTML" wrap="off" title="innerHTML" onchange="doInnerHTML=true" style="width:95%" cols="20" rows="14"></textarea>');
	}
}
$UI->display();
?>
<script type="text/javascript">
/*<![CDATA[ */
var tagName = '<?php echo $tagName ?>';
<?php echo $tagEditorAttributes ?>;
initTagEditor();
/* ]]>*/
</script>