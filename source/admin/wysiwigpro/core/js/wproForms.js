
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproForms () {}
wproForms.prototype.getSelectedRadio = function (buttonGroup) {
if (buttonGroup[0]) {
for (var i=0; i<buttonGroup.length; i++) {
if (buttonGroup[i].checked) {
return buttonGroup[i];
}
}
} else {
if (buttonGroup.checked) { return buttonGroup; }
}
return false;
}
wproForms.prototype.getSelectedRadioValue = function (buttonGroup) {
var button;
if (button = this.getSelectedRadio(buttonGroup)) {
return button.value;
}
return false;
}
wproForms.prototype.selectRadio = function (buttonGroup, value) {
if (buttonGroup[0]) {
for (var i=0; i<buttonGroup.length; i++) {
if (buttonGroup[i].value == value) {
buttonGroup[i].checked = true;
} else {
buttonGroup[i].checked = false;
}
}
} else {
if (buttonGroup.value == value) {
buttonGroup.checked = true;
} else {
buttonGroup.checked = false;
}
}
}
wproForms.prototype.getSelectedCheckbox = function (buttonGroup) {
var retArr = new Array();
var lastElement = 0;
if (buttonGroup[0]) {
for (var i=0; i<buttonGroup.length; i++) {
if (buttonGroup[i].checked) {
retArr.length = lastElement;
retArr[lastElement] = i;
lastElement++;
}
}
} else {
if (buttonGroup.checked) {
retArr.length = lastElement;
retArr[lastElement] = 0;
}
}
return retArr;
}
wproForms.prototype.getSelectedCheckboxValue = function (buttonGroup) {
var retArr = new Array();
var selectedItems = this.getSelectedCheckbox(buttonGroup);
if (selectedItems.length != 0) {
for (var i=0; i<selectedItems.length; i++) {
if (buttonGroup[selectedItems[i]]) {
retArr.push(buttonGroup[selectedItems[i]].value);
} else {
retArr.push(buttonGroup.value);
}
}
}
return retArr;
}
wproForms.prototype.getElementValues = function (group) {
var l = group.length
var retArr = []
if (l) {
for (var i=0; i<group.length; i++) {
if (group[i]) {
retArr.push(group[i].value);
} else {
retArr.push(group.value);
}
}
} else {
retArr.push(group.value);
}
return retArr;
}