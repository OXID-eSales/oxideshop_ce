<?php
if (!defined('IN_WPRO')) exit;
$defaultValues = array();
if (!isset($EDITOR)) exit;

// New tables
$defaultValues['rows'] = '3';
$defaultValues['cols'] = '3';
$defaultValues['width'] = '100';
$defaultValues['widthUnits'] = '%';
$defaultValues['headers'] = 'none';
$defaultValues['columnWidths'] = 'percent';
$defaultValues['fixedColumnWidths'] = '100';
$defaultValues['fixedColumnWidthsUnits'] = '';
$defaultValues['style'] = '';
$defaultValues['border'] = '1';
if (!$EDITOR->featureIsEnabled('htmlDepreciated')) {
	$defaultValues['borderColor'] = '';
} else {
	$defaultValues['borderColor'] = '#000000';
}
$defaultValues['borderCollapse'] = 'collapse';
$defaultValues['backgroundColor'] = '';
$defaultValues['cellSpacing'] = '0';
$defaultValues['cellPadding'] = '3';
$defaultValues['caption'] = '';
$defaultValues['captionAlign'] = '';
$defaultValues['summary'] = '';

// edit table 
// -- not configurable --

// merge
$defaultValues['mergeHorizontal'] = '1';
$defaultValues['mergeVertical'] = '0';

// Un-merge
// -- not configurable --

// insert row
$defaultValues['insrowAmount'] = '1';
$defaultValues['insrowLocation'] = 'above';

// insert column
$defaultValues['inscolAmount'] = '1';
$defaultValues['inscolLocation'] = 'right';


?>