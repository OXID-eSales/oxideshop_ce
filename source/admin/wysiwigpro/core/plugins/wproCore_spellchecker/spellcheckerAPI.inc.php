<?php
if (!defined('IN_WPRO')) exit;
require_once(dirname(__FILE__).'/baseAPI.inc.php');
switch (strtolower($SPELLCHECKER_API)) {
	case 'aspell':
		$includeFile = WPRO_DIR.'/plugins/spellcheckPlugins/aspell.inc.php';
		break;
	case 'pspell':
		$includeFile = WPRO_DIR.'/plugins/spellcheckPlugins/pspell.inc.php';
		break;
	case 'http':
	default:
		$includeFile = WPRO_DIR.'/plugins/spellcheckPlugins/http.inc.php';
		break;
}
require_once($includeFile);
?>