
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

var bookmarks = [];
var selectedBookmark = null;
function initBookmark () {
dialog.reselectRange();
var range = currentEditor.selAPI.getRange();
if (range.type=='control') {
if (range.nodes[0].tagName=='A') {
selectedBookmark = range.nodes[0];
}
}
if (selectedBookmark==null) selectedBookmark = range.getContainerByTagName('A');
var a = selectedBookmark;
if (a) {
var value;
if (value = a.getAttribute('id')) {
document.dialogForm.bookmarkName.value = value;
} else if (value = a.getAttribute('name')) {
document.dialogForm.bookmarkName.value = value;
}
if (document.dialogForm.bookmarkName.value.length > 0) {
var range = currentEditor.selAPI.getRange()
range.selectNodeContents(a);
range.select();
dialog.rangeToReselect = range;
}
}
var as = currentEditor.editDocument.getElementsByTagName('A');
var n = as.length;
for (var i=0; i<n; i++) {
if (as[i].getAttribute('id')) {
bookmarks.push(as[i]);
} else if (as[i].getAttribute('name')) {
bookmarks.push(as[i]);
}
}
var n = bookmarks.length;
bookmarkSelect = document.dialogForm.bookmarkSelect;
for (var i=0; i<n; i++) {
var name;
if (name = bookmarks[i].getAttribute('id')) {
} else if (name = bookmarks[i].getAttribute('name')) {
}
var s = document.createElement('OPTION');
s.setAttribute('value', name);
s.setAttribute('label', name);
if (document.dialogForm.bookmarkName.value == name) {
s.setAttribute('selected', true);
document.dialogForm.removeBookmark.disabled = false;
}
t = document.createTextNode(name);
s.appendChild(t);
bookmarkSelect.appendChild(s);
}
dialog.focus();
dialog.hideLoadMessage();
}
function selectBookmark(value) {
dialog.reselectRange();
var n = bookmarks.length;
var form = document.dialogForm;
bookmarkSelect = document.dialogForm.bookmarkSelect;
for (var i=0; i<n; i++) {
var name;
if (bookmarks[i]) {
if (name = bookmarks[i].getAttribute('id')) {
} else if (name = bookmarks[i].getAttribute('name')) {
}
if (name == value) {
form.bookmarkName.value = value;
selectedBookmark = bookmarks[i];
var range = currentEditor.selAPI.getRange()
range.selectNodeContents(bookmarks[i]);
range.select();
dialog.rangeToReselect = range;
var height = currentEditor.editFrame.offsetHeight;
var width = currentEditor.editFrame.offsetWidth;
currentEditor.editWindow.scrollTo(bookmarks[i].offsetLeft-(width/4), bookmarks[i].offsetTop-(height/4));
}
}
}
form.removeBookmark.disabled = false;
if (!dialog.iframeDialog) {
dialog.focus();
}
bookmarkSelect.focus();
}
function checkEnableButtons() {
var form = document.dialogForm;
if (form.bookmarkName.value.length >= 1) {
if (selectedBookmark) {
form.updateBookmark.disabled = false;
form.insertBookmark.disabled = true;
} else {
form.insertBookmark.disabled = false;
}
} else {
form.updateBookmark.disabled = true;
form.insertBookmark.disabled = true;
}
}
function clearBookmark () {
var form = document.dialogForm;
if (selectedBookmark) {
if (!selectedBookmark.getAttribute('href')) {
var arr = selectedBookmark.childNodes;
var n = arr.length;
while (selectedBookmark.firstChild) {
selectedBookmark.parentNode.insertBefore(selectedBookmark.firstChild, selectedBookmark);
}
selectedBookmark.parentNode.removeChild(selectedBookmark);
} else {
selectedBookmark.removeAttribute('name');
selectedBookmark.removeAttribute('id');
}
}
form.removeBookmark.disabled = true;
bookmarkSelect = form.bookmarkSelect;
var options = bookmarkSelect.getElementsByTagName('OPTION');
var n = options.length;
for (var i=0; i<n; i++) {
if (options[i].getAttribute('value') == form.bookmarkName.value) {
options[i].parentNode.removeChild(options[i]);
break;
}
}
form.bookmarkName.value = '';
checkEnableButtons();
selectedBookmark = null;
currentEditor.toggleGuidelines();
currentEditor.toggleGuidelines();
currentEditor.redraw();
}
function formAction () {
dialog.reselectRange();
var form = document.dialogForm;
var value = form.bookmarkName.value;
value = value.replace(/[^A-Za-z0-9\-_:.]+/gi, '');
form.bookmarkName.value = value;
if (value.length > 0) {
var a = selectedBookmark;
if (a) {
if (currentEditor.useXHTML) {
a.setAttribute('id', value);
}
a.setAttribute('name', value);
currentEditor._showGuideOnTags('A');
bookmarkSelect = form.bookmarkSelect;
var options = bookmarkSelect.getElementsByTagName('OPTION');
var n = options.length;
for (var i=0; i<n; i++) {
if (options[i].selected == true) {
options[i].setAttribute('label', value);
options[i].setAttribute('value', value);
options[i].removeChild(options[i].firstChild);
var t = document.createTextNode(value);
options[i].appendChild(t);
break;
}
}
} else {
var str = '<a';
if (!currentEditor.strict||!currentEditor.useXHTML) {
str += ' name="'+value+'"';
}
if (currentEditor.useXHTML) {
str += ' id="'+value+'"';
}
if (currentEditor._guidelines) {
str+=' class="wproGuide"';
}
var s = currentEditor.getSelectedHTML();
str+='>'+(s ? s : '&nbsp;')+'</a>';
currentEditor.insertAtSelection(str);
dialog.close();
}
}
return false;
}