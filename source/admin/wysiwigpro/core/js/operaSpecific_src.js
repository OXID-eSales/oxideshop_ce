function wproBrowserInit(obj) {
	obj.selectFix = wproSelectFix;
	obj.fillContent = wproFillContent;
	
	obj.addButtonStateHandler('cut', wproCutCopyBSH);
	obj.addButtonStateHandler('copy', wproCutCopyBSH);
	obj.addButtonStateHandler('paste', wproPasteBSH);
}

function wproPreventDefault(evt) {
	evt.stopPropagation();
	evt.preventDefault();
}
function wproMozAddAttrs(obj, tag, str) {
	elem = obj.editDocument.getElementsByTagName(tag)[0];
	if (elem) {
		WPro.stripAttributes(elem);
		var regex = new RegExp("<"+tag+"[^>]*?>", "gi");
		var strAttrs = str.match(regex);
		if (strAttrs) {
			if (strAttrs[0]) {
				attrs = strAttrs[0].match(/ [^=]+="[^"]*"/gi);
				if (attrs) {
					var n = attrs.length;
					for (var i=0; i<n; i++) {
						var attribute = attrs[i].split("=");
						var elm = attribute[0].trim().replace(/"/gi,'');
						var val = attribute[1].trim().replace(/"/gi,'');
						elem.setAttribute( elm, val, 0 );
					}
				}
			}
		}
	}
}
function wproWriteDocument(str) {
	this.editDocument = this.editWindow.document;
	var doc = this.editDocument;
	if (!this._initiated) {
		try{
		doc.open('text/html', 'replace');
		doc.write(this.doctype+'<html><head>'+(this.baseURL?'<base href="'+this.baseURL+'">':'')+'<title></title></head><body></body></html>');
		doc.close();
		this.editWindow.stop();
		}catch(e){}
	} else {
		var headContent = str.match(/<head[^>]*?>([\s\S]*?)<\/head>/gi);
		if (headContent) {
			if (headContent[0]) {
				headContent = headContent[0].replace(/<head[^>]*?>([\s\S]*?)<\/head>/gi, "$1");
				var headtag = doc.getElementsByTagName('HEAD')
				WPro.setInnerHTML(headtag[0], headContent);
			}
		} else {
			var headtag = doc.getElementsByTagName('HEAD')
			WPro.setInnerHTML(headtag[0], '');
		}
		var bodyContent = str.match(/<body[^>]*?>([\s\S]*?)<\/body>/gi);
		if (!bodyContent) {
			bodyContent = str.match(/<body[^>]*?>([\s\S]*)/gi);
		}
		if (bodyContent) {
			if (bodyContent[0]) {
				bodyContent = bodyContent[0].replace(/<body[^>]*?>/gi, "").replace(/<\/body>/gi, "");
				WPro.setInnerHTML(doc.body, bodyContent);
				
			}
		}
		wproMozAddAttrs(this, 'HTML', str);
		wproMozAddAttrs(this, 'HEAD', str);
		wproMozAddAttrs(this, 'BODY', str);
		//this.redraw();
	}
	this.editDocument = this.editWindow.document;
}
// clean up backspace
function wpro_backspace (obj, evt) {
	var wproRange = obj.selAPI.getRange();
	var node = wproRange.getStartContainer();
	
	// ensure content is always surrounded with a block tag
	var b = wproRange.getBlockContainer();
	if (!b || b.tagName=='BODY') {
		if (node && node.nodeValue && node.previousSibling && node.previousSibling.tagName && (node.previousSibling.tagName=='HR'||node.previousSibling.tagName=='TABLE')) {
			if (wproRange.range.startOffset == 0) {
				node.previousSibling.parentNode.removeChild(node.previousSibling);
				setTimeout('wpro_backspace_timeout(WPro.'+obj._internalId+')',1);
				return;
			}
		}
		if (!WPro.hasContent(b)) {
			b.appendChild(obj.editDocument.createTextNode(String.fromCharCode(160)));	 
		}
		switch (obj.lineReturns) {
			case 'br' :
				break;
			case 'p' :
				obj.callFormatting('formatblock', 'P');
				break;
			case 'div' :
				obj.callFormatting('formatblock', 'DIV');
				break;
		}
	} else {
		/*if (node && node.nodeValue && node.nextSibling && node.nextSibling.nodeName == "BR" && node.parentNode.nodeName != "BODY") {
			v = node.nodeValue;
			if (v != null && wproRange.range.startOffset == v.length)
				node.nextSibling.parentNode.removeChild(node.nextSibling);
		}*/
		setTimeout('wpro_backspace_timeout(WPro.'+obj._internalId+')',1);
		
	}
}
function wpro_backspace_timeout (obj) {
	var wproRange = obj.selAPI.getRange();
	var node = wproRange.getStartContainer();
	
	if (node && node.nodeValue && node.nextSibling && node.nextSibling.nodeName == "BR" && node.parentNode.nodeName != "BODY") {
			v = node.nodeValue;
			if (v != null && wproRange.range.startOffset == v.length)
				node.nextSibling.parentNode.removeChild(node.nextSibling);
		}
	
}
function wproLineReturn(evt) {
	var wproRange = this.selAPI.getRange();
	if (wproRange.getContainerByTagName('LI')) {
		return;
	}
	var body = this.editDocument.body;
	var rootElm = this.editDocument.documentElement;
	var sel = this.editWindow.getSelection()
	var range = wproRange.range;
	var startContainer = wproRange.getStartContainer();
	//if (wproRange.type=='control') {
	//	var container = startContainer
	//} else {
		var container = startContainer.parentNode
		if (container.tagName=='HTML') {
			container = null;
		}
	//}
	// find the parent node	
	var parentTag = wproRange.getBlockContainer()//WPro.getBlockParent(container)
	var endContainer = wproRange.getEndContainer()
	//if (wproRange.type=='control') {
	//	var endNode1 = endContainer
	//} else {
		var endNode1 = endContainer.parentNode
	//}
	if (this.lineReturns == 'br') {
		var br = this.editDocument.createElement('BR');
		wproRange.insertNode(br);
		var t = this.editDocument.createTextNode(' ');
		wproRange.insertNode(t);
		wproRange.select();
	} else {
		// determine the tag that the parent node replacement (before node) should be
		var beforeTag; var afterTag; var addAttributes = false; var attributes; var className; var cssText;
		if (parentTag.tagName) {
			// if a supported block get attributes
			if (WPro.supported_blocks.test(parentTag.tagName)) {
				addAttributes = true
				attributes = parentTag.attributes
			}
			if (parentTag.tagName != 'P' && WPro.supported_blocks.test(parentTag.tagName)) {
				beforeTag = parentTag.tagName
			} else if (this.lineReturns == 'p') {
				beforeTag = 'P'
			} else if (!WPro.supported_blocks.test(parentTag.tagName)) {
				beforeTag = 'P'
			} else {
				beforeTag = 'DIV'
				// replace with div tag then continue
				this.callFormatting("FormatBlock", "div")
			}
		} else if (this.lineReturns == 'p') {
			beforeTag = 'P'
		} else {
			beforeTag = 'DIV'
		}
		// determine the tag that the new after node should be (adjust this later)
		var afterTag = beforeTag
		
		// make sure we overwrite the selection
		if (container != endNode1) {
			this.callFormatting('Delete')
		} else if (container.tagName=='A') {
			var inA = true;
		}
		// create and find ranges to cut
		var rngbefore = this.editDocument.createRange()		
		var rngafter = this.editDocument.createRange()
		rngbefore.setStart(sel.anchorNode, sel.anchorOffset);	
		rngafter.setStart(sel.focusNode, sel.focusOffset);
		rngbefore.collapse(true);					
		rngafter.collapse(true);
		var direct = rngbefore.compareBoundaryPoints(rngbefore.START_TO_END, rngafter) < 0;
		var startNode = direct ? sel.anchorNode : sel.focusNode;
		var startOffset = direct ? sel.anchorOffset : sel.focusOffset;
		var endNode = direct ? sel.focusNode : sel.anchorNode;
		var endOffset = direct ? sel.focusOffset : sel.anchorOffset;
		
		// find parent blocks
		var startBlock = WPro.getBlockParent(startNode);
		var endBlock = WPro.getBlockParent(endNode);
		
		// find start and end points
		var startCut = startNode;
		var endCut = endNode;
				
		if (WPro.inline_tags.test(startCut.tagName)||startCut.nodeType!=1||!container) {
			while ((startCut.previousSibling && startCut.previousSibling.nodeName != beforeTag) 
					|| (startCut.parentNode && startCut.parentNode != startBlock && startCut.parentNode.nodeType != 9)) {
				startCut = startCut.previousSibling ? startCut.previousSibling : startCut.parentNode;
			}
		} 
		if (WPro.inline_tags.test(endCut.tagName)||endCut.nodeType!=1||!container) {
			while ((endCut.nextSibling && endCut.nextSibling.nodeName != afterTag) 
					|| (endCut.parentNode && endCut.parentNode != endBlock && endCut.parentNode.nodeType != 9)) {
				endCut = endCut.nextSibling ? endCut.nextSibling : endCut.parentNode;
			}
		} 
		
		// get the contents for each new tag
		rngbefore.setStartBefore(startCut);
		rngbefore.setEnd(startNode,startOffset);
		var beforeContents = rngbefore.cloneContents()
		rngafter.setEndAfter(endCut);
		rngafter.setStart(endNode,endOffset);
		var afterContents = rngafter.cloneContents()
		// test to see if after tag will be empty and if so change to p or div
		if (!WPro.hasContent(afterContents )) {
			if (this.lineReturns == 'p') {
				afterTag = 'p'
			} else {
				afterTag = 'div'
			}
		}
		// create the new elements
		var newbefore = this.editDocument.createElement(beforeTag);
		var newafter = this.editDocument.createElement(afterTag);
		// place content into the new tags
		newbefore.appendChild(beforeContents)
		newafter.appendChild(afterContents)
		// fill tags if empty
		this.fillContent(newbefore)
		this.fillContent(newafter)
		// add attributes
		if (addAttributes) {
			WPro.addAttributes(newbefore, attributes)
			WPro.addAttributes(newafter, attributes, false, true)
		}
		// make a range around everything
		var rngSurround = this.editDocument.createRange();
		if (!startCut.previousSibling && startCut.parentNode.nodeName == beforeTag) {
			rngSurround.setStartBefore(startCut.parentNode);
		} else {
			rngSurround.setStart(rngbefore.startContainer, rngbefore.startOffset)
		}
		if (!endCut.nextSibling && endCut.parentNode.nodeName == beforeTag) {
			rngSurround.setEndAfter(endCut.parentNode);
		} else {
			rngSurround.setEnd(rngafter.endContainer, rngafter.endOffset)
		}
		// delete old tag
		rngSurround.deleteContents();
		// insert the two new tags
		rngSurround.insertNode(newafter)
		rngSurround.insertNode(newbefore)
		// scroll to the new cursor position
		var scrollTop = this.editDocument.body.scrollTop + this.editDocument.documentElement.scrollTop
		var scrollLeft = this.editDocument.body.scrollLeft + this.editDocument.documentElement.scrollLeft
		var scrollBottom = this.editFrame.style.height
		scrollBottom = scrollBottom.replace(/px/i, '')
		var frameHeight = scrollBottom
		scrollBottom = scrollTop + parseInt(scrollBottom)
		var afterposition = WPro.getElementPosition(newafter)
		if (afterposition['top'] > scrollBottom - 25) {
			this.editWindow.scrollTo(afterposition['left'], afterposition['top'] - parseInt(frameHeight) + 25)
		} else {
			this.editWindow.scrollBy(afterposition['left'] - scrollLeft, 0)
		}
		// move the cursor
		while (newafter.firstChild && WPro.inline_tags.test(newafter.firstChild.nodeName)) {
			newafter = newafter.firstChild;
			if (newafter.tagName=='A') {
				var pnode = newafter.parentNode;
				var cn = newafter.childNodes;
				for (var i=0; i<cn.length; i++) {
					pnode.insertBefore(cn[i].cloneNode(true), newafter );
				}
				pnode.removeChild(newafter);
				newafter = pnode;
			}
		}
		if (newafter.firstChild && newafter.firstChild.nodeType == 3) {
			newafter = newafter.firstChild
		}
		var rngCaret = this.editDocument.createRange()
		rngCaret.setStart(newafter, 0);
		rngCaret.collapse(true)
		sel = this.editWindow.getSelection()
		sel.removeAllRanges()
		sel.addRange(rngCaret)
	}
	// stop browser default action
	WPro.preventDefault(evt);
}

function wproKeyDownHandler (obj, evt) {
	var keyCode = (evt.which || evt.charCode || evt.keyCode);
	//if (obj.editWindow.getSelection) {
		var doRD = true;
		if (evt.ctrlKey || evt.metaKey) {
			if (keyCode == 118) {
				setTimeout('wproDropPasteHandler(WPro.'+obj._internalId+');', 1);
			}
			if (keyCode == 122) {
				obj.callFormatting("Undo", false, null);
				WPro.preventDefault(evt);
			}
			if (keyCode == 121) {
				obj.callFormatting("Redo", false, null);
				WPro.preventDefault(evt);
			}
			if (keyCode == 98) {
				obj.callFormatting("Bold", false, null);
				WPro.preventDefault(evt);
			}
			if (keyCode == 105) {
				obj.callFormatting("Italic", false, null);
				WPro.preventDefault(evt);
			}
			if (keyCode == 117) {
				obj.callFormatting("Underline", false, null);
				WPro.preventDefault(evt);
			}
		} else if (!evt.shiftKey && !obj._inSource) {
			if (keyCode == 13) {
				obj.lineReturn(evt)
			} else if (keyCode == 8) {
				wpro_backspace(obj, evt)
			} 
		} 
	//}
	
	obj.triggerEditorEvent('keyDown', evt);
	
	if (doRD) obj.history.addKey(keyCode);
}
function wproKeyUpHandler (obj, evt) {
	var keyCode = (evt.which || evt.charCode || evt.keyCode);
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
	//obj.closePMenuTimeout()
}
function wproMouseUpHandler(obj, evt) {
	//wp_hide_menu(obj); 
	obj.setButtonStates();
	wp_current_obj = obj; 
	WPro.currentEditor = obj; 
	//wp_select_fix(obj, evt); 
	obj.selectFix(evt);
	//WPro.closePMenu();
	//obj.closePMenuTimeout()
	obj.triggerEditorEvent('mouseUp', evt);
	obj.history.keyPresses=0;
}

// adds white space to a node with no text nodes
function wproFillContent(node) {
	if (!WPro.hasContent(node)  ) {
		//node.innerHTML = node.innerHTML.trim()
		while (node.firstChild && node.firstChild.nodeType == 1) {
			node = node.firstChild;
		}
		node.innerHTML = '&nbsp;'
	}
}
// moves cursor to beginning of tags that contain only &nbsp;
function wproSelectFix(evt) {
	var sel = this.editWindow.getSelection()
	var range = sel.getRangeAt(0)
	var startContainer = range.startContainer
	var endContainer = range.endContainer
	var startNode = startContainer.parentNode
	var endNode = endContainer.parentNode
	if (startNode != endNode) {
		return
	} else {
		while (startNode.firstChild && WPro.inline_tags.test(startNode.firstChild.nodeName)) {
			startNode = startNode.firstChild;
		}
		if (startNode.innerHTML == '&nbsp;' && startNode.firstChild && startNode.firstChild.nodeType == 3) {
			startNode = startNode.firstChild
			var rngCaret = this.editDocument.createRange();
			rngCaret.setStart(startNode, 0);
			rngCaret.collapse(true);
			sel = this.editWindow.getSelection();
			sel.removeAllRanges();
			sel.addRange(rngCaret);
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

// selection interface...
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
	editor.history.add();
	this.range.deleteContents();
	editor.history.add();
}
function wproExtractContents () {
	var editor = WPro.editors[this.editor];
	editor.history.add();
	var df = this.range.extractContents();
	editor.history.add();
	return df;
}
function wproPasteHTML (html) {
	var editor = WPro.editors[this.editor];
	html = editor.triggerHTMLFilter('design', html);
	var div = editor.editDocument.createElement("DIV")
	WPro.setInnerHTML(div, html);
	var cn = div.childNodes;
	var num = cn.length;
	editor.history.add();
	var bd = editor.history.disabled;
	editor.history.disabled = true;
	for (var i=0; i < num; i++) {
		this.insertNode(cn[i].cloneNode(true));					
	}
	editor.history.disabled = bd;
	editor.history.add();
}	
// inserts a node into the range (this overwrites any selected nodes unlike the DOM version)
function wproInsertNode (insertNode, selectNode) {
	var editor = WPro.editors[this.editor];
	editor.history.add();
	// inserting an HR
	if (insertNode.tagName) {
		if (insertNode.tagName=='HR') {
			// in mozilla we need to break apart p tags...
			if (this.getBlockContainer().tagName == 'P') {
				var evt = new Object();
				evt.stopPropagation = function () {};
				evt.preventDefault = function () {};
				editor.lineReturn(evt);
				var range = editor.selAPI.getRange()
				var p = range.getBlockContainer()
				p.parentNode.insertBefore(insertNode, p);
				editor.history.add();
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
	editor.history.add();
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
	if (sel) {
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