function wproPlugin_wproCore_table () {}
wproPlugin_wproCore_table.prototype.init = function (EDITOR) {
	this.editor = EDITOR.name;
	EDITOR.tableEditor = this;
	EDITOR.addButtonStateHandler('edittable',wproPlugin_wproCore_table_bsh);
	EDITOR.addButtonStateHandler('deltable',wproPlugin_wproCore_table_bsh);
	EDITOR.addButtonStateHandler('insrowabove',wproPlugin_wproCore_table_bsh);
	EDITOR.addButtonStateHandler('insrowbelow',wproPlugin_wproCore_table_bsh);
	EDITOR.addButtonStateHandler('inscolleft',wproPlugin_wproCore_table_bsh);
	EDITOR.addButtonStateHandler('inscolright',wproPlugin_wproCore_table_bsh);
	EDITOR.addButtonStateHandler('insrowsandcols',wproPlugin_wproCore_table_bsh);
	EDITOR.addButtonStateHandler('delcol',wproPlugin_wproCore_table_bsh);
	EDITOR.addButtonStateHandler('delrow',wproPlugin_wproCore_table_bsh);
	EDITOR.addButtonStateHandler('unmergecells',wproPlugin_wproCore_table_bsh);
	EDITOR.addButtonStateHandler('mergecells',wproPlugin_wproCore_table_bsh);
}
function wproPlugin_wproCore_table_bsh (EDITOR,srcElement,cmd,inTable,inA,range) {
	var retVal = "wproDisabled";
	var tn='';
	if (range.nodes[0]) {
		tn = range.nodes[0].tagName;
	}
	switch(cmd) {
		case 'unmergecells' :
			if (inTable&&tn!='TABLE') {
				var cell
				if (cell = EDITOR.tableEditor.getCurrentCell()) {
					var cs = cell.getAttribute('colSpan');
					if (cs) {
						if (parseInt(cs)>1) {
							return "wproReady";
							break;	
						}
					}
					var rs = cell.getAttribute('rowSpan');
					if (rs) {
						if (parseInt(rs)>1) {
							return "wproReady";
							break;	
						}
					}
				}
			}
			return "wproDisabled";
			break
		case 'edittable' : case 'deltable' :
			if (tn=='TABLE') {
				return "wproReady";
				break;
			}
		default:
			if (inTable && tn == '') {
				var t = /^(IMG|A|HR|OBJECT|EMBED|APPLET)$/i;
				if (range.nodes[0]) {
					if (!t.test(range.nodes[0].tagName)) {
						retVal = "wproReady";
					}
				} else if (!inA) {
					retVal = "wproReady";
				}
			}
			break;
	}
	return retVal
}
	
	/* table editing API */
wproPlugin_wproCore_table.prototype.pre = function () {
	var editor=WPro.editors[this.editor];
	return editor.history.pre();
}
wproPlugin_wproCore_table.prototype.post = function (d) {
	var editor=WPro.editors[this.editor];
	editor.history.post(d);
}
	//this.obj = obj;
	// returns the current cell
wproPlugin_wproCore_table.prototype.getCurrentCell = function () {
	var editor=WPro.editors[this.editor];
	var c
	var range = editor.selAPI.getRange();
	if (c = range.getContainerByTagName('TD')) {
		return c;
	} else if (c = range.getContainerByTagName('TH')) {
		return c
	} else {
		return false;
	}
}
	// returns the current Row
wproPlugin_wproCore_table.prototype.getCurrentRow = function () {
	var editor=WPro.editors[this.editor];
	var c
	var range = editor.selAPI.getRange();
	if (c = range.getContainerByTagName('TR')) {
		return c;
	} else {
		return false;
	}
}
	// returns the current Table
wproPlugin_wproCore_table.prototype.getCurrentTable = function () {
	var editor=WPro.editors[this.editor];
	var c
	var range = editor.selAPI.getRange();
	if (range.type == "control") {
		var nodes = range.nodes;
		var num = nodes.length;
		//for (var i=0; i < num; i++) {
			if (nodes[0].tagName == 'TABLE') {
				c = nodes[0]
			}
		//}
	}
	if (c) {
		return c;
	} else if (c = range.getContainerByTagName('TABLE')) {
		return c;
	} else {
		return false;
	}
}
	// finds the next sibling that is a tag
