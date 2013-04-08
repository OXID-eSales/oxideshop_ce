<?php 
if (!defined('IN_WPRO')) exit;
// extract editor settings to local namespace
extract(get_object_vars($EDITOR)); 
$STR = '';
if ($saveButton) :
$STR .= '<input name="##name##_sourceSave" class="wproReady" type="image" src="'.htmlspecialchars($saveButtonURL).'" style="width:'.htmlspecialchars($saveButtonWidth).'px;height:'.htmlspecialchars($saveButtonHeight).'px" alt="'.$saveButtonLabel.'" title="'.$saveButtonLabel.'" align="middle" />';
endif;
$STR = '<textarea class="wproHTML" style="width:'.htmlspecialchars($width?$width:'100%').';height:'.htmlspecialchars($height).';" name="##_originalName##" id="##_originalName##" rows="10" cols="10">##value##</textarea>
<div>'.$langEngine->get('core', 'unsupportedBrowser').'</div>';

$contents = $STR;
?>