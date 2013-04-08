function wproBrowserInit(obj) {
	obj.selectFix = wproSelectFix;
	
	// paste
	obj.addButtonStateHandler('cut', wproCutCopyBSH);
	obj.addButtonStateHandler('copy', wproCutCopyBSH);
	obj.addButtonStateHandler('paste', wproPasteBSH);
	
	
	// register new handlers for bold, italic and underline
	obj.addFormattingHandler('bold',wproSafariFormatting);
	obj.addFormattingHandler('italic',wproSafariFormatting);
	if (!obj.strict) {
		obj.addFormattingHandler('underline',wproSafariFormatting);
	}
	// subscript, superscript, strikethrough
	obj.addFormattingHandler('subscript',wproSafariFormatting);
	obj.addFormattingHandler('superscript',wproSafariFormatting);
	if (!obj.strict) {
		obj.addFormattingHandler('strikethrough',wproSafariFormatting);
	}
	// new handlers for font and font-size
	if (!obj.strict) {
		obj.addFormattingHandler('fontname',wproSafariFormatting);
		obj.addFormattingHandler('fontsize',wproSafariFormatting);
		obj.addFormattingHandler('forecolor',wproSafariFormatting);
	}
	// indent, outdent
	//obj.addFormattingHandler('indent',wproSafariFormatting);
	//obj.addFormattingHandler('outdent',wproSafariFormatting);
	
	// alignment
	if (!obj.strict) {
		obj.addFormattingHandler('JustifyNone',wproSafariFormatting);
		obj.addFormattingHandler('JustifyLeft',wproSafariFormatting);
		obj.addFormattingHandler('JustifyCenter',wproSafariFormatting);
		obj.addFormattingHandler('JustifyRight',wproSafariFormatting);
		obj.addFormattingHandler('JustifyFull',wproSafariFormatting);
		
		// button state handlers
		obj.addButtonStateHandler('JustifyNone',wproSafariBSH);
		obj.addButtonStateHandler('JustifyLeft',wproSafariBSH);
		obj.addButtonStateHandler('JustifyCenter',wproSafariBSH);
		obj.addButtonStateHandler('JustifyRight',wproSafariBSH);
		obj.addButtonStateHandler('JustifyFull',wproSafariBSH);
	}
	
	// new button state handlers for bold, italic and underline
	if (!obj.strict) {
		obj.addFormattingValueHandler('fontsize',wproSafariFV);
	}
	
}
function wproSafariFormatting (EDITOR, sFormatString, sValue) {
	var range = EDITOR.selAPI.getRange();
	if (range.type == 'control') {
		var node = range.nodes[0];
	} else {
		var node = WPro.getParent(range.getStartContainer());
		var endnode = WPro.getParent(range.getEndContainer());
	}
	var tagName
	switch(sFormatString) {
		case "bold" :
		case "italic" :
		case "underline" :
		case "subscript" :
		case "superscript" :
		case "strikethrough" :
			switch(sFormatString) {
				case "bold" :
					tagName = 'STRONG';
					break;
				case "italic" :
					tagName = 'EM';
					break;
				case "underline" :
					tagName = 'U';
					break;
				case "subscript" :
					tagName = 'SUB';
					break;
				case "superscript" :
					tagName = 'SUP';
					break;
				case "strikethrough" :
					tagName = 'STRIKE';
					break;
			}
			var s = WPro.getParentNodeByTagName(node, tagName);
			var es = WPro.getParentNodeByTagName(endnode, tagName);
			if (s && es) {
				if (es != s) {
					var nodes = EDITOR.editDocument.getElementsByTagName(tagName);
					var found = false;
					for (var i = 0; i < nodes.length; i++) {
						if (nodes[i] == es) found = false;
						if (found) {
							WPro.removeNode(nodes[i]);
						}
						if (nodes[i] == s) found = true;
					}
					WPro.removeNode(es);
					var nodes = es.getElementsByTagName(tagName);
					for (var i = 0; i < nodes.length; i++) {
						WPro.removeNode(nodes[i]);
					}
				} else {
					var nodes = s.getElementsByTagName(tagName);
					for (var i = 0; i < nodes.length; i++) {
						WPro.removeNode(nodes[i]);
					}
				}
				WPro.removeNode(s);
			} else {
				EDITOR.applyStyle(tagName.toLowerCase());
			}
			break;
		case "fontname" :
			EDITOR.applyStyle('font face="'+sValue+'"');
			break;
		case "fontsize" :
			EDITOR.applyStyle('font size="'+sValue+'"');
			break;
		case "forecolor" :
			EDITOR.applyStyle('font color="'+sValue+'"');
			break;
		/*
		case "indent" :
		case "outdent" :
			// if within li use the default
			if (WPro.getParentNodeByTagName(node, 'LI')) {
				WPro.callCommand(EDITOR.editDocument, sFormatString, false, null);
				break;	
			} else {
				var q = WPro.getParentNodeByTagName(node, 'BLOCKQUOTE')
				if (q && sFormatString == 'outdent') {
					WPro.removeNode(q);
					range.select();
				} else if (sFormatString=='indent') {
					var a = range.getCommonAncestorContainer();
					var p = WPro.getBlockParent(a);
					//alert(p.tagName);
					var q = EDITOR.editDocument.createElement('BLOCKQUOTE');
					if (!/^(t[a-z]+|body|html)$/i.test(p.tagName) && !p.getElementsByTagName('P')) {
						p.parentNode.insertBefore(q,p);
						p.parentNode.removeChild(p);
						q.appendChild(p);
						range.select();
					} else {
						//alert('here');
						var cn = p.childNodes;
						var found = false;
						var foundend = false;
						var qinserted = false
						var c = [];
						for (var i=0; i<cn.length; i++) {
							if (cn[i]!=q) {
								if (!found) {
									if (cn[i] == node || (cn[i].getElementsByTagName && wproInArray(node, cn[i].getElementsByTagName(node.tagName)))) {
										found = true;
									}
								}
								if (cn[i] == endnode || (cn[i].getElementsByTagName && wproInArray(endnode, cn[i].getElementsByTagName(endnode.tagName)))) {
									foundend = true;
								}
								if (found) {
									if (!qinserted) {
										p.insertBefore(q, cn[i]);
										qinserted = true;
										i++;
									}
									c[i] = cn[i];
									p.removeChild(cn[i]);
									q.appendChild(c[i]);
									i--
									
								}
								if (foundend) break;
							}
						}
						range.select();
					}
				}
			}			
			break;
		*/
		case 'justifynone':
			EDITOR.applyStyle('*block* align="left"');
			break;
		case 'justifyleft':
			EDITOR.applyStyle('*block* align="left"');
			break;
		case 'justifycenter':
			EDITOR.applyStyle('*block* align="center"');
			break;
		case 'justifyright':
			EDITOR.applyStyle('*block* align="right"');
			break;
		case 'justifyfull':
			EDITOR.applyStyle('*block* align="justify"');
			break;
	}
}

