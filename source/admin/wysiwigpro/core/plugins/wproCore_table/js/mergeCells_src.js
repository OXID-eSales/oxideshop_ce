var TABLE;
function initMergeCells () {
	
	//dialog.events.addEvent(window, 'unload', unhighlightAffectedCells);
	
	var table = dialog.editor.tableEditor.getCurrentTable();
	
	TABLE = table;
	
	var row = dialog.editor.tableEditor.getCurrentRow();
	
	var cell = dialog.editor.tableEditor.getCurrentCell();
	
	var rs;
	if (parseInt(cell.getAttribute('rowSpan')) > 1) {
		rs = parseInt(cell.getAttribute('rowSpan'))
	} else {
		rs= 1
	}
	var cs;
	if (parseInt(cell.getAttribute('colSpan')) > 1) {
		cs = parseInt(cell.getAttribute('colSpan'))
	} else {
		cs= 1
	}
	
	var cols = document.getElementById('cols');
	
	var cells = row.cells 
	
	var n = cells.length;
	var colCount = cs;
	var colCount2 = 0;
	var found = false;
	for (var i=0; i<n; i++) {
		if (cells[i].nodeType == 1) {
			if (found) {
				
				if (parseInt(cells[i].getAttribute('colSpan')) > 1) {
					c = parseInt(cells[i].getAttribute('colSpan'))
					colCount2 += c
					colCount += c;
				} else {
					colCount2 ++
					colCount ++;
				}
				var o = document.createElement('OPTION');
				o.setAttribute('value', colCount2)
				o.setAttribute('label', colCount)
				var t = document.createTextNode(colCount);
				o.appendChild(t);
				cols.appendChild(o);
				
			}
			if (cells[i] == cell) {
				found = true;
			}
			
		}
					
	}
	
	var rows = document.getElementById('rows');
	
	var r = table.rows;
	
	var n = r.length;
	var rowCount = 0;
	var s = rs;
	var found = false;
	for (var i=0; i<n; i++) {
		if (r[i].nodeType == 1) {
			if (found) {
				s--;
				if (s<=0) {
					rowCount ++;
					var o = document.createElement('OPTION');
					o.setAttribute('value', rowCount)
					o.setAttribute('label', rowCount+rs)
					var t = document.createTextNode(rowCount+rs);
					o.appendChild(t);
					rows.appendChild(o);
				}
			}
			if (r[i] == row) {
				found = true;
			}
		}
	}
	

	
	//updatePreview();
	dialog.hideLoadMessage();
}

function highlightAffectedCells () {
	var table = dialog.editor.tableEditor.getCurrentTable();
	//var row = dialog.editor.tableEditor.getCurrentRow();
	//var cell = dialog.editor.tableEditor.getCurrentCell();
	removeHighlight(table);
	
	var cells = mergeCells(null, document.getElementById('cols').value, document.getElementById('rows').value, true);
	if (cells) {
		highlightCells(cells);
	}
}
function unhighlightAffectedCells () {
	
	//var table = dialog.editor.tableEditor.getCurrentTable();
	//var row = dialog.editor.tableEditor.getCurrentRow();
	//var cell = dialog.editor.tableEditor.getCurrentCell();
	removeHighlight(TABLE);	
}
function unloadDialog() {
	removeHighlight(TABLE);	
}

