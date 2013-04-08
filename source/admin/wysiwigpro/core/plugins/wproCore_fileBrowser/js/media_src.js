// JavaScript Document
// constrain functions!!
//
fbObj.prototype.constrains = {};
fbObj.prototype.setConstrain = function (fieldX, fieldY, valueX, valueY) {
	
	// check ration
	var ratioX, ratioY;
	
	if (typeof(valueX)=='undefined') {
		valueX = document.dialogForm.elements[fieldX].value;
	}
	if (typeof(valueY)=='undefined') {
		valueY = document.dialogForm.elements[fieldY].value;
	}
	if (!isNaN(valueX) && !isNaN(valueY)) {
		ratioY = parseInt(valueX)/parseInt(valueY);
		ratioX = parseInt(valueY)/parseInt(valueX);
	} else {
		ratioX = 1;	
		ratioY = 1
	}
	
	this.constrains[fieldX+'-'+fieldY] = {x:ratioX,y:ratioY};
	
}
fbObj.prototype.removeConstrain = function (fieldX, fieldY) {
	if (this.constrains[fieldX+'-'+fieldY]) {
		delete this.constrains[fieldX+'-'+fieldY];
	}	
}
fbObj.prototype.doConstrain = function (fieldX, fieldY, axis) {
	if (this.constrains[fieldX+'-'+fieldY]) {
		ratio = this.constrains[fieldX+'-'+fieldY];
		if (axis == 'y') {	
			if (document.dialogForm.elements[fieldY].value=='') {
				document.dialogForm.elements[fieldX].value = '';
			} else {
				document.dialogForm.elements[fieldX].value = Math.round(document.dialogForm.elements[fieldY].value * ratio.y);
			}
		} else if (axis == 'x') {
			if (document.dialogForm.elements[fieldX].value == '') {
				document.dialogForm.elements[fieldY].value = '';
			} else {
				document.dialogForm.elements[fieldY].value = Math.round(document.dialogForm.elements[fieldX].value * ratio.x);
			}
		}
	}
}