wproPlugin_wproCore_table.prototype.getNextSibling = function (tag) {
	var editor=WPro.editors[this.editor];
	if (!tag.nextSibling || tag == null) {
		return null;
	}
	var thisTag = tag.nextSibling
	while(thisTag.nodeType != 1) {
		if (!thisTag.nextSibling) {
			break;
		}
		thisTag = thisTag.nextSibling
	}
	if (thisTag.nodeType != 1) {
		return null;
	} else {
		return thisTag
	}
}
	// finds the previous sibling that is a tag
wproPlugin_wproCore_table.prototype.getPreviousSibling = function (tag) {
	var editor=WPro.editors[this.editor];
	if (!tag.previousSibling) {
		return null;
	}
	var thisTag = tag.previousSibling
	while(thisTag.nodeType != 1) {
		if (!thisTag.previousSibling) {
			break;
		}
		thisTag = thisTag.previousSibling
	}
	if (thisTag.nodeType != 1) {
		return null;
	} else {
		return thisTag
	}
}
	// finds the parent node that is a tag
wproPlugin_wproCore_table.prototype.getParent = function (tag) {
	var editor=WPro.editors[this.editor];
	var thisTag = tag.parentNode
	while(thisTag.nodeType != 1) {
		if (!thisTag.parentNode) {
			break;
		}
		thisTag = thisTag.parentNode
	}
	if (thisTag.tagName) {
		return thisTag
	} else {
		return false
	}
}

// find any cells that intersect this row.
wproPlugin_wproCore_table.prototype.getRowIntersections = function (row) {
	var editor=WPro.editors[this.editor];
	var rowSpanIndexes = [];
	var rowIndex = row.rowIndex;
	//var range = editor.selAPI.getRange();
	if (rowIndex > 0) {
		//var rows = this.getParent(row).childNodes;
		var rows = WPro.getParentNodeByTagName(row, 'TABLE').rows;
		
		//var n = rows.length;
		var n = row.rowIndex;
		for (var i=0; i<n; i++) {
			if (rows[i].nodeType == 1) {
				var cells = rows[i].cells
				
				var n2 = cells.length;
				var ci = 0;
				for (var j=0; j<n2; j++) {
					if (cells[j].nodeType == 1) {
						//ci += cells[j].getAttribute('colSpan') ? parseInt(cells[j].getAttribute('colSpan')) : 1;
						if (parseInt(cells[j].getAttribute('rowSpan')) > 1) {
							var r = parseInt(cells[j].getAttribute('rowSpan'))
							if (r + rows[i].rowIndex >= rowIndex+1) {
								rowSpanIndexes[ci] = cells[j];
							}
						}
						ci += cells[j].getAttribute('colSpan') ? parseInt(cells[j].getAttribute('colSpan')) : 1;
					}
				}
				
			}
		}
	}
	
	return rowSpanIndexes;
}

// gets the cell index taking into account rowspans and colspans, 
wproPlugin_wproCore_table.prototype.getCellIndex = function (cell, position) {
	var editor=WPro.editors[this.editor];
	if (!cell) {
		cell = this.getCurrentCell();
	}
	if (!position) {
		position = 'left';
	}
	var cellIndex = 0;
	var row = WPro.getParentNodeByTagName(cell, 'TR'); //this.getParent(cell);
	
	var rowSpanIndexes = this.getRowIntersections(row);

	// count the number of cells
	//var cells = row.childNodes;
	var cells = row.cells
	var n = cell.cellIndex;
	if (position == 'right') {
		n++
	}
	for (var i=0; i<n; i++) {
		if (cells[i].nodeType == 1) {
			if (rowSpanIndexes[i]) {
				if (parseInt(rowSpanIndexes[i].getAttribute('colSpan')) > 1) {
					cellIndex += parseInt(rowSpanIndexes[i].getAttribute('colSpan'));
				} else {
					cellIndex ++
				}
				
			}
			if (parseInt(cells[i].getAttribute('colSpan')) > 1) {
				cellIndex += parseInt(cells[i].getAttribute('colSpan'));
			} else {
				cellIndex ++
			}
		}		
	}
	return cellIndex;
}	

