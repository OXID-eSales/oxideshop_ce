<?php

/* 
* WysiwygPro 3.2.1, 30 November 2009.
* (c) Copyright 2007 and forever thereafter Chris Bolt and ViziMetrics Inc.
*/

/*
Functions you can use to post-process code 
These functions can be used outside of WP
*/

class wproUtilities {	
	
	// function longwordbreak
	// This is an optional function that you can call before saving HTNL data sent from WYSIWYG PRO
	// this breaks up words that are too long and might damage the page layout such as excessive use of tabs
	// it does not cut through html tags
	// call it before saving your code like this: $myCode = wproUtilities::longWordBreak($myCode);
	// $str = required, your html code
	// $cols = optional, words over this length will be cut (the default is 40, how many real words can you think of over this length?)
	// $cut = optional, how would you like your excessively long words cut sir? (the default is a space, other options would be a hyphen or carriage return)
	function longWordBreak($str, $cols=40, $cut=' ') {
		$len = strlen($str);
		$tag = 0;
		$ent = 0; // do not cut in the middle of an entity
		$result = '';
		$wordlen = 0;
		for ($i = 0; $i < $len; $i++) {
			$chr = $str[$i];
			
			if ($chr == '&' && preg_match("/^&[a-z0-9#]{2,10};/", substr($str, $i, 10))) { // look ahead to check for a valid entity
				$ent = 1;
				if (!$tag) {
					$wordlen++;
				}
			} else if ($chr == ';') {
				$ent = 0;
			} else if ($chr == '<') {
				$tag++;
			} elseif ($chr == '>') {
				$tag--;
			} elseif (!$tag && preg_match("#\s#",$chr)) {
				$wordlen = 0;
			} elseif (!$tag && !$ent) {
				$wordlen++;
			}
			if (!$tag && !$ent && $wordlen && !($wordlen % $cols)) {
				$chr .= $cut;
			}
			$result .= $chr;
		}
		return $result;
	}
	
	// function remove_tags
	// This is an optional function that you can call before saving HTNL data sent from WYSIWYG PRO
	// allows you to remove unwanted tags from the code
	// $code = the html code to be processed
	// $tags = an associative array of tags to remove where the key is the tag name and the value is a boolean,
	// this should be true to remove the tag AND its contents or false to remove the tag but keep its contents.
	function removeTags($code, $tags) {
		if (!empty($code)) {
			if (!is_array($tags)) {
				die('<p><b>WYSIWYGPRO Paramater Error:</b> Your list of tags is not an array!</p>');
			} else {
				foreach($tags as $k => $v) {
					if (!empty($k)) {
						if ($v) {
							// remove tags and all code contained within the tags
							$code = preg_replace("/<".quotemeta($k)."(>|\/>| [^>]*?>).*?<\/".quotemeta($k).">/si",  "", $code);
							$code = preg_replace("/<".quotemeta($k)."(>|\/>| [^>]*?>)/si",  "", $code);
						} else {
							// remove tags but leave code within the tags
							$code = preg_replace("/<".quotemeta($k)."(>|\/>| [^>]*?>)(.*?)<\/".quotemeta($k).">/si",  "\$2", $code);
							$code = preg_replace("/<".quotemeta($k)."(>|\/>| [^>]*?>)/si",  "", $code);
						}
					}
				}
			}
		}
		return $code;
	}
	
	// function removeAttributes
	// This is an optional function that you can call before saving HTNL data sent from WYSIWYG PRO
	// allows you to remove unwanted attributes from tags
	// $code = the html code to be processed
	// $attributes = an array of attributes to remove
	// You can use pattern matching in your attribute names, e.g. this will remove all event handlers:
	//$myCode =  wproUtilities::removeAttributes($myCode, array("on[A-Z]+"));
	function removeAttributes($code, $attrs) {
		if (!empty($code)) {
			if (!is_array($attrs)) {
				die('<p><b>WYSIWYGPRO Paramater Error:</b> Your list of attributes is not an array!</p>');
			} else {
				$num = count($attrs);
				for ($i=0; $i<$num; $i++) {
					if (!empty($attrs[$i])) {
						// remove attributes
						// dbl quotes
						$code = preg_replace("/<([^>]*?) ".$attrs[$i]."=\"[^\"]*\"([^>]*?)>/si",  "<\$1\$2>", $code); 
						// single quotes
						$code = preg_replace("/<([^>]*?) ".$attrs[$i]."='[^']*'([^>]*?)>/si",  "<\$1\$2>", $code); 
						// no quotes
						$code = preg_replace("/<([^>]*?) ".$attrs[$i]."=[^ ]* ([^>]*?)>/si",  "<\$1\$2>", $code); 
						// boolean with no values
						$code = preg_replace("/<([^>]*?) ".$attrs[$i]." ([^>]*?)>/si",  "<\$1\$2>", $code); 
					}
				}
			}
		}
		return $code;
	}
		
