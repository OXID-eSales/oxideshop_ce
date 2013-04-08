<?php 
if (!defined('IN_WPRO')) exit;
$lang=array();
$lang['wproCore_codeCleanup'] = array();
$lang['wproCore_codeCleanup']['instructions'] = 'This will clean up HTML source code before pasting it into your document.<br />
Paste or drag content from Office documents, web pages and other programs into the box below (To paste use the keyboard shortcut, Ctrl + v on Windows or Command + v on a Mac). Please be patient, pasting from Office documents can take a considerable amount of time. When the paste is complete press Insert.';
$lang['wproCore_codeCleanup']['proprietary'] = 'Remove recognized proprietary markup';
$lang['wproCore_codeCleanup']['removeConditional'] = 'Remove &lt;!if ...]&gt; &lt;![endif]&gt; conditional tags and their contents';
$lang['wproCore_codeCleanup']['removeComments'] = 'Remove hidden comment tags';
$lang['wproCore_codeCleanup']['removeEmptyContainers'] = 'Remove empty container tags (&lt;b&gt;&lt;/b&gt;, &lt;h1&gt;&lt;/h1&gt;, ...)';
$lang['wproCore_codeCleanup']['removeLang'] = 'Remove lang attributes';
$lang['wproCore_codeCleanup']['removeDel'] = 'Remove &lt;del&gt; tags and their contents';
$lang['wproCore_codeCleanup']['removeIns'] = 'Remove &lt;ins&gt; tags but keep their contents';
$lang['wproCore_codeCleanup']['removeXML'] = 'Remove XML tags and attributes';
$lang['wproCore_codeCleanup']['removeScripts'] = 'Remove scripts and event handlers';
$lang['wproCore_codeCleanup']['removeObjects'] = 'Remove Video, Audio and Java applications';
$lang['wproCore_codeCleanup']['removeImages'] = 'Remove images';
$lang['wproCore_codeCleanup']['removeLinks'] = 'Remove links';
$lang['wproCore_codeCleanup']['removeAnchors'] = 'Remove bookmarks (named anchors)';
$lang['wproCore_codeCleanup']['removeEmptyP'] = 'Remove margin styles and empty paragraph tags';
$lang['wproCore_codeCleanup']['convertP'] = 'Convert &lt;p&gt; tags to &lt;div&gt; tags';
$lang['wproCore_codeCleanup']['convertDiv'] = 'Convert &lt;div&gt; tags to &lt;p&gt; tags';
$lang['wproCore_codeCleanup']['fixCharacters'] = 'Fix Windows specific characters';
$lang['wproCore_codeCleanup']['removeStyles'] = 'Remove inline CSS styles';
$lang['wproCore_codeCleanup']['removeClasses'] = 'Remove CSS classes';
$lang['wproCore_codeCleanup']['removeFont'] = 'Remove &lt;font&gt; tags';
$lang['wproCore_codeCleanup']['combineFont'] = 'Combine &lt;font&gt; tags where possible';
$lang['wproCore_codeCleanup']['removeAttributelessFont'] = 'Remove &lt;font&gt; tags without attributes';
$lang['wproCore_codeCleanup']['removeSpan'] = 'Remove &lt;span&gt; tags';
$lang['wproCore_codeCleanup']['combineSpan'] = 'Combine &lt;span&gt; tags where possible';
$lang['wproCore_codeCleanup']['removeAttributelessSpan'] = 'Remove &lt;span&gt; tags without attributes';
$lang['wproCore_codeCleanup']['fileWarning'] = 'Links to files stored on your computer were found, these files must be uploaded to the web-server before they will be visible to other users.';
$lang['wproCore_codeCleanup']['fileFound'] = 'File found on your computer:';
$lang['wproCore_codeCleanup']['fileInstructions'] = 'Replace with:';
$lang['wproCore_codeCleanup']['skip'] = 'Skip';
?>