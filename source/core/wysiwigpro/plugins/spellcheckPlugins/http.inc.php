<?php
/* retrieves spelling results from the WysiwygPro.com server */
if (!defined('IN_WPRO')) exit;
if (!wpro_class_exists('DOMIT_Lite_Document')) require_once(WPRO_PATH_DOMIT.'xml_domit_lite_include.php');
class spellcheckerAPI extends spellcheckerBaseAPI {
	
	var $mode = 'sendForChecking';
	//var $resultsXML = '';
	var $XMLCollection = NULL;
		
	/* returns an array of available dictionaries */
	function getAvailableDictionaries() {
		if (empty($this->availDicts)) {
			if ($this->mode=='showResults') {
				// find the available dicts from the results XML
				$textNode = & $this->XMLCollection->documentElement->childNodes[1]->firstChild;
				if ($textNode!=NULL) {
					$val = & $textNode->toString();
					$this->availDicts = explode(',', $val);
				}

			}			
		} 
		return $this->availDicts;
	}
	
	function htmlentityDecode ($str) {
		return str_replace(array('&lt;','&gt;','&quot;','&amp;'), array('<','>','"','&'), $str);
	}
		
	/* returns an array of misspelt words and suggestions */
	function getSpellingResults ($data, $lang) {
		// connect and get results
		global $SPELLCHECKER_PROXY_URL, $SPELLCHECKER_PROXY_PORT, $SPELLCHECKER_PROXY_USER, $SPELLCHECKER_PROXY_PASS;
		//$resultsXML=stripslashes($_POST['wproSpellcheckerResultsXML']);
		require_once(WPRO_DIR.'core/libs/wproWebAgent.class.php');
		$fs = new wproWebAgent();
		$fs->proxyURL = $SPELLCHECKER_PROXY_URL;
		$fs->proxyPort = $SPELLCHECKER_PROXY_PORT;
		$fs->proxyUser = $SPELLCHECKER_PROXY_USER;
		$fs->proxyPass = $SPELLCHECKER_PROXY_PASS;
		$fs->requestMethod = 'POST';
		
		// create XML
		$fs->postData = array('XMLRequest' => '<xml version="1.0"><wproSpellcheckerQuery version="1.0"><dictionary>'.htmlspecialchars($lang).'</dictionary><bodyHTML>'.htmlspecialchars($data).'</bodyHTML></wproSpellcheckerQuery>');
				
		if ($resultsXML = $fs->fetch(WPRO_CENTRAL_SPELLCHECKER_URL)) {
							
			$this->XMLCollection = new DOMIT_Lite_Document();
			$this->XMLCollection->resolveErrors(true);
			
			if (!@$this->XMLCollection->parseXML($resultsXML, false)) {
				exit ( "<strong>WysiwygPro SpellChecker error</strong>: XML Parser Error: ".$this->XMLCollection->getErrorString() );
			}
			
			$textNode = & $this->XMLCollection->documentElement->childNodes[0]->firstChild;
			if ($textNode!=NULL) {
				exit ( "<p><strong>WysiwygPro Central SpellChecker error</strong>: ".htmlspecialchars($textNode->toString()) ."</p>");
			}
			
		} else {
			exit ( "<strong>WysiwygPro SpellChecker error</strong>: Connection Error: Failed to connect to remote server" );
		}
		
		// get dictionaries
		$this->mode='showResults';
		$availDicts = $this->getAvailableDictionaries();
		$lang = $this->getLang($lang);
		
		$words_elem = array();
		if ($this->mode=='showResults') {
			// process the results from the results XML
			$nodes = & $this->XMLCollection->documentElement->getElementsByTagName('missSpelt');
			if ($nodes != null) {
				 $n = $nodes->getLength();
				 for ( $i=0; $i<$n; $i++) {
				 	$node = & $nodes->item($i);
				 	$words_elem[$this->htmlentityDecode($node->getAttribute('word'))] = $this->htmlentityDecode ($node->getAttribute('suggestions'));
				 }
 			}
		}
		return $words_elem;
	}
}
?>