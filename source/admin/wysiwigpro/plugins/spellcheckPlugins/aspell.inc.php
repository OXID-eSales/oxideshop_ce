<?php
if (!defined('IN_WPRO')) exit;
class spellcheckerAPI extends spellcheckerBaseAPI {
	
	/* returns an array of available dictionaries */
	function getAvailableDictionaries() {
		if (empty($this->availDicts)) {
			$dictsArr = array('en');
			$cmd = escapeshellcmd($this->programLocation)." dump dicts";
			if( $returnVal = shell_exec( $cmd )) {
				$dictsArr = explode("\n", $returnVal);
				array_pop($dictsArr);
				if (empty($dictsArr)) {
					exit ( "<strong>WysiwygPro SpellChecker error</strong>: There are no dictionaries installed." );
				}
			} else {
				exit ( "<strong>WysiwygPro SpellChecker error</strong>: Aspell program execution failed {$cmd}" );
			}
			$this->availDicts = $dictsArr;
		} 
		return $this->availDicts;		
	}
		
	/* returns an array of misspelt words and suggestions */
	function getSpellingResults ($data, $lang, $tempfiledir) {
		
		// set available dictionaries
		$this->getAvailableDictionaries();
		// set dictionary to use
		$lang = $this->getLang($lang);
		
		
		
		$aspellLocation = $this->programLocation;
		
		$aspellOptions = '-a --dont-backup --lang='.escapeshellarg($lang).' --mode=sgml';
		
		// strip out tags that shouldn't be checked
		$data = preg_replace('/<script[^>]*>.*?<\/script>/si', '', $data);
		$data = preg_replace('/<style[^>]*>.*?<\/style>/si', '', $data);
		$data = preg_replace('/<object[^>]*>.*?<\/object>/si', '', $data);
		$data = preg_replace('/<embed[^>]*>.*?<\/embed>/si', '', $data);
		$data = preg_replace('/<applet[^>]*>.*?<\/applet>/si', '', $data);
		$data = preg_replace('/<iframe[^>]*>.*?<\/iframe>/si', '', $data);
		$data = preg_replace('/<frame[^>]*>.*?<\/frame>/si', '', $data);
		$data = preg_replace('/<!--.*?-->/si', '', $data);
		
		$data = preg_replace('/<[^>]*>/si', ' ', $data);
		
		//exit ( htmlspecialchars($data) );
		
		// strip out any HTML-like entities
		//$data = preg_replace('/&#[0-9]+;/si', ' ', $data);
		//$data = preg_replace('/&[a-z]+;/si', ' ', $data);
		
		$words_elem = array();
		// get the list of misspelled words. 
		
		// create temp file
		/* find a suitable temp file location */
		if (!is_writable($tempfiledir)) {
			if (isset($_ENV["TMP"])) {
				if (is_writable($_ENV["TMP"])) {
					$tempfiledir = $_ENV["TMP"].'/';
				} else {
					exit('<strong>WysiwygPro config error</strong>: Please make WPRO_TEMP_DIR writable.');
				}
			}
		}
		$tempFile = str_replace(array('\\','\\\\','//'), '/', tempnam( $tempfiledir, 'aspell_data_' ));
		
		// open temp file, add the submitted text.
		if( $fh = fopen( $tempFile, 'w' )) {
		
			// parse out newlines for command passing
			$lines = explode(" \n", $data);
		
			foreach( $lines as $value ) {
				// use carat on each line to escape possible aspell commands
				fwrite( $fh, '^'.$value.'\n' );
			}
			fclose( $fh );
		
			// exec aspell command
			$cmd = escapeshellcmd($this->programLocation).' '.$aspellOptions.' < '.escapeshellarg($tempFile);
			if( $returnVal = shell_exec( $cmd )) {
				
				$linesout = explode( "\n", $returnVal );
				$index = 0;
				// parse each line of aspell return
				foreach( $linesout as $val ) {
					$chardesc = substr( $val, 0, 1 );
					// if '&', then not in dictionary but has suggestions
					// if '#', then not in dictionary and no suggestions
					if( $chardesc == '&' || $chardesc == '#' ) {
						$line = explode( " ", $val, 5 );
						$words_elem["$line[1]"] = isset($line[4]) ? $line[4] : '';
					}
				}
			} else {
				unlink( $tempFile );
				exit ( "<strong>WysiwygPro SpellChecker error</strong>: Aspell program execution failed." );
			}
		
		} else {
			unlink( $tempFile );
			exit ( "<strong>WysiwygPro SpellChecker error</strong>: Could not open temp file" );
		}
		
		// close temp file, delete file
		unlink( $tempFile );

		return $words_elem;
	}


}
?>