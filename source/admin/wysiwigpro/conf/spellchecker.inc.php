<?php
if (!defined('IN_WPRO')) exit;
/*
* WysiwygPro spellchecker configuration file
*/

/* $SPELLCHECKER_API 
* The spellchecker API to use.
* This should be one of the following values:
* 
* 'http' - use remote spellchecker on WysiwygPro.com servers.
* 'pspell' - use the PHP Pspell extension. Your PHP installation must have the Pspell extension installed. See http://www.php.net/manual/en/ref.pspell.php
* 'aspell' - use Aspell. Aspell plus your required Aspell dictionaries must be installed on your server, you can download Aspell free from: http://aspell.sourceforge.net/
* 
* Obviously 'http' will be MUCH SLOWER than using an application installed on your server.
* If you have a choice pspell is probably best.
*
* NOTE:
* Unlawful access to the http service or access in breach of your license may result in your license being revoked.
*/

$SPELLCHECKER_API = 'http';

/* 
* If you are using 'http' and your server connects to the internet through a proxy then set the following:
*/	

$SPELLCHECKER_PROXY_URL = '';
$SPELLCHECKER_PROXY_PORT = '';
$SPELLCHECKER_PROXY_USER = '';
$SPELLCHECKER_PROXY_PASS = '';


/* $SPELLCHECKER_PROGRAM_PATH
* If you are using 'aspell' then set this to the full directory location of your Aspell executable:
*/	
									
$SPELLCHECKER_PROGRAM_PATH = 'aspell';


/* $SPELLCHECKER_DICTIONARIES
* If you are using 'pspell' then set all the dictionaries (languages) you have installed below. 
* This is required because Pspell does not provide an API for finding out what dictionaries are installed.
* The default lists all known available dictionaries for aspell
* These values should be set as an iso-639 language code followed by an optional hyphen and iso-3166 country code.
*/	

$SPELLCHECKER_DICTIONARIES = array('br','ca','cs','cy','da','de','de-ch','de-de','el','en','en-ca','en-gb','en-us','eo','es','fo','fr','fr-ch','fr-fr','it','nl','no','pl','pt','pt-br','pt-pt','ro','ru','sk','sv','uk');



?>