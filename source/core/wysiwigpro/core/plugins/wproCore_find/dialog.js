
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

var fr = new wproFindAndReplace();
var initSearch = false;
function initFind() {
var selectedText = dialog.editor.getSelectedText();
if (selectedText.length > 1) {
var form = document.dialogForm
form.strSearch.value = selectedText;
initSearch = true;
dialog.events.addEvent(window, 'load', checkEnableButtons);
}
dialog.focus();
dialog.hideLoadMessage();
}
function checkEnableButtons() {
var form = document.dialogForm
if (form.strSearch.value.length >= 1) {
form.replaceButton.disabled = false;
form.replaceAllButton.disabled = false;
form.findNextButton.disabled = false;
} else {
form.replaceButton.disabled = true;
form.replaceAllButton.disabled = true;
form.findNextButton.disabled = true;
}
}
function newSearch() {
initSearch = false;
fr.newSearch();
}
function findNext() {
if (initSearch) newSearch();
var form = document.dialogForm
var matchCase = form.matchCase.checked ? true : false;
var wholeWords = form.wholeWords.checked ? true : false;
var strSearch = form.strSearch.value;
if (!fr.findNext(strSearch,matchCase,wholeWords)) {
alert(strFinishedSearching);
initSearch = true;
}
}
function replaceText() {
if (initSearch) newSearch();
var form = document.dialogForm
var strSearch = form.strSearch.value;
var strReplace = form.strReplace.value;
var matchCase = form.matchCase.checked ? true : false;
var wholeWords = form.wholeWords.checked ? true : false;
if (!fr.replaceWith(strSearch,strReplace,matchCase,wholeWords)) {
alert(strFinishedSearching);
initSearch = true;
}
}
function replaceAllText(init) {
if (init!=false) {
init = true;
} else {
init = false
}
if (init) newSearch();
var form = document.dialogForm
var strSearch = form.strSearch.value;
var strReplace = form.strReplace.value;
var matchCase = form.matchCase.checked ? true : false;
var wholeWords = form.wholeWords.checked ? true : false;
fr.replaceAll(strSearch,strReplace,matchCase,wholeWords,init);
var matches = fr.replaceCount;
alert(strFinishedSearching + ' ' + strReplacements.replace(/##num##/, matches));
initSearch = true;
}
function formAction () {
return false;
}