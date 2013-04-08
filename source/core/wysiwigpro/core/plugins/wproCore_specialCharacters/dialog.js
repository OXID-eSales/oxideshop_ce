
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

var CURRENT_CHARACTER = null;
var okChar = /^&[#a-z0-9]+;/i;
function initSpecialCharacters () {
var b = document.getElementsByTagName('A');
var n = b.length;
for (var i=0; i<n; i++) {
if (b[i].parentNode.className.match(/characterScroll/i)) {
addActions(b[i]);
}
}
dialog.focus();
dialog.hideLoadMessage();
}
function addActions(node) {
node.onclick = selectCharacter;
node.onmouseover = function () {
this.className = 'selected';
}
node.onfocus = node.onmouseover
node.onmouseout = function () {
if (CURRENT_CHARACTER) {
if (CURRENT_CHARACTER != this) {
this.className = 'cl';
}
} else {
this.className = 'cl';
}
}
node.onblur = node.onmouseout
}
function selectCharacter() {
if (CURRENT_CHARACTER) {
if (CURRENT_CHARACTER==this) {
return;
}
}
displayCharacter(this.title);
CURRENT_CHARACTER = this;
this.className = 'selected';
}
function displayCharacter(val) {
if (!okChar.test(val)) {
val = '';
document.dialogForm.ok.disabled=true;
} else {
document.dialogForm.ok.disabled=false;
document.dialogForm.ok.focus();
}
document.dialogForm.selectedCharacter.value = val;
document.getElementById('charDisplay').innerHTML = val;
if (CURRENT_CHARACTER) {
CURRENT_CHARACTER.className = 'cl';
}
}
function formAction () {
var v = document.dialogForm.selectedCharacter.value
if (v.length > 1) {
currentEditor.insertAtSelection (v);
var c = new wproCookies();
var val = c.readCookie('wproRecentlyUsedSpecialChars');
v2 = v.toString();
v2 = v2.replace(/&/gi, '').replace(/;/gi, '');
var test = new RegExp(','+WPro.quoteMeta(v2)+'(,|$)', 'i');
if (!test.test(val)) {
val = v2+','+val;
c.writeCookie('wproRecentlyUsedSpecialChars', val, null, '/');
var a = document.createElement('A');
a.setAttribute('href', 'javascript:undefined');
a.setAttribute('title', v);
a.className = 'cl';
a.innerHTML = v;
addActions(a);
document.getElementById('recent').insertBefore(a, document.getElementById('recent').firstChild);
}
}
dialog.focus();
document.dialogForm.close.focus();
return false;
}