function wproTagPath (editor) {
	this.editor = editor;
	this.tags = [];
	this.selectedItem = null;
	this.selectedTag = null;
	
	this.kill = wproTagPath_kill;
	this.build = wproTagPath_build;
	this.deleteTag = wproTagPath_deleteTag;
	this.removeTag = wproTagPath_removeTag;
	this.select = wproTagPath_select;
}
function wproTagPath_kill () {
	var editor = WPro.editors[this.editor];
	var tagPathHolder = editor.tagPathHolder;
	if ((!editor._movingToDesign && !editor._inDesign)||editor._movingToSource||editor._movingToPreview) {
		tagPathHolder.style.display = 'none';
	}
	while (tagPathHolder.lastChild) {
		tagPathHolder.removeChild(tagPathHolder.lastChild);
	}
	tagPathHolder.appendChild(document.createTextNode(String.fromCharCode(160)));
	this.selectedItem = null;
	this.selectedTag = null;
}
function wproTagPath_build () {
	var editor = WPro.editors[this.editor];
	var tagPathHolder = editor.tagPathHolder;
	this.selectedItem = null;
	this.selectedTag = null;
	this.tags = [];
	var range = editor.selAPI.getRange();
	if (range.type == 'control') {
		for (var i=0; i<range.nodes.length; i++) {
			this.tags.push(range.nodes[i]);	
		}
	}
	var container = range.getCommonAncestorContainer();
	var topNode = 'BODY';
	if (!editor.snippet) {
		topNode = 'HTML';
	}		
	while (container.parentNode && container.tagName != topNode) {
		if (container.nodeType != 1) {
			container = container.parentNode;
		}
		this.tags.push(container);
		container = container.parentNode
	}
	// clear old nodes
	this.kill();
	// build nodes
	var l = this.tags.length
	for (var i = 0; i < l; i++) {
		
		var f = document.createElement('BUTTON');
		f.setAttribute('type', 'button');
		var s = this.tags[i].tagName.toString();
		if (s=='IMG' && this.tags[i].className.match(/wproFilePlugin/i)) {
			if (!/('object'\:\{|\%27object\%27\%3A\%7B)/i.test(String(this.tags[i].getAttribute("_wpro_media_data")))) {
				s = 'EMBED';
			} else {
				s = 'OBJECT';	
			}
		}
		var t = document.createTextNode('<'+s+'>');
		f.appendChild(t);
		f.className = 'wproReady';
				
		var a = WPro.getNodeAttributesString(this.tags[i])/*.replace(/"/gi, "&quot;")*/.replace(/ _([a-z_]+)="[^"]*"/gi, '');
		if (a!='') a = ' '+a;
		f.title = '<'+s+a+'>';
		
		var ss = editor.lng['selecttag'].replace('##tagname##', '&lt;'+s+'&gt;');
		var rs = editor.lng['removetag'].replace('##tagname##', '&lt;'+s+'&gt;');
		var ds = editor.lng['deletetag'].replace('##tagname##', '&lt;'+s+'&gt;');
		var es = editor.lng['tageditor'].replace('##tagname##', '&lt;'+s+'&gt;');
		
		// ["titleSeparator", "&lt;'+s+a+'&gt;"],
		
		var e = 'f.onmousedown = function () {WPro.'+editor._internalId+'.showButtonMenu( this, [["forecolor","'+ss+'","WPro.'+editor._internalId+'.tagPath.select('+i+')","spacer.gif","22","22",""], ["forecolor","'+es+'","WPro.'+editor._internalId+'.showTagEditor(true,'+i+')","spacer.gif","22","22",""]'
																																																																																																																																											
		if (s!='BODY') {
			e+=',["separator"]';
			if (this.tags[i].childNodes.length && !/^(table|td|th|tr|ul|ol)$/i.test(s)) {
				e += ',["forecolor","'+rs+'","WPro.'+editor._internalId+'.tagPath.removeTag(['+i+'])","spacer.gif","22","22",""]';	
			}
			e+=',["forecolor","'+ds+'","WPro.'+editor._internalId+'.tagPath.deleteTag(['+i+'])","delete.gif","22","22",""]';
		}
		e+= ',]);};'
				
		e+='f.onmouseup = function () {WPro.'+editor._internalId+'._mUp(this) };f.onmouseover = function () {WPro.'+editor._internalId+'._mOver(this) };f.onmouseout = function () {WPro.'+editor._internalId+'._mOut(this) };';
		eval(e);
		
		if (i==0 || i < range.nodes.length) {
			f.style.fontWeight='bold';
			this.selectedItem = f;
		}
		tagPathHolder.insertBefore(f,tagPathHolder.firstChild);
		
	}
}
function wproTagPath_deleteTag (i, ignore) {
	var editor = WPro.editors[this.editor];
	//if (confirm(editor.lng['confirmDeleteTag'])||ignore) {
		var UDBeforeState = editor.history.pre();
		var tagPathHolder = editor.tagPathHolder;
		if (this.tags[i].parentNode && this.tags[i].tagName != 'BODY') {
			this.tags[i].parentNode.removeChild(this.tags[i]);
		}
		editor.history.post(UDBeforeState);
	//}
	this.build();
}
function wproTagPath_removeTag (i) {
	var editor = WPro.editors[this.editor];
	var UDBeforeState = editor.history.pre();
	var tagPathHolder = editor.tagPathHolder;
	if (this.tags[i].parentNode && this.tags[i].tagName != 'BODY') {
		if (this.tags[i].childNodes.length) {
			WPro.removeNode(this.tags[i]);
		} else {
			this.deleteTag(i);
		}
	}
	editor.history.post(UDBeforeState);
	this.build();
}
function wproTagPath_select (i) {
	var editor = WPro.editors[this.editor];
	var tagPathHolder = editor.tagPathHolder;
	if (this.tags[i].parentNode) {
		var range = editor.selAPI.getRange();
		range.selectNodeContents(this.tags[i]);
		range.select();
		this.selectedTag = this.tags[i];
		if (this.selectedItem)this.selectedItem.style.fontWeight = '';
		//var l = this.tags.length
		var as = tagPathHolder.getElementsByTagName('BUTTON');
		this.selectedItem = as.item(as.length-(i+1));
		this.selectedItem.style.fontWeight='bold';
	}
}