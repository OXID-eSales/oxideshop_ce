var previousWidth='';
var previousSpacing = '0';
function initNewTable () {
	var form = document.dialogForm;
	frame = document.getElementById('tablePreview');
	if (frame.contentWindow ) {
		previewWindow = frame.contentWindow 
	} else {
		previewWindow = window.frames['tablePreview']; 
	}
	if (form.elements['width']) {
		previousWidth=form.elements['width'].value;
	}
	//table = '<table border="1"><tr><td>Column 1</td><td>Column 2</td><td>Column 3</td></tr><tr><td>Row 2</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
	//dialog.writeFrame(frame, table);
	var forms = new wproForms();
	colWidthChange(forms.getSelectedRadioValue(form.elements['columnWidths']));
	//updatePreview();
	dialog.hideLoadMessage();
}

function colWidthChange(value) {
	var form = document.dialogForm;
	if (form.elements['width']) {
		if (value=='fixedWidth') {
			form.elements['fixedColumnWidths'].disabled=false;
			
			previousWidth=form.elements['width'].value;
			form.elements['width'].value='';
			
			form.elements['width'].disabled=true;
			form.elements['widthUnits'].disabled=true;
		} else {
			form.elements['fixedColumnWidths'].disabled=true;
			
			form.elements['width'].value=previousWidth;
			
			form.elements['width'].disabled=false;
			form.elements['widthUnits'].disabled=false;
		}
	}
	updatePreview();
}

function borderCollapseChange(value) {
	var form = document.dialogForm;
	if (form.elements['borderCollapse'] && form.elements['cellSpacing']) {
		if (value) {
			form.elements['cellSpacing'].disabled=true;
			
			previousSpacing=form.elements['cellSpacing'].value;
			form.elements['cellSpacing'].value='0';
			
		} else {
			form.elements['cellSpacing'].disabled=false;
			
			form.elements['cellSpacing'].value=previousSpacing;
			
		}
	}
	updatePreview();
}

