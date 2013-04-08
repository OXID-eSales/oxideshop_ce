<?php
////////////////////////////////////////////
// User functions, can be used outside WP 
// This file is provided for compatability with applications written for WysiwygPro 2.x
// You should now use wproUtilities.class.php instead!
////////////////////////////////////////////
require_once(dirname(__FILE__).'/wproUtilities.class.php');
function longwordbreak($str, $cols=40, $cut=' ') {
   return wproUtilities::longWordBreak($str, $cols, $cut);
}
function remove_tags($code, $tags) {
	return wproUtilities::removeTags($code, $tags);
}

// function comm2php
// Converts comments back to PHP
function comm2php($code) {
	if (!empty($code)) {
		$code = preg_replace("/<\!--p(.*?)-->/si",  "<?php\$1 ?>", $code);
		$code = preg_replace("/<\?xml version=\"1.0\" encoding=\"(.*?)\"\?>/si",  '<?php echo "<?xml version=\"1.0\" encoding=\"$1\"?".">"; ?>', $code);
	}
	return $code;
}

// function comm2asp
// Converts comments back to ASP
function comm2asp($code) {
	if (!empty($code)) {
		$code = preg_replace("/<\!--asp(.*?)-->/si",  "<%\$1 %>", $code);
	}
	return $code;
}

// function fixcharacters
// XHTML requires all special characters to be encoded, this nice little hack makes sure of that.
function fixcharacters($string, $charset='iso-8859-1') {
	$arr = explode('</script>',$string);
	$num = count($arr);
	for ($i=0; $i<$num; $i++) { 
		$arr2 = explode('<script', $arr[$i]);//>
		
		$foo = fixcharacters2($arr2[0], $charset);
		
		if (isset($arr2[1])) {
			$arr[$i] = $foo.'<script'.$arr2[1];//>
		} else {
			$arr[$i] = $foo;
		}				
	}
	$string = implode('</script>',$arr);
	return $string;
}
function fixcharacters2($string, $charset='iso-8859-1') {
	$fixed = htmlentities( $string, ENT_NOQUOTES, $charset );
	static $trans_array = array();
	if (empty($trans_array)) {
		// html entities doesn't fix ascii characters above 127 so we'll do this ourselves using a string translation
		for ($i=127; $i<256; $i++) {
			$trans_array[chr($i)] = "&#" . $i . ";";
		}
		// add html entities to the translation table so they will be converted back
		$trans_array['&lt;'] = '<';
		$trans_array['&gt;'] = '>';
		$trans_array['&quot;'] = '"';
		$trans_array['&amp;nbsp;'] = '&nbsp;';
		$trans_array['&amp;quot;'] = '&quot;';
		$trans_array['&amp;lt;'] = '&lt;';
		$trans_array['&amp;gt;'] = '&gt;';
		$trans_array['&amp;amp;'] = '&amp;';
	}
	// do translation and return
	$str = strtr($fixed, $trans_array);
	// fix for extended characters
	$str = preg_replace("/&amp;#([0-9]+);/", "&#$1;", $str);
	return $str;
}
function email_encode($code) {
	wproUtilities::emailEncode($code);
}
?>