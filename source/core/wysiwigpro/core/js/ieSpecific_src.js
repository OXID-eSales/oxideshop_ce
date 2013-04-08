function wproBrowserInit(obj) {
	obj.selectFix = wproSelectFix;
}

function wproPreventDefault (evt) {
	evt.cancelBubble = true;
	evt.returnValue = false;
}
function wproWriteDocument (html) {
	var doc = this.editDocument;
	if (WPro.browserVersion > 6) {
		html = html.replace(/<body([^>]*)>/i, '<body$1 contentEditable="true">');
	}
	doc.open('text/html', 'replace');
	doc.write(html);
	doc.close();
	WPro.callCommand(doc, "LiveResize", false, true)
	WPro.callCommand(doc, "MultipleSelection", false, true)
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
	if (this.lineReturns == 'div') {
		if (parentTagName == "TD"|| parentTagName == "TH"|| parentTagName == "BODY"|| parentTagName == "HTML" || parentTagName == "P") {
			this.callFormatting("FormatBlock", "<div>")
		}
	} else if (this.lineReturns == 'p') {
		if (parentTagName == "DIV") {
			this.callFormatting("FormatBlock", "<p>")
		}
	} else if (this.lineReturns == 'br') {
		range.pasteHTML('<br>');
		WPro.preventDefault(evt);
		range.collapse(false);
		range.select();
	}
}
function wproKeyDownHandler (obj, evt) {
	var keyCode = evt.keyCode;
	var doRD = true;
	if (evt.ctrlKey) {
		if (keyCode == 90) {
			doRD = false;
			obj.callFormatting("Undo");
			WPro.preventDefault(evt);
		}
		if (keyCode == 89) {
			doRD = false;
			obj.callFormatting("Redo");
			WPro.preventDefault(evt);
		}
	} else if (!evt.shiftKey) {
		if (keyCode == 13) {
			obj.lineReturn(evt)
		} else if (keyCode == 9) { // TAB
			var sel = obj.editDocument.selection.createRange() 
			sel.pasteHTML(' &nbsp;&nbsp; ')
			WPro.preventDefault(evt);
		}
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
	if (WPro.browserVersion>=7) {
		//if (!obj._initiated) {
			obj._enableDesignMode();
		//}
	}
	wp_current_obj = obj;
	WPro.currentEditor = obj;
	//obj.closePMenu();
	obj.triggerEditorEvent('mouseDown', evt);
}
function wproMouseUpHandler(obj, evt) {
	obj.setButtonStates();
	wp_current_obj = obj; 
	WPro.currentEditor = obj; 
	//wp_hide_menu(obj); 
	obj.selectFix(evt);
	//WPro.closePMenu();
	
	WPro.updateAll('closePMenu');
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
				if (/<(p|h[0-9]|div|address) *[^>]*>/i.test(range.range.htmlText)) {
					range.range.moveEnd('character', -1);		   
				}
				range.select();
			}
		}
	}
}
// selection interface
// range object
function wproGetCommonAncestorContainer () {
	if (this.type == 'control' && this.nodes[0]) {
		return this.getContainer();
	} else {
		var startContainer = this.getStartContainer();
		var endContainer = this.getEndContainer();
		while (startContainer != endContainer && (startContainer.parentNode && endContainer.parentNode)) {
			startContainer = startContainer.parentNode;
			endContainer = endContainer.parentNode;
		}
		if (startContainer == endContainer) {
			while (startContainer.nodeType!=1) {
				startContainer = startContainer.parentNode();
			}
		} else {
			startContainer = WPro.editors[this.editor].editDocument.getElementsByTagName('BODY').item(0);
		}
		return startContainer;
	}
}
function wproGetEndContainer () {
	if (this.type == 'control' && this.nodes[0]) {
		return this.getContainer();
	} else {
		var endRange = this.cloneRange();
		endRange.collapse(false);
		var node = endRange.range.parentElement();
		while (node.nodeType!=1) {
			node = node.parentNode;
		}
		return node;
	}
}
function wproGetStartContainer () {
	if (this.type == 'control' && this.nodes[0]) {
		return this.getContainer();
	} else {
		var startRange = this.cloneRange();
		startRange.collapse(true);
		var node = startRange.range.parentElement();
		while (node.nodeType != 1) {
			node = node.parentNode;
		}
		return node;
	}
}
function wproGetHTMLText () {
	if (this.type == 'control' && this.nodes[0]) {
		var div = WPro.editors[this.editor].editDocument.createElement('div');
		div.appendChild(this.nodes[0]);
		return div.innerHTML;
	} else {
		return this.range.htmlText;
	}
}
// makes this range the currently selected range!
function wproSelect () {
	this.range.select();
	WPro.editors[this.editor].focus();
}
function wproSelectNodeContents (referenceNode) {	
	if (this.type == 'control') return false;
	this.range.moveToElementText(referenceNode);
}
function wproCloneContents () {
	var editor = WPro.editors[this.editor];
	var df = editor.editDocument.createDocumentFragment();
	if (this.type == 'control' && this.nodes[0]) {
		var n = this.nodes[0].cloneNode();
		df.appendChild(n);
	} else {
		WPro.setInnerHTML(df, this.range.htmlText);
	}
	return df;
}
function wproDeleteContents () {
	var editor = WPro.editors[this.editor];
	var UDBeforeState = editor.history.pre();
	if (this.type == 'control' && this.nodes[0]) {
		WPro.callCommand(this.range, 'delete', false, null);
	}
	editor.history.post(UDBeforeState);
}
function wproExtractContents () {
	var editor = WPro.editors[this.editor];
	var UDBeforeState = editor.history.pre();
	var df = editor.editDocument.createDocumentFragment();
	if (this.type == 'control' && this.nodes[0]) {
		df.appendChild(this.nodes[0]);
	} else {
		WPro.setInnerHTML(df, this.range.htmlText);
		this.range.pasteHTML('');
	}
	this.deleteContents();
	editor.history.post(UDBeforeState);
	return df;
}
function wproPasteHTML (html) {
	var editor = WPro.editors[this.editor];
	html = editor.triggerHTMLFilter('design', html);
	var UDBeforeState = editor.history.pre();
	if (this.type == 'control') {
		editor.callFormatting('delete');
		this.range = editor.editDocument.selection.createRange();
		this.type='text';	
	}
	this.range.pasteHTML(html);
	editor.history.post(UDBeforeState);
}	
// inserts a node into the range (this overwrites any selected nodes unlike the DOM version)
function wproInsertNode (insertNode, selectNode) {
	var editor = WPro.editors[this.editor];
	var UDBeforeState = editor.history.pre();
	if (this.type == 'control' && this.nodes[0]) {
		var pNode = this.nodes[0].parentNode
		pNode.replaceChild(insertNode, this.nodes[0]);
		if (selectNode) range.addElement(insertNode);
		//pNode.removeChild(this.nodes[0]);	
	} else {
		var d = editor.editDocument.createElement('DIV');
		d.appendChild(insertNode);
		this.range.pasteHTML(d.innerHTML);
		if (selectNode) this.range.moveStart('character', -1)
	}
	editor.history.post(UDBeforeState);
}
function wproCloneRange () {
	var r;
	if (this.range.duplicate) {
		r = this.range.duplicate();
	} else if (document.selection) {
		r = WPro.editors[this.editor].editDocument.selection.createRange();
	}
	var nr = new wproRange(r, this.editor);
	nr.type = this.type;
	nr.nodes = this.nodes;
	return nr;
}
function wproToString () {
	if (this.range.text) {
		return this.range.text;
	} else {
		return '';
	}
}
// if the range selects a single node then use this to quickly retrieve that node.
// returns false if the selection does not select a single node
function wproGetSelectedNodes () {
	this.range = null;
	var nodes = [];
	var editor = WPro.editors[this.editor];
	this.range = editor.editDocument.selection.createRange();
	if (editor.editDocument.selection.type == "Control") {
		var num = this.range.length;
		for (var i=0; i < num; i++) {
			nodes[i] = this.range(i)
		}
		return nodes;
	} else {
		return false;
	}
	return false;
}
// returns a new range object
function wproCreateRange () {
	var range = WPro.editors[this.editor].editDocument.body.createTextRange();	
	var r = new wproRange(range, this.editor);
	return r;
}