// adjusts rowspans on cells that intersect row based upon the action (insert/delete) and returns an array of cells whose rowspans end in row,
// so that when inserting a row below we can copy the row above.
wproPlugin_wproCore_table.prototype._processRowSpans = function (row, action) {
	var editor=WPro.editors[this.editor];
	var rowSpanIndexes = [];
	var rowIndex = row.rowIndex;
	//var range = editor.selAPI.getRange();
	if (rowIndex > 0) {
		//var rows = this.getParent(row).childNodes;
		var rows = this.getCurrentTable().rows
		
		//var n = rows.length;
		var n = row.rowIndex;
		for (var i=0; i<n; i++) {
			if (rows[i].nodeType == 1) {
				var cells = rows[i].cells
				
				var n2 = cells.length;
				var ci = 0;
				for (var j=0; j<n2; j++) {
					if (cells[j].nodeType == 1) {
						if (parseInt(cells[j].getAttribute('rowSpan')) > 1) {
							var r = parseInt(cells[j].getAttribute('rowSpan'))
							if (r + rows[i].rowIndex > rowIndex+1) {
								if (action == 'delete') {
									cells[j].setAttribute('rowSpan', parseInt(cells[j].getAttribute('rowSpan')) - 1)
								} else {
									cells[j].setAttribute('rowSpan', parseInt(cells[j].getAttribute('rowSpan')) + 1)
								} 
							} else if (r + rows[i].rowIndex == rowIndex+1 && action == 'insert') {
								rowSpanIndexes[ci] = cells[j];
							}
						}
						
						ci += cells[j].getAttribute('colSpan') ? parseInt(cells[j].getAttribute('colSpan')) : 1;
					}
				}
			}
		}
	}

	return rowSpanIndexes;
}
	
wproPlugin_wproCore_table.prototype.deleteRow = function (cell) {
	
	var editor=WPro.editors[this.editor];
	
	var UDBeforeState = this.pre();
	
	if (!cell) {
		cell = this.getCurrentCell();
		row = WPro.getParentNodeByTagName(cell, 'TR');//this.getParent(cell);
	}
	// get this rows row index.
	//var rowIndex = row.rowIndex;
	//var rowSpanIndexes = [];
	
	var rowSpanIndexes = this._processRowSpans(row, 'delete');
	
	// now loop through and check for cells in this row that have a rowSpan, if they do then move the cell down into the row below
	//var cells = row.childNodes;
	var cells = row.cells;
	var n = cells.length;
	var ci = 0;
	for (var i=0; i<n; i++) {
		if (cells[i].nodeType == 1) {
			//if (rows[i] == row) {
			if (parseInt(cells[i].getAttribute('rowSpan')) > 1) {
				var nextRow = this.getNextSibling(row);
				//var nextRowCells = nextRow.childNodes
				var nextRowCells = nextRow.cells
				var nrc = nextRowCells.length
				var ci2 = 0;
				for (var k=0; k<nrc; k++) {
					if (nextRowCells[k].nodeType == 1) {
						if (ci2 >= ci-1) {
							//var f = editor.editDocument.createElement(cells[i].tagName);
							//WPro.addAttributes(f, cells[i].attributes, cells[i]);
							//f.innerHTML = cells[i].innerHTML;
							var f = cells[i].cloneNode(true);
							
							f.setAttribute('rowSpan', parseInt(cells[i].getAttribute('rowSpan')) - 1);
							//var parentNode = this.getParent(nextRowCells[k])
							nextRow.insertBefore(f , this.getNextSibling(nextRowCells[k]))
							break;
						}
						ci2 += nextRowCells[k].getAttribute('colSpan') ? parseInt(nextRowCells[k].getAttribute('colSpan')) : 1;
					}
				}
			}
			ci += cells[i].getAttribute('colSpan') ? parseInt(cells[i].getAttribute('colSpan')) : 1;
		}
	}
	
	//this.getParent(row).removeChild(row);
	
	//this.getParent(row).deleteRow(row.rowIndex);
	WPro.getParentNodeByTagName(row, 'TABLE').deleteRow(row.rowIndex);
	
	this.post(UDBeforeState);

}
	
	
wproPlugin_wproCore_table.prototype.insertRow = function (position, row) {
	var editor=WPro.editors[this.editor];
	var UDBeforeState = this.pre();
	//var range = editor.selAPI.getRange();
	if (!row) {
		cell = this.getCurrentCell();
		row = WPro.getParentNodeByTagName(cell, 'TR');//this.getParent(cell);
		//row = editor.isInside(;);
	}
	var newRow = editor.editDocument.createElement('TR');
	newRow.setAttribute('vAlign', 'top');
	// get this rows row index.
	//
	var rowIndex = row.rowIndex;
	var rowSpanIndexes = [];
	
	var rowSpanIndexes = this._processRowSpans(row, 'insert');
	
	// now loop through and build the required rows
	//var cells = row.childNodes;
	var cells = row.cells
	var n = cells.length;
	for (var i=0; i<n; i++) {
		if (cells[i].nodeType == 1) {
			if (rowSpanIndexes[i]) {
				//var f = editor.editDocument.createElement(rowSpanIndexes[i].tagName);
				var f = rowSpanIndexes[i].cloneNode(false);
				//WPro.addAttributes(f, rowSpanIndexes[i].attributes, rowSpanIndexes[i]);
				f.removeAttribute('rowSpan')
				this.formatNewCell(f);
				newRow.appendChild(f);
			}
			//var f = editor.editDocument.createElement(cells[i].tagName);
			//WPro.addAttributes(f, cells[i].attributes, cells[i]);
			var f = cells[i].cloneNode(false);
			f.removeAttribute('rowSpan')
			this.formatNewCell(f);
			newRow.appendChild(f);
		}
	}
	var parentNode = this.getParent(row);
	if (position == 'above') {
		parentNode.insertBefore(newRow, row);
	} else {
		if (parseInt(cell.getAttribute('rowSpan')) > 1) {
			var rs = parseInt(cell.getAttribute('rowSpan'));
			while (rs > 1) {
				row = this.getNextSibling(row);
				rs--;
			}
		}
		parentNode.insertBefore(newRow, this.getNextSibling(row) );
	}
		
	this.post(UDBeforeState);
	
}
	
