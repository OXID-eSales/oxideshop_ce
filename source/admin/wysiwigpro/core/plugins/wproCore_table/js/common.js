
function highlightCell(cell) {
c = cell.className.toString()
c = c.replace(/[\s]*wproHighlight[\s]*/gi, '');
cell.className = (c+' wproHighlight');
}
function highlightCells (cells) {
var n = cells.length;
for (var i=0; i<n; i++) {
if (cells[i].nodeType == 1) {
c = cells[i].className.toString()
c = c.replace(/[\s]*wproHighlight[\s]*/gi, '');
cells[i].className = (c+' wproHighlight');
}
}
}
function removeHighlight(table) {
if (!table&&TABLE) table = TABLE
var rows = table.rows
var n = rows.length;
for (var i=0; i<n; i++) {
var cells = rows[i].cells;
var k = cells.length;
for (var j=0; j<k; j++) {
c = cells[j].className.toString()
c = c.replace(/[\s]*wproHighlight[\s]*/gi, '');
cells[j].className = c;
}
}
}