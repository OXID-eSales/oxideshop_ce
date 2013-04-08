function wproPlugin_wproCore_styleWithCSS(){}
wproPlugin_wproCore_styleWithCSS.prototype.init=function(EDITOR){
	// formatting handlers
	// font style formatting handlers
	EDITOR.addFormattingHandler('FontName',wproPlugin_styleWithCSS_callF);
	EDITOR.addFormattingHandler('FontSize',wproPlugin_styleWithCSS_callF);
	EDITOR.addFormattingHandler('ForeColor',wproPlugin_styleWithCSS_callF);
	EDITOR.addFormattingHandler('BackColor',wproPlugin_styleWithCSS_callF);
	
	EDITOR.addFormattingHandler('underline',wproPlugin_styleWithCSS_callF);
	EDITOR.addFormattingHandler('strikethrough',wproPlugin_styleWithCSS_callF);
	
	// justification formatting handlers
	EDITOR.addFormattingHandler('JustifyNone',wproPlugin_styleWithCSS_callF);
	EDITOR.addFormattingHandler('JustifyLeft',wproPlugin_styleWithCSS_callF);
	EDITOR.addFormattingHandler('JustifyCenter',wproPlugin_styleWithCSS_callF);
	EDITOR.addFormattingHandler('JustifyRight',wproPlugin_styleWithCSS_callF);
	EDITOR.addFormattingHandler('JustifyFull',wproPlugin_styleWithCSS_callF);
	
	// button state handlers
	EDITOR.addButtonStateHandler('underline',wproPlugin_styleWithCSS_td_bsh);
	EDITOR.addButtonStateHandler('strikethrough',wproPlugin_styleWithCSS_td_bsh);
	
	EDITOR.addButtonStateHandler('justifyleft',wproPlugin_styleWithCSS_bsh);
	EDITOR.addButtonStateHandler('justifycenter',wproPlugin_styleWithCSS_bsh);
	EDITOR.addButtonStateHandler('justifyright',wproPlugin_styleWithCSS_bsh);
	EDITOR.addButtonStateHandler('justifyfull',wproPlugin_styleWithCSS_bsh);
	
	// formatting value handlers (used for generating values for the drop down menus)
	EDITOR.addFormattingValueHandler('FontName',wproPlugin_styleWithCSS_getFV);
	EDITOR.addFormattingValueHandler('FontSize',wproPlugin_styleWithCSS_getFV);
}
// button state handler function
function wproPlugin_styleWithCSS_bsh(EDITOR,srcElement,cid,inTable,inA,range){
	var ret = 'wproReady';
	if (range.type == 'control') {
		var node = range.nodes[0];
	} else {
		var node = range.getBlockContainer();
	}
	var style = String(EDITOR.getComputedStyle(node, 'textAlign'));
	switch(cid) {
		case 'justifyleft' :
			ret = (style.match(/left/i)||style.match(/start/i))?'wproLatched':'wproReady';
			break;
		case 'justifycenter' :
			ret = (style.match(/center/i))?'wproLatched':'wproReady';
			break;
		case 'justifyright' :
			ret = (style.match(/right/i))?'wproLatched':'wproReady';
			break;
		case 'justifyfull' :
			ret = (style.match(/justify/i))?'wproLatched':'wproReady';
			break;
	}
	return ret;
}
// button state handler function for underline
function wproPlugin_styleWithCSS_td_bsh(EDITOR,srcElement,cid,inTable,inA,range){
	var ret = 'wproReady';
	if (range.type == 'control') {
		var node = range.nodes[0];
	} else {
		var node = WPro.getParent(range.getStartContainer());
	}
	var style = String(EDITOR.getComputedStyle(node, 'textDecoration'))
	switch(cid) {
		case 'underline' :
			ret = (style.match(/underline/i))?'wproLatched':'wproReady';
			break;
		case 'strikethrough' :
			ret = (style.match(/line-through/i))?'wproLatched':'wproReady';
			break;
	}
	return ret;
}

