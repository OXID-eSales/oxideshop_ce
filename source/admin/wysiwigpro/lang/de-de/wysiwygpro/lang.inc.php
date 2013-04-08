<?php
/*
* WysiwygPro Language file
* Language: de-de - Deutsch
* Version: 3.0.1
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
$lang['conf']['code'] = 'de-de'; 

/* preferred charset for displaying this language (make certain this file is encoded properly using this charset)
* Important: make sure this charset is supported by PHP!
*/
$lang['conf']['charset'] = 'UTF-8';

/* text direction (rtl may not work with all themes and may have unpredicatable results in some browsers) */
$lang['conf']['dir'] = 'ltr'; // ('ltr' for left to right, 'rtl' for right to left)

/* units */
$lang['conf']['thousandsSeparator'] = '.';
$lang['conf']['decimalSeparator'] = ',';

/* Format for date, the syntax used is identical to the PHP date function see: http://www.php.net/date */
$lang['conf']['dateFormat'] = 'm/d/Y, g:i a';


/* now the translations... */

/* core stuff used everywhere */

$lang['core'] = array();

/* buttons/confirmations */
$lang['core']['ok'] = 'OK';
$lang['core']['cancel'] = 'Abbrechen';
$lang['core']['yes'] = 'Ja';
$lang['core']['no'] = 'Nein';
$lang['core']['apply'] = 'Anwenden';
$lang['core']['insert'] = 'Einfuegen';
$lang['core']['remove'] = 'Entfernen';
$lang['core']['close'] = 'Schliessen';
$lang['core']['next'] = 'Weiter';
$lang['core']['previous'] = 'Vorher';
$lang['core']['back'] = 'Zurück';
$lang['core']['forward'] = 'Vorwärts';
$lang['core']['basic'] = 'Grundeinstellungen';
$lang['core']['options'] = 'Optionen';
$lang['core']['advanced'] = 'Erweitert';
$lang['core']['appearance'] = 'Aussehen:';

/* text */
$lang['core']['pleaseWait'] = 'Bitte warten...';
$lang['core']['default'] = 'Standard';
$lang['core']['recentlyUsed'] = 'Zuletzt verwendet:';
$lang['core']['style'] = 'Format:';
$lang['core']['styleOverrides'] = 'Manche Formate &uuml;berschreiben andere Optionen.';

/* dimensions/values */
$lang['core']['none'] = 'Ohne';
$lang['core']['width'] = 'Breite:';
$lang['core']['height'] = 'H&ouml;he:';
$lang['core']['pixels'] = 'Pixel';
$lang['core']['percent'] = '%';
$lang['core']['color'] = 'Farbe:';
$lang['core']['align'] = 'Richtung:';
$lang['core']['left'] = 'Links';
$lang['core']['center'] = 'Zentriert';
$lang['core']['right'] = 'Rechts';
$lang['core']['top'] = 'Oben';
$lang['core']['middle'] = 'Mitte';
$lang['core']['bottom'] = 'Unten';

/* form value checking */
$lang['core']['JSWrongSize'] = 'Das Feld muss einen numerischen Wert zwischen ##lower## und  ##upper## enthalten.';
$lang['core']['JSWrongFormat'] = 'Das Feld muss einen numerischen Wert enthalten.';

/* unsupported error message */
$lang['core']['unsupportedBrowser'] = '<small>Sie müssen einen Browser verwenden der den WYSIWYG Editot unterst&uuml;tzt. Eine Liste der unterst&uuml;tzen Browser finden Sie unter: <a href="http://www.wysiwygpro.com/browsers/" target="_blank">http://www.wysiwygpro.com/browsers/</a></small>';

/* message exit stuff */
$lang['core']['question'] = 'Frage';
$lang['core']['warning'] = 'Warnung';
$lang['core']['error'] = 'Fehler';
$lang['core']['information'] = 'Information';

/* other */
$lang['core']['thumbnailFolderDisplayName'] = 'Thumbnails';

/* editor interface */

$lang['editor'] = array();

