<?php
if (!defined('IN_WPRO')) exit;
class spellcheckerBaseAPI {
	
	var $programLocation = '';
	var $availDicts = array();
	var $defaultLang = 'en_US';
	var $currentDict = 'en_US';
	
	/* ensures that a language will always find a matching dictionary */
	function getLang ($lang) {
		
		$defaultLang = $this->defaultLang;
		$availDicts = $this->getAvailableDictionaries();
		
		$lang2 = str_replace(array('-'),array('_'),$lang);
		
		// do a basic syntax check
		if (!preg_match('/^[a-zA-z][a-zA-z_\-]+/si',$lang)) {
			$lang = $this->defaultLang;
		} else {
			// look for an exact match:
			$found = false;
			foreach($availDicts as $v) {
				if (strtolower($lang2)==strtolower($v) || strtolower($lang)==strtolower($v)) {
					$lang = $v;
					$found = true;
					break;
				}
			}
			// if an exact match was not found look for a similar match...
			if (!$found) {
				foreach($availDicts as $v) {
					if (preg_match('/^'.quotemeta($v).'/si', $lang) || preg_match('/^'.quotemeta($v).'/si', $lang2)) {
						$lang = $v;
						$found = true;
						break;
					}
				}
				// if still no match found use the default lang...
				if (!$found) {
					$lang = $defaultLang;
				}
			}
		}
		$this->currentDict = $lang;
		return $lang;
	}
	
	function htmlchars($str) {
		return str_replace(array('<','>','"'),array('&lt;','&gt;','&quot;'),$str);
	}

	/* 
	assembles the HTML with errors marked and hidden suggestions 
	*/
	function markupResultsHTML ($data, $resultsArray, $ignoreArray) {
		
		if (is_array($resultsArray)) {
			$keys = array_unique(array_keys($resultsArray));
		} else {
			$keys = array();
		}
		
		// Remove potentially dangerous attributes, this is done before spelling substitution so that the results are not affected.
		// break script and style attributes
		$results = array();
		if (preg_match_all('/<[a-z][^>]+>/si', $data, $results)) {
			$arr = array_unique($results[0]);
			sort($arr);
			$rl = count($arr);
			for ($i=0; $i < $rl; $i++) {
				$original = $arr[$i];
				$arr[$i] = preg_replace('/ (on[a-z]+|data|href|src|action|longdesc|profile|usemap|background|cite|classid|codebase)/i', " wproDefanged_$1", $arr[$i]);
				$data = str_replace($original, $arr[$i], $data);
			}
		}
		
		// Begin spelling results markup
		
		$ignore = 'script|style|iframe|frame|applet|object|embed';
		$in_ignore = 0;
		$parts = explode(">", $data);
		
		$replace_keys = array();
		$replace_values = array();
		
		foreach($parts as $key=>$part) {
			$pL = "";
			$pR = "";
		
			//echo htmlspecialchars($part).',<br>';
		
			if (($pos = strpos($part, "<")) === false) {
				$pL = $part;
				
			} else/*if ($pos > 0)*/ {
				$pL = substr($part, 0, $pos);
				$pR = substr($part, $pos, strlen($part));
			} //else {
				//echo htmlspecialchars($part).',<br>';
			//}
			
			//if ($pL != '') echo 'pL - '.htmlspecialchars($pL).',';
			if (preg_match('/<('.$ignore.')/si', $pR) && !preg_match('/\/$/', $pR)) {
				//echo htmlspecialchars($pR).',';
				$in_ignore ++;
			} else if (preg_match('/<\/('.$ignore.')/si', $pR)) {
				//echo htmlspecialchars($pR).',';
				$in_ignore --;
			} else {
				//echo htmlspecialchars($pR).',<br>';
			
			}
			
			//echo htmlspecialchars($pR).',<br>';
			
			if($pL != "") {
				if ($in_ignore<=0) {
					foreach ($keys as $k) {
						if (!in_array($k,$ignoreArray)) {
						//echo '/\b('.quotemeta($k).')\b/sm';
							
							$pL = preg_replace('/(^|\b)('.str_replace('/', '\/', quotemeta($k)).')($|\b)/sm',  '$1[[WPSPELLREPLACE_'.count($replace_values).']]$3', $pL);
							
							array_push($replace_keys, '[[WPSPELLREPLACE_'.count($replace_values).']]');
							array_push($replace_values, '<span class="wproSpellcheckerError">' . $k . '</span><input class="wproSpellcheckerInput" type="hidden" name="'.$this->htmlchars($k).'_'.$this->htmlchars($key).'" value="' . $this->htmlchars($resultsArray[$k]) . '" />');
							
						}
					}
				}
				$parts[$key] = $pL . $pR;
			}
			
			
		}
		//exit;
		$html = implode(">", $parts);
		
		// put back the replacements
		$html = str_replace($replace_keys, $replace_values, $html);
		
		// Remove potentially dangerous elements
		
		// break dangerous tags
		$illegal = array('meta','link','img','xml','script','iframe','frame','applet','object','embed', 'style');
		foreach ($illegal as $tag) {
			$results = array();
			if (preg_match_all('/<'.quotemeta($tag).'[^>]*>(.*?<\/'.quotemeta($tag).'>|)/si', $html, $results)) {
				$arr = array_unique($results[0]);
				sort($arr);
				$rl = count($arr);
				for ($i=0; $i < $rl; $i++) {
					$original = $arr[$i];					
					$arr[$i] = str_replace('-->', '--WPDEFANGED',$arr[$i]);
					$arr[$i] = '<!--[WPDEFANGED'.$arr[$i].'WPDEFANGED]-->';
					$html = str_replace($original, $arr[$i], $html);
				}
			}
			
		}
		// strip overlapping or enclosed instances
		foreach ($illegal as $tag) {
			$results = array();
			if (preg_match_all('/<'.quotemeta($tag).'[^>]*>.*?<\/'.quotemeta($tag).'>/si', $html, $results)) {
				$arr = array_unique($results[0]);
				sort($arr);
				$rl = count($arr);
				for ($i=0; $i < $rl; $i++) {
					$original = $arr[$i];
					$arr[$i] = str_replace('<!--[WPDEFANGED', "",$arr[$i]);
					$arr[$i] = str_replace('WPDEFANGED]-->', "",$arr[$i]);
					$html = str_replace($original, $arr[$i], $html);
				}				
			}
			
		}
		
		//exit('<pre>'.htmlspecialchars($html));
		
		return $html;
	}


}

?>