	// function email_encode
	// Requires email_encode2
	// encode email addresses to prevent spam bots from finding them 
	function emailEncode($code, $only_links=true) {
		
		if ($only_links) {
			// match only email links
			$matches = array();
			preg_match_all("/<a .*?href=\"mailto:[a-zA-Z0-9!#$%*\/?|^{}`~&'+\-=_.]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,4}.*?\".*?>.*?<\/a>/", $code, $matches);
			for ($i=0;$i<count($matches[0]);$i++) {
				$original = $matches[0][$i];
				$matches[0][$i] = preg_replace("/((mailto:|)[a-zA-Z0-9!#$%*\/?|^{}`~&'+\-=_.]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,4})/e", "wproUtilities::_emailEncode2('$1')",$matches[0][$i]);
				$code = str_replace($original, $matches[0][$i], $code);
			}
		} else {
			// match all email addresses in all tags, attributes and text
			$code = preg_replace("/((mailto:|)[a-zA-Z0-9!#$%*\/?|^{}`~&'+\-=_.]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,4})/e", "wproUtilities::_emailEncode2('$1')",$code);
		}
		
		return $code;
	}
	function _emailEncode2 ($email_address) {
		static $trans_array = array();
		if (empty($trans_array)) {
			for ($i=1; $i<255; $i++) {
				$trans_array[chr($i)] = "&#" . $i . ";";
			}
		}
		return strtr($email_address, $trans_array);    
	}
	
	// Closes any tags left open.
	function closeTags ($html) {
	
		// put all opened tags into an array
		preg_match_all ( "#<([a-z0-9:]+)( .*)?(?!/)>#iU", $html, $result );
		$openedtags = $result[1];
				
		// put all closed tags into an array
		preg_match_all ( "#</([a-z0-9:]+)>#iU", $html, $result );
		$closedtags = $result[1];
		$len_opened = count ( $openedtags );
						
		// all tags are closed
		if( count ( $closedtags ) == $len_opened ) {
			return $html;
		}
		
		// tags that are allowed open
		$allowed = array('area','bgsound','base','basefont','br','comment','col','frame','hr','input','img','isindex','link','meta','param','spacer','wbr');
		
		// remove tags that have been closed from both arrays
		for( $i = 0; $i < $len_opened; $i++ ) {
			if (in_array($openedtags[$i],$closedtags)) {
				unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
				unset($openedtags[$i]);
			}
		}
		
		$openedtags = array_reverse ( $openedtags );
		
		// close tags
		for( $i = 0; $i < $len_opened; $i++ ) {
			if (!isset($openedtags[$i])) continue;
			if ( !in_array ( $openedtags[$i], $closedtags ) && !in_array($openedtags[$i], $allowed) ) {
				$html .= "</" . $openedtags[$i] . ">";
			} else {
				unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
			}
		}
		
		// return
		return $html;
	}
	
	// cuts an HTML string to the desired length and closes any tags left open at the cut point.
	// cut length does not include tags.
	// completly rewritten based upon long word break for 3.0.4, no-longer breaks in the middle of a word or entity reference
	// the tradeoff is that the function is a bit slower
	function cutHTML( $html, $len, $cut='' ) {
		$fullLen = strlen($html);
		$tag = 0;
		$ent = 0; // do not cut in the middle of an entity
		
		$c = 0;	// full character count
		$c2 = 0; // non html character count
		
		$lastWhite = 0; // last occurance of white space
		
		for ($i = 0; $i < $fullLen; $i++) {
			$chr = $html[$i];
			if ($chr == '&' && preg_match("/^&[a-z0-9#]{2,10};/", substr($html, $i, 10))) { // look ahead to check for a valid entity
				$ent = 1;
				if (!$tag) {
					$c2++;
				}
			} else if ($chr == ';') {
				$ent = 0;
			} else if ($chr == '<') {
				$tag++;
			} elseif ($chr == '>') {
				$tag--;
			} elseif (!$tag && preg_match("#\s#",$chr)) { 
				$lastWhite = $c; // record position of last white space
				$c2++;
			} elseif (!$tag && !$ent) {
				$c2++;
			}
			$c ++;
			if ($c2 >= $len && !$tag && !$ent) {
				if ($lastWhite > 0) {
					// return to last white space position
					$c = $lastWhite;
				}
				break;
			}
		}

		// cut the string
		if (strlen($html) > $c) {
		
			$snippet = substr ( $html, 0, $c ) . $cut;
			
			$snippet = wproUtilities::closeTags ( $snippet );
			
		} else {
			$snippet = $html;
		}
		
		return $snippet;
	}

}
?>