function wproSafariBSH (EDITOR,srcElement,cid,inTable,inA,range) {
	var ret = 'wproReady';
	if (range.type == 'control') {
		var node = range.nodes[0];
	} else {
		var node = WPro.getParent(range.getStartContainer());
	}
	var bnode = WPro.getBlockParent(node);
	switch(cid) {
		/*case 'bold' :
			var b = WPro.getParentNodeByTagName(node, 'STRONG');
			ret = (b)?'wproLatched':'wproReady';
			break;
		case 'italic' :
			var i = WPro.getParentNodeByTagName(node, 'EM');
			ret = (i)?'wproLatched':'wproReady';
			break;
		case 'underline' :
			var u = WPro.getParentNodeByTagName(node, 'U');
			ret = (u)?'wproLatched':'wproReady';
			break;*/
		case 'justifynone':
			EDITOR.applyStyle('*block* align="left"');
			ret = (!(bnode && bnode.getAttribute('align')))?'wproLatched':'wproReady';
			break;
		case 'justifyleft':
			ret = (bnode && bnode.getAttribute('align')=='left')?'wproLatched':'wproReady';
			break;
		case 'justifycenter':
		//alert(bnode.getAttribute('align'));
			ret = (bnode && bnode.getAttribute('align')=='center')?'wproLatched':'wproReady';
			break;
		case 'justifyright':
			ret = (bnode && bnode.getAttribute('align')=='right')?'wproLatched':'wproReady';
			break;
		case 'justifyfull':
			ret = (bnode && bnode.getAttribute('align')=='justify')?'wproLatched':'wproReady';
			break;
	}
	return ret;
}

