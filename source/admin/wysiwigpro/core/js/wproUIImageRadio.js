
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproUIImageRadio () {}
wproUIImageRadio.prototype.init = function (UID) {
this.id = UID;
this.currentSelection = null;
this.buttonNode = document.getElementById('UIImageRadio_'+UID);
this.inputNode = this.buttonNode.lastChild;
this.inputNode.setValue = function (value) {
eval (UID+'.select(null, \''+dialog.addSlashes(value)+'\')');
};
this.inputNode.swapImage = function (value, src) {
eval (UID+'.swapImage(\''+dialog.addSlashes(value)+'\', \''+dialog.addSlashes(src)+'\')');
};
var s = this.buttonNode.getElementsByTagName('A');
var n = s.length;
for (var i=0; i<n; i++) {
if (s[i].className == 'outset selected') {
this.currentSelection = s[i];
break;
}
}
}
wproUIImageRadio.prototype.select = function (node, value) {
if (node==null) {
var s = this.buttonNode.getElementsByTagName('A');
var n = s.length;
for (var i=0; i<n; i++) {
if (this.options[i] == value) {
node = s[i];
}
}
}
if (node!=null) node.className = 'outset selected';
if (this.currentSelection) {
if (this.currentSelection != node) {
this.currentSelection.className = 'outset'
}
}
this.currentSelection = node;
this.inputNode.value = value;
if (this.onChange) {
this.onChange();
}
}
wproUIImageRadio.prototype.swapImage = function (value, src) {
var s = this.buttonNode.getElementsByTagName('A');
var n = s.length;
for (var i=0; i<n; i++) {
if (this.options[i] == value) {
var node = s[i];
}
}
if (node) {
var img = node.getElementsByTagName('IMG').item(0);
img.src = src;
}
}