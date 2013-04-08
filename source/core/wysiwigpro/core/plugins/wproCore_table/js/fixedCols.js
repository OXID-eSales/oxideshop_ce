
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

var TABLE;
function initFixedCols () {
var list = document.getElementById('cols');
var table = dialog.editor.tableEditor.getCurrentTable();
TABLE = table;
var width;
if (!width) {
width = table.getAttribute('width');
}
if (table.style) {
if (table.style.width) {
width = table.style.width;
}
}
var percent = false;
if (width) {
if (width.match(/\%/)) {
var percent = true
}
} else {
width = '';
}
width = width.replace(/[^0-9.]/g, '');
document.getElementById('width').value = width;
document.getElementById('widthUnits').value = !percent ? 'px' : '%';
var row
var row = table.rows[0];
var cells = row.cells
var n = cells.length;
for (var i=0; i<n; i++) {
if (cells[i].nodeType == 1) {
var width;
if (cells[i].style) {
width = cells[i].style.width;
}
if (!width) {
width = cells[i].getAttribute('width');
}
if (!width) {
width = '';
}
var percent = false;
if (width.match(/\%/)) {
var percent = true
width = width.replace('%', '');
}
width = width.replace(/[^0-9.]/g, '');
var row = document.createElement('DIV');
row.className = 'row';
var label = document.createElement('LABEL');
label.className = 'ltd';
label.setAttribute('for', i);
var t1 = document.createTextNode(strColumnNumber.replace('##number##', (i+1)));
label.appendChild(t1);
row.appendChild(label);
var rtd = document.createElement('DIV');
rtd.className = 'rtd';
var p = document.createElement('INPUT');
p.setAttribute('type', 'text');
p.setAttribute('value', width);
p.setAttribute('name', i);
p.id = i;
p.setAttribute('size', '3');
rtd.appendChild(p);
var s = document.createElement('SELECT');
s.setAttribute('name', i+'Units');
s.id = i+'Units';
var o1 = document.createElement('OPTION');
o1.setAttribute('label', strPercent);
o1.setAttribute('title', strPercent);
o1.setAttribute('value', '%');
if (percent) {
o1.selected = true;
}
t2 = document.createTextNode(strPercent);
o1.appendChild(t2);
s.appendChild(o1);
var o2 = document.createElement('OPTION');
o2.setAttribute('label', strPixels);
o2.setAttribute('title', strPixels);
o2.setAttribute('value', 'px');
if (!percent) {
o2.selected = true;
}
t3 = document.createTextNode(strPixels);
o2.appendChild(t3);
s.appendChild(o2);
rtd.appendChild(s);
row.appendChild(rtd);
list.appendChild(row);
}
}
dialog.hideLoadMessage();
}
function formAction () {
var table = TABLE;
var tw = document.getElementById('width').value
if (isNaN(tw)&&tw.length>0) {
dialog.alertWrongFormat();
dialog.focus();
document.getElementById('width').value='';
document.getElementById('width').focus();
return false;
}
if (tw) {
tw += document.getElementById('widthUnits').value;
}
var UDBeforeState = dialog.editor.tableEditor.pre();
dialog.editor.tableEditor.autoFitCols(table);
table.style.width = tw;
table.removeAttribute('width');
var row
if (row = dialog.editor.tableEditor.getCurrentRow()) {
} else {
var row = table.rows[0];
}
var cells = row.cells
var n = cells.length;
for (var i=0; i<n; i++) {
if (cells[i].nodeType == 1) {
var width = document.getElementById(i).value;
if (isNaN(width)&&width.length>0) {
dialog.alertWrongFormat();
dialog.focus();
document.getElementById(i).value='';
document.getElementById(i).focus();
return false;
}
if (currentEditor.strict) {
if (width) {
width += document.getElementById(i+'Units').value
}
cells[i].style.width = width;
cells[i].removeAttribute('width');
} else {
if (width) {
width += document.getElementById(i+'Units').value.replace(/px/i, '');
}
cells[i].setAttribute('width', width);
}
}
}
dialog.editor.tableEditor.post(UDBeforeState);
dialog.close();
return false;
}