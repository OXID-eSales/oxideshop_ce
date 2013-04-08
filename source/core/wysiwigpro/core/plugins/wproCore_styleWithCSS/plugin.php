<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_wproCore_styleWithCSS {
	function onAfterFixHTML(&$EDITOR) {
		$search = strtolower($EDITOR->htmlVersion);
		if (strstr($search, 'strict')||strstr($search, 'xhtml 2')||strstr($search, 'xhtml 1.1')) {
			
			$EDITOR->addJSPlugin('wproCore_styleWithCSS', 'plugin_src.js');
			$EDITOR->htmlVersion .= ' STRICT';
			$EDITOR->disableFeatures(array('htmlDepreciated'));
			
			//if (strstr($search, 'xhtml 1.1')) {
				if (strstr($search, 'target')) {
					$EDITOR->enableFeature('target');
				}
			//}
			
			if (strstr($search, 'noevents')) {
				$EDITOR->disableFeature('events'); // not currently in use.
			}
			
			$defaultSizeMenu = array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7');
			// change the default editor size menu...
			$i=1;
			foreach ($EDITOR->sizeMenu as $k => $v) {
				if ($k==$v && $v == $defaultSizeMenu[$i]) {
				} else {
					break;
				}
				$i++;
			}
			if ($i == count($defaultSizeMenu)+1) {
				$EDITOR->sizeMenu = array('8px'=>'8','9px'=>'9','10px'=>'10','11px'=>'11','12px'=>'12','14px'=>'14','16px'=>'16','18px'=>'18','20px'=>'20','22px'=>'22','24px'=>'24','26px'=>'26','28px'=>'28','36px'=>'36','48px'=>'48','72px'=>'72');
			} else {
			
				// fix sizes without any value
				$trans = array(
				'1' => '10px',
				'2' => '13px',
				'3' => '16px',
				'4' => '18px',
				'5' => '24px',
				'6' => '32px',
				'7' => '48px',
				);
				
				$menu = array();
				foreach ($EDITOR->sizeMenu as $k => $v) {
					if (isset($trans[$k])) {
						$menu[$trans[$k]] = $v;
					} else {
						$menu[$k] = $v;
					}
				}
				$EDITOR->sizeMenu = $menu;
				
			}
			
		}		
	}
}
?>