// formatting value query function: gets display text for menus
function wproSafariFV(EDITOR, com) {
	var value = '';
	var range = EDITOR.selAPI.getRange();
	if (range.type == 'control') {
		var node = range.nodes[0];
	} else {
		var node = WPro.getParent(range.getStartContainer());
	}
	switch (com) {
		case 'fontsize' :
			var f = WPro.getParentNodeByTagName(node, 'FONT');
			if (f) value = f.getAttribute('size');
			break;
	}
	return value;
}

function wproPreventDefault(evt) {
	evt.stopPropagation();
	evt.preventDefault();
}
function wproWriteDocument (html) {
	if (!this.editDocument) {
		this.editFrame = document.getElementById(this.id + '_editFrame');
		this.previewFrame = document.getElementById(this.id + '_previewFrame');
		if (this.editFrame.contentWindow) {
			this.editWindow = this.editFrame.contentWindow;
			this.previewWindow = this.previewFrame.contentWindow;
		} else {
			this.editWindow = window.frames[this.id + '_editFrame'];
			this.previewWindow = window.frames[this.id + '_previewFrame'];
		}
		this.editDocument= this.editWindow.document;
	}	
	this.editDocument.open('text/html', 'replace');
	this.editDocument.write(html);
	this.editDocument.close();
}
function wproLineReturn (evt) {
	var range = this.selAPI.getRange();
	if (range.getContainerByTagName('LI')) {
		return;
	}
	var parentTagName = range.getBlockContainer().tagName
	if (parentTagName=='LI') {
		return;
	}
	if (this.lineReturns == 'br' || evt.shiftKey) {
		range.pasteHTML('<br>');
		WPro.preventDefault(evt);
		range.collapse(false);
		range.select();
	} else if (this.lineReturns == 'div') {
		if (parentTagName == "TD"|| parentTagName == "TH"|| parentTagName == "BODY"|| parentTagName == "HTML" || parentTagName == "P") {
			this.callFormatting("FormatBlock", "<div>")
		}
	} else if (this.lineReturns == 'p') {
		if (parentTagName == "DIV") {
			this.callFormatting("FormatBlock", "<p>")
		}
	}
}
function wproKeyDownHandler (obj, evt) {
	var keyCode = evt.keyCode;
	var doRD = true;
	if (evt.ctrlKey || evt.metaKey) {
		if (keyCode == 122) {
			doRD = false;
			obj.callFormatting("Undo");
			WPro.preventDefault(evt);
		}
		if (keyCode == 121) {
			doRD = false;
			obj.callFormatting("Redo");
			WPro.preventDefault(evt);
		}
		if (keyCode == 98) {
			obj.callFormatting("Bold");
			WPro.preventDefault(evt);
		}
		if (keyCode == 105) {
			obj.callFormatting("Italic");
			WPro.preventDefault(evt);
		}
		if (keyCode == 117 || keyCode == 21) {
			obj.callFormatting("Underline");
			WPro.preventDefault(evt);
		}
	} else if (!evt.shiftKey) {
		if (keyCode == 9) { // TAB
			var range = obj.selAPI.getRange() 
			range.pasteHTML(' &nbsp;&nbsp; ')
			WPro.preventDefault(evt);
		}
	} else if (keyCode == 13) {
		obj.lineReturn(evt)
	}
	
	obj.triggerEditorEvent('keyDown', evt);
	
	if (doRD) obj.history.addKey(keyCode);	
}
function wproKeyUpHandler (obj, evt) {
	var keyCode = evt.keyCode;
	if (keyCode == 39 || keyCode == 37 || keyCode == 38 || keyCode == 40) {
		obj.setButtonStates();
	}
	obj.triggerEditorEvent('keyUp', evt);
	//obj.history.addKey(keyCode);
}
function wproMouseDownHandler (obj, evt) {
	wp_current_obj = obj;
	WPro.currentEditor = obj;
	//obj.closePMenu();
	WPro.updateAll('closePMenu');
	obj.triggerEditorEvent('mouseDown', evt);
}
function wproMouseUpHandler(obj, evt) {
	obj.setButtonStates();
	wp_current_obj = obj; 
	WPro.currentEditor = obj; 
	//wp_hide_menu(obj); 
	//wp_select_fix(obj, evt);
	obj.selectFix(evt);
	//WPro.closePMenu();
	
	//WPro.updateAll('closePMenu');
	obj.triggerEditorEvent('mouseUp', evt);
	obj.history.keyPresses=0;
}

