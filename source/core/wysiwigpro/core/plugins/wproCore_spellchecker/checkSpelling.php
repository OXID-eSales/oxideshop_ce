<?php
ob_start();
if (!defined('IN_WPRO')) define('IN_WPRO', true);

/* include files */
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/core/libs/common.inc.php');
require_once(WPRO_DIR.'conf/spellchecker.inc.php');
require_once(WPRO_DIR.'core/plugins/wproCore_spellchecker/config.inc.php');
require_once(dirname(__FILE__).'/spellcheckerAPI.inc.php');
$EDITOR->langEngine->loadFile('wysiwygpro/includes/wproCore_spellchecker.inc.php');

header('Content-Type: text/html; charset='.(empty($EDITOR->htmlCharset) ? 'UTF-8' : preg_replace("/[^a-z0-9\-]/i",'',$EDITOR->htmlCharset)));

$defaultLang = 'en_US';

/* gat vars from post */
$html = isset($_POST['bodyHTML']) ? $_POST['bodyHTML'] : '' ;
$dictionary = isset($_POST['dictionary']) ? $_POST['dictionary'] : $defaultLang;

/* setup spellchecker object */
$spellchecker = new spellcheckerAPI();
$spellchecker->programLocation = $SPELLCHECKER_PROGRAM_PATH;
$spellchecker->defaultLang = $defaultLang;

/* get spelling results */
$resultsArray = $spellchecker->getSpellingResults($html, $dictionary, WPRO_TEMP_DIR);

/* get result data */
$availDicts = $spellchecker->availDicts;
$lang = $spellchecker->currentDict;

/* get the learnt words from the cookie */
$ignoreStr = isset($_COOKIE['wproLearntWords']) ? $_COOKIE['wproLearntWords'] : '';
$ignoreArray = explode(',', $ignoreStr);

/* markup the spelling errors in the HTML */
$markup = $spellchecker->markupResultsHTML($html, $resultsArray, $ignoreArray);

if(!empty($availDicts)) {
	require(WPRO_DIR.'conf/langCodes.inc.php');
	$dictsString = '';
	foreach($availDicts as $i) {
		$langCode = '';
		$countryCode = '';
		$langTrans = '';
		$country_trans = '';
		
		$langCode = strtoupper(preg_replace("/^([a-z][a-z]).*?$/si", "$1", $i));
		if (preg_match("/^[a-z][a-z][\-_]([a-z][a-z]).*?$/si", $i)) {
			$countryCode = strtoupper(preg_replace("/^[a-z][a-z][\-_]([a-z][a-z]).*?$/si", "$1", $i));
		}
		
		$langTrans = isset($ISO_639_LANGUAGES[$langCode]) ? $ISO_639_LANGUAGES[$langCode] : $langCode;
		$countryTrans = isset($ISO_3166_COUNTRIES[$countryCode]) ? $ISO_3166_COUNTRIES[$countryCode] : $countryCode;
		
		$dictsString .= "'".addslashes(strip_tags($i))."':'".addslashes(strip_tags($langTrans.(empty($countryTrans)?"":" (".$countryTrans.")")))."',";
	}
	$dictsString = substr($dictsString, 0, strlen($dictsString)-1);
} else {
	$dictsString = '';
}

/* output */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo empty($EDITOR->htmlLang) ? 'en' : htmlspecialchars($EDITOR->htmlLang) ?>" dir="<?php echo empty($EDITOR->htmlDirection) ? 'ltr' : htmlspecialchars($EDITOR->htmlDirection) ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo empty($EDITOR->htmlCharset) ? 'UTF-8' : htmlspecialchars($EDITOR->htmlCharset) ?>" />
<style type="text/css">
body {
	background-color: #ffffff;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	color:#000000;
}
font {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	color:#000000;
}
span.wproSpellcheckerError { 
	border-bottom: 2px dashed #ff0000; 
	cursor: default; 
}
span.wproSpellcheckerFixed { 
	border-width: 0px;
}
span.wproSpellcheckerCurrent { 
	color: #ff0000; 
	font-weight:bold
}
input.wproSpellcheckerInput {
	padding:0px;
	margin: 0px;
	width: 0px;
	border-width: 0px;
	position: static;
	clear: none;
}
img, object, embed, applet {
	display: none;
}
</style>
<script type="text/javascript">
/*<![CDATA[ */
var dictionaries = {<?php echo $dictsString ?>};
var current_dict = '<?php echo addslashes(strip_tags($lang)) ?>';
/* ]]>*/
</script>
</head>
<body onload="window.parent.finishedSpellChecking();" onclick="return false;" onmousedown="return false;" onmouseup="return false;">
<?php echo $markup; ?>
</body>
</html>