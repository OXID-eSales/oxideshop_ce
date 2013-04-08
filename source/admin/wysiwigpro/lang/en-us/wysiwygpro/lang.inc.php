<?php
/*
* WysiwygPro Language file
* Langauge: en-us - United States English
* Version: 3.2.0
*/

/*
* PLEASE NOTE: The $lang variable contained in the translation files is NOT loaded into the global scope. 
* The language files are loaded into a class and will not interfere with any $lang variable in your application.
*/

if (!defined('IN_WPRO')) exit;

/* 
* A reference to the editor $EDITOR is available for use in this script to provide API access if needed.
*/

/*
* We recommend UTF-8 encoding where possible.
* Since developers may display the editor on pages that have a different charset to the one normally required by your language please
* make sure that special characters are escaped where possible, except for variables that start with JS, these variables are used in JavaScript alerts 
*/

$lang = array();
$lang['conf'] = array();

/* language configuration 
*
* The code should be an iso-639 language code followed by an optional hyphen and iso-3166 country code.
*/
$lang['conf']['code'] = 'en-us'; 

/* preferred charset for displaying this language (make certain this file is encoded properly using this charset)
* Important: make sure this charset is supported by PHP!
*/
$lang['conf']['charset'] = 'UTF-8';

/* text direction (rtl may not work with all themes and may have unpredicatable results in some browsers) */
$lang['conf']['dir'] = 'ltr'; // ('ltr' for left to right, 'rtl' for right to left)

/* units */
$lang['conf']['thousandsSeparator'] = ',';
$lang['conf']['decimalSeparator'] = '.';

/* Format for date, the syntax used is identical to the PHP date function see: http://www.php.net/date */
$lang['conf']['dateFormat'] = 'm/d/Y, g:i a';


/* now the translations... */

/* core stuff used everywhere */

$lang['core'] = array();

/* buttons/confirmations */
$lang['core']['ok'] = 'OK';
$lang['core']['cancel'] = 'Cancel';
$lang['core']['yes'] = 'Yes';
$lang['core']['no'] = 'No';
$lang['core']['apply'] = 'Apply';
$lang['core']['insert'] = 'Insert';
$lang['core']['remove'] = 'Remove';
$lang['core']['close'] = 'Close';
$lang['core']['next'] = 'Next';
$lang['core']['previous'] = 'Previous';
$lang['core']['back'] = 'Back';
$lang['core']['forward'] = 'Forward';
$lang['core']['basic'] = 'Basic';
$lang['core']['options'] = 'Options';
$lang['core']['advanced'] = 'Advanced';
$lang['core']['appearance'] = 'Appearance:';

/* text */
$lang['core']['pleaseWait'] = 'Please Wait...';
$lang['core']['default'] = 'Default';
$lang['core']['recentlyUsed'] = 'Recently used:';
$lang['core']['style'] = 'Style:';
$lang['core']['styleOverrides'] = 'Some styles may override other options.';

/* dimensions/values */
$lang['core']['none'] = 'None';
$lang['core']['width'] = 'Width:';
$lang['core']['height'] = 'Height:';
$lang['core']['pixels'] = 'pixels';
$lang['core']['percent'] = '%';
$lang['core']['color'] = 'Color:';
$lang['core']['align'] = 'Align:';
$lang['core']['left'] = 'Left';
$lang['core']['center'] = 'Center';
$lang['core']['right'] = 'Right';
$lang['core']['top'] = 'Top';
$lang['core']['middle'] = 'Middle';
$lang['core']['bottom'] = 'Bottom';

/* form value checking */
$lang['core']['JSWrongSize'] = 'The field must be a numeric value between ##lower## and ##upper##.';
$lang['core']['JSWrongFormat'] = 'The field must be a numeric value.';

/* unsupported error message */
$lang['core']['unsupportedBrowser'] = '<small>To enable WYSIWYG editing you must be using a supported browser, however you may still edit the raw HTML code in the above textarea. For a full list of supported browsers please see <a href="http://www.wysiwygpro.com/browsers/" target="_blank">http://www.wysiwygpro.com/browsers/</a></small>';

/* message exit stuff */
$lang['core']['question'] = 'Question';
$lang['core']['warning'] = 'Warning';
$lang['core']['error'] = 'Error';
$lang['core']['information'] = 'Information';

/* other */
$lang['core']['thumbnailFolderDisplayName'] = 'Thumbnails';