function createTableHTML(mode) {
	
	var forms = new wproForms();
	
	var form = document.dialogForm
	var cols = form.elements['cols'].value;
	var rows = form.elements['rows'].value;
	
	var style = form.elements['style'].value;
	
	if (rows>63||isNaN(rows)||rows<1) {
		dialog.alertWrongSize(1, 63);
		dialog.focus();
		document.getElementsByTagName('A').item(0).onclick();
		form.rows.value=3;
		form.rows.focus();
		return false;
	}
	if (cols>63||isNaN(cols)||cols<1) {
		dialog.alertWrongSize(1, 63);
		dialog.focus();
		document.getElementsByTagName('A').item(0).onclick();
		form.cols.value=3;
		form.cols.focus();
		return false;
	}
	if (form.elements['width']) {
		var width = form.elements['width'].value;
		if (isNaN(width)&&width.length>0) {
			dialog.alertWrongFormat();
			dialog.focus();
			document.getElementsByTagName('A').item(0).onclick();
			form.width.value='';
			form.width.focus();
			return false;
		} else if (width) {
			width += form.elements['widthUnits'].value;
		}
	}
	
	var headers = form.elements['headers'].value;
	
	columnWidths = forms.getSelectedRadioValue(form.elements['columnWidths']);
	
	if (columnWidths == 'fixedWidth') {
		var fixedColumnWidths = dialog.makeAttraValueOK(form.fixedColumnWidths.value);
		if (isNaN(fixedColumnWidths)&&fixedColumnWidths.length>0) {
			dialog.alertWrongFormat();
			dialog.focus();
			document.getElementsByTagName('A').item(0).onclick();
			form.fixedColumnWidths.value='';
			form.fixedColumnWidths.focus();
			return false;
		} else if (fixedColumnWidths) {
			fixedColumnWidths;
		}
	}
	
	// style
	if (form.elements['border']) {
		var border = form.elements['border'].value;
		if (isNaN(border)&&border.length>0) {
			dialog.alertWrongFormat();
			dialog.focus();
			document.getElementsByTagName('A').item(1).onclick();
			form.border.value='';
			form.border.focus();
			return false;
		}
	}
	if (form.elements['borderColor']) {
		var borderColor = form.elements['borderColor'].value
	}
	if (form.elements['borderCollapse']) {
		var borderCollapse = form.elements['borderCollapse'].checked ? 'collapse' : false;
	}
	if (form.elements['backgroundColor']) {
		var backgroundColor = form.elements['backgroundColor'].value;
	}
	//var backgroundImage = form.elements['backgroundImage'].value;
	if (form.elements['cellSpacing']) {
		var cellSpacing = form.elements['cellSpacing'].value;
		if (isNaN(cellSpacing)&&cellSpacing.length>0) {
			dialog.alertWrongFormat();
			dialog.focus();
			document.getElementsByTagName('A').item(1).onclick();
			form.cellSpacing.value='';
			form.cellSpacing.focus();
			return false;
		}
	}
	if (form.elements['cellPadding']) {
		var cellPadding = form.elements['cellPadding'].value;
		if (isNaN(cellPadding)&&cellPadding.length>0) {
			dialog.alertWrongFormat();
			dialog.focus();
			document.getElementsByTagName('A').item(1).onclick();
			form.cellPadding.value='';
			form.cellPadding.focus();
			return false;
		}
	}
	
	var caption = form.elements['caption'].value;
	
	var summary = form.elements['summary'].value;
	
	//var frame0 =  form.frame.value;
	
	//var rules0 =  form.rules.value;
	
	
	var table = '<table';
	
	if ((typeof(width)!='undefined') || (typeof(borderCollapse)!='undefined') || (backgroundColor) || (/ style=/.test(style))) {
		table += ' style="'
		if (typeof(width)!='undefined' && !/width:/i.test(style))
			table += ' width:'+dialog.makeAttraValueOK(width)+';'
		
		if (typeof(borderCollapse)!='undefined' && !/border-collapse:/i.test(style))
			table += ' border-collapse:'+dialog.makeAttraValueOK(borderCollapse)+';'
		
		if (backgroundColor && !/background-color:/i.test(style))
			table += ' background-color:'+dialog.makeAttraValueOK(backgroundColor)+';'
		
		if (currentEditor.strict) {
			if ((borderColor && !/border-color:/i.test(style)) && (typeof(border)!='undefined' && parseInt(border)>0 && !/ border="0"/i.test(style)) ) {
				table += ' border-color:'+dialog.makeAttraValueOK(borderColor)+';'
				table += ' border-style: solid;'
			}
			if (typeof(border)!='undefined' && !/ border-width:/i.test(style))
				table += ' border-width: '+dialog.makeAttraValueOK(border)+'px;';
		}
		//if (backgroundImage) 
			//table += ' background-image:url('+backgroundImage+');'
			
		// style
		if (style.match(/[\s\S]*?style="([^"]+)"/gi)) {
			var s = style.replace(/[\s\S]*?style="([^"]+)"/gi, "$1");
			table += s;	
			style = style.replace(/ style="([^"]+)"/gi, '');
		}
		
		table += '"';
	}
	
	if (typeof(summary)!='undefined' && !/ summary=/i.test(style))
		table += ' summary="'+dialog.makeAttraValueOK(summary)+'"';
	
	if (typeof(cellSpacing)!='undefined' && !/ cellspacing=/i.test(style))
		table += ' cellspacing="'+dialog.makeAttraValueOK(cellSpacing)+'"';
	
	if (typeof(cellPadding)!='undefined' && !/ cellpadding=/i.test(style))
		table += ' cellpadding="'+dialog.makeAttraValueOK(cellPadding)+'"';
	
	if (!currentEditor.strict) {
		if (borderColor && !/ bordercolor=/i.test(style))
			table += ' bordercolor="'+dialog.makeAttraValueOK(borderColor)+'"';
	}
	
	if (typeof(border)!='undefined' && !/ border=/i.test(style))
		table += ' border="'+dialog.makeAttraValueOK(border)+'"';
	
	
	
	//if (rules0 && (rules0 != 'all'))
	//	table += ' rules="'+rules0+'"';
	
	//if (frame0 && (frame0 != 'box' && frame0 != 'border'))
		//table += ' frame="'+frame0+'"';
		
	table += style.replace(/^table/i,'');
	
	table += '>';
		
	if (caption) {
		var captionAlign = '';
		if (form.elements['captionAlign']) {
			var captionAlign = form.elements['captionAlign'].value;
			if (captionAlign) {
				if (currentEditor.strict) {
					captionAlign = ' style="caption-side:'+captionAlign+'"';
				} else {
					captionAlign = ' align="'+captionAlign+'"';
				}
				
			}
		}
		
		table += '<caption'+captionAlign+'>'+caption+'</caption>';
	
	}
	
	
	for (var i = 0; i < rows; i++) {
		table += '<tr valign="top">'		
		for (var j = 0; j < cols; j++) {
			var inners = '&nbsp;'
			var cell = 'td';
			var cellAttrs = '';
			var cellWidths = '';
			var cellStyles = [];
			if (mode == 'preview') {
				if (i==0) {
					inners = strColumnNumber.replace('##number##', j+1);
				} else if (j==0) {
					inners = strRowNumber.replace('##number##', i+1);
				} else {
					inners = '&nbsp;';
				}
			} else {
				var v = currentEditor.newCellInners;
				if (v=='auto') {
					if (currentEditor.lineReturns == 'p') {
						inners = '<p>&nbsp;</p>';
					} else if (currentEditor.lineReturns == 'div') {
						inners = '<div>&nbsp;</div>';
					} else {
						inners = '<br>';
					}
				} else {
					inners = currentEditor.newCellInners;
				}
			}
			if ((headers == 'top' || headers == 'both') && i==0) {
				cell = 'th'
				cellAttrs = ' scope="col"'
			} else if ((headers == 'left' || headers == 'both') && j==0) {
				cell = 'th'
				cellAttrs = ' scope="row"'
			} else {
				cell = 'td';
				cellAttrs = '';
			}
			if (i == 0) {
				if (columnWidths == 'percent') {
					if (currentEditor.strict) {
						cellStyles.push('width:'+Math.round(100/cols) +'%');
					} else {
						cellWidths = ' width="'+ Math.round(100/cols) +'%"';
					}
				} else if (columnWidths == 'fixedWidth') {
					if (fixedColumnWidths) {
						if (currentEditor.strict) {
							cellStyles.push('width:'+fixedColumnWidths+(fixedColumnWidths.match(/%/)?'%':'px'));
						} else {
							cellWidths = ' width="'+ fixedColumnWidths +'"';
						}
					}
				}
			}
			if (currentEditor.strict&&borderColor) {
				//cellStyles.push('border-color: '+borderColor);
				//cellStyles.push('border-style: solid');
				if ((borderColor && !/border-color:/i.test(style)) && (typeof(border)!='undefined' && parseInt(border)>0 && !/ border="0"/i.test(style)) ) {
					cellStyles.push('border: 1px solid '+borderColor);
				}
			}
			if (cellStyles.length) {
				cellStyles = ' style="'+(cellStyles.join(';'))+'"';
			} else {
				cellStyles = '';	
			}
			table += '<'+cell+cellAttrs+cellWidths+cellStyles+'>'+inners+'</'+cell+'>'
		}
		table += '</tr>'
	}
	
	table +='</table>';
	
	
	return table;
}

