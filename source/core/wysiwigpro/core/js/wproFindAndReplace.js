
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproFindAndReplace() {
this.replaceCount = 0;
this.storedRange = null;
}
wproFindAndReplace.prototype.newSearch = function () {
this.storedRange = null;
if ( window.find ) {
var rngCaret = dialog.editor.editDocument.createRange()
rngCaret.setStart(currentEditor.editWindow.document.body, 0);
rngCaret.collapse(true)
sel = dialog.editor.editWindow.getSelection()
sel.removeAllRanges()
sel.addRange(rngCaret)
} else {
var theRange = currentEditor.editDocument.body.createTextRange();
theRange.collapse(true);
theRange.select();
}
}
wproFindAndReplace.prototype.replaceWith = function (strSearch, strReplace, matchCase, wholeWords) {
if (!matchCase) matchCase = false;
if (!wholeWords) wholeWords = false;
var selectedText = dialog.editor.getSelectedText();
if ((matchCase && selectedText != strSearch || !matchCase && selectedText.toLowerCase() != strSearch.toLowerCase()) || selectedText.length == 0) {
return this.findNext(strSearch, matchCase, wholeWords);
} else if (selectedText.length >= 1) {
if (strReplace.length >= 1) {
currentEditor.insertAtSelection(wproHtmlSpecialChars(strReplace));
} else {
currentEditor.callFormatting('delete');
}

}
return this.findNext(strSearch, matchCase, wholeWords, false);
}
wproFindAndReplace.prototype.findNext = function (strSearch, matchCase, wholeWords, allowNewSearches) {
if (allowNewSearches==undefined) {
allowNewSearches = true;
}
if (!matchCase) matchCase = false;
if (!wholeWords) wholeWords = false;
if (dialog.editor.getSelectedText().length == 0 && allowNewSearches) {
this.newSearch();
}
if ( window.find ) {

var found = dialog.editor.editWindow.find( strSearch, matchCase, false, false, wholeWords, false, false);
} else {

var theRange = currentEditor.editDocument.selection.createRange();
theRange.collapse( false );
var flags = (matchCase ? 4 : 0) + (wholeWords ? 2 : 0)
var found = theRange.findText(strSearch, 1000000000, flags);
if (found) theRange.select();
}
if (found) {
return true;
} else {
return false;
}
}
wproFindAndReplace.prototype.replaceAll = function (strSearch, strReplace, matchCase, wholeWords, init) {
if (!matchCase) matchCase = false;
if (!wholeWords) wholeWords = false;
if (init!=false) {
this.replaceCount = 0;
this.storedRange = null;
}
if (!this.replaceWith(strSearch, strReplace, matchCase, wholeWords)) {
return false;
} else {
this.replaceCount ++;
return this.replaceAll (strSearch, strReplace, matchCase, wholeWords, false);
}
}