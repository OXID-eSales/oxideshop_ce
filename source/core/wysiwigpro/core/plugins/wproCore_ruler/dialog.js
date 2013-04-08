
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

var CURRENT_RULER = null;
function initRuler () {
dialog.reselectRange();
var range = currentEditor.selAPI.getRange();
var form = document.dialogForm;
if (currentEditor._selectedNode && currentEditor._selectedNode.tagName == 'HR') {
CURRENT_RULER = currentEditor._selectedNode;
} else if (range.nodes[0] && range.nodes[0].tagName == 'HR') {
CURRENT_RULER = range.nodes[0];
}
if (CURRENT_RULER) {
var h = CURRENT_RULER;
CURRENT_RULER = h;
if (form.rulerAlign) {
var a;
if (a=h.getAttribute('align')) {
form.rulerAlign.value = a;
}
}
if (form.rulerWidth) {
var width;
if (a=h.style.width) {
width = a;
} else if (a=h.getAttribute('width')) {
width = a;
}
if (width) {
if (width.search('%') != -1 ) {
form.widthUnits.value = '%';
} else {
form.widthUnits.value = 'px';
}
form.rulerWidth.value = width.replace(/[^0-9.]/gi,'');
}
}
if (form.rulerHeight) {
var height;
if (a=h.style.height) {
height = a;
} else if (a=h.getAttribute('size')) {
height = a;
}
if (height) {
form.rulerHeight.value = height.replace(/[^0-9.]/gi,'');
}
}
if (form.rulerColor) {
var color;
if (a=h.style.backgroundColor) {
color = a;
} else if (a=h.getAttribute('color')) {
color = a;
}
if (color) {
form.rulerColor.setColor(color);
}
}
dialog.events.addEvent(window, 'load', function(e){document.dialogForm.ok.value=strApply;});
}
dialog.selectCurrentStyle(form.elements['style']);
dialog.focus();
dialog.hideLoadMessage();
}
function formAction () {
var form = document.dialogForm;
if (CURRENT_RULER) {
var node = CURRENT_RULER;
} else {
var node = currentEditor.editDocument.createElement('HR');
}
var style = form.elements['style'].value;
if (form.rulerAlign) {
var align = form.rulerAlign.value;
}
if (form.rulerWidth) {
var width = form.rulerWidth.value;
if (isNaN(width)&&width.length>0) {
dialog.alertWrongFormat();
dialog.focus();
form.rulerWidth.value='';
form.rulerWidth.focus();
return false;
}
}
if (form.rulerHeight) {
var height = form.rulerHeight.value;
if (isNaN(height)&&height.length>0) {
dialog.alertWrongFormat();
dialog.focus();
form.rulerHeight.value='';
form.rulerHeight.focus();
return false;
}
}
if (form.rulerAlign) {
if (align!='') {
node.setAttribute('align', align);
} else {
node.removeAttribute('align');
}
}
if (form.rulerWidth) {
if (width!='') {
node.style.width = width + form.widthUnits.value;
} else {
node.style.width = '';
}
node.removeAttribute('width');
}
if (form.rulerHeight) {
if (height == '' && form.rulerColor) {
if (form.rulerColor.value!='') {
height = 1;
}
}
if (height!='') {
node.style.height = height + 'px';
} else {
node.style.height = '';
}
node.removeAttribute('size');
}
if (form.rulerColor) {
if (form.rulerColor.value!='') {
node.style.backgroundColor = form.rulerColor.value;
if (!currentEditor.strict) {
node.setAttribute('color', form.rulerColor.value);
node.setAttribute('noShade', true);
} else {
node.removeAttribute('color');
node.removeAttribute('noShade');
node.style.borderWidth = '0px';
if (height) {
node.style.borderTop = height +' solid '+form.rulerColor.value;
}
}
} else {
node.style.backgroundColor = '';
node.removeAttribute('color');
node.removeAttribute('noShade');
}
}
if (!CURRENT_RULER) {
currentEditor.applyStyle(style, [node], false);
var range = currentEditor.selAPI.getRange()
range.insertNode(node);
} else {
currentEditor.applyStyle(style);
}
dialog.close();
return false;
}