// moves cursor to beginning of tags that contain only &nbsp;
function wproSelectFix(evt) {
	var range =  this.selAPI.getRange();
	var c = null
	if (c = range.getCommonAncestorContainer()) {
		if (c.innerHTML) {
			if (c.innerHTML.match(/^(&nbsp;|\xA0)$/)) {
				range.selectNodeContents(c);
				range.select();
			}
		}
	}
}

// button state handelers
function wproCutCopyBSH (EDITOR,srcElement,cmd,inTable,inA,range){
	return range.getHTMLText()?'wproReady':'wproDisabled';
}
function wproPasteBSH (EDITOR,srcElement,cmd,inTable,inA,range){
	return WPro.clipBoard?'wproReady':'wproDisabled';
}

// selection interface
// range object functions
function wproGetCommonAncestorContainer () {
	if (this.type == 'control' && this.nodes) {
		return this.getContainer();
	} else {
		var n = this.range.commonAncestorContainer;
		while (n.nodeType!=1 && n.parentNode) {
			n = n.parentNode;
		}
		return n;
	}
}
function wproGetEndContainer () {
	if (this.type == 'control' && this.nodes) {
		return this.getContainer();
	} else {
		return this.range.endContainer;
	}
}
function wproGetStartContainer () {
	if (this.type == 'control' && this.nodes) {
		return this.getContainer();
	} else {
		return this.range.startContainer;
	}
}
function wproGetHTMLText () {
	if (this.type == 'control' && this.nodes[0]) {
		var div = WPro.editors[this.editor].editDocument.createElement('div');
		div.appendChild(this.nodes[0].cloneNode(true));
		return div.innerHTML;
	} else {
		var clonedSelection = this.range.cloneContents();
		var div = WPro.editors[this.editor].editDocument.createElement('div');
		div.appendChild(clonedSelection);
		return div.innerHTML;
	}
}	
// makes this range the currently selected range!
function wproSelect () {
	var sel = WPro.editors[this.editor].editWindow.getSelection()
	sel.removeAllRanges()
	sel.addRange(this.range)
	WPro.editors[this.editor].focus();
}
function wproSelectNodeContents (referenceNode) {	
	if (this.type == 'control') return false;
	this.range.selectNodeContents(referenceNode);
}
function wproCloneContents () {
	return this.range.cloneContents();
}
function wproDeleteContents () {
	var editor = WPro.editors[this.editor];
	var UDBeforeState = editor.history.pre();
	this.range.deleteContents();
	editor.history.post(UDBeforeState);
}
function wproExtractContents () {
	var editor = WPro.editors[this.editor];
	var UDBeforeState = editor.history.pre();
	var df = this.range.extractContents();
	editor.history.post(UDBeforeState);
	return df;
}
function wproPasteHTML (html) {
	var editor = WPro.editors[this.editor];
	html = editor.triggerHTMLFilter('design', html);
	var div = editor.editDocument.createElement("DIV")
	WPro.setInnerHTML(div, html);
	var cn = div.childNodes;
	var num = cn.length;
	var UDBeforeState = editor.history.pre();
	for (var i=0; i < num; i++) {
		this.insertNode(cn[0]);					
	}
	editor.history.post(UDBeforeState);
}	
// inserts a node into the range (this overwrites any selected nodes unlike the DOM version)
function wproInsertNode (insertNode, selectNode) {
	var editor = WPro.editors[this.editor];
	var UDBeforeState = editor.history.pre();
	// inserting an HR
	if (insertNode.tagName) {
		if (WPro.blocks.test(insertNode.tagName)) {
			// in mozilla we need to break apart p tags...
			if (this.getBlockContainer().tagName == 'P') {
				if (this.getText() != '') {
					WPro.callCommand(editor.editDocument, 'delete', false, null);	
				}
				var h = editor.editDocument.getElementsByTagName('HR');
				var hs = [];
				for (var i=0;i<h.length;i++) {
					hs.push(h[i]);
				}
				WPro.callCommand(editor.editDocument,'InsertHorizontalRule',false,null);
				var hs2 = editor.editDocument.getElementsByTagName('HR');
				for (var i=0;i<hs2.length;i++) {
					if (!wproInArray(hs2[i],hs)) {
						if (insertNode.tagName=='HR') {
							editor.applyStyle(insertNode, [hs2[i]], false);
						} else {
							hs2[i].parentNode.insertBefore(insertNode, hs2[i]);
							hs2[i].parentNode.removeChild(hs2[i]);
						}
						break;
					}
				}
				editor.history.post(UDBeforeState);
				return;
			}
		}
	}
	if (selectNode) editor.selAPI.removeAllRanges();
	this.range.deleteContents();
	var container = this.range.startContainer
	var pos = this.range.startOffset
	if (container.nodeType==3 && insertNode.nodeType==3) {
		container.insertData(pos, insertNode.nodeValue)
		this.range.setEnd(container, pos+insertNode.length)
		this.range.setStart(container, pos+insertNode.length)
	} else {
		var afterNode
		if (container.nodeType==3) {
			var textNode = container
			container = textNode.parentNode
			var text = textNode.nodeValue
			var textBefore = text.substr(0,pos)
			var textAfter = text.substr(pos)
			var beforeNode = editor.editDocument.createTextNode(textBefore)
			afterNode = editor.editDocument.createTextNode(textAfter)
			container.insertBefore(afterNode, textNode)
			container.insertBefore(insertNode, afterNode)
			container.insertBefore(beforeNode, insertNode)
			container.removeChild(textNode)
		} else {
			afterNode = container.childNodes[pos]
			container.insertBefore(insertNode, afterNode)
		}
		this.range.selectNode(insertNode);
		if (!selectNode) {
			this.range.collapse(false);
		}
	}
	editor.history.post(UDBeforeState);
}
function wproCloneRange () {
	var r;
	if (this.range.cloneRange) {
		r = this.range.cloneRange();
	} 
	var nr = new wproRange(r, this.editor);
	nr.type = this.type;
	nr.nodes = this.nodes;
	return nr;
}
function wproToString () {
	return this.range.toString();
}
// selection interface functions
function wproGetSelectedNodes () {
	this.range = null;
	var nodes = [];
	var sel = WPro.editors[this.editor].editWindow.getSelection();
	var range
	var num = sel.rangeCount
	var j = 0;
	for (var i=0; i < num; i++) {
		range = sel.getRangeAt(i);
		if (i == 0) this.range = range//.cloneRange();
		var container = range.startContainer
		var endContainer = range.endContainer
		var pos = range.startOffset;
		if (container == endContainer) {
			if (range.endOffset == (pos+1)) {
				if (container.tagName) {
					var cn = container.childNodes
					if (cn[pos]) {
						if (cn[pos].tagName) {
							nodes[j] = cn[pos];
							j++;
						} else {
							//return false;
						}
					}
				}
			}
		}
	}
	if (j > 0) {
		return nodes;
	}
	if (!this.range) {
		this.range = WPro.editors[this.editor].editDocument.createRange();
	}
	return false;
}
// returns a new range object
function wproCreateRange () {
	var range = WPro.editors[this.editor].editDocument.createRange();
	var r = new wproRange(range, this.editor);
	return r;
}