fbObj.prototype.getCommonMediaOptions = function () {
	var form = document.dialogForm;
	var o = {};
	var s = '';
	var prefix = 'media';
	
	if (form.elements[prefix+'border']) {
		if (currentEditor.strict) {
			if (form.elements[prefix+'border'].value!='') {
				if (form.elements[prefix+'border'].value=='0') {
					s += 'border-width:'+form.elements[prefix+'border'].value+'px;';
				} else {
					s += 'border:'+form.elements[prefix+'border'].value+'px solid #000000;';
				}
			}
		} else {
			o['border'] = form.elements[prefix+'border'].value;
		}
	}
	if (form.elements[prefix+'screenTip']) {
		o['title'] = form.elements[prefix+'screenTip'].value;
	}
	if (form.elements[prefix+'align']) {
		if (currentEditor.strict) {
			switch (form.elements[prefix+'align'].value) {
				case 'top': case 'middle': case 'bottom':
					s += 'vertical-align:'+form.elements[prefix+'align'].value+';';
					break;
				case 'left': case 'right':
					s += 'float:'+form.elements[prefix+'align'].value+';';
					break;				
			}
		} else {
			o['align'] = form.elements[prefix+'align'].value;	
		}
	}
	if (form.elements[prefix+'mtop']) {
		var pxReg = /^(-*[0-9 ]+)$/;
		if (form.elements[prefix+'mtop'].value) {
			if (pxReg.test(form.elements[prefix+'mtop'].value)) {
				s += 'margin-top: '+form.elements[prefix+'mtop'].value+'px;';
			} else {
				s += 'margin-top: '+form.elements[prefix+'mtop'].value+';';
			}
		}
		if (form.elements[prefix+'mbottom'].value) {
			if (pxReg.test(form.elements[prefix+'mbottom'].value)) {
				s += 'margin-bottom: '+form.elements[prefix+'mbottom'].value+'px;';
			} else {
				s += 'margin-bottom: '+form.elements[prefix+'mbottom'].value+';';
			}
		}
		if (form.elements[prefix+'mright'].value) {
			if (pxReg.test(form.elements[prefix+'mright'].value)) {
				s += 'margin-right: '+form.elements[prefix+'mright'].value+'px;';
			} else {
				s += 'margin-right: '+form.elements[prefix+'mright'].value+';';
			}
		}
		if (form.elements[prefix+'mleft'].value) {
			if (pxReg.test(form.elements[prefix+'mleft'].value)) {
				s += 'margin-left: '+form.elements[prefix+'mleft'].value+'px;';
			} else {
				s += 'margin-left: '+form.elements[prefix+'mleft'].value+';';
			}
		}
	}
	if (s) o['style'] = s;
	
	return o;

}
fbObj.prototype.mediaPreview = function () {
	var form = document.dialogForm;
	
	var wrappreview = document.getElementById('mediawrappreview');
	
	var o = this.getCommonMediaOptions();
	
	for (var a in o) {
		if (o=='class') {
			if (wrappreview) wrappreview.className = o[a];
		} else if (a=='style') {
			if (wrappreview) wrappreview.style.cssText = o[a];
			if (wrappreview) wrappreview.style.borderWidth='0';
		} else {
			if (wrappreview) wrappreview.setAttribute(a, o[a]);
		}
	}
	if (wrappreview) wrappreview.style.width = '48px';
	if (wrappreview) wrappreview.style.height = '48px';
	
	if (this.currentRemotePlugin&&this.currentRemotePrefix) {
		if (this.embedPlugins[this.currentRemotePlugin].onMediaPreview) {
			this.embedPlugins[this.currentRemotePlugin].onMediaPreview(this.currentRemotePrefix);
		}
	}
	
	dialog.focus();
	
}
fbObj.prototype.mediaProperties = {};
fbObj.prototype.getMediaProperties = function () {
	if (this.mediaProperties.length) {
		return this.mediaProperties;
	} else {
		var range = currentEditor.selAPI.getRange();
		if (range.type=='control'||currentEditor._selectedNode) {
			var node = currentEditor._selectedNode ? currentEditor._selectedNode : range.nodes[0];
			var data = unescape(node.getAttribute('_wpro_media_data'));
			var o = currentEditor.plugins['wproCore_fileBrowser'].unserializeMedia(data, 'design');
			var attrs = ['width','height','style','class','hspace','vspace','border','align','title'];
			for (var i=0;i<attrs.length;i++) {
				if (attrs[i]=='class') {
					if (node.className.replace(/[\s]*wproFilePlugin[\s]*/, '')) {
						if (o['object']) o['object']['class'] = node.className.replace(/\s*wproFilePlugin\s*/, '')
						if (o['embed']) o['embed']['class'] = node.className.replace(/\s*wproFilePlugin\s*/, '')	
					}
				} else if (attrs[i]=='style') {
					if (node.style.cssText) {
						if (o['object']) o['object']['style'] = node.style.cssText;	
						if (o['embed']&&!o['object']) o['embed']['style'] = node.style.cssText;	
					}
				} else {
					if (node.getAttribute(attrs[i])) {
						if (o['object']) o['object'][attrs[i]] = node.getAttribute(attrs[i]);
						if (o['embed']) o['embed'][attrs[i]] = node.getAttribute(attrs[i]);
					}
				}
			}	
			this.mediaProperties = o;
			return this.mediaProperties;
		}
	}
}
fbObj.prototype.insertMedia = function(plugin, attrs, style, preserve) {
	
	var form = document.dialogForm;
	var width = '';
	var height = '';
	var istyle = '';
	var className = '';
	var hspace = '';
	var vspace = '';
	var title = '';
	var border = '';
	var align = '';
	
	var mediaData = '';
	
	var pane = '';
	var panes = ['site', 'email', 'web', 'doc', 'fileBrowser']
	var n = panes.length;
	for (var i=0; i<n; i++) {
		var f
		if (f=document.getElementById(panes[i])) {
			if (f.style.display=='block') {
				pane = panes[i]
				break;
			} 
		}
	}

	if (attrs['object']) {
		d = attrs['object']
	} else if (attrs['embed']) {
		d = attrs['embed']
	}
	
	if (pane=='web') {
		var o = this.getCommonMediaOptions();
		for (x in o) {
			if (x=='style'&&d[x]) {
				d[x] = d[x].replace(/margin[a-z\-]*[\s]*:[^;]+[;]*/gi,'').replace(/^;/, '') + ';' + o[x];
			} else if (x=='class'&&d[x]) {
				d[x] = d[x] + ' ' + o[x];
			} else {
				d[x] = o[x];
			}
		}
		if (!style) {
			if (form.elements['mediastyle']) {
				style = form.elements['mediastyle'].value
			}
		}	
	}
	
	if (style) {
		if (style.match('class=')) {
			style = style.replace(/ class="([^"]*)"/gi, " class=\"wproFilePlugin $1\"");
		} else {
			style += ' class="wproFilePlugin"';
		}
	}
	
	for(var x in d) {
		if (x == 'width') {
			width = d[x];
		} else if (x == 'height') {
			height = d[x];
		} else if (x == 'style') {
			istyle = d[x];
		} else if (x == 'class') {
			className = ' '+d[x];
		} else if (x == 'hspace') {
			hsapce = d[x];
		} else if (x == 'vspace') {
			vspace = d[x];
		} else if (x == 'align') {
			align = d[x];
		} else if (x == 'border') {
			border = d[x];
		} else if (x == 'title') {
			title = d[x];
		}
	}
		
	var mediaData = currentEditor.plugins['wproCore_fileBrowser'].serializeMedia(attrs);
	
	//var location = document.location.toString().replace(/^([\s\S]*\/)[^\/]*$/i, "$1");
	var location = dialog.domain+dialog.URL;
	
	currentEditor.insertImage(location+'core/images/placeholder.gif', {'width':width,'height':height,'style':istyle,'class':'wproFilePlugin'+className,'hspace':hspace,'vspace':vspace,'border':border,'align':align,'title':title,'_wpro_media_data':escape(mediaData)}, style);
	
	
}