/* styles menu */
$lang['editor']['clearFormatting'] = 'Format l&ouml;schen';
$lang['editor']['paragraphStyles'] = 'Format:';
$lang['editor']['textStyles'] = 'Text Format:';
$lang['editor']['linkStyles'] = 'Hyperlink Format:';
$lang['editor']['listStyles'] = 'Format Liste:';
$lang['editor']['listItemStyles'] = 'Format Listeintr&auml;ge:';
$lang['editor']['tableStyles'] = 'Tabellen Format:';
$lang['editor']['rowStyles'] = 'Tabellenzeilen Format:';
$lang['editor']['cellStyles'] = 'Tabellenzellen Format:';
$lang['editor']['rulerStyles'] = 'Linien Format:';
$lang['editor']['imageStyles'] = 'Bild und Media Format:';
$lang['editor']['listBoxStyles'] = 'Listboxen Format:';
$lang['editor']['textBoxStyles'] = 'Textboxen Format:';
$lang['editor']['textFieldStyles'] = 'Texfeld Format:';
$lang['editor']['buttonStyles'] = 'Button Format:';
$lang['editor']['optionButtonStyles'] = 'Option Button Format:';
$lang['editor']['checkBoxStyles'] = 'Check Box Format:';
$lang['editor']['imageButtonStyles'] = 'Image Button Format:';
$lang['editor']['fileSelectStyles'] = 'Dateiauswahl Format:';
$lang['editor']['formInputStyles'] = 'Form Input Format:';

/* built in editor buttons */
$lang['editor']['help'] = 'Hilfe...';
$lang['editor']['save'] = 'Speichern';
$lang['editor']['post'] = 'Post';
$lang['editor']['send'] = 'Senden';
$lang['editor']['fullwindow'] = 'Auf vollen Bildschirm';
$lang['editor']['print'] = 'Drucken...';
$lang['editor']['cut'] = 'Ausschneiden';	
$lang['editor']['copy'] = 'Kopieren';
$lang['editor']['paste'] = 'Einf&uuml;gen';
$lang['editor']['undo'] = 'R&uuml;ckg&auml;ngig';
$lang['editor']['redo'] = 'Wiederholen';
$lang['editor']['zoom'] = 'Zoom';
$lang['editor']['styles'] = 'Format';
$lang['editor']['font'] = 'Schrift';
$lang['editor']['size'] = 'Gr&ouml;sse';
$lang['editor']['bold'] = 'Fett';
$lang['editor']['italic'] = 'Kursiv';
$lang['editor']['underline'] = 'Unterstrichen';
$lang['editor']['moretextformatting'] = 'Mehr Schrift Optionen';
$lang['editor']['subscript'] = 'Tiefgestellt';
$lang['editor']['superscript'] = 'Hochgestellt';
$lang['editor']['strikethrough'] = 'Durchgestrichen';
$lang['editor']['left'] = 'Links ausgerichtet';
$lang['editor']['right'] = 'Rechts ausgerichtet';
$lang['editor']['center'] = 'Zentriert';
$lang['editor']['full'] = 'Blocksatz';
$lang['editor']['morealignmentformatting'] = 'Mehr Optionen';
$lang['editor']['numbering'] = 'Nummerierung';
$lang['editor']['bullets'] = 'Aufz&auml;hlung';
$lang['editor']['morelistformatting'] = 'Mehr Optionen';
$lang['editor']['indent'] = 'Einzug vergr&ouml;&szlig;ern';
$lang['editor']['outdent'] = 'Einzug verkleinern';
$lang['editor']['fontcolor'] = 'Schriftfarbe...';
$lang['editor']['highlight'] = 'Highlight...';
$lang['editor']['moreformatting'] = 'Mehr Formtierungs Optionen';
$lang['editor']['syntaxHighlight'] = 'Syntax Highlight';
$lang['editor']['wordWrap'] = 'Umschalten Word Wrap On/Off';

/* view tabs */
$lang['editor']['design'] = 'Design';
$lang['editor']['source'] = 'Quelle';
$lang['editor']['preview'] = 'Vorschau';

/* other main editor interface items */
$lang['editor']['shift+entermessage'] = 'Shift+Enter f&uuml;r &lt;BR&gt; tag';
$lang['editor']['toggleGuidelines'] = 'Gitternetzlinien an/aus';

/* context menu specific */
$lang['editor']['selecttag'] = 'W&auml;hle tag ##tagname##...';
$lang['editor']['tageditor'] = 'Bearbeite tag ##tagname##...';
$lang['editor']['deletetag'] = 'L&ouml;sche tag ##tagname##';
$lang['editor']['removetag'] = 'Entferne tag ##tagname##';

/* misc */
$lang['editor']['previewMode'] = 'Vorschau';

/* Basic paragraph styles */
$lang['editor']['normal'] = 'Normal';
$lang['editor']['heading_1'] = '&Uuml;berschrift 1';
$lang['editor']['heading_2'] = '&Uuml;berschrift 2';
$lang['editor']['heading_3'] = '&Uuml;berschrift 3';
$lang['editor']['heading_4'] = '&Uuml;berschrift 4';
$lang['editor']['heading_5'] = '&Uuml;berschrift 5';
$lang['editor']['heading_6'] = '&Uuml;berschrift 6';
$lang['editor']['pre_formatted'] = 'Pre Formatiert';
$lang['editor']['address1'] = 'Addresse';

