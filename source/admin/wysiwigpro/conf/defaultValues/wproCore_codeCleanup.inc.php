<?php
if (!defined('IN_WPRO')) exit;
if (!isset($action)) exit;
$defaultValues = array();
$defaultValues['proprietary'] = true;
$defaultValues['removeConditional'] = true;
$defaultValues['removeComments'] = true;
$defaultValues['removeEmptyContainers'] = true;
$defaultValues['removeLang'] = true;
$defaultValues['removeDel'] = true;
$defaultValues['removeIns'] = true;
$defaultValues['removeXML'] = true;
$defaultValues['removeScripts'] = true;
$defaultValues['removeObjects'] = ($action=='paste') ? true : false;
$defaultValues['removeImages'] = false;
$defaultValues['removeLinks'] = false;
$defaultValues['removeAnchors'] = ($action=='paste') ? true : false;
$defaultValues['removeEmptyP'] = ($action=='paste') ? true : false;
$defaultValues['convertP'] = false;
$defaultValues['convertDiv'] = false;
$defaultValues['fixCharacters'] = true;
$defaultValues['removeStyles'] = ($action=='paste') ? true : false;
$defaultValues['removeClasses'] = ($action=='paste') ? true : false;
$defaultValues['removeFont'] = ($action=='paste') ? true : false;
$defaultValues['combineFont'] = ($action=='paste') ? true : false;
$defaultValues['removeAttributelessFont'] = ($action=='paste') ? true : false;
$defaultValues['removeSpan'] = ($action=='paste') ? true : false;
$defaultValues['combineSpan'] = ($action=='paste') ? true : false;
$defaultValues['removeAttributelessSpan'] = ($action=='paste') ? true : false;

?>