function togglePreviewColumn() {
	var col = document.getElementById('previewColumn');
	if (col.style.display == 'none') {
		document.dialogForm.togglePreview.value='Show Preview >>';
		col.style.display = '';
		dialog.resizeTo(400, 800);
	} else {
		document.dialogForm.togglePreview.value='<< Hide Preview';
		col.style.display = 'none'
	}
}


function updatePreview() {
	var table; 
	if (table = createTableHTML('preview')) {
		dialog.writeFrame(frame, table);
	}
}

function formAction () {
	var table; 
	if (table = createTableHTML()) {
		var UDBeforeState = currentEditor.history.pre();
		var range = currentEditor.selAPI.getRange()
		if (WPro.isIE) {
			var div = currentEditor.editDocument.createElement("DIV");
			div.innerHTML = table;
			var insertNode = div.firstChild;
			var h = currentEditor.editDocument.getElementsByTagName('HR');
			var hs = [];
			for (var i=0;i<h.length;i++) {
				hs.push(h[i]);
			}
			currentEditor.editDocument.selection.createRange().pasteHTML('<hr>');
			var hs2 = currentEditor.editDocument.getElementsByTagName('HR');
			for (var i=0;i<hs2.length;i++) {
				if (!wproInArray(hs2[i],hs)) {
					hs2[i].parentNode.insertBefore(insertNode, hs2[i]);
					hs2[i].parentNode.removeChild(hs2[i]);
					break;
				}
			}
		} else {
			currentEditor.insertAtSelection(table);
		}
		currentEditor.history.post(UDBeforeState);
		currentEditor.redrawTimeout();
		dialog.close();
	}
	return false;
}