/* editor interface */

$lang['editor'] = array();

/* styles menu */
$lang['editor']['clearFormatting'] = 'Clear formatting';
$lang['editor']['paragraphStyles'] = 'Paragraph Styles:';
$lang['editor']['textStyles'] = 'Text Styles:';
$lang['editor']['linkStyles'] = 'Hyperlink Styles:';
$lang['editor']['listStyles'] = 'List Styles:';
$lang['editor']['listItemStyles'] = 'List Item Styles:';
$lang['editor']['tableStyles'] = 'Table Styles:';
$lang['editor']['rowStyles'] = 'Table Row Styles:';
$lang['editor']['cellStyles'] = 'Table Cell Styles:';
$lang['editor']['rulerStyles'] = 'Ruler Styles:';
$lang['editor']['imageStyles'] = 'Image and Media Styles:';
$lang['editor']['listBoxStyles'] = 'List Box Styles:';
$lang['editor']['textBoxStyles'] = 'Text Box Styles:';
$lang['editor']['textFieldStyles'] = 'Text Field Styles:';
$lang['editor']['buttonStyles'] = 'Button Styles:';
$lang['editor']['optionButtonStyles'] = 'Option Button Styles:';
$lang['editor']['checkBoxStyles'] = 'Check Box Styles:';
$lang['editor']['imageButtonStyles'] = 'Image Button Styles:';
$lang['editor']['fileSelectStyles'] = 'File Select Styles:';
$lang['editor']['formInputStyles'] = 'Form Input Styles:';

/* built in editor buttons */
$lang['editor']['help'] = 'Help...';
$lang['editor']['save'] = 'Save';
$lang['editor']['post'] = 'Post';
$lang['editor']['send'] = 'Send';
$lang['editor']['fullwindow'] = 'Full-Window';
$lang['editor']['print'] = 'Print...';
$lang['editor']['cut'] = 'Cut';	
$lang['editor']['copy'] = 'Copy';
$lang['editor']['paste'] = 'Paste';
$lang['editor']['undo'] = 'Undo';
$lang['editor']['redo'] = 'Redo';
$lang['editor']['selectall'] = 'Select All';
$lang['editor']['zoom'] = 'Zoom';
$lang['editor']['styles'] = 'Styles';
$lang['editor']['font'] = 'Font';
$lang['editor']['size'] = 'Size';
$lang['editor']['bold'] = 'Bold';
$lang['editor']['italic'] = 'Italic';
$lang['editor']['underline'] = 'Underline';
$lang['editor']['moretextformatting'] = 'More Font Options';
$lang['editor']['subscript'] = 'Subscript';
$lang['editor']['superscript'] = 'Superscript';
$lang['editor']['strikethrough'] = 'Strikethrough';
$lang['editor']['left'] = 'Align Left';
$lang['editor']['right'] = 'Align Right';
$lang['editor']['center'] = 'Align Center';
$lang['editor']['full'] = 'Justify';
$lang['editor']['morealignmentformatting'] = 'More Paragraph Options';
$lang['editor']['numbering'] = 'Numbering';
$lang['editor']['bullets'] = 'Bullets';
$lang['editor']['morelistformatting'] = 'More List Options';
$lang['editor']['indent'] = 'Increase Indent';
$lang['editor']['outdent'] = 'Decrease Indent';
$lang['editor']['fontcolor'] = 'Font Color...';
$lang['editor']['highlight'] = 'Highlight...';
$lang['editor']['moreformatting'] = 'More Formatting Options';
$lang['editor']['syntaxHighlight'] = 'Syntax Highlight';
$lang['editor']['wordWrap'] = 'Toggle Word Wrap On/Off';

/* view tabs */
$lang['editor']['design'] = 'Design';
$lang['editor']['source'] = 'Source';
$lang['editor']['preview'] = 'Preview';

/* other main editor interface items */
$lang['editor']['shift+entermessage'] = 'Use Shift+Enter for a &lt;BR&gt; tag';
$lang['editor']['toggleGuidelines'] = 'Show/Hide Hidden Elements';

/* context menu specific */
$lang['editor']['selecttag'] = 'Select tag ##tagname##...';
$lang['editor']['tageditor'] = 'Edit tag ##tagname##...';
$lang['editor']['deletetag'] = 'Delete tag ##tagname##';
$lang['editor']['removetag'] = 'Remove tag ##tagname##';

