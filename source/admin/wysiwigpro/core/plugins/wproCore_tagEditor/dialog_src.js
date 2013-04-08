var SELECTED_TAG = null;
var TYPE_CHANGED = false;
var doInnerHTML = false;
var objectPlugin = false;
var objectPluginData = {};
objectPluginData['object'] = {};
objectPluginData['param'] = {};
objectPluginData['embed'] = {};
function initTagEditor () {
	dialog.reselectRange();
	/*if (!parentWindow.WPro) {
		mode = 'generate';
	} else if (!parentWindow.WPro.selectedTag) {
		mode = 'generate';
	}*/
	if (currentEditor.tagPath) {
		if (currentEditor.tagPath.selectedNode) {
			if (currentEditor.tagPath.selectedNode.tagName.toString()==tagName.toUpperCase() 
			|| (tagName.toUpperCase()=='OBJECT'&&currentEditor.tagPath.selectedNode.tagName=='IMG'&&currentEditor.tagPath.selectedNode.className.match(/wproFilePlugin/i))) {
				node = currentEditor.tagPath.selectedNode;
				currentEditor.tagPath.selectedNode = null;
			}
		}
	}
	if (!node) {
		
		// find node to edit
		var range = dialog.editor.selAPI.getRange();
		var tagName1 = '';
		if (range.type=='control') {
			var node = range.nodes[0];
		} else {
			var node = range.getCommonAncestorContainer();
		}
	}
	if ((tagName.toUpperCase()=='OBJECT'||tagName.toUpperCase()=='EMBED')&&node.tagName=='IMG'&&node.className.match(/wproFilePlugin/i)) {
		objectPlugin = true;
	}
	
	if (node.tagName.toString()==tagName.toUpperCase()||objectPlugin) {
		SELECTED_TAG = node;
		loadAttributes (SELECTED_TAG)
	}
	if (tagName == 'INPUT' && inputTypeChange) {
		inputTypeChange((document.dialogForm.type.value.length>1) ? document.dialogForm.type.value : 'text');
	}
	dialog.focus();
	dialog.hideLoadMessage();
}
function _getAttributes(o) {
	var attrs = String(o).match(/ [a-z]+="[^"]*"/gi);
	var arr = {};
	if (attrs) {
		var rl2 = attrs.length;
		for (var j=0; j < rl2; j++) {
			var name = attrs[j].replace(/([a-z]+)="[^"]*"/gi, "$1");
			var value = attrs[j].replace(/[a-z]+="([^"]*)"/gi, "$1");
			arr[name.trim()] = value.trim();
		}
	}
	return arr;
}
function loadAttributes (node) {
	var wp_link_attributes = /^(data|href|src|action|longdesc|profile|usemap|background|cite|classid|codebase)$/i;
	
	var elements = document.dialogForm.elements;
	var n = elements.length;
	var e;
	
	if (objectPlugin) {
		
		objectPluginData = currentEditor.plugins['wproCore_fileBrowser'].unserializeMedia(unescape(SELECTED_TAG.getAttribute("_wpro_media_data")), 'source');
		
		var d, x;
		
		if (objectPluginData['object']) {
			var arrs = ['width','height','style','class','align','border','hspace','vspace','title','alt'];
			for (i=0;i<arrs.length;i++) {
				if (arrs[i]=='style') {
					objectPluginData['object'][arrs[i]] = SELECTED_TAG.style.cssText
				} else if (arrs[i]=='class') {
					objectPluginData['object'][arrs[i]] = String(SELECTED_TAG.className).replace(/\s*wproFilePlugin\s*/i, '');
				} else if (x = SELECTED_TAG.getAttribute(arrs[i])) {
					objectPluginData['object'][arrs[i]] = x
					if (objectPluginData['embed']&&(arrs[i]=='width'||arrs[i]=='height')) {
						objectPluginData['embed'][arrs[i]] = x
					}
				}
			}
			
			var str =  currentEditor.plugins['wproCore_fileBrowser'].serializeMediaToTag(objectPluginData);
			elements['innerHTML'].value = str.replace(/\s*<object[^>]*>\s*/i,'').replace(/\s*<\/object>\s*/i,'')
		} else if (objectPluginData['embed']) {
			var arrs = ['width','height','style','class','align','border','hspace','vspace','title','alt'];
			for (i=0;i<arrs.length;i++) {
				if (arrs[i]=='style') {
					objectPluginData['embed'][arrs[i]] = SELECTED_TAG.style.cssText
				} else if (arrs[i]=='class') {
					objectPluginData['embed'][arrs[i]] = String(SELECTED_TAG.className).replace(/\s*wproFilePlugin\s*/i, '');
				} else if (x = SELECTED_TAG.getAttribute(arrs[i])) {
					objectPluginData['embed'][arrs[i]] = x
				}
			}
			
			var str =  currentEditor.plugins['wproCore_fileBrowser'].serializeMediaToTag(objectPluginData);
			elements['innerHTML'].value = str.replace(/\s*<embed[^>]*>\s*/i,'').replace(/\s*<\/embed>\s*/i,'')
		}
		
	}
	
	for (var i=0;i<n;i++) {
		if (elements[i].className == 'chooserButton') continue;
		if (objectPlugin) {
			var d = ''
			if (objectPluginData['object']) {
				d = objectPluginData['object']
			} else {
				d = objectPluginData['embed']
			}			
			if (d[elements[i].getAttribute('id').toLowerCase()]) {
				e = d[elements[i].getAttribute('id').toLowerCase()]
				if (elements[i].setColor) {
					elements[i].setColor(e);
				} else if (elements[i].type == 'checkbox' && e==elements[i].getAttribute('id').toLowerCase()) {
					elements[i].checked = true;
				} else if (wp_link_attributes.test(elements[i].getAttribute('id'))) {					
					elements[i].value = dialog.urlFormatting(e);
				} else {
					elements[i].value = e;
				}
			}		
		} else {
			if (elements[i].getAttribute('id')!='innerHTML') {
				if (wp_link_attributes.test(elements[i].getAttribute('id'))) {
					e = node.getAttribute(elements[i].getAttribute('id'), 2)
				} else {
					e = node.getAttribute(elements[i].getAttribute('id') )
				}
				if (e) {
					//if (!e.specified) continue;
					if (elements[i].setColor) {
						elements[i].setColor(e);
					} else if (elements[i].type == 'checkbox') {
						elements[i].checked = true;
					} else if (wp_link_attributes.test(elements[i].getAttribute('id'))) {
						var wpe
						if (wpe = node.getAttribute('_wpro_'+elements[i].getAttribute('id'))) {
							elements[i].value = dialog.urlFormatting(wpe);
						} else {
							elements[i].value = dialog.urlFormatting(node.getAttribute(elements[i].getAttribute('id'), 2));	
						}
					} else {
						elements[i].value = e;
					}
				}
			} else {
				if (node.innerHTML) {
					elements[i].value = currentEditor.sourceFormatting(node.innerHTML);
				}
			}
		}
	}
	
	document.getElementById('class').value = node.className.toString().replace(/wproGuide/gi, '').replace(/wproFilePlugin/gi, '').trim();
	document.getElementById('style').value = WPro.styleFormatting(node.style.cssText);
}
function formAction () {
	var wp_attribute_allowed_empty = /^(alt|title|action|href|src|value)$/i;
	var wp_wpro_link_attributes = /^(href|src|action)$/i;
	var v;
	
	var UDBeforeState = currentEditor.history.pre();
	
	if (objectPlugin&&SELECTED_TAG) {
		
		var str = '';
		var strE = '';
		var d;
		if (objectPluginData['object']) {
			d = objectPluginData['object'];	
			str = '<object';
			strE = '</object>';
		} else {
			d = objectPluginData['embed']
			str = '<embed';
			strE = '</embed>';
		}
		
		var n = tagEditorAttributes.length
		for (var i=0;i<n;i++) {
			if (tagEditorAttributes[i]) {
				tagName = tagEditorAttributes[i].toLowerCase()
				if (v = document.getElementById(tagEditorAttributes[i])) {	
					if (v.type == 'checkbox') {
						if (v.checked) {
							d[tagName] = tagName;
						} else if (d[tagName]) {
							delete d[tagName];
						}
					} else if (v.value!='') {
						d[tagName] = v.value
					} else if (d[tagName]) {
						delete d[tagName]
					}
				}
			}
		}
		
		var width = '';
		var height = '';
		var style = '';
		var className = '';
		var align = '';
		var border = '';
		var hspace = '';
		var vspace = '';
		var title = '';
		var alt='';
				
		for(var x in d) {
			if (x == 'width') {
				width = d[x];
			}
			if (x == 'height') {
				height = d[x];
			}
			if (x == 'style') {
				style = d[x];
			}
			if (x == 'class') {
				className = ' '+d[x];
			}
			if (x == 'align') {
				align = d[x];
			}
			if (x == 'border') {
				border = d[x];
			}
			if (x == 'hspace') {
				hsapce = d[x];
			}
			if (x == 'vspace') {
				vspace = d[x];
			}
			if (x == 'title') {
				title = d[x];
			}
			if (x == 'alt') {
				alt = d[x];
			}
			str+=' '+x+'="'+dialog.htmlSpecialChars(d[x])+'"';
		}
		
		str+='>'+document.getElementById('innerHTML').value+strE
			
		str = currentEditor.plugins['wproCore_fileBrowser'].unserializeMediaTag(str);

		title = currentEditor.plugins['wproCore_fileBrowser'].serializeMedia(str);
						
		if (title) SELECTED_TAG.setAttribute('_wpro_media_data', escape(title));
		if (width) SELECTED_TAG.setAttribute('width', width);
		if (height) SELECTED_TAG.setAttribute('height', height);
		if (hspace) SELECTED_TAG.setAttribute('hspace', hspace);
		if (vspace) SELECTED_TAG.setAttribute('vspace', vspace);
		if (align) SELECTED_TAG.setAttribute('align', align);
		if (border) SELECTED_TAG.setAttribute('border', border);
		if (title) SELECTED_TAG.setAttribute('title', title);
		if (alt) SELECTED_TAG.setAttribute('alt', alt);
		if (className) SELECTED_TAG.className = 'wproFilePlugin'+className;
		if (style) SELECTED_TAG.style.cssText = style;
		
	} else {
		
		if (SELECTED_TAG) {
			
			if (TYPE_CHANGED) {
				var oldNode = SELECTED_TAG;
				SELECTED_TAG = currentEditor.editDocument.createElement('INPUT');
			}
			var n = tagEditorAttributes.length
			for (var i=0;i<n;i++) {
				if (tagEditorAttributes[i]) {
					if (v = document.getElementById(tagEditorAttributes[i])) {				
						var nodeName = tagEditorAttributes[i];
						var nodeValue			
						if (v.type == 'checkbox') {
							if (v.checked) {
								eval("SELECTED_TAG."+nodeName+"=true");
								nodeValue = true;
							} else {
								eval("SELECTED_TAG."+nodeName+"=false");
								SELECTED_TAG.removeAttribute(nodeName);
								continue;
							}
						} else {
							nodeValue = v.value.toString().trim();
						}
						if (wp_wpro_link_attributes.test(nodeName)) {
							SELECTED_TAG.setAttribute(nodeName, nodeValue);
							SELECTED_TAG.setAttribute('_wpro_'+nodeName, nodeValue);
						} else if (nodeName == 'class') {
							SELECTED_TAG.className = nodeValue;
						} else if (nodeName == 'style') {
							SELECTED_TAG.style.cssText = WPro.styleFormatting(nodeValue);
						} else if (nodeValue == '' && !wp_attribute_allowed_empty.test(nodeName)) {
							SELECTED_TAG.removeAttribute(nodeName);
						} else if (nodeValue == '' && !SELECTED_TAG.getAttribute(nodeName)) {
							// do nothing
						} else {
							if (SELECTED_TAG.getAttribute(nodeName)!=nodeValue) {
								SELECTED_TAG.setAttribute(nodeName, nodeValue);
							}
						}
					}
				}
			}
			if (doInnerHTML && SELECTED_TAG.innerHTML && document.getElementById('innerHTML')) {
				var str = document.getElementById('innerHTML').value;
				str = WPro.escapeServerTags(str);
				if (WPro.isGecko) {
					str = str.replace(/<strong>/gi, '<b>').replace(/<strong /gi, '<b ').replace(/<\/strong>/gi, '</b>').replace(/<em>/gi, '<i>').replace(/<em /gi, '<i ').replace(/<\/em>/gi, '</i>');
				}
				WPro.setInnerHTML(SELECTED_TAG, currentEditor.triggerHTMLFilter('design',str));
			}
			if (TYPE_CHANGED&&oldNode) {
				oldNode.parentNode.replaceChild(SELECTED_TAG, oldNode);
			}		
			if (currentEditor._guidelines) {
				var tagName = SELECTED_TAG.tagName
				if (tagName=='TABLE'||tagName=='A'||tagName=='FORM') {
					currentEditor.showGuidelines();
				}
			}
		}
	}
	currentEditor.history.post(UDBeforeState);
	dialog.close();
	return false;
}