
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

var TABLE;
function initUnmergeCells () {
var table = dialog.editor.tableEditor.getCurrentTable();
TABLE = table;
var row = dialog.editor.tableEditor.getCurrentRow();
var cell = dialog.editor.tableEditor.getCurrentCell();
var rs = parseInt(cell.getAttribute('rowSpan'));
var cs = parseInt(cell.getAttribute('colSpan'));
if (!cs) cs=1;
if(!rs) rs=1;
var cols = document.getElementById('cols');
var cells = row.cells
for (var i=0; i<cs; i++) {
var o = document.createElement('OPTION');
o.setAttribute('value', i+1)
o.setAttribute('label', i+1)
if ((rs==1 || (rs>1&&cs>1)) && i==1) {
o.selected=true
} else if (i==0) {
o.selected=true;
}
var t = document.createTextNode(i+1);
o.appendChild(t);
cols.appendChild(o);
}
var rows = document.getElementById('rows');
for (var i=0; i<rs; i++) {
var o = document.createElement('OPTION');
o.setAttribute('value', i+1)
o.setAttribute('label', i+1)
if (cs==1&&i==1) {
o.selected=true
} else if (i==0) {
o.selected=true;
}
var t = document.createTextNode(i+1);
o.appendChild(t);
rows.appendChild(o);
}
dialog.hideLoadMessage();
}
function unmergeDown(cell) {
if (!cell) {
cell = dialog.editor.tableEditor.getCurrentCell();
}
var rowspan = parseInt(cell.getAttribute('rowSpan'));
var row = WPro.getParentNodeByTagName(cell, 'TR');
var table = WPro.getParentNodeByTagName(cell, 'TABLE');
var rowIndex = row.rowIndex + rowspan -1;
var row = table.rows[rowIndex];
var cellIndex = dialog.editor.tableEditor.getCellIndex(cell);
var cells = row.cells;
var rowSpanIndexes = dialog.editor.tableEditor.getRowIntersections(row);
var n = cells.length;
var colCount = 0;
for (var i=0; i<n; i++) {
if (cells[i].nodeType == 1) {
if (rowSpanIndexes[i]) {
if (parseInt(rowSpanIndexes[i].getAttribute('colSpan')) > 1) {
colCount += parseInt(rowSpanIndexes[i].getAttribute('colSpan'));
} else {
colCount ++
}
}
if (parseInt(cells[i].getAttribute('colSpan')) > 1) {
colCount += parseInt(cells[i].getAttribute('colSpan'))
} else {
colCount ++
}
if (colCount >= cellIndex) {
var newCell = cell.cloneNode(false);
newCell.removeAttribute('rowSpan');
newCell.removeAttribute('width');
newCell.removeAttribute('height');
dialog.editor.tableEditor.formatNewCell(newCell);
row.insertBefore(newCell, cells[i].nextSibling)
break;
}
}
}
cell.setAttribute('rowSpan', rowspan-1);
if (newCell) {
return newCell;
} else {
return null
}
}
function unmergeRight(cell) {
if (!cell) {
cell = dialog.editor.tableEditor.getCurrentCell();
}
var colspan = parseInt(cell.getAttribute('colSpan'));
var rowspan = parseInt(cell.getAttribute('rowSpan'));
var row = WPro.getParentNodeByTagName(cell, 'TR');
var table = WPro.getParentNodeByTagName(cell, 'TABLE');
var width
var percent = false;
if (width = cell.getAttribute('width')) {
if (width.match(/%/)) {
width = width.replace(/%/, '');
percent = true;
}
width = parseInt(width) / colspan;
cell.setAttribute('width', width*(colspan-1) + (percent?'%':0));
}
cell.setAttribute('colSpan', colspan-1);
var newCell = cell.cloneNode(false);
if (width) {
newCell.setAttribute('width', width + (percent?'%':0));
}
newCell.removeAttribute('colSpan');
dialog.editor.tableEditor.formatNewCell(newCell);
var parent = dialog.editor.tableEditor.getParent(cell);
parent.insertBefore(newCell, dialog.editor.tableEditor.getNextSibling(cell));
}
function formAction () {
var UDBeforeState = dialog.editor.tableEditor.pre();
var right = parseInt(document.getElementById('cols').value) - 1;
var down = parseInt(document.getElementById('rows').value) - 1;
var cell = dialog.editor.tableEditor.getCurrentCell();
for (var i=1; i<=down; i++) {
var c = unmergeDown(cell);
if (c) {
for (var j=1; j<=right; j++) {
unmergeRight(c);
}
}
}
for (var i=1; i<=right; i++) {
unmergeRight(cell);
}
dialog.editor.tableEditor.post(UDBeforeState);
dialog.close();
return false;
}