var CELL = null;
var CELL_IDX = 0;
var ROW = null;
var ROW_IDX = 0;
var TABLE = null;
var tableEditor = null;
var previousSpacing = '0';
function initEditTable () {
	// get cells
	tableEditor = currentEditor.tableEditor;
	var range = currentEditor.selAPI.getRange();
	//wproCurrentRange = range;
	if (range.type=='control') {
		if (range.nodes[0].tagName == 'TABLE') {
			TABLE = range.nodes[0];
		}
	}
	if (!TABLE) {
		TABLE = range.getContainerByTagName('TABLE');
	}
	ROW = range.getContainerByTagName('TR');
	CELL = range.getContainerByTagName('TD');
	if (!CELL) {
		CELL = range.getContainerByTagName('TH');
	}
	if (!CELL) {
		CELL = TABLE.rows.item(0).cells.item(0);
	}
	if (!ROW) {
		ROW = TABLE.rows.item(0);
	}
	
	if (CELL) {
		initCell();
	} else {
		var a = document.getElementsByTagName('A');
		a.item(1).style.display='none';
	}
	if (ROW) {
		initRow();
	} else {
		a = document.getElementsByTagName('A');
		a.item(2).style.display='none';
	}
	initTable();
}

function borderCollapseChange(value) {
	var form = document.dialogForm;
	if (form.elements['tableBorderCollapse'] && form.elements['tableCellSpacing']) {
		if (value) {
			form.elements['tableCellSpacing'].disabled=true;
			
			previousSpacing=form.elements['tableCellSpacing'].value;
			form.elements['tableCellSpacing'].value='0';
			
		} else {
			form.elements['tableCellSpacing'].disabled=false;
			
			form.elements['tableCellSpacing'].value=previousSpacing;
			
		}
	}
}