function mergeCells (cell, right, down, test) {
	
	var UDBeforeState = dialog.editor.tableEditor.pre();
	
	if (!cell) {
		cell = dialog.editor.tableEditor.getCurrentCell();	
	}
	
	var cellindex = dialog.editor.tableEditor.getCellIndex(cell);
	var row = dialog.editor.tableEditor.getParent(cell);
	var table = WPro.getParentNodeByTagName(row, 'TABLE');
	
	var rows = table.rows;
	
	
	if (!right) {
		right = 0;	
	}
	if (!down) {
		down = 0;	
	}
	
	if (parseInt(cell.getAttribute('colSpan')) > 1) {
		right += parseInt(cell.getAttribute('colSpan'))
	} else {
		right ++
	}
	if (parseInt(cell.getAttribute('rowSpan')) > 1) {
		down += parseInt(cell.getAttribute('rowSpan'))
	} else {
		down ++
	}
	
	var r = row
	var c
	var arr = [];
	var rowCount = 0;
	
	var iright = right
	
	var cells = [];
	
	while (r && down > 0) {
		if (r.nodeType == 1) {
			c = mergeRight(cell, iright, r, down, test);
			if (c===false) {
				break;
			} else {
				rowCount++;
				iright = right;
				
				if (test) {
					var n = c[0].length;
					for (var i=0; i<n; i++) {
						cells.push(c[0][i]);
					}
					c = c[1];
				}
				
				var n = c.length;
				for (var i=0; i<n; i++) {
					arr.push(c[i]);
				}
				var n = arr.length;
				for (var i=0; i<n; i++) {
					arr[i]['rowSpan'] --
					if (arr[i]['rowSpan'] > 0) {
						iright -= arr[i]['colSpan'];
					}
				}
			}
			down --
		}
		r = r.nextSibling
	}
	if (rowCount > 1 && !test) {
		cell.setAttribute('rowSpan', rowCount);	
	}
	
	dialog.editor.tableEditor.post(UDBeforeState);
	
	dialog.focus();
	
	return cells
}

// merges with cells to right of base cell. Base cell could be in a different row to facilitate merging down.
// if complete returns an array of cell indexes and rowSpans for any cells found to span rows.
function mergeRight (mergeCell, num, row, maxDown, test) {
	if (!mergeCell) {
		mergeCell = dialog.editor.tableEditor.getCurrentCell();	
	}
	if (!num) {
		num = 1;	
	}
	if (!maxDown) {
		maxDown = 1;	
	}
	if (!row) {
		row = dialog.editor.tableEditor.getParent(mergeCell);
	}
	var cellindex = dialog.editor.tableEditor.getCellIndex(mergeCell);
	
	var cells = row.cells;
	
	var rowSpanIndexes = dialog.editor.tableEditor.getRowIntersections(row);
	
	var mergeNodes = [];
	
	var retArray = [];

	var cs = 0;
	//var rs = 1;
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
			if (colCount >= cellindex) {
				var c
				if (parseInt(cells[i].getAttribute('colSpan')) > 1) {
					c = parseInt(cells[i].getAttribute('colSpan'))
					num -= c
					cs += c
				} else {
					c = 1;
					num --
					cs ++
				}
				var r
				if (parseInt(cells[i].getAttribute('rowSpan')) > 1) {
					r = parseInt(cells[i].getAttribute('rowSpan'))
					if (r > maxDown) {
						return false;
					} else {
						retArray.push({'cellIndex':colCount, 'rowSpan':r, 'colSpan':c})
					}
				}
				mergeNodes.push(cells[i]);
				if (num == 0) {
					break;
				}
				if (num < 0) {
					return false;	
				}
			}
			if (parseInt(cells[i].getAttribute('colSpan')) > 1) {
				colCount += parseInt(cells[i].getAttribute('colSpan'))
			} else {
				colCount ++
			}
			
		}
					
	}
	
	if (!test) {
		var n = mergeNodes.length;
		var colSpan = 0;
		for (var i=1; i<n; i++) {
			if (WPro.hasContent(mergeNodes[i])) {
				var cn = mergeNodes[i].childNodes;
				for (var k=0; k<cn.length; k++) {
					mergeNodes[0].appendChild(cn[k].cloneNode(true));
				}
			}
			row.deleteCell(mergeNodes[i].cellIndex);
		}
		
		if (n && mergeNodes[0] != mergeCell) {
			if (WPro.hasContent(mergeNodes[0])) {
				var cn = mergeNodes[0].childNodes;
				for (var k=0; k<cn.length; k++) {
					mergeCell.appendChild(cn[k].cloneNode(true));
				}
			}
			row.deleteCell(mergeNodes[0].cellIndex);
		} else if (cs > 1) {
			mergeCell.setAttribute('colSpan', cs);
			mergeCell.removeAttribute('width');
			WPro.removeStyleAttribute(mergeCell, 'width');
		}
	}
	
	if (test) {
		return [mergeNodes, retArray];
	} else {
		return retArray;	
	}
	

}


function formAction () {
	var right = document.getElementById('cols').value;
	var down = document.getElementById('rows').value;
	
	unhighlightAffectedCells ();
	
	mergeCells(null, right, down);

	dialog.close();
	return false;
}