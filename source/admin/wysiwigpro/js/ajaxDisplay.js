/*
 * (c) Copyright Chris Bolt 2003-2008, All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */
wproAjaxLoadNeeded = {};
wproAjaxLoaded = [];
function wproAjaxRecordLoad (s) {
	wproAjaxLoaded.push(s.toLowerCase());
	for (var x in wproAjaxLoadNeeded) {
		var not = false;
		if (wproAjaxLoadNeeded[x]==null) {
			continue;	
		}
		var l = wproAjaxLoadNeeded[x].length;
		for (var i=0; i<l;i++) {
			if (!wproAjaxInArray(wproAjaxLoadNeeded[x][i], wproAjaxLoaded) ) {
				not = true;
				break;
			}
		}
		if (!not) {
			wproAjaxParseScripts(x);	
		}
	}
}
function wproAjaxInArray(n, arr) {
	for (var x in arr) {
		if (arr[x] == n) {
			return true;	
		}
	}
	return false;
}
function wproAjaxDisplay (editorCode, node, hsc) {	
	if (hsc) {
		editorCode = editorCode.replace(/&lt;/gi, '<').replace(/&gt;/gi, '>').replace(/&quot;/gi, '"').replace(/&amp;/gi, '&')
	}
	if (document.createStyleSheet) {
		var links = editorCode.match(/<link [^>]*href="[^"]+"[^>]*>/gi);
		var l = links.length;
		for (var i=0; i<l;i++) {
			document.createStyleSheet(links[i].replace(/<link [^>]*href="([^"]+)"[^>]*>/gi, "$1"));	
		}
	}
	if (typeof(node).toString().toLowerCase() == 'string') {
		var node = document.getElementById(node);
	}
	node.innerHTML = editorCode;
	wproAjaxEnable(node.id);
}
function wproAjaxEnable(node) {
	if (typeof(node).toString().toLowerCase() == 'string') {
		// check if the node is the name of an editor.
		var n2 = node.replace(/[^a-z0-9_]/gi, '').replace(/^[0-9]+/, 'wp');
		if (document.getElementById(n2+'_container')) {
			var node = document.getElementById(n2+'_container');
		} else {
			var node = document.getElementById(node);
		}
	}
	// load JavaScript includes
	wproAjaxLoadNeeded[node.id] = [];
	var scripts = node.getElementsByTagName('SCRIPT');
	var l = scripts.length;
	for (var i=0; i<l;i++) {
		var s = scripts.item(i)
		var a = s.getAttribute('src')
		if (a) {
			// strip domain:
			var ss = s.src.toString().toLowerCase()
			ss = ss.replace(/http(|s):\/\/[^\/]+/gi, '');
			try {
			//if (!wproAjaxInArray(ss, wproAjaxLoaded)) {
				var n = document.createElement('SCRIPT');
				n.setAttribute('type', 'text/javascript');
				s.parentNode.insertBefore(n, s);
				n.src = s.src;
				wproAjaxLoadNeeded[node.id].push(ss)
				s.parentNode.removeChild(s);
			//}
			}catch(e){}
		}
	}
	if (document.createStyleSheet) {
		var links = node.getElementsByTagName('LINK');
		var l = links.length;
		for (var i=0; i<l;i++) {
			document.createStyleSheet(links[i].href);
		}
	}
	// show the load message...
	var divs = node.getElementsByTagName('DIV')
	var l = divs.length;
	for (var i=0; i<l;i++) {
		if (divs[i].className == 'wproLoadMessageHolder') {
			divs[i].style.display = 'block';
			break;
		}
	}
}
function wproAjaxParseScripts (node, ignore) {
	if (typeof(node).toString().toLowerCase() == 'string') {
		var node = document.getElementById(node);
	}
	wproAjaxLoadNeeded[node.id] = null;
	var scripts = node.getElementsByTagName('SCRIPT');
	var l = scripts.length;
	for (var i=0; i<l;i++) {
		var str = scripts.item(i).text;
		if (str.length > 1 && !str.match(/document\.write/gi)) {
			eval(str);
		}
	}
}