/* Please see the includes folder for plugins, dialogs and extensions... */

/* core dialog windows and plugins, these translations are here for performance reasons, your plugins should not store values here */
/* bookmark */
$lang['editor']['bookmark'] = 'Bookmark einf&uuml;gen/bearbeiten';
$lang['editor']['bookmarkproperties'] = 'Bookmark Eigenschaften...';

/* find */
$lang['editor']['find'] = 'Suchen und ersetzen...';

/* special characters */
$lang['editor']['specialchar'] = 'Sonderzeichen...';

/* bullets and numbering dialog */
$lang['editor']['bulletsandnumbering'] = 'Nummerierung und Aufz&auml;hlungszeichen...';

/* snippets */
$lang['editor']['snippets'] = 'Insert Snippet...';

/* emoticon dialog */
$lang['editor']['emoticon'] = 'Emoticon einf&uuml;gen...';

/* ruler */
$lang['editor']['ruler'] = 'Horizontale Linie einf&uuml;gen/bearbeiten ...';
$lang['editor']['defaultruler'] = 'Horizontale Linie einf&uuml;gen ...';
$lang['editor']['rulerproperties'] = 'Horizontale Linie Eigenschaften...';

/* table dialogs */
$lang['editor']['tablemenu'] = 'Tabelle';
$lang['editor']['instable'] = 'Tabelle Einf&uuml;gen...';
$lang['editor']['edittable'] = 'Tabelle Eigenschaften...';
$lang['editor']['deltable'] = 'L&ouml;sche Tabelle';
$lang['editor']['addrow'] = 'Zeile hinzuf&uuml;gen...';
$lang['editor']['delrow'] = 'L&ouml;sche Zeile';
$lang['editor']['inscol'] = 'Spalte hinzuf&uuml;gen...';
$lang['editor']['delcol'] = 'Spalte l&ouml;schen';
$lang['editor']['mergecells'] = 'Zellen zusammenf&uuml;hren...';
$lang['editor']['unmergecells'] = 'Zellen trennen...';
$lang['editor']['insrowabove'] = 'Zeile davor einf&uuml;gen';
$lang['editor']['insrowbelow'] = 'Zeile danach einf&uuml;gen';
$lang['editor']['inscolleft'] = 'Spalte links einf&uuml;gen';
$lang['editor']['inscolright'] = 'Spalte rechts einf&uuml;gen';
$lang['editor']['insrowsandcols'] = 'Zeilen und Spalten einf&uuml;gen...';
$lang['editor']['mergeright'] = 'F&uuml;ge mit der rechten Zelle zusammen';
$lang['editor']['mergebelow'] = 'F&uuml;ge mit der Zelle danach zusammen';
$lang['editor']['unmergeright'] = 'Trenne von der rechten Zelle';
$lang['editor']['unmergebelow'] = 'Trenne von der Zelle danach';
$lang['editor']['distcols'] = 'Spaltenbreite gleichmä&szlig;ig verteilen';
$lang['editor']['autofitcols'] = 'Auto-Fit Spalten';
$lang['editor']['fixedcols'] = 'Fixiere Spaltenbreite...';

/* spellchecker */
$lang['editor']['spelling'] = 'Rechtschreibpr&uuml;fung...';

/* filebrowser */
$lang['editor']['image'] = 'Bild einf&uuml;gen/bearbeiten...';
$lang['editor']['link'] = 'Hyperlink einf&uuml;gen/bearbeiten...';
$lang['editor']['document'] = 'Link auf...';
$lang['editor']['media'] = 'Medien Objekt einf&uuml;gen/bearbeiten...';
$lang['editor']['imageproperties'] = 'Bild Eigenschaften...';
$lang['editor']['mediaproperties'] = 'Media Eigenschaften...';
$lang['editor']['linkproperties'] = 'Link Eigenschaften...';

/* codecleanup */
$lang['editor']['pastecleanup'] = 'Einf&uuml;gen aus externer Quelle...';
$lang['editor']['codecleanup'] = 'R&auml;ume Source Code auf...';

/* directionality */
$lang['editor']['dirltr'] = 'Ausrichtung Links nach Rechts';
$lang['editor']['dirrtl'] = 'Ausrichtung Rechts nach Links';
?>