/* misc */
$lang['editor']['previewMode'] = 'Preview Mode';

/* Basic paragraph styles */
$lang['editor']['normal'] = 'Normal';
$lang['editor']['heading_1'] = 'Heading 1';
$lang['editor']['heading_2'] = 'Heading 2';
$lang['editor']['heading_3'] = 'Heading 3';
$lang['editor']['heading_4'] = 'Heading 4';
$lang['editor']['heading_5'] = 'Heading 5';
$lang['editor']['heading_6'] = 'Heading 6';
$lang['editor']['pre_formatted'] = 'Pre Formatted';
$lang['editor']['address1'] = 'Address';

/* Please see the includes folder for plugins, dialogs and extensions... */

/* core dialog windows and plugins, these translations are here for performance reasons, your plugins should not store values here */
/* bookmark */
$lang['editor']['bookmark'] = 'Insert/Edit Bookmark...';
$lang['editor']['bookmarkproperties'] = 'Bookmark Properties...';

/* find */
$lang['editor']['find'] = 'Find And Replace...';

/* special characters */
$lang['editor']['specialchar'] = 'Special Characters...';

/* bullets and numbering dialog */
$lang['editor']['bulletsandnumbering'] = 'Bullets and Numbering...';

/* snippets */
$lang['editor']['snippets'] = 'Insert Snippet...';

/* emoticon dialog */
$lang['editor']['emoticon'] = 'Insert Emoticon...';

/* ruler */
$lang['editor']['ruler'] = 'Insert/Edit Horizontal Line...';
$lang['editor']['defaultruler'] = 'Insert Horizontal Line';
$lang['editor']['rulerproperties'] = 'Horizontal Line Properties...';

/* table dialogs */
$lang['editor']['tablemenu'] = 'Table';
$lang['editor']['instable'] = 'Insert Table...';
$lang['editor']['edittable'] = 'Table Properties...';
$lang['editor']['deltable'] = 'Delete Table';
$lang['editor']['addrow'] = 'Add Row...';
$lang['editor']['delrow'] = 'Delete Row';
$lang['editor']['inscol'] = 'Insert Column...';
$lang['editor']['delcol'] = 'Delete Column';
$lang['editor']['mergecells'] = 'Merge Cells...';
$lang['editor']['unmergecells'] = 'Unmerge Cells...';
$lang['editor']['insrowabove'] = 'Insert Row Above';
$lang['editor']['insrowbelow'] = 'Insert Row Below';
$lang['editor']['inscolleft'] = 'Insert Column to the Left';
$lang['editor']['inscolright'] = 'Insert Column to the Right';
$lang['editor']['insrowsandcols'] = 'Insert Rows and Columns...';
$lang['editor']['mergeright'] = 'Merge with Cell to the Right';
$lang['editor']['mergebelow'] = 'Merge with Cell Below';
$lang['editor']['unmergeright'] = 'Unmerge with Cell to the Right';
$lang['editor']['unmergebelow'] = 'Unmerge with Cell Below';
$lang['editor']['distcols'] = 'Distribute Column Widths Evenly';
$lang['editor']['autofitcols'] = 'Auto-Fit Column Widths to Contents';
$lang['editor']['fixedcols'] = 'Fixed Column Widths...';

/* spellchecker */
$lang['editor']['spelling'] = 'Spelling...';

/* filebrowser */
$lang['editor']['image'] = 'Insert/Edit Image...';
$lang['editor']['link'] = 'Insert/Edit Hyperlink...';
$lang['editor']['unlink'] = 'Remove Link';
$lang['editor']['document'] = 'Link To A Document...';
$lang['editor']['media'] = 'Insert/Edit Media Object...';
$lang['editor']['imageproperties'] = 'Image Properties...';
$lang['editor']['mediaproperties'] = 'Media Properties...';
$lang['editor']['linkproperties'] = 'Link Properties...';

/* codecleanup */
$lang['editor']['pastecleanup'] = 'Paste From External Source...';
$lang['editor']['codecleanup'] = 'Clean Up Source Code...';

/* directionality */
$lang['editor']['dirltr'] = 'Direction Left To Right';
$lang['editor']['dirrtl'] = 'Direction Right To Left';

/* insert html */
$lang['editor']['inserthtml'] = 'Insert HTML...';
?>