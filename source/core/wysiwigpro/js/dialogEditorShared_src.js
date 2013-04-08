/* The unusual location of this script file is designed to support scripts written for version 2 */
if (!String.prototype.trim) {
	String.prototype.trim=function () {
	   return this.replace(/^\s+|\s+$/g,"");
	};
};
if (!Array.prototype.push) {
	Array.prototype.push = function(){
		for(var i = 0; i < arguments.length; i++){
			this[this.length] = arguments[i];
		};
		return this.length;
	};
};
if (!wproWinOpen) {
	var wproWinOpen = eval('wi'+'nd'+'ow.'+'op'+'en')
}
if (!wproModelessDialog) {
	var wproModelessDialog = eval('wi'+'nd'+'ow'+'.s'+'ho'+'wM'+'od'+'el'+'es'+'sD'+'ia'+'log')
}
if (!wproModalDialog) {
	var wproModalDialog = eval('wi'+'nd'+'ow'+'.s'+'ho'+'wM'+'od'+'al'+'Dia'+'log')
}
wproDialogRecord = {};
wproDialogRecord['frames'] = [];
wproDialogRecord['windows'] = [];
function wproCloseOpenDialogs(dialog) {
	if (typeof(dialog) != 'undefined') {
		if (wproDialogRecord['frames'][dialog]) {
			var n = wproDialogRecord['frames'][dialog].length;
			for (var i=0; i<n; i++) {
				if (wproDialogRecord['frames'][dialog][i]) {
					var iframe = wproDialogRecord['frames'][dialog][i];
					if (iframe.parentNode) {
						try{iframe.parentNode.removeChild(iframe);
						wproDialogRecord['frames'][dialog][i]=null;
						}catch(e){}
					}
				}
			} 
			wproDialogRecord['frames'][dialog] = [];
		}
	} else {
		var n = wproDialogRecord['windows'].length;
		for (var i=0; i<n; i++) {
			if (wproDialogRecord['windows'][i] /*&& typeof(wproDialogRecord[i]) != 'object'*/) {
				if (typeof(wproDialogRecord['windows'][i].close) != 'undefined') {
					wproDialogRecord['windows'][i].close();
					wproDialogRecord['windows'][i]=null;
				}
			}
		} 
		wproDialogRecord['windows'] = [];
	}
}
var WPRO_FB_RETURN_FUNCTION = {}
var WPRO_FB_GET_FUNCTION = {}
function wproOpenFileBrowser(type, returnFunction, getFunction, baseurl, sid, iframe, phpsid, appendToQueryStrings, route) {
	
	if (!type) type = 'link';
	
	WPRO_FB_RETURN_FUNCTION[sid] = returnFunction
	WPRO_FB_GET_FUNCTION[sid] = getFunction
	
	var url = wproEditorLink('dialog.php?dialog=wproCore_fileBrowser&action=link&chooser=true&dirs='+type + '&' + sid + (phpsid ? '&' + phpsid : '') + (appendToQueryStrings ? '&' + appendToQueryStrings : ''), baseurl, route);
	
	if (iframe) {
		var frame = 'wpfileBrowser_dialogFrame';	
	}
	
	wp_openDialog(url, 'modal', 760, 480-55, '', frame);
}
function wpro_sessTimeout (internalId, sid, phpsid, url, appendToQueryStrings, sessRefresh, route) {
	var tag
	if (tag = document.getElementById(internalId+'_sessTag')) {
		tag.parentNode.removeChild(tag);
	}
	//WPro.timer.addTimer('WPro.'+this._internalId+'._createSessTag()', this.sessRefresh*1000);
	setTimeout('try{wpro_createSessTag("touch", "'+wproAddSlashes(internalId)+'", "'+wproAddSlashes(sid)+'", "'+wproAddSlashes(phpsid)+'", "'+wproAddSlashes(url)+'", "'+wproAddSlashes(appendToQueryStrings)+'", "'+wproAddSlashes(route)+'");}catch(e){}', sessRefresh*1000);
	//setInterval('WPro.'+this._internalId+'._createSessTag()', this.sessRefresh*1000);
}
function wpro_createSessTag (action, internalId, sid, phpsid, baseurl, appendToQueryStrings, route) {
	var h = document.getElementsByTagName('BODY')[0];
	var tag;
	if (tag = document.getElementById(internalId+'_sessTag')) {
		tag.parentNode.removeChild(tag);				
	}
	var s = document.createElement('SCRIPT');
	s.setAttribute('type', 'text/javascript');
	s.setAttribute('id', this._internalId+'_sessTag');
	
	var srcStr = 'core/touch.php?' + sid + (phpsid ? '&' + phpsid : '');
	
	if (action == 'destroy') {
		srcStr += '&action=destroy';
	} else {
		srcStr += '&action=touch';
	}
	
	srcStr += '&name='+internalId;
	
	srcStr += '&rand='+Math.random();
	
	if (appendToQueryStrings) srcStr += '&' + appendToQueryStrings;
	
	h.appendChild(s);
	
	s.src = wproEditorLink(srcStr, baseurl, route);
}
function wproEditorLink(url, base, route) {
	if (route && route!='') {
		var a = url.split(/\?/);
		var u = a[0];
		var q = (typeof(a[1])=='undefined') ? '' : a[1];
		return route + (route.match(/\?/) ? '&' : '?') + 'wproroutelink=' + escape(u.replace(/\//g,'-').replace(/\.php/,'')) + '&'+q;		
	} else {
		return base + url;	
	}
}
function wp_openDialog(url, modal, width, height, features, iframe, dialog) {
	if (!modal) {
		modal = 'modal';
	}
	if (!features) {
		features = '';
	}
	var win = false;
	if (iframe) {
				
		width +=4;
		height += 20;
		
		if (window.showModalDialog) {
			height += 5;	
		}
		
		var left = 0;
		var top = 0;
		
		iframe = document.getElementById(iframe);
		iframe.setAttribute('scrolling', 'no');
		iframe.setAttribute('bgColor', '#ffffff;');
		
		if (iframe.contentWindow.document.body) {
			iframe.contentWindow.document.body.style.display="none";
		}
		
		iframe.contentWindow.document.location.replace( url );
		
		iframe.style.width = width +'px';
		iframe.style.height = height +'px';
		iframe.style.visibility = 'visible';
		iframe.style.display = 'block';
		
		// work out position...
	
		var scrollLeft = document.body.scrollLeft + document.documentElement.scrollLeft
		var scrollTop = document.body.scrollTop + document.documentElement.scrollTop
		var winDim = wproGetWindowInnerHeight();
		var availHeight = winDim['height'];
		var availWidth = winDim['width'];
		
		if (width < availWidth) {
			left = (availWidth/2)-(width/2);
				
		}
		
		if (height < availHeight) {
			
			top = (availHeight/2)-(height/2);
			
		}
		
		left += scrollLeft
		top += scrollTop
			
		iframe.style.top = top+'px';
		iframe.style.left = left+'px';
				
		if (typeof(dialog) != 'undefined') {
			if (typeof(wproDialogRecord['frames'][dialog])=='undefined') {
				wproDialogRecord['frames'][dialog] = [];
			}
			wproDialogRecord['frames'][dialog].push(iframe);
		}
		
	} else {
		wproCloseOpenDialogs();
		// add width and height to url:
		if (/\?/.test(url)) {
			url+='&dWidth='+width+'&dHeight='+height;
		} else {
			url+='?dWidth='+width+'&dHeight='+height;
		}
		if (window.showModalDialog&&/msie/i.test(navigator.appVersion)) {
			if (parseInt(navigator.appVersion.replace(/[\s\S]*?MSIE ([0-9\.]*)[\s\S]*?/gi, "$1") ) < 7) {
				var nWidth = width + 12;
				var nHeight = height + 56;
			} else {
				var nWidth = width;
				var nHeight = height;
			}
			var params = 'dialogWidth:'+nWidth+'px;dialogHeight:'+ nHeight + 'px;help:no;';
			features = features.replace(/\,/gi, ';');
			features = features.replace(/\=/gi, ':');
			features = features.replace(/scrollbars/gi, 'scroll');
			features = features.replace(/left/gi, 'dialogLeft');
			features = features.replace(/top/gi, 'dialogTop');
			features = features.replace(/width/gi, 'dialogWidth');
			features = features.replace(/height/gi, 'dialogHeight');
			if ((features.search('scroll')) == -1) {
				params += "scroll:no;";
			}
			if (features.search('status') == -1) {
				params += "status:yes;";
			} 
			if (features.search('resizable') == -1) {
				params += "resizable:yes;";
			} 
			params += features;
			if (modal == 'modeless') {
				win=wproModelessDialog(url, window, params);
			} else {
				win=wproModalDialog(url, window, params);
			}
		} else {
			var name = url.split('?');
			var params = '';
			if (features.search('left') == -1) {
				params = "left="+((screen.width/2)-(width/2))+",";
			}
			if (features.search('top') == -1) {
				params += "top="+((screen.height/2)-(height/2))+",";
			}
			if ((features.search('scrollbars')) == -1) {
				params += "scrollbars=no,";
			}
			if (features.search('status') == -1) {
				params += "status=yes,"
			} 
			if (features.search('resizable') == -1) {
				params += "resizable=yes,";
			} 
			if (modal == 'modeless') {
				win = wproWinOpen(url, name[1], "dependent=yes,width="+width+"px,height="+height+"px,"+features+","+params);
			} else {
				win = wproWinOpen(url, name[1], "dependent=yes,modal=yes,width="+width+"px,height="+height+"px,"+features+","+params);
			}
			if (!win) {alert('This feature requires you to enable p'+'o'+'p'+'u'+'p windows.');return false;}
			win.focus();
			wproDialogRecord['windows'].push(win);
		}
	}
	return win;
}
// auto stops timers when page unloads...
function wproTimer () {
	this.timerIds = [];
}
wproTimer.prototype.addTimer = function (func, delay) {
	var id = setTimeout(func,delay);
	this.timerIds.push(id);
	return id;
}
wproTimer.prototype.stopTimer = function (id) {
	clearTimeout(id); 
}
wproTimer.prototype.clearAllTimers = function () {
	var n = this.timerIds.length;
	for (var i=0; i<n; i++) {
		if (this.timerIds[i]) {
			clearTimeout(this.timerIds[i]);
			this.timerIds[i]=null; 
		}
	} 
	this.timerIds = [];
}
function wproEvents() {
	this.events = [];
}
wproEvents.prototype.addEvent = function (node, trigger, func) {
	if (trigger.substr(0,2) == 'on') {
		trigger = trigger.substr(2);
	}
	if (node.addEventListener) {
		node.addEventListener(trigger, func, false);
	} else if (node.attachEvent) {
		node.attachEvent('on'+trigger, func);
	}
	this.events.push(arguments);
	//EventCache.add(node, trigger, func, false);
	return arguments;
}
wproEvents.prototype.removeEvent = function (node, trigger, func) {
	if (node) {
		if (trigger.substr(0,2) == 'on') {
			trigger = trigger.substr(2);
		}
		if (node.removeEventListener) {
			node.removeEventListener(trigger, func, false);
		} else if (node.detachEvent) {
			node.detachEvent('on'+trigger, func);
		}
		try{if (node['on'+trigger])
			node['on'+trigger] = null;}catch(e){}
	}
}
wproEvents.prototype.removeEventById = function (id) {
	if (id) {
		this.removeEvent (id[0], id[1], id[2]);
	}
}
wproEvents.prototype.removeAllEvents = function () {
	var l = this.events.length;
	for (var i = this.events.length - 1; i >= 0; i--) {
		if (this.events[i]) {
			this.removeEventById(this.events[i]);
		}
	}
	this.events=[];
}
wproEvents.prototype.preventDefault = function (e) {
	if (e.stopPropagation) e.stopPropagation();
	if (e.preventDefault) e.preventDefault();
	e.cancelBubble = true;
	e.returnValue = false;
}
function wproGetWindowInnerHeight(win) {
	if (!win) win = window;
	var doc = window.document;
	var availHeight, availWidth
	if (window.innerHeight) {
		availHeight = window.innerHeight;
		availWidth = window.innerWidth;
	} else if (document.documentElement.clientHeight) {
		availHeight=document.documentElement.clientHeight;
		availWidth=document.documentElement.clientWidth;
	} else {
		availHeight=document.body.clientHeight;
		availWidth=document.body.clientWidth;
	}
	return {'width':availWidth, 'height':availHeight};
}
function wproCreateStyleSheet(url) {
	var head = document.getElementsByTagName('HEAD')[0];
	var link = document.createElement('LINK');
	link.setAttribute('rel', 'stylesheet');
	link.setAttribute('href', url);
	link.setAttribute('type', 'text/css');
	head.appendChild(link);
}
function wproLoadMessage(name,width,height,text,image,show){
	if(!show) {
		show='none';
	} else {
		show='block';
	}
	if (!height) {
		height = '200px';
	}
	if (!width) {
		width = '100%';
	}
	if (!width.toString().match(/(%|px)/)) {
		width = width + 'px';
	}
	if (height=='0') {
		height='200px'
	}
	if (!height.toString().match(/(px|%)/)) {
		height = height + 'px';
	}
	document.write('<div id="'+name+'_loadMessage" class="wproLoadMessageHolder" style="'+(width?'width:'+width+';':'')+'height:'+(height)+';display:'+show+'"><div class="wproLoadMessage" style="margin-top:'+(parseInt(height.replace(/[px %]/gi, ''))/2 - 40)+'px"><img src="'+image+'" alt="" /> '+text+'</div></div>');
}
function wproResizeLoadMessageTo(name, width, height) {
	if (!width) {
		width = '100%';
	}
	if (!width.toString().match(/(%|px)/)) {
		width = width + 'px';
	}
	if (!height||height=='0') {
		height='200px'
	}
	if (!height.toString().match(/(px|%)/)) {
		height = height + 'px';
	}
	document.getElementById(name+'_loadMessage').style.height=height;
	document.getElementById(name+'_loadMessage').style.width=width;
	document.getElementById(name+'_loadMessage').firstChild.style.marginTop = (parseInt(height.replace(/[px %]/gi, ''))/2 - 40)+'px';
}

/* WPro functions */
function wproGetNodeAttributesString (node, events) {
	if(events==undefined)events=true;
	var str = '';
	var a = node.attributes;
	var n = a.length
	if (node.className) {
		str += ' class="'+wproHtmlSpecialChars(node.className)+'"';
	}
	if (node.style.cssText) {
		if (node.style.cssText.length) str += ' style="'+wproHtmlSpecialChars(node.style.cssText)+'"';
	}
	if (node.onload!=null&&node.onload!=undefined) {
		str += ' onload="'+wproHtmlSpecialChars(String(node.onload))+'"';
	}
	for (var i=0; i < n; i++) {
		if(!events&&a[i].nodeName.substr(0,2)=='on')continue;
		if (a[i].specified && a[i].nodeName != 'class' && a[i].nodeName != 'style') {
			str += ' '+a[i].nodeName.toLowerCase()+'="'+wproHtmlSpecialChars(a[i].nodeValue)+'"';
		}
	}
	return str;	
}
function wproQuoteMeta (str) {
	str = str.replace( /([^A-Za-z0-9])/g , "\\$1" );
	return str;
}
function wproRgbToHex(R,G,B) {return wproToHex(R)+wproToHex(G)+wproToHex(B)}
function wproToHex(N) {
 if (N==null) return "00";
 N=parseInt(N); if (N==0 || isNaN(N)) return "00";
 N=Math.max(0,N); N=Math.min(N,255); N=Math.round(N);
 return "0123456789abcdef".charAt((N-N%16)/16)
	  + "0123456789abcdef".charAt(N%16);
}
function wproHexToR(h) {return parseInt((wproCutHex(h)).substring(0,2),16)}
function wproHexToG(h) {return parseInt((wproCutHex(h)).substring(2,4),16)}
function wproHexToB(h) {return parseInt((wproCutHex(h)).substring(4,6),16)}
function wproCutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}
function wproHexToRGB(h) {return 'rgb('+wproHexToR(h)+', '+wproHexToG(h)+', '+wproHexToB(h)+')'}

function wproStyleFormatting (str, raw) {
	
	if (/style="/.test(str)) {
		str = str.substring(7);
	}
	if (raw) {
		str = this.htmlSpecialCharsDecode(str);
	}
	// build styles array
	var arr = {};
	
	// encode strings
	str = str.replace(/url\([\s\S]*?\)/gi, function(x){return '[WP'+escape(x)+'WP]';});
	str = str.replace(/"[\s\S]*?"/g, function(x){return '[WP'+escape(x)+'WP]';});
	str = str.replace(/'[\s\S]*?'/g, function(x){return '[WP'+escape(x)+'WP]';});
	
	var styles = str.match(/([A-Za-z\-]*:[^;]*)/gi);
	if (styles) {
		var n = styles.length;
		for (var i=0; i<n; i++) {
			s = styles[i].split(':');
			if (s[0] && s[1]) {
				if (/^\s*mso-/.test(s[0])) continue; // strip microsoft office styles
				arr[s[0].toLowerCase()] = s[1].replace(/^([^\s]+)/, ' $1');
			}
		}
		
		this._compressBoxStyles(arr, 'border', '-width', 'border-width');
		this._compressBoxStyles(arr, 'border', '-color', 'border-color');
		this._compressBoxStyles(arr, 'border', '-style', 'border-style');
		this._compressBoxStyles(arr, 'border', '', 'border');
		this._compressBoxStyles(arr, 'padding', '', 'padding');
		this._compressBoxStyles(arr, 'margin', '', 'margin');
		
		this._shorthandStyles(arr, 'list-style', ['-type','-position','-color']);
		this._shorthandStyles(arr, 'border', ['-color','-width','-style']);
		this._shorthandStyles(arr, 'border-top', ['-color','-width','-style']);
		this._shorthandStyles(arr, 'border-right', ['-color','-width','-style']);
		this._shorthandStyles(arr, 'border-bottom', ['-color','-width','-style']);
		this._shorthandStyles(arr, 'border-left', ['-color','-width','-style']);
		this._shorthandStyles(arr, 'background', ['-color','-image','-repeat','-attachment','-position']);
		
		var str = '';
		for (var key in arr) {
			var val = arr[key];
			if (!val) continue;
			str += key + ':'+val+'; ';
		}
		if (/; $/.test(str)) {
			str = str.substring(0, str.length - 2);
		}
	}
	// unencode strings
	str = str.replace(/\[WP[\s\S]*?WP\]/g, function(x){return unescape(x).replace(/\[WP/g, '').replace(/WP\]/g, '');});
	
	// find and format RGB color
	str = str.replace(/rgb\([0-9]+,\s*[0-9]+,\s*[0-9]+\)/gi, function(x){
		x = x.replace(/[^0-9,]/g,'');
		x = x.split(',');
		return '#'+wproRgbToHex(parseInt(x[0]),parseInt(x[1]),parseInt(x[2]));								  
	});
	
	// find and format URL's
	var urls = str.match(/url\([\s\S]*?\)/gi);
	if (urls) {
		var n = urls.length;
		for (var i=0; i<n; i++) {
			// get the URL
			var url = urls[i].replace(/url\([\s"']*([\s\S]*?)[\s"']*\)/gi, "$1");
			
			if (this.urlFormatting) {
				url = this.urlFormatting(url);
			} else if (this.currentEditor.urlFormatting) {
				url = this.currentEditor.urlFormatting(url);
			} else {
				
			}
			// finally replace the existing tag with the new tag
			str = str.replace(urls[i], 'url(\''+url+'\')');
		}
	}
	
	if (raw) {
		str = this.htmlSpecialChars(str);
	}
	return str;
}
function wp_shorthandStyles(arr, pr, sArr) {
	var n = sArr.length
	var fCount = new Array;
	for (var i=1; i<n; i++) {
		if (arr[pr+sArr[i]]) {
			fCount.push(pr+sArr[i]);
		}
	}
	if (fCount.length < 3) return;
	arr[pr]='';
	var n = fCount.length;
	for (var i=1; i<n; i++) {
		arr[pr] += arr[fCount[i]];
		arr[fCount[i]] = null;
	}
} 
function wp_compressBoxStyles (arr, pr, sf, res) {
	var box = new Array();
	box[0] = arr[pr + '-top' + sf];
	box[1] = arr[pr + '-right' + sf];
	box[2] = arr[pr + '-bottom' + sf];
	box[3] = arr[pr + '-left' + sf];
	if (box[0]==null||box[1]==null||box[2]==null||box[3]==null) {
		return;
	}
	if (pr=='margin'||pr=='padding') {
		if (box[0]==box[2]&&box[1]==box[3]) {
			box[2]='';box[3]='';
		} else if (box[1]==box[3]) {
			box[3] = '';
		}
		if (box[0]==box[1]) box[1]='';
		arr[res] = box[0] + box[1] + box[2] + box[3];
	} else {
		var n = box.length
		for (var i=1; i<n; i++) {
			if (box[i] != box[0]) {
				return;
			}
		}
		arr[res] = box[0];
	}
	arr[pr + '-top' + sf] = null;
	arr[pr + '-left' + sf] = null;
	arr[pr + '-right' + sf] = null;
	arr[pr + '-bottom' + sf] = null;
}

function wp_setBrowserTypeStrings () { 
	this.isIE = (this.browserType=='msie') ? true : false;
	this.isGecko = (this.browserType=='gecko') ? true : false;
	this.isSafari = (this.browserType=='safari') ? true : false;
	this.isOpera = (this.browserType=='opera') ? true : false;
	// create popup window function
	if (this.isIE) {
		try{document.execCommand("BackgroundImageCache", false, true);}catch(e){}
	}
}
wproUrlEncode = function (sStr) {
	return escape(sStr).replace(/\+/g, '%2C').replace(/\"/g,'%22').replace(/\'/g, '%27');
}
wproUrlDecode = function (psEncodeString) {	
 //alert (unescape(psEncodeString))
 return unescape(psEncodeString); 
// return psEncodeString.replace(/%20/g, ' ')
}
wproAddSlashes = function (str) {
	return String(str).replace(/\\/gi, "\\\\").replace(/'/gi, "\\'").replace(/"/gi, '\\"');
}
wproHtmlSpecialChars = function (str, ignoreEscaped) {
	str = String(str);
	if (!ignoreEscaped) {
		str = str.replace(/&/gi, '&amp;');
	} else {
		str = str.replace(/&(?!([a-z0-9#]{2,10};))/gi, '&amp;');
	}		
	return str.replace(/\xA0/gi, '&nbsp;').replace(/</gi, '&lt;').replace(/>/gi, '&gt;').replace(/"/gi, '&quot;')	
}
wproHtmlSpecialCharsDecode =  function (str) {
	return String(str).replace(/&nbsp;/gi, String.fromCharCode(160)).replace(/&lt;/gi, '<').replace(/&gt;/gi, '>').replace(/&quot;/gi, '"').replace(/&amp;/gi, '&')	
}
function wproInArray(needle, haystack) {
	for (var i=0;i<haystack.length;i++) {
		if (haystack[i] == needle) return true;
	}
	return false;
}
// drop down menu functions 
function wproFMenuOver () {
	//this.focus();
	var c = this.className;
	if (c=='wproLatched') {
		this.className = 'wproOver wproLatched';
	} else {
		this.className = 'wproOver';
	}
}
function wproFMenuOut () {
	//this.focus();
	var c = this.className;
	if (c=='wproOver wproLatched' || c=='wproLatched' ) {
		this.className = 'wproLatched';
	} else {
		this.className = '';
	}
}
