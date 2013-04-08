
function initInsertRowsAndColumns () {
dialog.hideLoadMessage();
}
function formAction () {
var numCols = document.getElementById('cols').value;
var numRows = document.getElementById('rows').value;
var colPos = document.getElementById('colPosition').value;
var rowPos = document.getElementById('rowPosition').value;
if (isNaN(numCols)&&numCols.length>0) {
dialog.alertWrongFormat();
dialog.focus();
document.getElementById('cols').value='0';
document.getElementById('cols').focus();
return false;
}
if (isNaN(numRows)&&numRows.length>0) {
dialog.alertWrongFormat();
dialog.focus();
document.getElementById('rows').value='0';
document.getElementById('rows').focus();
return false;
}
var UDBeforeState = dialog.editor.tableEditor.pre();
var table = dialog.editor.tableEditor.getCurrentTable();
var row = dialog.editor.tableEditor.getCurrentRow();
var cell = dialog.editor.tableEditor.getCurrentCell();
for (var i=0; i<parseInt(numRows); i++) {
dialog.editor.tableEditor.insertRow(rowPos);
}
for (var i=0; i<parseInt(numCols); i++) {
dialog.editor.tableEditor.insertColumn(colPos);
}
dialog.editor.tableEditor.post(UDBeforeState);
dialog.close();
return false;
}