wproPlugin_wproCore_table.prototype.deleteColumn = function (cell) {
	var UDBeforeState = this.pre();
	var editor=WPro.editors[this.editor];
	if (!cell) {
		cell = this.getCurrentCell();
	}
	var row = WPro.getParentNodeByTagName(cell, 'TR');//this.getParent(cell);
	// find index of current cell;
	var cellIndex = this.getCellIndex(cell);
	cellIndex ++
	this.processColumn(WPro.getParentNodeByTagName(row, 'TABLE'), cellIndex, 'delete');
	
	this.post(UDBeforeState);
}

wproPlugin_wproCore_table.prototype.insertColumn = function (position, cell) {
	var UDBeforeState = this.pre();
	var editor=WPro.editors[this.editor];
	//var range = editor.selAPI.getRange();
	if (!cell) {
		cell = this.getCurrentCell();
	}
	var row = WPro.getParentNodeByTagName(cell, 'TR');//this.getParent(cell);
	var cellIndex = this.getCellIndex(cell, position);


	//this.processColumn(this.getParent(row), cellIndex, 'add');
	this.processColumn(WPro.getParentNodeByTagName(row, 'TABLE'), cellIndex, 'add');		
	
	this.post(UDBeforeState);
}
	
wproPlugin_wproCore_table.prototype.processColumn = function (table, cellIndex, action) {
	var editor=WPro.editors[this.editor];
	//if (!cell) {
		//cell = this.getCurrentCell();
	//}
	//var row = this.getParent(cell);
	// find index of current cell;
	//var cellIndex = this.getCellIndex(cell);


	// we now have the index at which the new cell should be added
	// now loop through each row and each of their siblings adding a new cell at the correct index
	// if encounter a cell that spans the index, increase that cells colSpan
	// if encounter a cell that spans rows record the cells index for the next row
	//var rowSpanIndexes = [];
	
	
	///var rows = table.childNodes;
	var rows = table.rows
	
	var n = rows.length;
	for (var i=0; i<n; i++) {
		if (rows[i].nodeType == 1) {
			
			if (cellIndex == 0) {
				this.insertCell('before', rows[i].cells[0])
			} else {
				var cells = rows[i].cells
				var n2 = cells.length;
				var rowSpanIndexes = this.getRowIntersections(rows[i]);
				
				var ci = 0;
				
				for (var j=0; j<n2; j++) {
					if (cells[j].nodeType == 1) {
						if (rowSpanIndexes[j]) {
							if (parseInt(rowSpanIndexes[j].getAttribute('colSpan')) > 1) {
								ci += parseInt(rowSpanIndexes[j].getAttribute('colSpan'));
								/*if (ci > cellIndex) {
									if (action == 'add') {
										rowSpanIndexes[j].setAttribute('colSpan', parseInt(rowSpanIndexes[j].getAttribute('colSpan')) + 1);
									} else {
										rowSpanIndexes[j].setAttribute('colSpan', parseInt(rowSpanIndexes[j].getAttribute('colSpan')) - 1);
									}
									//break;
								}*/
							} else {
								ci ++
							}
						}
						if (parseInt(cells[j].getAttribute('colSpan')) > 1) {
							ci += parseInt(cells[j].getAttribute('colSpan'));
							if (ci > cellIndex) {
								if (action == 'add') {
									cells[j].setAttribute('colSpan', parseInt(cells[j].getAttribute('colSpan')) + 1);
								} else {
									cells[j].setAttribute('colSpan', parseInt(cells[j].getAttribute('colSpan')) - 1);
								}
								break;
							}
						
						} else {
							ci ++
						}
						if (ci >= cellIndex) {
							if (action == 'add') {
								
								this.insertCell('after', cells[j])
							} else {
								rows[i].deleteCell(cells[j].cellIndex);
							}
							break;
						} 
					}
				}
			}
			
			
			
			
			
			//var cells = rows[i].childNodes;
			/*var cells = rows[i].cells
			var n2 = cells.length;
			var ci = 0
			if (cellIndex == 0) {
				this.insertCell('before', cells[0])
			} else {
				for (var j=0; j<n2; j++) {
					if (cells[j].nodeType == 1) {
						if (rowSpanIndexes[ci]) {
							var tci = ci;
							ci += rowSpanIndexes[tci].colSpan;
							rowSpanIndexes[tci].rowSpan -= 1;
							if (rowSpanIndexes[tci].rowSpan == 1) {
								rowSpanIndexes[tci] = null;
							}
							if (ci == cellIndex) {
								if (action == 'add') {
									this.insertCell('before', cells[j])
								}
								break;
							} 
						}
						if (parseInt(cells[j].getAttribute('rowSpan')) > 1) {
							// record the rowSpan, then at this cell index in subsequent rows increase the cellindex
							rowSpanIndexes[ci] = new Object;
							var c = cells[j].getAttribute('colSpan') ? parseInt(cells[j].getAttribute('colSpan')) : 1;
							var s = parseInt(cells[j].getAttribute('rowSpan'));
							rowSpanIndexes[ci].colSpan = c
							rowSpanIndexes[ci].rowSpan = s
						}
						// if next cell is in a colSpan
						if (parseInt(cells[j].getAttribute('colSpan')) > 1) {
							ci += parseInt(cells[j].getAttribute('colSpan'));
							if (ci > cellIndex) {
								if (action == 'add') {
									cells[j].setAttribute('colSpan', parseInt(cells[j].getAttribute('colSpan')) + 1);
								} else {
									cells[j].setAttribute('colSpan', parseInt(cells[j].getAttribute('colSpan')) - 1);
								}
								break;
							}
						} else {
							ci ++
						}
						if (ci == cellIndex) {
							if (action == 'add') {
								this.insertCell('after', cells[j])
							} else {
								rows[i].deleteCell(cells[j].cellIndex);
							}
							break;
						} 
					}
				}
			}*/
		}
	}


}