/* these function populate the dialog */
function initCell() {
	removeHighlight(TABLE);	
	showCellCount();
	
	var form = document.dialogForm;
	var a;
	if (form.cellWidth) {
		var width;
		if (a=CELL.style.width) {
			width = a;
		} else if (a=CELL.getAttribute('width')) {
			width = a;
		}
		if (width) {
			if (width.search('%') != -1 ) {
				form.cellWidthUnits.value = '%';
			} else {
				form.cellWidthUnits.value = 'px';
			}
			form.cellWidth.value = width.replace(/[^0-9.]/gi,'');
		} else {
			form.cellWidth.value='';	
		}
	}
	if (form.cellHeight) {
		var height;
		if (a=CELL.style.height) {
			height = a;
		} else if (a=CELL.getAttribute('height')) {
			height = a;
		}
		if (height) {
			if (height.search('%') != -1 ) {
				form.cellHeightUnits.value = '%';
			} else {
				form.cellHeightUnits.value = 'px';
			}
			form.cellHeight.value = height.replace(/[^0-9.]/gi,'');
		} else {
			form.cellHeight.value='';	
		}
	}
	if (form.cellvAlign) {
		if (a=CELL.getAttribute('vAlign')) {
			form.cellvAlign.setValue(a);
		} else {
			form.cellvAlign.setValue('');
		}
	}
	if (form.cellType) {
		if (CELL.tagName == 'TH') {
			if (a=CELL.getAttribute('scope')) {
				form.cellType.value = a;
			} else {
				form.cellType.value = 'heading';
			}
		} else {
			form.cellType.value = 'normal';
		}
	}
	if (form.cellBackgroundColor) {
		var color;
		if (a=CELL.style.backgroundColor) {
			color = a;
		} else if (a=CELL.getAttribute('bgColor')) {
			color = a;
		}
		if (color) {
			form.cellBackgroundColor.setColor(color);
		} else {
			form.cellBackgroundColor.setColor('');
		}
	}
	if (form.cellBackgroundImage) {
		var image;
		if (a=CELL.style.backgroundImage) {
			image = dialog.urlFormatting(a.replace(/url\(([\s'"]*|)([^)]*)([\s'"]*|)\)/,'$2'));
		} else if (a=CELL.getAttribute('background')) {
			image = a;
		}
		if (image) {
			form.cellBackgroundImage.value = dialog.urlFormatting(image);
		} else {
			form.cellBackgroundImage.value = '';
		}
	}
	dialog.selectCurrentStyle(form.elements['cellStyle']);
	highlightCell(CELL);
	dialog.focus();
}
function initRow() {
	removeHighlight(TABLE);	
	showRowCount()

	var form = document.dialogForm;
	var a;
	if (form.rowvAlign) {
		if (a=ROW.getAttribute('vAlign')) {
			form.rowvAlign.setValue(a);
		} else {
			form.rowvAlign.setValue('');
		}
		changeCellVAlignDefault()
	}
	if (form.rowBackgroundColor) {
		var color;
		if (a=ROW.style.backgroundColor) {
			color = a;
		} else if (a=ROW.getAttribute('bgColor')) {
			color = a;
		}
		if (color) {
			form.rowBackgroundColor.setColor(color);
		} else {
			form.rowBackgroundColor.setColor('');
		}
	}
	dialog.selectCurrentStyle(form.elements['rowStyle']);

	highlightCells(ROW.childNodes);
	dialog.focus();
}
function changeCellVAlignDefault() {
	var form = document.dialogForm;
	var a;
	if (form.rowvAlign) {
		if (form.cellvAlign) {
			var s = '';
			switch(form.rowvAlign.value) {
				case 'top':
					s = dialog.themeURL+'misc/td.top.gif';
					break;
				case 'bottom':
					s = dialog.themeURL+'misc/td.bottom.gif';
					break;
				case 'middle':
				default:
					s = dialog.themeURL+'misc/td.middle.gif';
					break;
			}
			form.cellvAlign.swapImage('', s);
		}
	}
}
function initTable() {
	removeHighlight(TABLE);	

	var form = document.dialogForm;
	var a;
	if (form.tableWidth) {
		var width;
		if (a=TABLE.style.width) {
			width = a;
		} else if (a=TABLE.getAttribute('width')) {
			width = a;
		}
		if (width) {
			if (width.search('%') != -1 ) {
				form.tableWidthUnits.value = '%';
			} else {
				form.tableWidthUnits.value = 'px';
			}
			form.tableWidth.value = width.replace(/[^0-9.]/gi,'');
		} else {
			form.tableWidth.value = '';
		}
	}
	if (form.tableHeight) {
		var height;
		if (a=TABLE.style.height) {
			height = a;
		} else if (a=TABLE.getAttribute('height')) {
			height = a;
		}
		if (height) {
			if (height.search('%') != -1 ) {
				form.tableHeightUnits.value = '%';
			} else {
				form.tableHeightUnits.value = 'px';
			}
			form.tableHeight.value = height.replace(/[^0-9.]/gi,'');
		} else {
			form.tableHeight.value = '';
		}
	}
	if (form.tableAlign) {
		if (a=TABLE.style.cssFloat) {
			form.tableAlign.setValue(a);
		} else if (a=TABLE.style.styleFloat) {
			form.tableAlign.setValue(a);
		} else if (a=TABLE.getAttribute('align')) {
			form.tableAlign.setValue(a);
		} else {
			form.tableAlign.setValue('');
		}
	}
	if (form.tableBackgroundColor) {
		var color;
		if (a=TABLE.style.backgroundColor) {
			color = a;
		} else if (a=TABLE.getAttribute('bgColor')) {
			color = a;
		}
		if (color) {
			form.tableBackgroundColor.setColor(color);
		} else {
			form.tableBackgroundColor.setColor('');
		}
	}
	if (form.tableBackgroundImage) {
		var image;
		if (a=TABLE.style.backgroundImage) {
			image = dialog.urlFormatting(a.replace(/url\(([\s'"]*|)([^)]*)([\s'"]*|)\)/,'$2'));
		} else if (a=TABLE.getAttribute('background')) {
			image = a;
		}
		if (image) {
			form.tableBackgroundImage.value = dialog.urlFormatting(image);
		} else {
			form.tableBackgroundImage.value = '';	
		}
	}
	if (form.tableBorderColor) {
		var color;
		if (a=TABLE.style.borderTopColor) {
			color = a;
		} else if (a=TABLE.getAttribute('borderColor')) {
			color = a;
		}
		if (color) {
			form.tableBorderColor.setColor(color);
		} else {
			form.tableBorderColor.setColor('');
		}
	}
	if (form.tableBorderCollapse) {
		var collapse;
		if (a=TABLE.style.borderCollapse) {
			collapse = a;
		}
		if (collapse == 'collapse') {
			form.tableBorderCollapse.checked = true;
		} else {
			form.tableBorderCollapse.checked = false;
		}
	}
	if (form.tableBorder) {
		var border;
		a=TABLE.getAttribute('border')
		border = a;
		
		//if (border) {
		form.tableBorder.value = border;
		//}
	}
	if (form.tableCellSpacing) {
		var spacing;
		a=TABLE.getAttribute('cellSpacing')
		spacing = a;
		
		//if (border) {
		form.tableCellSpacing.value = spacing;
		previousSpacing = spacing;
		//}
	}

	if (form.tableCellPadding) {
		var padding;
		a=TABLE.getAttribute('cellPadding')
		padding = a;
		
		//if (border) {
		form.tableCellPadding.value = padding;
		//}
	}
	
	if (form.tableSummary) {
		var summary;
		if (a=TABLE.getAttribute('summary')) {
			summary = a;
		}
		if (summary) {
			form.tableSummary.value = summary;
		} else {
			form.tableSummary.value = '';
		}
	}
	
	
	if (form.tableCaption) {
		var caption = TABLE.getElementsByTagName("CAPTION");
		if (caption.length) {
			form.tableCaption.value = caption.item(0).innerHTML;
			if (form.tableCaptionAlign) {
				var align = '';
				if (a=caption.item(0).style.captionSide) {
					align = a;
				} else if (a=caption.item(0).getAttribute('align')) {
					align = a;
				}
			}
		} else {
			form.tableCaption.value = '';
			if (form.tableCaptionAlign) {
				form.tableCaptionAlign.value = '';
			}
		}
	}
	
	borderCollapseChange(form.tableBorderCollapse.checked)

	dialog.selectCurrentStyle(form.elements['tableStyle']);

	dialog.focus();
}

/* these function are called when swapping tabs */
function tabCell() {
	removeHighlight(TABLE);	
	if (CELL) {
		highlightCell(CELL);
	}
}
function tabRow() {
	removeHighlight(TABLE);	
	if (ROW) {
		highlightCells(ROW.childNodes);
	}
}
function tabTable() {
	removeHighlight(TABLE);
}


/* these functions apply changes */
function applyCell() {
	
	
	var form = document.dialogForm;
	var a;
	
	/* first validate fields */
	if (form.cellWidth) {
		var width = form.cellWidth.value;
		if (isNaN(width)&&width.length>0) {
			dialog.alertWrongFormat();
			tUI1.swapTab(2);
			tUI4.swapTab(0);
			dialog.focus();
			form.cellWidth.value='';
			form.cellWidth.focus();
			return false;
		}
	}
	if (form.cellHeight) {
		var height = form.cellHeight.value;
		if (isNaN(height)&&height.length>0) {
			dialog.alertWrongFormat();
			tUI1.swapTab(2);
			tUI4.swapTab(0);
			dialog.focus();
			form.cellHeight.value='';
			form.cellHeight.focus();
			return false;
		}
	}
	
	/* then update table */
	removeHighlight(TABLE);
	var UDBeforeState = currentEditor.history.pre();
	
	if (form.cellWidth) {
		if (a=CELL.style.width||currentEditor.strict) {
			if (width!='') {
				CELL.style.width = width + form.cellWidthUnits.value;
			} else {
				WPro.removeStyleAttribute(CELL, 'width');
			}
			CELL.removeAttribute('width');
		} else if (a=CELL.getAttribute('width') && width == '') {
			CELL.removeAttribute('width');
		} else if (width!='') {
			CELL.setAttribute('width', width + (form.cellWidthUnits.value=='%'?'%':''));
		}
		// fix widths on other cells in column
			// column
		if (width!='') {
			var cellidx = CELL.cellIndex
			var rows = TABLE.rows
			var n=rows.length
			for (var i = 0; i < n; i++) {
				if (rows[i].cells[cellidx]) {
					if (rows[i].cells[cellidx] != CELL && rows[i].cells[cellidx].rowSpan == CELL.rowSpan) {
						if (rows[i].cells[cellidx].width) {
							//rows[i].cells[cellidx].setAttribute("WIDTH", width + (form.cellWidthUnits.value=='%'?'%':''),0);
							rows[i].cells[cellidx].removeAttribute('width');
						}
						if (rows[i].cells[cellidx].style.width) {
							//rows[i].cells[cellidx].style.width = setAttribute("WIDTH", width + (form.cellWidthUnits.value=='%'?'%':''),0);
							WPro.removeStyleAttribute(rows[i].cells[cellidx], 'width');
						}
					}	
				}
			}
		}
	}
	
	
	if (form.cellHeight) {
		if (a=CELL.style.height||currentEditor.strict) {
			if (height!='') {
				CELL.style.height = height + form.cellHeightUnits.value;
			} else {
				WPro.removeStyleAttribute(CELL, 'height');
			}
			CELL.removeAttribute('height');
		} else if (a=CELL.getAttribute('height') && height == '') {
			CELL.removeAttribute('height');
		} else if (height!='') {
			CELL.setAttribute('height', height + (form.cellHeightUnits.value=='%'?'%':''));
		}
		// fix height on other cells in row
		// row
		if (height!='') {
			var row = WPro.getParentNodeByTagName(CELL, 'TR');
			var cells = row.cells
			var n=cells.length
			for (var i = 0; i < n; i++) {
				if (cells[i] != CELL && cells[i].colSpan == CELL.colSpan) {
					if (cells[i].height) {
						//cells[i].setAttribute("HEIGHT", document.getElementById('td_height').value,0);
						cells[i].removeAttribute("height");
					}
					if (cells[i].style.height) {
						//cells[i].setAttribute("HEIGHT", document.getElementById('td_height').value,0);
						WPro.removeStyleAttribute(cells[i], "height");
					}
				}	
			}
		}
	}

	if (form.cellvAlign) {
		var vAlign = form.cellvAlign.value;
		if (vAlign!='') {
			CELL.setAttribute('vAlign', vAlign);
		} else {
			CELL.removeAttribute('vAlign');
		}
	}
	
	if (form.cellBackgroundColor) {
		var color = form.cellBackgroundColor.value;
		if (CELL.style.backgroundColor||currentEditor.strict) {
			if (color!='') {
				CELL.style.backgroundColor = color;
			} else {
				WPro.removeStyleAttribute(CELL, 'backgroundColor');
			}
			CELL.removeAttribute('bgColor');
		} else if (CELL.getAttribute('bgColor') && color == '') {
			CELL.removeAttribute('bgColor');
		} else if (color!='') {
			CELL.setAttribute('bgColor', color);
		}
	}
	if (form.cellBackgroundImage) {
		var image = form.cellBackgroundImage.value;
		if (CELL.style.backgroundImage||currentEditor.strict) {
			if (image!='') {
				CELL.style.backgroundImage = 'url("'+dialog.urlFormatting(image)+'")';
			} else {
				WPro.removeStyleAttribute(CELL, 'backgroundImage');
			}
			CELL.removeAttribute('background');
		} else if (CELL.getAttribute('background') && image == '') {
			CELL.removeAttribute('background');
		} else if (image!='') {
			CELL.setAttribute('background', image);
		}
	}
	
	// change cell type and create new CELL reference
	if (form.cellType) {
		
		var value = form.cellType.value;
		if (CELL.tagName == 'TH' && value=='normal') {
			var ne = currentEditor.editDocument.createElement('TD');
			
			WPro.addAttributes(ne, CELL.attributes, CELL);
			
			if (CELL.childNodes) {
				var cn = CELL.childNodes;
				for (var i=0; i<cn.length; i++) {
					ne.appendChild(cn[i].cloneNode(true));
				}
			}
			CELL.parentNode.insertBefore(ne, CELL.nextSibling);
			CELL.parentNode.removeChild(CELL);
			//CELL.parentNode.replaceChild(CELL, ne);
			CELL = ne;
		} else if (CELL.tagName == 'TD' && (value=='heading'||value=='row'||value=='col')) {
			var ne = currentEditor.editDocument.createElement("TH");
			
			WPro.addAttributes(ne, CELL.attributes, CELL);
			
			if (CELL.childNodes) {
				var cn = CELL.childNodes;
				for (var i=0; i<cn.length; i++) {
					ne.appendChild(cn[i].cloneNode(true));
				}
			}
			CELL.parentNode.insertBefore(ne, CELL.nextSibling);
			CELL.parentNode.removeChild(CELL);
			//CELL.parentNode.replaceChild(CELL, ne);
			CELL = ne;
		}
		
		// incase cell has changed find it using the idx
		var rows = TABLE.rows
		var n = rows.length;
		var found = false;
		var cellCount = 1;
		for (var i=0; i<n; i++) {
			var cells = rows[i].cells;
			var k = cells.length;
			for (var j=0; j<k; j++) {
				if (cellCount == CELL_IDX) {
					CELL = cells[j];
				}
				cellCount ++;
			}
			if (found) break;
		}

		
		if (value == 'row') {
			CELL.setAttribute('scope', 'row');
		} else if (value=='col') {
			CELL.setAttribute('scope', 'col');
		} else {
			CELL.removeAttribute('scope');
		}
		
		
	}
	
	var style = form.elements['cellStyle'].value
	if (style!='') {
		currentEditor.applyStyle(style, [CELL], /^(TH|TD)$/i);
		// incase cell has changed find it using the idx
		var rows = TABLE.rows
		var n = rows.length;
		var found = false;
		var cellCount = 1;
		for (var i=0; i<n; i++) {
			var cells = rows[i].cells;
			var k = cells.length;
			for (var j=0; j<k; j++) {
				if (cellCount == CELL_IDX) {
					CELL = cells[j];
				}
				cellCount ++;
			}
			if (found) break;
		}
	}
		
	currentEditor.history.post(UDBeforeState);
	
	if (style!='') {
		initCell();	
	}
	dialog.focus();
	return true;
}
function convertCells (cells, to, scope) {
	for (var i=0; i<cells.length;i++) {
		if (to=='TH') {
			if (cells[i].tagName == 'TD') {
				var ne = currentEditor.editDocument.createElement("TH");
				WPro.addAttributes(ne, cells[i].attributes, cells[i]);
				if (cells[i].childNodes) {
					var cn = cells[i].childNodes;
					for (var j=0; j<cn.length; j++) {
						ne.appendChild(cn[j].cloneNode(true));
					}
				}
				cells[i].parentNode.insertBefore(ne, cells[i].nextSibling);
				cells[i].parentNode.removeChild(cells[i]);
				//cells[i].parentNode.replaceChild(cells[i], ne);
				if (scope) {
					ne.setAttribute('scope', scope);
				} else {
					ne.removeAttribute('scope');
				}
			} else {
				if (scope) {
					cells[i].setAttribute('scope', scope);
				} else {
					cells[i].removeAttribute('scope');
				}
			}
		} else if (to=='TD') {
			if (cells[i].tagName == 'TH') {
				var ne = currentEditor.editDocument.createElement("TD");
				WPro.addAttributes(ne, cells[i].attributes, cells[i]);
				ne.removeAttribute('scope');
				if (cells[i].childNodes) {
					var cn = cells[i].childNodes;
					for (var j=0; j<cn.length; j++) {
						ne.appendChild(cn[j].cloneNode(true));
					}
				}
				cells[i].parentNode.insertBefore(ne, cells[i].nextSibling);
				cells[i].parentNode.removeChild(cells[i]);
				//cells[i].parentNode.replaceChild(cells[i], ne);
			}
		}	
	}
}
function applyRow() {
	removeHighlight(TABLE);
	var UDBeforeState = currentEditor.history.pre();

	var form = document.dialogForm;
	var a;
	if (form.rowvAlign) {
		var vAlign = form.rowvAlign.value;
		if (vAlign!='') {
			ROW.setAttribute('vAlign', vAlign);
		} else {
			ROW.removeAttribute('vAlign');
		}
	}
	if (form.rowBackgroundColor) {
		var color = form.rowBackgroundColor.value;
		if (ROW.style.backgroundColor||currentEditor.strict) {
			if (color!='') {
				ROW.style.backgroundColor = color;
			} else {
				WPro.removeStyleAttribute(ROW, 'backgroundColor');
			}
			ROW.removeAttribute('bgColor');
		} else if (ROW.getAttribute('bgColor') && color == '') {
			ROW.removeAttribute('bgColor');
		} else if (color!='') {
			ROW.setAttribute('bgColor', color);
		}
	}
	
	var style = form.elements['rowStyle'].value
	if (style!='') {
		currentEditor.applyStyle(style, [ROW]);
	}
	
	if (form.overrideCellAlignment) {
		if (form.overrideCellAlignment.checked==true) {
			var cells = ROW.cells;
			for (var i=0; i<cells.length;i++) {
				cells[i].removeAttribute('vAlign');	
			}
		}
		form.overrideCellAlignment.checked=false;
	}
	
	if (form.overrideCellBackground) {
		if (form.overrideCellBackground.checked==true) {
			var cells = ROW.cells;
			for (var i=0; i<cells.length;i++) {
				WPro.removeStyleAttribute(cells[i], 'backgroundColor');	
				cells[i].removeAttribute('bgcolor');	
			}
		}
		form.overrideCellBackground.checked=false;
	}
	
	// options
	if (form.convertCells) {
		if (form.doConvertCells.checked==true) {
			var value = form.convertCells.value
			switch (value) {
				case 'normal':
					convertCells(ROW.cells, 'TD');
					break;
				case 'heading' :
					convertCells(ROW.cells, 'TH');
					break;
				case 'col' :
					convertCells(ROW.cells, 'TH', 'col');
					break;
				case 'row' :
					convertCells(ROW.cells, 'TH', 'row');
					break;
				
			}
		}
		form.doConvertCells.checked=false;
	}
	
	currentEditor.history.post(UDBeforeState);
	
	if (style!='') {
		initRow();	
	}
	dialog.focus();
	return true;
}
function applyTable() {
	
	var form = document.dialogForm;
	var a;
	
	/* first validate fields */
	if (form.tableWidth) {
		var width = form.tableWidth.value;
		if (isNaN(width)&&width.length>0) {
			dialog.alertWrongFormat();
			tUI1.swapTab(0);
			tUI2.swapTab(0);
			dialog.focus();
			form.tableWidth.value='';
			form.tableWidth.focus();
			return false;
		}
	}
	if (form.tableHeight) {
		var height = form.tableHeight.value;
		if (isNaN(height)&&height.length>0) {
			dialog.alertWrongFormat();
			tUI1.swapTab(0);
			tUI2.swapTab(0);
			dialog.focus();
			form.tableHeight.value='';
			form.tableHeight.focus();
			return false;
		}
	}
	if (form.tableBorder) {
		var border = form.tableBorder.value;
		if (isNaN(border)&&border.length>0) {
			dialog.alertWrongFormat();
			tUI1.swapTab(0);
			tUI2.swapTab(1);
			dialog.focus();
			form.tableBorder.value='';
			form.tableBorder.focus();
			return false;
		}
	}
	if (form.tableCellSpacing) {
		var spacing = form.tableCellSpacing.value;
		if (isNaN(spacing)&&spacing.length>0) {
			dialog.alertWrongFormat();
			tUI1.swapTab(0);
			tUI2.swapTab(1);
			dialog.focus();
			form.tableCellSpacing.value='';
			form.tableCellSpacing.focus();
			return false;
		}
	}
	if (form.tableCellPadding) {
		var padding = form.tableCellPadding.value;
		if (isNaN(padding)&&padding.length>0) {
			dialog.alertWrongFormat();
			dialog.focus();
			tUI1.swapTab(0);
			tUI2.swapTab(1);
			form.tableCellPadding.value='';
			form.tableCellPadding.focus();
			return false;
		}
	}
	
	/* then update table */
	removeHighlight(TABLE);
	var UDBeforeState = currentEditor.history.pre();
	
	if (form.tableWidth) {
		if (TABLE.style.width) {
			if (width!='') {
				TABLE.style.width = width + form.tableWidthUnits.value;
			} else {
				WPro.removeStyleAttribute(TABLE, 'width');
			}
		} else if (TABLE.getAttribute('width') && width == '') {
			TABLE.removeAttribute('width');
		} else if (width!='') {
			TABLE.setAttribute('width', width + (form.tableWidthUnits.value=='%'?'%':''));
		}
	}
	if (form.tableHeight) {
		if (TABLE.style.height||currentEditor.strict) {
			if (height!='') {
				TABLE.style.height = height + form.tableHeightUnits.value;
			} else {
				WPro.removeStyleAttribute(TABLE, 'height');
			}
			TABLE.removeAttribute('height');
		} else if (TABLE.getAttribute('height') && height == '') {
			TABLE.removeAttribute('height');
		} else if (height!='') {
			TABLE.setAttribute('height', height + (form.tableHeightUnits.value=='%'?'%':''));
		}
	}

	if (form.tableAlign) {
		var align = form.tableAlign.value;
		if (TABLE.style.cssFloat||TABLE.style.styleFloat||currentEditor.strict) {
			if (align!=''&&align!='center') {
				if (dialog.isIE) {
					TABLE.style.styleFloat = align;
				} else {
					TABLE.style.cssFloat = align;
				}
			} else {
				WPro.removeStyleAttribute(TABLE, 'float');	
				if (align=='center') {
					TABLE.setAttribute('align', align);
				}
			}
		} else {
			if (align!='') {
				TABLE.setAttribute('align', align);
			} else {
				TABLE.removeAttribute('align');
			}
		}
	}
	
	if (form.tableCellSpacing) {
		if (spacing!='') {
			TABLE.setAttribute('cellSpacing', spacing);
		} else {
			TABLE.removeAttribute('cellSpacing');
		}
	}
	if (form.tableCellPadding) {
		if (padding!='') {
			TABLE.setAttribute('cellPadding', padding);
		} else {
			TABLE.removeAttribute('cellPadding');
		}
	}
	
	if (form.tableBorder) {
		if (border!='') {
			TABLE.setAttribute('border', border);
			if (currentEditor.strict) {
				TABLE.style.borderWidth = border+'px';
			}
		} else {
			TABLE.removeAttribute('border');
		}
		if ((border==''||parseInt(border)==0)&&currentEditor.strict) {
			WPro.removeStyleAttribute(TABLE, 'borderColor');
			WPro.removeStyleAttribute(TABLE, 'borderWidth');
			WPro.removeStyleAttribute(TABLE, 'borderStyle');
			WPro.removeStyleAttribute(TABLE, 'border');
			for (var i=0;i<TABLE.rows.length;i++) {
				for (var j=0;j<TABLE.rows[i].cells.length;j++) {
					WPro.removeStyleAttribute(TABLE.rows[i].cells[j], 'borderColor');
					WPro.removeStyleAttribute(TABLE.rows[i].cells[j], 'borderWidth');
					WPro.removeStyleAttribute(TABLE.rows[i].cells[j], 'borderStyle');
					WPro.removeStyleAttribute(TABLE.rows[i].cells[j], 'border');
				}
			}
		}
	}
	if (form.tableBorderColor) {
		var border = 1;
		if (form.tableBorder) {
			border = parseInt(form.tableBorder.value);
		}		
		var color = form.tableBorderColor.value;
		if ((TABLE.style.borderTopColor||currentEditor.strict)) {
			if (border > 0 && color) {
				TABLE.style.borderColor = color;
				TABLE.style.borderStyle = 'solid';
				for (var i=0;i<TABLE.rows.length;i++) {
					for (var j=0;j<TABLE.rows[i].cells.length;j++) {
						TABLE.rows[i].cells[j].style.border = '1px solid '+color;
					}
				}
			} else {
				WPro.removeStyleAttribute(TABLE, 'borderColor');
				WPro.removeStyleAttribute(TABLE, 'borderWidth');
				WPro.removeStyleAttribute(TABLE, 'borderStyle');
				WPro.removeStyleAttribute(TABLE, 'border');
				for (var i=0;i<TABLE.rows.length;i++) {
					for (var j=0;j<TABLE.rows[i].cells.length;j++) {
						WPro.removeStyleAttribute(TABLE.rows[i].cells[j], 'borderColor');
						WPro.removeStyleAttribute(TABLE.rows[i].cells[j], 'borderWidth');
						WPro.removeStyleAttribute(TABLE.rows[i].cells[j], 'borderStyle');
						WPro.removeStyleAttribute(TABLE.rows[i].cells[j], 'border');
					}
				}
			}
			TABLE.removeAttribute('borderColor');
		} else {
			if (color!='') {
				TABLE.setAttribute('borderColor', color);
			} else {
				TABLE.removeAttribute('borderColor');
			}
		}
	}
	if (form.tableBorderCollapse) {
		if (form.tableBorderCollapse.checked==true) {
			TABLE.style.borderCollapse = 'collapse';
		} else {
			WPro.removeStyleAttribute(TABLE, 'borderCollapse');
		}
	}
	
	if (form.tableBackgroundColor) {
		var color = form.tableBackgroundColor.value;
		if (TABLE.style.backgroundColor||currentEditor.strict) {
			if (color!='') {
				TABLE.style.backgroundColor = color;
			} else {
				WPro.removeStyleAttribute(TABLE, 'backgroundColor');
			}
			TABLE.removeAttribute('bgColor');
		} else if (TABLE.getAttribute('bgColor') && color == '') {
			TABLE.removeAttribute('bgColor');
		} else if (color!='') {
			TABLE.setAttribute('bgColor', color);
		}
	}
	if (form.tableBackgroundImage) {
		var image = form.tableBackgroundImage.value;
		if (TABLE.style.backgroundImage||currentEditor.strict) {
			if (image!='') {
				TABLE.style.backgroundImage = "url('"+dialog.urlFormatting(image)+"')";
			} else {
				WPro.removeStyleAttribute(TABLE, 'backgroundImage');
			}
			TABLE.removeAttribute('background');
		} else if (TABLE.getAttribute('background') && image == '') {
			TABLE.removeAttribute('background');
		} else if (image!='') {
			TABLE.setAttribute('background', image);
		}
	}
	
	if (form.tableSummary) {
		var summary = form.tableSummary.value;
		if (summary!='') {
			TABLE.setAttribute('summary', summary);
		} else {
			TABLE.removeAttribute('summary');
		}
	}
	
	if (form.tableCaption) {
		var caption = form.tableCaption.value;
		if (caption!='') {
			var c = TABLE.getElementsByTagName("CAPTION");
			if (c.length) {
				c.item(0).innerHTML = caption;
				if (form.tableCaptionAlign) {
					//c.item(0).setAttribute('align', form.tableCaptionAlign.value);
					var align = '';
					if (c.item(0).style.captionSide||currentEditor.strict) {
						if (form.tableCaptionAlign.value!='') {
							c.item(0).style.captionSide = form.tableCaptionAlign.value;
						} else {
							WPro.removeStyleAttribute(c.item(0), 'captionSide');
						}
						c.item(0).removeAttribute('align');
					} else if (c.item(0).getAttribute('align')) {
						if (form.tableCaptionAlign.value!='') {
							c.item(0).setAttribute('align', form.tableCaptionAlign.value);	
						} else {
							c.item(0).removeAttribute('align');
						}
					}
				}
			} else {
				var c = currentEditor.editDocument.createElement('CAPTION');
				if (form.tableCaptionAlign) {
					if (currentEditor.strict) {
						c.style.captionSide = form.tableCaptionAlign.value;
					} else {
						c.setAttribute('align', form.tableCaptionAlign.value);
					}
				}
				c.innerHTML = caption
				TABLE.insertBefore(c, TABLE.firstChild);
			}
			//TABLE.removeAttribute('summary');
		} else {
			var c = TABLE.getElementsByTagName("CAPTION");
			if (c.length) {
				c.item(0).parentNode.removeChild(c.item(0));
			}
		}
	}

	var style = form.elements['tableStyle'].value
	if (style!='') {
		currentEditor.applyStyle(style, [TABLE]);
	}

	
	currentEditor.history.post(UDBeforeState);
	
	if (style!='') {
		initTable();	
	}
	
	dialog.focus();
	return true;
}

/* these functions move the selection to the next cell or row */
function showCellCount() {
	var rows = TABLE.rows
	var n = rows.length;
	var found = false;
	var cellCount = 1;
	for (var i=0; i<n; i++) {
		var cells = rows[i].cells;
		var k = cells.length;
		for (var j=0; j<k; j++) {
			if (cells[j] == CELL) {
				found = true;
				break;
			}
			cellCount ++;
		}
		if (found) break;
	}
	CELL_IDX = cellCount;
	document.getElementById('cellNumber').innerHTML = strCellNumber.replace('##number##', cellCount);
}
function showRowCount() {
	var rows = TABLE.rows;
	var n = rows.length;
	var rowCount = 1;
	for (var i=0; i<n; i++) {
		if (rows[i] == ROW) {
			break;
		}
		rowCount ++;
	}
	ROW_IDX = rowCount;
	document.getElementById('rowNumber').innerHTML = strRowNumber.replace('##number##', rowCount);	
}

function nextCell() {
	if (!applyCell()) return;
	var rows = TABLE.rows
	var n = rows.length;
	var found = false;
	var changed = false;
	for (var i=0; i<n; i++) {
		var cells = rows[i].cells;
		var k = cells.length;
		for (var j=0; j<k; j++) {
			if (found) {
				CELL = cells[j];
				changed=true;
				break;
			}
			if (cells[j] == CELL) {
				found = true;
			}
			
		}
		if (found && changed) break;
	}
	if (found && ! changed) {
		CELL = TABLE.rows.item(0).cells.item(0);
	}
	initCell();
}
function previousCell() {
	if (!applyCell()) return;
	var rows = TABLE.rows
	var n = rows.length;
	var found = false;
	var changed = false;
	for (var i=n-1; i>=0; i--) {
		var cells = rows[i].cells;
		var k = cells.length;
		for (var j=k-1; j>=0; j--) {
			if (found) {
				CELL = cells[j];
				changed=true;
				break;
			}
			if (cells[j] == CELL) {
				found = true;
			}
			
		}
		if (found && changed) break;
	}
	if (found && ! changed) {
		CELL = TABLE.rows.item(rows.length-1).cells.item(cells.length-1);
	}
	initCell();
}
function nextRow() {
	if (!applyRow()) return;
	var rows = TABLE.rows;
	var n = rows.length;
	var found = false;
	var changed = false;
	for (var i=0; i<n; i++) {
		if (found) {
			ROW = rows[i];
			changed=true;
			break;
		}
		if (rows[i] == ROW) {
			found = true;
		}
	}
	if (found && ! changed) {
		ROW = rows[0];
	}
	initRow();
}
function previousRow() {
	if (!applyRow()) return;
	var rows = TABLE.rows;
	var n = rows.length;
	var found = false;
	var changed = false;
	for (var i=n-1; i>=0; i--) {
		if (found) {
			ROW = rows[i];
			changed=true;
			break;
		}
		if (rows[i] == ROW) {
			found = true;
		}
	}
	if (found && ! changed) {
		ROW = rows[n-1];
	}
	initRow();
}
function unloadDialog() {
	removeHighlight(TABLE);	
	if (wproCurrentRange) wproCurrentRange.select();
}
function formAction () {
	removeHighlight(TABLE);
	var ret = true;
	var UDBeforeState = currentEditor.history.pre();
	
	if (CELL) {
		if (!applyCell()) ret = false;
	}
	if (ROW) {
		if (!applyRow()) ret = false;
	}
	if (!applyTable()) ret = false;
	
	// redraw the editor
	if (dialog.isGecko) {
		wproCurrentRange = currentEditor.selAPI.getRange();
		wproCurrentScrollTop = currentEditor.editDocument.body.scrollTop + currentEditor.editDocument.documentElement.scrollTop;
		wproCurrentScrollLeft = currentEditor.editDocument.body.scrollTop + currentEditor.editDocument.documentElement.scrollTop;
		currentEditor.showGuidelines();
		currentEditor.editFrame.parentNode.style.height = currentEditor.editFrame.offsetHeight + 'px';
		currentEditor.editFrame.style.display = 'none';
		setTimeout("currentEditor.editFrame.style.display='';currentEditor.editFrame.parentNode.style.height='';currentEditor._enableDesignMode();currentEditor.editWindow.scrollTo(wproCurrentScrollLeft,wproCurrentScrollTop);dialog.focus();",1);
		//currentEditor.redrawTimeout();
	} else {
		currentEditor.redraw();	
	}
	
	currentEditor.history.post(UDBeforeState);
		
	if (!ret) return false;
	
	//dialog.close();
	return false;
}
