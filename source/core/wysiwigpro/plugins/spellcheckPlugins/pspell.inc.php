<?php
if (!defined('IN_WPRO')) exit;
class spellcheckerAPI extends spellcheckerBaseAPI {
	
	/* returns an array of available dictionaries */
	function getAvailableDictionaries() {
		if (empty($this->availDicts)) {
			global $SPELLCHECKER_DICTIONARIES;
			$this->availDicts = $SPELLCHECKER_DICTIONARIES;
		} 
		return $this->availDicts;		
	}
	
	function stripPunctuation( $text ) {
		$urlbrackets    = '\[\]\(\)';
		$urlspacebefore = ':;\'_\*%@&?!' . $urlbrackets;
		$urlspaceafter  = '\.,:;\'\-_\*@&\/\\\\\?!#' . $urlbrackets;
		$urlall         = '\.,:;\'\-_\*%@&\/\\\\\?!#' . $urlbrackets;
	 
		$specialquotes  = '\'"\*<>';
	 
		$fullstop       = '\x{002E}\x{FE52}\x{FF0E}';
		$comma          = '\x{002C}\x{FE50}\x{FF0C}';
		$arabsep        = '\x{066B}\x{066C}';
		$numseparators  = $fullstop . $comma . $arabsep;
	 
		$numbersign     = '\x{0023}\x{FE5F}\x{FF03}';
		$percent        = '\x{066A}\x{0025}\x{066A}\x{FE6A}\x{FF05}\x{2030}\x{2031}';
		$prime          = '\x{2032}\x{2033}\x{2034}\x{2057}';
		$nummodifiers   = $numbersign . $percent . $prime;
	 
		return preg_replace(
			array(
			// Remove separator, control, formatting, surrogate,
			// open/close quotes.
				'/[\p{Z}\p{Cc}\p{Cf}\p{Cs}\p{Pi}\p{Pf}]/u',
			// Remove other punctuation except special cases
				'/\p{Po}(?<![' . $specialquotes .
					$numseparators . $urlall . $nummodifiers . '])/u',
			// Remove non-URL open/close brackets, except URL brackets.
				'/[\p{Ps}\p{Pe}](?<![' . $urlbrackets . '])/u',
			// Remove special quotes, dashes, connectors, number
			// separators, and URL characters followed by a space
				'/[' . $specialquotes . $numseparators . $urlspaceafter .
					'\p{Pd}\p{Pc}]+((?= )|$)/u',
			// Remove special quotes, connectors, and URL characters
			// preceded by a space
				'/((?<= )|^)[' . $specialquotes . $urlspacebefore . '\p{Pc}]+/u',
			// Remove dashes preceded by a space, but not followed by a number
				'/((?<= )|^)\p{Pd}+(?![\p{N}\p{Sc}])/u',
			// Remove consecutive spaces
				'/ +/',
			),
			' ',
			$text );
	}
		
	/* returns an array of misspelt words and suggestions */
	function getSpellingResults ($data, $lang) {
	
		// set available dictionaries
		$this->getAvailableDictionaries();
		// set dictionary to use
		$lang = $this->getLang($lang);
		
		// strip out tags with contetn that shouldn't be checked
		$data = preg_replace('/<script[^>]*>.*?<\/script>/si', '', $data);
		$data = preg_replace('/<style[^>]*>.*?<\/style>/si', '', $data);
		$data = preg_replace('/<object[^>]*>.*?<\/object>/si', '', $data);
		$data = preg_replace('/<embed[^>]*>.*?<\/embed>/si', '', $data);
		$data = preg_replace('/<applet[^>]*>.*?<\/applet>/si', '', $data);
		$data = preg_replace('/<iframe[^>]*>.*?<\/iframe>/si', '', $data);
		$data = preg_replace('/<frame[^>]*>.*?<\/frame>/si', '', $data);
		$data = preg_replace('/<!--.*?-->/si', '', $data);
		
		// strip remaining tags
		$data = preg_replace('/<[^>]*>/si', '', $data);
		
		// strip out any HTML-like entities
		$data = preg_replace('/&#[0-9]+;/si', ' ', $data);
		$data = preg_replace('/&[a-z]+;/si', ' ', $data);
		
		// strip punctuation
		$data = $this->stripPunctuation($data);
		
		$words_elem = array();
		// get the list of misspelled words. 
		
		$wordlist = preg_split('/\s/',$data);
		
		// Filter words
		$words = array();
		for($i = 0; $i < count($wordlist); $i++) {
			$word = trim($wordlist[$i]);
			if(!in_array($word, $words, true) && !empty($word)) {
				$words[] = $word;
			}
		}
		//$misspelled = $return = array();
		$spelling = "";
		$jargon = "";
		// spelling
		if (preg_match("/^en[_\-]us/si", $lang)) {
			$spelling = 'american';
		}
		if (preg_match("/^en[_\-]gb/si", $lang)) {
			$spelling = 'british';
		}
		if (preg_match("/^en[_\-]ca/si", $lang)) {
			$spelling = 'canadian';
		}
		// jargon
		if (preg_match("/^[a-z][a-z][_\-][a-z][a-z][_\-].*?$/si", $lang)) {
			$jargon = preg_replace("/^[a-z][a-z][_\-][a-z][a-z][_\-](.*?)$/si", "$1", $lang);
			$lang = preg_replace("/^([a-z][a-z][_\-][a-z][a-z])[_\-].*?$/si", "$1", $lang);
		}
		
		//exit($lang.' | '.$spelling.' | '.$jargon);
		
		$int = pspell_new($lang, $spelling, $jargon, 'UTF-8');
		
		foreach ($words as $value) {
			if (!pspell_check($int, $value)) {
				$words_elem[$value] = implode(', ', @pspell_suggest($int, $value));
			}
		}

		return $words_elem;
	}


}
?>