wproPlugin_wproCore_table.prototype.insertCell = function(position, cell) {
	var editor=WPro.editors[this.editor];
	if (!cell) {
		cell = this.getCurrentCell();
	}
	//var newCell = editor.editDocument.createElement(cell.nodeName);
	var newCell = cell.cloneNode(false);

	//WPro.addAttributes(newCell, cell.attributes, cell);
	if (parseInt(newCell.getAttribute('colSpan')) > 1) {
		newCell.removeAttribute('width');
		WPro.removeStyleAttribute(newCell, 'width');
	}
	newCell.removeAttribute('rowSpan');
	newCell.removeAttribute('colSpan');

	this.formatNewCell(newCell);
	
	var parent = this.getParent(cell);
	if (position == 'before') {
		parent.insertBefore(newCell, cell);
	} else {
		parent.insertBefore(newCell, this.getNextSibling(cell));
	}	
}
wproPlugin_wproCore_table.prototype.formatNewCell = function (newCell) {
	var editor=WPro.editors[this.editor];
	var v = editor.newCellInners;
	if (v=='auto') {
		if (editor.lineReturns == 'br') {
			var b = editor.editDocument.createElement('BR');
			newCell.appendChild(b);
		} else if (editor.lineReturns == 'p') {
			var b = editor.editDocument.createElement('P');
			//b.innerHTML = '&nbsp;';
			var t = editor.editDocument.createTextNode(String.fromCharCode(160));
			b.appendChild(t);
			newCell.appendChild(b);
		} else {
			var b = editor.editDocument.createElement('DIV');
			//b.innerHTML = '&nbsp;';
			var t = editor.editDocument.createTextNode(String.fromCharCode(160));
			b.appendChild(t);
			newCell.appendChild(b);
		}
	} else {
		newCell.innerHTML = editor.newCellInners;	
	}
}
wproPlugin_wproCore_table.prototype.deleteTable = function (table) {
	var UDBeforeState = this.pre();
	var editor=WPro.editors[this.editor];
	if (!table) {
		table = this.getCurrentTable();
	}
	if (table) {
		table.parentNode.removeChild(table);
	}
	this.post(UDBeforeState);
}
wproPlugin_wproCore_table.prototype.countCols = function (table) {
	var rows = table.rows
	var cells = rows[0].cells
	var n = cells.length;
	var colCount = 0;
	for (var i=0; i<n; i++) {
		if (cells[i].nodeType == 1) {
			if (parseInt(cells[i].getAttribute('colSpan')) > 1) {
				colCount += parseInt(cells[i].getAttribute('colSpan'))
			} else {
				colCount ++
			}
		}
	}
	return colCount;	
}
wproPlugin_wproCore_table.prototype.autoFitCols = function (table) {
	var editor=WPro.editors[this.editor];
	if (!table) {
		table = this.getCurrentTable();
	}
	var rows = table.rows;
	var n = rows.length;
	for (var i=0; i<n; i++) {
		if (rows[i].nodeType == 1) {
			var cells = rows[i].cells;
			var n2 = cells.length;
			for (var j=0; j<n2; j++) {
				if (cells[j].nodeType == 1) {
					cells[j].removeAttribute('width');
					if (cells[j].style) {
						WPro.removeStyleAttribute(cells[j], 'width');
					}
				}
			}
		}
	}
	editor.redraw();
}
wproPlugin_wproCore_table.prototype.distCols = function (table) {
	var editor=WPro.editors[this.editor];
	if (!table) {
		table = this.getCurrentTable();
	}
	
	var numCols = this.countCols(table);
	var p = 100/numCols;
	
	this.autoFitCols(table);
	var rows = table.rows
	var cells = rows[0].cells
	var n = cells.length;
	for (var i=0; i<n; i++) {
		if (cells[i].nodeType == 1) {
			if (parseInt(cells[i].getAttribute('colSpan')) > 1) {
				var c = parseInt(cells[i].getAttribute('colSpan'))
				if (editor.strict) {
					cells[i].style.width = (p*c)+'%';
					cells[i].removeAttribute('width');
				} else {
					cells[i].setAttribute('width', (p*c)+'%');
				}
			} else {
				if (editor.strict) {
					cells[i].style.width = p+'%';
					cells[i].removeAttribute('width');
				} else {
					cells[i].setAttribute('width', p+'%');
				}
			}
		}
	}
	editor.redraw();
}