// formatting handler function
function wproPlugin_styleWithCSS_callF(EDITOR, sFormatString, sValue) {
	switch(sFormatString) {
		case "fontname" :
			if (sValue != "wp_bogus_font") {
				wproPlugin_styleWithCSS_applyFont(EDITOR, 'fontFamily', sValue);
			} else {
				WPro.callCommand(EDITOR.editDocument, "fontname", false, sValue);
			}
			break;
		case "fontsize" :
			wproPlugin_styleWithCSS_applyFont(EDITOR, 'fontSize', sValue);
			break;
		case "forecolor" :
			wproPlugin_styleWithCSS_applyFont(EDITOR, 'color', sValue);
			break;
		case "backcolor" :
			wproPlugin_styleWithCSS_applyFont(EDITOR, 'backgroundColor', sValue);
			break;
		case "underline" :
			var range = EDITOR.selAPI.getRange();
			if (range.type == 'control') {
				var node = range.nodes[0];
			} else {
				var node = WPro.getParent(range.getStartContainer());
			}
			var style = EDITOR.getComputedStyle(node, 'textDecoration')
			sValue = (style.match(/underline/i))?'none':'underline';
			wproPlugin_styleWithCSS_applyFont(EDITOR, 'textDecoration', sValue);
			break;
		case "strikethrough" :
			var range = EDITOR.selAPI.getRange();
			if (range.type == 'control') {
				var node = range.nodes[0];
			} else {
				var node = WPro.getParent(range.getStartContainer());
			}
			var style = EDITOR.getComputedStyle(node, 'textDecoration')
			sValue = (style.match(/line-through/i))?'none':'line-through';
			wproPlugin_styleWithCSS_applyFont(EDITOR, 'textDecoration', sValue);
			break;
		case "justifynone" :
			EDITOR.applyStyle('*block* style="text-align:inherit"', false , false , true);
			break;
		case "justifyleft" :
			EDITOR.applyStyle('*block* style="text-align:left"', false , false , true);
			break;
		case "justifycenter" :
			EDITOR.applyStyle('*block* style="text-align:center"', false , false , true);
			break;
		case "justifyright" :
			EDITOR.applyStyle('*block* style="text-align:right"', false , false , true);
			break;
		case "justifyfull" :
			EDITOR.applyStyle('*block* style="text-align:justify"', false , false , true);
			break;
	}		
}
/* Applies any inline style value using span tags */
wproPlugin_styleWithCSS_applyFont = function (EDITOR, style, value) {
	if (!value) {
		value = '';	
	}
	
	var range = EDITOR.selAPI.getRange();
	if (range.toString().length == 0 && range.type != 'control') {
		if (!WPro.isIE) {
			var s = EDITOR.editDocument.createElement('SPAN');
			if (value) {
				eval('s.style.'+style+' = "'+WPro.addSlashes(value)+'"');
			}
			range.insertNode(s);
			range.selectNodeContents(s);
			range.select();
		}
	} else {
		
		// apply font using bogus font name
		WPro.callCommand(EDITOR.editDocument, "FontName", false, "wp_bogus_font");
		var spans = [];
		
		// Find all bogus font tags and convert them to span and add attributes, add the new span tags to an array for further processing...
		wproPlugin_styleWithCSS_applyFont_font2Span(EDITOR, EDITOR.editDocument.body, style, value, spans, false);
		
		// combine or remove span tags nested within this span
		wproPlugin_styleWithCSS_applyFont_resolveNesting(EDITOR, EDITOR.editDocument.body, style, value, spans);
		
		// re-select the selection
		if (WPro.browserType != 'safari') {
			range = EDITOR.selAPI.createRange();
			while (spans.length && !spans[0]) {
				spans.shift();
			}
			while (!spans[spans.length-1]) {
				spans.pop();
			}
			if (!WPro.isIE) {
				range.range.setStart(spans[0], 0);
				range.range.setEnd(spans[spans.length-1], spans[spans.length-1].childNodes.length);
				range.select();
			}
		}
	}

	EDITOR.focus();
	
}
// helper function: converts font tags to span tags
wproPlugin_styleWithCSS_applyFont_font2Span = function (EDITOR, node, style, value, retArr, nested) {
	
	//if (WPro.isSafari) {
	//	var fonts = node.getElementsByTagName("SPAN")
	//} else {
		var fonts = node.getElementsByTagName("FONT")
	//}
	var n = fonts.length
	var j = 0
 	//if (style == 'backgroundColor' || style=='color') {
		//value = WPro.rgbToHex(value);
	//}
	for (var i = 0; i < n; i++) {
		if (fonts[j]) {
			if (fonts[j].getAttribute('face') == "wp_bogus_font" || fonts[j].style.fontFamily == "wp_bogus_font") {
				/*
				if (WPro.isSafari) {
					var cNode = fonts[j];
					if (value) {
						eval('cNode.style.'+style+' = "'+WPro.addSlashes(value)+'"');
					} else if (eval('cNode.style.'+style)) {
						WPro.removeStyleAttribute(cNode, style);
					}
					if (fonts[j].style.fontFamily == "wp_bogus_font") {
						WPro.removeStyleAttribute(fonts[j], 'fontFamily');
					}
					retArr.push(fonts[j]);
					j++;
				} else {*/
					
					var newNode = EDITOR.editWindow.document.createElement("SPAN")
					//WPro.addAttributes(newNode, attributes, origElm)
					if (value) {
						eval('newNode.style.'+style+' = "'+WPro.addSlashes(value)+'"');
					} else if (eval('newNode.style.'+style)) {
						WPro.removeStyleAttribute(newNode, style);
					}
					
					wproPlugin_styleWithCSS_applyFont_font2Span(EDITOR, fonts[j], style, value, retArr, true)
					
					//try {
						WPro.setInnerHTML(newNode, fonts[j].innerHTML);
						fonts[j].parentNode.insertBefore(newNode, fonts[j].nextSibling)
						fonts[j].parentNode.removeChild(fonts[j]);
						retArr.push(newNode);
					//} catch (e) {
						//j++
					//}
				/*}*/
			} else {
				j++
			}
		} else {
			j++
		}
	}
}
// helper function: fixes nested font tags
wproPlugin_styleWithCSS_applyFont_resolveNesting = function (EDITOR, node, style, value, spans) {
	n = spans.length;
	//alert(n);
	var j = 0;
	for (var i=0;i<n;i++) {
		if (spans[j]) {
			// check for a matching only child
			var cn = spans[j].childNodes;
			if (cn.length == 1) {
				if (spans[j].firstChild.tagName) {
					if (spans[j].firstChild.tagName == 'SPAN') {
						var fc = spans[j].firstChild
						WPro.addAttributes(spans[j], fc.attributes, fc)
						if (value) {
							eval('spans[j].style.'+style+' = "'+WPro.addSlashes(value)+'"');
						} else if (eval('spans[j].style.'+style)) {
							WPro.removeStyleAttribute(spans[j], style);
						}
						var cn = fc.childNodes;
						for (var m=0; m<cn.length; m++) {
							spans[j].appendChild(cn[m].cloneNode(true));
						}
						spans[j].removeChild(fc);
						//j++
						//continue;
					}
				}
			}
			
			// check that this isn't an only matching child
			var p = spans[j].parentNode
			if (p.tagName) {
				var cn = p.childNodes;
				if (p.tagName == 'SPAN' || WPro.blocks.test(p.tagName)) {
					if (cn.length == 1) {
						WPro.addAttributes(p, spans[j].attributes, spans[j])
						if (value) {
							eval('p.style.'+style+' = "'+WPro.addSlashes(value)+'"');
						} else if (eval('p.style.'+style)) {
							WPro.removeStyleAttribute(p, style);
						}
						var cn = spans[j].childNodes;
						for (var m=0; m<cn.length; m++) {
							p.appendChild(cn[m].cloneNode(true));
						}
						p.removeChild(spans[j]);
						j++;
						continue;
					}
				}
			}
			
			// check that this doesn't have a matching next sibling
			var s = spans[j].nextSibling;
			if (s && s.tagName) {
				if (s.tagName == 'SPAN') {
					if (WPro.getNodeAttributesString(s) == WPro.getNodeAttributesString(spans[j])) {
						var cn = s.childNodes;
						for (var m=0; m<cn.length; m++) {
							spans[j].appendChild(cn[m].cloneNode(true));
						}
						s.parentNode.removeChild(s);
					}
				}
			}
			// check that this doesn't have a matching previous sibling
			var s = spans[j].previousSibling;
			if (s && s.tagName) {
				if (s.tagName == 'SPAN') {
					if (WPro.getNodeAttributesString(s) == WPro.getNodeAttributesString(spans[j])) {
						var cn = s.childNodes;
						for (var m=cn.length-1; m>-1; m--) {
							spans[j].insertBefore(cn[m].cloneNode(true), spans[j].firstChild);
						}
						s.parentNode.removeChild(s);
					}
				}
			}
			
			// fix nested childred
			var s = spans[j].getElementsByTagName('SPAN');
			var n2 = s.length;
			var l = 0;
			for (var k=0;k<n2;k++) {
				if (s[l]) {
					var node = s[l];
					//eval('node.style.'+style+'=\'\'');
					WPro.removeStyleAttribute(node, style);
					// check attributes and if has none remove the node...
					var kill = true;
					if (node.className || node.style.cssText != '') {
						var kill = false;
					} else {
						var a = node.attributes;
						var n3 = a.length
						for (var m=0; m < n3; m++) {
							if (a[m].specified && a[m].nodeName!='style' && a[m].nodeName!='class' && a[m].nodeValue != '') {
								kill = false;
								break;
							}
						}
					}
					if (kill) {
						var pnode = node.parentNode;
						var cn = node.childNodes;
						for (var m=0; m<cn.length; m++) {
							pnode.insertBefore(cn[m].cloneNode(true), node );
						}
						pnode.removeChild(node);
					} else {
						l++;	
					}
				} else {
					l++;	
				}
			}
			
			j++;	
		}
	}
}

// formatting value query function: gets display text for menus
function wproPlugin_styleWithCSS_getFV(EDITOR, com) {
	var value = '';
	var range = EDITOR.selAPI.getRange();
	var rule = '';
	switch (com) {
		case 'fontname' :
			rule = 'fontFamily';
			break;
		case 'fontsize' :
			rule = 'fontSize';
			break;
	}
	if (rule) {
		if (range.type == 'control') {
			var node = range.nodes[0];
		} else {
			var node = WPro.getParent(range.getStartContainer());
		}
		value = EDITOR.getComputedStyle(node, rule);
	}
	return value;
}