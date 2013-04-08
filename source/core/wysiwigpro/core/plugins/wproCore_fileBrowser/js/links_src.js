// JavaScript Document
function localLink (url, title) {
	var form = document.dialogForm;
	if (form.linkText) {
		if (FB.setLinkTextToFilename) {
			form.linkText.value = title;	
		}
	}
	form.URL.value = dialog.urlFormatting(url);
	var f = document.getElementById('sitePreview')
	dialog.changeFrameLocation(f, dialog.appendBaseToURL(url));
}
function oc(a) {
  var o = {};
  for(var i=0;i<a.length;i++) {
    o[a[i]]='';
  }
  return o;
}
fbObj.prototype.sharedInit = function (url, range, a) {
	var form = document.dialogForm;
	var startPane = '';
	// check for site link
	if (document.getElementById('site')&&url!='') {
		// for now just confirm it isn't a web link
		var webLink = true;
		var domain = dialog.urlFormatting(url, true).replace(/(^(http|https):\/\/[^\/]*)[\s\S]*/i, '$1');
		var domain2 = domain.replace(/www\./);
		if (dialog._baseDomain) {
			if (domain==dialog._baseDomain||domain2==dialog._baseDomain) {
				webLink = false;	
			}
		}
		if (domain == dialog.domain||domain2==dialog.domain) {
			webLink = false;	
		}
		if (webLink) startPane = 'web';
		
	}		
	// check for file browser link
	if (this.dirs) {
		if (this.dirs.length) {
			// check extensions
			var fileBrowserLink = false;
			var extension = this.getExtension(url);
			for (var i=0; i<this.dirs.length; i++) {
				var durl = dialog.urlFormatting(this.dirs[i]['URL'], true);
				var furl = dialog.urlFormatting(url, true);
				
				if (furl.substr(0, durl.length) == durl) {
					var extension = this.getExtension(url);
					if (
						(this.dirs[i]['type']=='document'&&(extension in oc(this.docExtensions)||extension==''))
						||
						(this.dirs[i]['type']=='image'&&(extension in oc(this.imageExtensions)||extension==''))
						||
						(this.dirs[i]['type']=='media'&&(extension in oc(this.mediaExtensions)||extension==''))												  
					) {
						fileBrowserLink = true;
						startPane = this.dirs[i]['id'];
						
						// find start path and file.
						FB.startID = this.dirs[i]['id'];
						FB.startPath = furl.substr(durl.length).replace(/[^\/]*$/gi, '');
						FB.startFile = dialog.urlDecode(furl.substr(durl.length).substr(FB.startPath.length));
						FB.startPath = dialog.urlDecode(FB.startPath);
						
						break;
					}					
				}				
			}
			if (!FB.chooser) {
				if (fileBrowserLink) {
					if (form.linkText) {
						var t = a.innerHTML.replace(/<[^>]*>/g,'')
						var right = unescape(url.substr(url.lastIndexOf('/')+1))
						if (t==right||(form.linkText.value==''&&url==''&&t=='')) {
							this.setLinkTextToFilename = true;
						}
					}
					if (form.prefixFileIcon) {
						if (a.firstChild) {
							if (a.firstChild.tagName) {
								if (a.firstChild.tagName == 'IMG') {
									if (/\/wysiwygpro\/icons\//i.test(a.firstChild.getAttribute('src', 2))) {
										form.prefixFileIcon.checked = true;
									}
								}
							}
						}
					}
					if (form.appendFileType) {
						var nx = a.nextSibling;
						if (nx) {
							if (nx.nodeType==3) {
								var nv = nx.nodeValue;
								if (/^[\s\xA0]*\[[^\]]+\]/i.test(nv)) {
									form.appendFileType.checked = true;
								}
							}
						}
					}
				}
			}
		}	
	}
	// check for email
	if (document.getElementById('email')) {
		if (/^mailto:/i.test(url)) {
			form.emailAddress.value = unescape(url.replace(/mailto:([^?]*)($|[\s\S]*$)/i, "$1"));
			if (/[?&]subject=/.test(url) ) {
				form.emailSubject.value = unescape(url.replace(/mailto:[\s\S]*?[?&]subject=([^&]*)($|[\s\S]*$)/i, "$1"));
			}
			if (/[?&]body=/.test(url) ) {
				form.emailMessage.value = unescape(url.replace(/mailto:[\s\S]*?[?&]body=([^&]*)($|[\s\S]*$)/i, "$1"));
			}
			startPane = 'email';
		}
	}
	// check for doc
	if (document.getElementById('doc')) {
		if (/^#/.test(url)) {
			var name = url.replace(/^#/, '');
			form.bookmarkSelect.value = name;
			
			startPane = 'doc';				
		}
	}
	if (startPane) {
		//parentOutlookSelect(startPane, true);
		switchPane(startPane);
		_initiated = true;
		parentOutlookSelect(startPane, true);
	}
}
fbObj.prototype.initChooser = function() {
	if (parentWindow.WPRO_FB_GET_FUNCTION) {
		if (parentWindow.WPRO_FB_GET_FUNCTION[dialog.sid]!= undefined) {
			var url = dialog.urlFormatting(parentWindow.WPRO_FB_GET_FUNCTION[dialog.sid]());
			document.dialogForm.URL.value = url;
			this.sharedInit(url);	
		}
	}
}
fbObj.prototype.initLink = function () {
	var form = document.dialogForm;
	var range = currentEditor.selAPI.getRange();
	var a
	//if (form.linkText) form.linkText.value = range.getText();
	if (range.nodes[0]) {
		if (range.nodes[0].tagName == 'A') {
			a = range.nodes[0];
		} else {
			a = range.getContainerByTagName('A');
		}
	} else {
		a = range.getContainerByTagName('A');
	}
	if (a) {
		if (!a.getAttribute('href', 2)) {
			a = false;	
		}
	}
	if (a) {
		if(form.elements['unlink'])	document.getElementById('removeLink').style.display = '';
	}
	// build bookmark select
	if (document.getElementById('doc')) {
		var bookmarks = [];
		var as = currentEditor.editDocument.getElementsByTagName('A');
		var n = as.length;
		for (var i=0; i<n; i++) {
			if (as[i].getAttribute('id')) {
				bookmarks.push(as[i]);
			} else if (as[i].getAttribute('name')) {
				bookmarks.push(as[i]);
			}
		}
		var n = bookmarks.length;
		bookmarkSelect = document.dialogForm.bookmarkSelect;
		for (var i=0; i<n; i++) {
			var name;
			if (name = bookmarks[i].getAttribute('id')) {
			} else if (name = bookmarks[i].getAttribute('name')) {
			}
			var s = document.createElement('OPTION');
			s.setAttribute('value', name);
			s.setAttribute('label', name);
			t = document.createTextNode(name);
			s.appendChild(t);
			bookmarkSelect.appendChild(s);
		}
	}
	// end
	if (a) {
		if (typeof(strApply)!='undefined') form.ok.value = strApply;
		if (a.getAttribute('_wpro_href')) {
			var url = a.getAttribute('_wpro_href', 2)
		} else {
			var url = a.getAttribute('href', 2)
		}
		url = dialog.urlFormatting(url);
		form.URL.value = url;
		if(form.screenTip) form.screenTip.value = a.getAttribute('title');
		
		if (form.elements['target']) {
			if (a.getAttribute('target')) form.elements['target'].value = a.getAttribute('target');
			if (form.elements['targetOptions']) {
				if (/^(_self|_parent|_top|_blank)$/i.test(a.getAttribute('target'))) {
					form.elements['targetOptions'].value = a.getAttribute('target');
				} else if (!a.getAttribute('target')) {
					form.elements['targetOptions'].value = '_self';	
				} else {
					form.elements['targetOptions'].value = '';	
				}
				this.targetChanged(form.elements['targetOptions'], false);
			}
		}
		if (form.elements['onclick']) {
			var s = String(a.getAttribute('_wpro_onclick'));
			if (!s) {
				s = String(a.getAttribute('onclick'));
			}
			if (s!=''&&s!='null'&&s!='undefined') {
				form.elements['onclick'].value = s;
				//if (s.match(/window\.open\((this.getAttribute\('href'\)|')[^)]+\)/i)) {
				if (s.match(/window\.open\((this\.getAttribute\('href'\)|')([^)]+)\)/g)) {
					form.elements['targetOptions'].value = '_blank';
				}
			}
			this.targetChanged(form.elements['targetOptions'], false);
		}
		
		this.sharedInit(url, range, a);
		
		if (form.linkText) {
			if (range.nodes[0]&&form.linkText.value=='') {
				form.linkText.disabled = true;	
			} 
		}
		
	} else if (form.linkText) {
		if (range.nodes[0]) {
			form.linkText.disabled = true;	
		} else if (form.linkText.value==''&&range.getText()=='') {
			this.setLinkTextToFilename = true	
		} else {
			var value = range.getText()
			if (/@/.test(value)) {
				form.linkText.value = '';
				form.emailAddress.value = unescape(value.replace(/(|mailto:)([^?]*)($|[\s\S]*$)/i, "$2"));
				if (/[?&]subject=/.test(value) ) {
					form.emailSubject.value = unescape(value.replace(/(|mailto:)[\s\S]*?[?&]subject=([^&]*)($|[\s\S]*$)/i, "$2"));
				}
				if (/[?&]body=/.test(value) ) {
					form.emailMessage.value = unescape(value.replace(/(|mailto:)[\s\S]*?[?&]body=([^&]*)($|[\s\S]*$)/i, "$2"));
				}
				
				switchPane('email');
				_initiated = true;
				parentOutlookSelect('email', true);
			}	
		}
	}
	if(form.elements['style']) dialog.selectCurrentStyle(form.elements['style']);
}
fbObj.prototype.linkAction = function (pane) {
	var form = document.dialogForm;
	var prefix = '';
	var append = '';
	var url = form.URL.value;
	var text = form.linkText.value;
	var style = form.elements['style'].value;
	var attrs = {};
	
	if (pane!='email') {
		if (form.elements['target']) attrs['target'] = form.elements['target'].value;
		if (form.elements['onclick']) attrs['_wpro_onclick'] = form.elements['onclick'].value;
	}
	attrs['title'] = form.elements['screenTip'].value;
	switch (pane) {
		case 'fileBrowser' : // used by all.
			if (document.getElementById('filePane').style.display=='block') {
				if (form.prefixFileIcon.checked) {
					prefix = currentEditor.editDocument.createElement('IMG');
					prefix.setAttribute('alt', '');
					prefix.style.borderWidth = '0';
					if (!/^http(s|):/i.test(dialog.themeURL)) {
						prefix.src = dialog.domain+dialog.themeURL+'icons/'+form.prefixFileIcon.value+'.gif';
					} else {
						prefix.src = dialog.themeURL+'icons/'+form.prefixFileIcon.value+'.gif';
					}
				}
				if (form.appendFileType.checked) {
					append = currentEditor.editDocument.createTextNode(form.appendFileType.value);
				}
			}
			break;
		case 'email' : // only used by link browser
			if (form.emailAddress.value!='') {
				url = 'mailto:'+form.emailAddress.value;
				if (form.emailSubject.value!='') {
					url+='?subject='+escape(form.emailSubject.value)
				}
				if (form.emailMessage.value!='') {
					url+=(form.emailSubject.value!=''?'&':'?')+'body='+escape(form.emailMessage.value)
				}
				
			}
			break;
		case 'web' : // used by all
		case 'doc' : // place on this site, only used by link browser
		case 'site' : // place on this site, only used by link browser
	}
	this.insertLink (url, text, attrs, style, prefix, append);
}
fbObj.prototype.insertLink = function (url, text, attrs, style, prefix, append) {
		
	var UDBeforeState = currentEditor.history.pre();
	
	var range = currentEditor.selAPI.getRange();
	
	var bad = /^www\./i
	if (bad.test(url)) {
		url = 'http://'+url;	
	}
	var bad = /^[^:@]*@[a-z0-9\._\-]*/i
	if (bad.test(url)) {
		url = 'mailto:'+url;	
	}
	
	var rt = range.getText();
	var oa = false;
	var na;

	if (range.nodes[0]) {
		if (range.nodes[0].tagName == 'A') oa = range.nodes[0];
		if (range.nodes[0].tagName == 'IMG') {
			if (currentEditor.strict) {
				range.nodes[0].style.borderWidth = '0px';
			} else {
				range.nodes[0].setAttribute('border', '0');
			}			
		}
	} else {
		oa = range.getContainerByTagName('A');
	}
	
	if (oa&&url=='') {
		WPro.removeNode(oa);
		currentEditor.history.post(UDBeforeState);
		return;	
	} else if(url=='') {
		return;	
	}
	if (rt==''&&!oa&&!range.nodes[0]) {
		
		if (!text) {
			text = range.getText();	
		}
		if (text=='') {
			text = url;
			text = url.replace(/^mailto:([^?]*)($|[\s\S]*$)/i, "$1");
		}
		na = currentEditor.editDocument.createElement('A');
		na.innerHTML = text;
		if (dialog.isIE) {
			na.setAttribute('href', url);
		} else {
			na.setAttribute('href', 'WPRO_TEMP_LINK_'+url);
			range.insertNode(na);
		}
	} else if (!oa) {
		WPro.callCommand(currentEditor.editDocument, "CreateLink",false,'WPRO_TEMP_LINK_'+url)
	}
	var a = [];
	if (oa) {
		a.push(oa);
		if (!text) {
			if (
			(dialog.urlFormatting(oa.innerHTML.replace(/<[^>]*>/g,''))==dialog.urlFormatting(oa.getAttribute('href', 2)))
			||
			(oa.innerHTML.replace(/<[^>]*>/g,'').replace(/([^?]*)($|[\s\S]*$)/i, "$1")==String(oa.getAttribute('href', 2)).replace(/^mailto:([^?]*)($|[\s\S]*$)/i, "$1"))
			) {
				text = url;
			}
		} else if (
			(text==dialog.urlFormatting(oa.getAttribute('href', 2)))
			||
			(text==oa.getAttribute('href', 2).replace(/^mailto:([^?]*)($|[\s\S]*$)/i, "$1"))
			) {
			text = url;	
		}
	}
	
	var links = currentEditor.editDocument.getElementsByTagName('A')
	var l=links.length
	for (var i=0; i < l; i++) {
		try {
		if (links[i].getAttribute('href', 2)) {
			if (String(links[i].getAttribute('href', 2)).search('WPRO_TEMP_LINK_') != -1) {
				a.push(links[i]);
			}
		}
		} catch (e) {}
	}
	
	if (dialog.isIE&&na) {
		a = [na];	
	}
	
	var l = a.length
	for (var i=0; i < l; i++) {
		for (var v in attrs) {
			if (v=='class') {
				a[i].className = attrs[v];
			} else if (v=='style') {
				a[i].style.cssText = attrs[v];
			} else if (attrs[v]==''&&v!='alt'&&v!='title') {
				a[i].removeAttribute(v);
			} else {
				a[i].setAttribute(v, attrs[v]);
			}
		}
		a[i].setAttribute('href', url);
		a[i].setAttribute('_wpro_href', url);
		if (text) a[i].innerHTML = text.replace(/^mailto:([^?]*)($|[\s\S]*$)/i, "$1");
		var range = currentEditor.selAPI.getRange();
		//range.selectNodeContents(a[i]);
	}
	if (l>0) {
		if (prefix) {
			var doPrefix = true;
			if (a[0].firstChild) {
				if (a[0].firstChild.tagName) {
					if (a[0].firstChild.tagName == 'IMG') {
						if (/\/wysiwygpro\/icons\//i.test(a[0].firstChild.getAttribute('src', 2))) {
							a[0].replaceChild(prefix, a[0].firstChild);
							doPrefix = false;
						}
					}
				}
			}
			if (doPrefix) a[0].insertBefore(prefix, a[0].firstChild);
		}
		if (dialog.isIE&&na) {
			if (style) currentEditor.applyStyle(style, a);
			range.insertNode(na);
		}
		if (append) {
			var doAppend = true;
			var nx = a[l-1].nextSibling;
			if (nx) {
				if (nx.nodeType==3) {
					var nv = nx.nodeValue;
					if (/^[\s\xA0]*\[[^\]]+\]/i.test(nv)) {
						nx.nodeValue = String(nx.nodeValue).replace(/^ \[[^\]]+\]/i, append.nodeValue);
						doAppend = false;
					}
				}
			}
			if (dialog.isIE&&na&&append) {
				range.insertNode(append);
			} else if (doAppend) a[l-1].parentNode.insertBefore(append, a[l-1].nextSibling);
		}
		if (!(dialog.isIE&&na)) {
			range.selectNodeContents(a[l-1]);
			if (style) {
				currentEditor.applyStyle(style, a);	
			}
		}
	}
	currentEditor.history.post(UDBeforeState);
	currentEditor.redraw();
}
fbObj.prototype.removeLink = function () {
	
	var range = currentEditor.selAPI.getRange();
	if (range.type == 'control') {
		var node = range.nodes[0];
	} else {
		var node = WPro.getParent(range.getStartContainer());
	}
	
	if (node) {
		var UDBeforeState = currentEditor.history.pre();
		WPro.removeNode(node);
		currentEditor.history.post(UDBeforeState);
	}
	dialog.close();
	return false;
}
fbObj.prototype.flinkTextChanged = function () {
	FB.linkTextChanged=true; // we must now update the link text on the selected link
	var panes = ['site', 'email', 'web', 'doc', 'fileBrowser']
	var pane = '';
	var n = panes.length;
	// find current pane
	for (var i=0; i<n; i++) {
		var f
		if (f=document.getElementById(panes[i])) {
			if (f.style.display=='block') {
				pane = panes[i];
				break;
			} 
		}
	}
	if (pane=='fileBrowser'||pane=='site') {
		FB.setLinkTextToFilename = false; // stop changing link text to the filename
	}
}
fbObj.prototype.linksPopulateLocalOptions = function (data) {
	var form = document.dialogForm;
	if (data.icon) {
		form.prefixFileIcon.value = data.icon;
	}
	if (data.description&&data.size) {
		form.appendFileType.value = ' ['+(data.description.trim())+' - '+(data.size.trim())+']';
	}
}
fbObj.prototype._sitePaneTimeout = function () {
	var cs = String(dialog.getFrameWindow(document.getElementById('siteFrame')).location);
	if (/iframeSecurity\.htm/i.test(cs)) {
		dialog.changeFrameLocation(document.getElementById('siteFrame'), FB.linksBrowserURL);
	}
}
fbObj.prototype.targetChanged = function (elm, change) {
	if (change&&elm.form.elements['target']) elm.form.elements['target'].value=elm.value;
	if (elm.form.elements['target']) {
		if(elm.value=='') {
			elm.form.elements['target'].style.display = '';
			elm.form.elements['target'].focus()	
		} else {
			elm.form.elements['target'].style.display = 'none';
		}
	}
	if (elm.form.elements['windowOptions']) {
		if(elm.value=='_blank') {
			elm.form.elements['windowOptions'].style.display = '';
			if (!elm.form.elements['target']&&elm.form.elements['onclick']) {
				// set window options first!!!
				this.setWindowOptions();
				this.windowOptionsAction();
			}
		} else {
			elm.form.elements['windowOptions'].style.display = 'none';
			
		}
	}
	if (elm.form.elements['onclick']) {
		if(elm.value!='_blank') {
			//elm.form.elements['onclick'].value = '';
			this.windowOptionsRemove();
		}
	}
}
fbObj.prototype.windowOptionsRemove = function () {
	var form = document.dialogForm;
	if (form.elements['onclick']) {
		form.elements['onclick'].value = form.elements['onclick'].value.replace(/window\.open\((this\.getAttribute\('href'\)|')[^)]+\)(;return false;|)/i, '');
	}
}
fbObj.prototype.windowOptionsAction = function () {
	var form = document.dialogForm;
	var s = 'window.open(this.getAttribute(\'href\'),\''+(form.windowName.value.replace(/[']/g,''))+'\'';
	if (!form.windowDefaultAppearance.checked) {		 
		s += ',\'';
		var arr = [];
		form.windowLocationBar.checked ? arr.push('location=yes') : 'x';
		form.windowMenuBar.checked ? arr.push('menubar=yes') : 'x';
		form.windowToolBar.checked ? arr.push('toolbar=yes') : 'x';
		form.windowStatusBar.checked ? arr.push('status=yes') : 'x';
		form.windowResizable.checked ? arr.push('resizable=yes') : 'x';
		form.windowScrollbars.checked ? arr.push('scrollbars=yes') : 'x';
		form.windowWidth.value.replace(/[^0-9]/g,'') ? arr.push('width='+(form.windowWidth.value.replace(/[^0-9]/g,''))) : 'x';
		form.windowHeight.value.replace(/[^0-9]/g,'') ? arr.push('height='+(form.windowHeight.value.replace(/[^0-9]/g,''))) : 'x';
		s+=arr.join(',');
		s += '\'';
	}
	s += ')'
	
	var v =  form.elements['onclick'].value;
	if (v.match(/window\.open\((this\.getAttribute\('href'\)|')[^)]+\)/i)) {
		v = v.replace(/window\.open\((this\.getAttribute\('href'\)|')[^)]+\)/i, s);	
	} else if (v=='') {
		v = s+';return false;';
	} else if (v.match(/return false(;$|$)/i)) {
		v = v.replace(/(return false(;$|$))/i, s+';return false;');	
	} else {
		v += s+';return false;';
	}
	
	form.elements['onclick'].value = v;
	
	this.hideWindowOptions();
}
fbObj.prototype.setWindowOptions = function () {
	var form = document.dialogForm;
	
	// populate options
	var u = 'this.href';
	var n = '';
	var p = '';
	
	var properties = {};
	properties.location = false;
	properties.menubar = false;
	properties.status = false;
	properties.toolbar = false;
	properties.resizable = false;
	properties.scrollbars = false;
	properties.width = '';
	properties.height = '';
	
	//form.windowOK.setAttribute('type', 'submit');
	// create buttons
	var b = getButtonHTML([{'onclick':'FB.windowOptionsAction();return false;', 'type':'submit','name':'windowOK','value':strOK},{'onclick' : 'FB.hideWindowOptions()','type':'button','name':'windowCancel','value':strCancel}]);
	document.getElementById('windowOptionButtons').innerHTML = b;
	
	var s =  form.elements['onclick'].value;
	s = s.match(/window\.open\((this\.getAttribute\('href'\)|')[^)]+\)/i)
	if (s) {
		n = s[0].replace(/^[^,]+,\s*'([^']*)'[\s\S]*$/g, '$1');
		
		// attributes
		if (s[0].match(/^[^,]+,\s*'[^']*',\s*'([^']*)'[\s\S]*$/g)) {
			p = s[0].replace(/^[^,]+,\s*'[^']*',\s*'([^']*)'[\s\S]*$/g, '$1');
		}
	}
	
	form.windowName.value = n;
	if (p) {
		
		form.windowDefaultAppearance.checked = false;
		document.getElementById('windowAppearance').style.display = '';
		// parse properties		
		p = p.match(/[A-Z]+=[A-Z0-9]+/gi);
		if (p) {
			for(var i=0;i<p.length;i++) {
				var k = p[i].split('=')[0];
				var v = p[i].split('=')[1];
				if (k=='width'||k=='height') {
					properties[k] = v;
				} else if (typeof(properties[k])!='undefined') {
					properties[k] = /^(yes|true|1)$/i.test(v);
				}
			}
		}
	} else {
		form.windowDefaultAppearance.checked = true;
		document.getElementById('windowAppearance').style.display = 'none';
	}
	
	form.windowLocationBar.checked = properties.location;
	form.windowMenuBar.checked = properties.menubar;
	form.windowToolBar.checked = properties.toolbar;
	form.windowStatusBar.checked = properties.status;
	form.windowResizable.checked = properties.resizable;
	form.windowScrollbars.checked = properties.scrollbars;
	form.windowWidth.value = properties.width;
	form.windowHeight.value = properties.height;
	
}
fbObj.prototype.showWindowOptions = function () {
	var form = document.dialogForm;
	var box = document.getElementById('windowOptionsBox');
	
	this.setWindowOptions();
	
	// set dimensions
	var width = 400;
	var height = 320;
	
	var left = 0;
	var top = 0;
	// get window width
	//var scrollLeft = parseInt(document.body.scrollLeft + document.documentElement.scrollLeft)
	//var scrollTop = parseInt(document.body.scrollTop + document.documentElement.scrollTop)
	var winDim = wproGetWindowInnerHeight();
	var availHeight = winDim['height'];
	var availWidth = winDim['width'];
	
	availHeight -= 40;
	
	if (width < availWidth) {
		left = (availWidth/2)-(width/2);
	}
	
	if (height < availHeight) {
		top = (availHeight/2)-(height/2);
	}
	
	box.style.width = width+'px';
	box.style.height = height+'px';
	
	box.style.top = top+'px';
	box.style.left = left+'px';

	if (document.getElementById('folderFrame'))document.getElementById('folderFrame').style.overflow = 'hidden';
	if (document.getElementById('lookInSelect'))document.getElementById('lookInSelect').style.visibility = 'hidden';
	box.style.display = 'block';
	
}
fbObj.prototype.hideWindowOptions = function () {
	document.getElementById('windowOptionButtons').innerHTML = '';
	if (document.getElementById('folderFrame'))document.getElementById('folderFrame').style.overflow = '';
	if (document.getElementById('lookInSelect'))document.getElementById('lookInSelect').style.visibility = '';
	document.getElementById('windowOptionsBox').style.display = 'none';	
}
