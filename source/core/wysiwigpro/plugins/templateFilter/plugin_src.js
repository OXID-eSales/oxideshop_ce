function wproPlugin_templateFilter(){}
wproPlugin_templateFilter.prototype.init=function(EDITOR){
	//EDITOR.addHTMLFilter('rawSource',wproPlugin_templateFilter_tdFilter);
	EDITOR.addHTMLFilter('source',wproPlugin_templateFilter_tsFilter);
	EDITOR.addHTMLFilter('design',wproPlugin_templateFilter_tdFilter);
	EDITOR.addHTMLFilter('preview',wproPlugin_templateFilter_tpFilter);
}
// design filter
function wproPlugin_templateFilter_tdFilter (editor, html) {
	return wproPlugin_templateFilter__dFilter (editor, html, 'd');
}
// preview
function wproPlugin_templateFilter_tpFilter (editor, html) {
	return wproPlugin_templateFilter__dFilter (editor, html, 'p');
}
function wproPlugin_templateFilter__dFilter (editor, html, mode) {
	// place holders
	for (var x in editor._templateFilterTags) {
		if (mode=='p'||editor._templateFilterTags[x].rd) { 
			var regex1 = new RegExp(WPro.quoteMeta(x), 'gi');
			html = html.replace(regex1, editor._templateFilterTags[x].v);
		}
	}
	// open/close tags and contents
	for (var i=0;i<editor._templateFilterOpen.length;i++) {
		html = wproPlugin_templateFilter_escapeServerTags(html, editor._templateFilterOpen[i], editor._templateFilterClose[i], editor);
	}
	return html
}
// source code filter
function wproPlugin_templateFilter_tsFilter (editor, html) {
	// filter open/close tags
	for (var i=0;i<editor._templateFilterOpen.length;i++) {
		var open = editor._templateFilterOpen[i];
		var close = editor._templateFilterClose[i];
		var encodedOpen = escape(open);
		var encodedClose = escape(close);
		var regex1 = new RegExp(WPro.quoteMeta(open)+'([\\s\\S]*?)'+WPro.quoteMeta(close), 'gi');
		var regex2 = new RegExp(WPro.quoteMeta(encodedOpen)+'([\\s\\S]*?)'+WPro.quoteMeta(encodedClose), 'gi');
		html = html.replace(regex1, function(x){return wproPlugin_templateFilter_ocFilter2(unescape(x));});
		html = html.replace(regex2, function(x){return wproPlugin_templateFilter_ocFilter2(unescape(x));});
	}
	// filter open/close tags within HTML tags
	html = wproPlugin_templateFilter_unescapeServerTags(html);
	
	// filter place holder tags
	for (var x in editor._templateFilterTags) {
		var regex1 = new RegExp(WPro.quoteMeta(editor._templateFilterTags[x].v), 'gi');
		html = html.replace(regex1, x);
	}
	return html;
};
function wproPlugin_templateFilter_ocFilter2(str) {
	return str.replace('&nbsp;', String.fromCharCode(160)).replace(/\&\#[0-9]+\;/g, function (x) {
		var r; 
		var n=x.replace(/[^0-9]/g, ''); 
		if (r=String.fromCharCode(parseInt(n))) {
			return r;
		}else{
			return x;
		}
	}).replace(/&quot;/g,'"').replace(/&gt;/g, '>').replace(/&lt;/g, '<').replace(/&amp;/g, '&');
}
// unescapes template open/close tags within HTML tags
function wproPlugin_templateFilter_unescapeServerTags (html) {
	html = html.replace(/ {0,1}\[WPTFCODE_goback1_/gi, "[WPTFCODE");
	html = html.replace(/_goforward1_WPTFCODE((\]|%5D)=""|=""|(\]|%5D)|) {0,1}/gi, "WPTFCODE]");												
	html = html.replace(/(\[|%5B)WPTFCODE([\s\S]*?)WPTFCODE((\]|%5D)=""|=""|(\]|%5D)|)/gi, function (x) {
		c =  x.replace(/(\[|%5B)WPTFCODE([\s\S]*?)WPTFCODE[\s\S]*/i, '$2');
		return unescape(c).replace(/_wproup_[a-z]/gi, function (x) {return x.substr(x.length-1).toUpperCase();}).replace(/wproslash/g, '/');
	});
		
	html = html.replace(/(<|\&lt\;)!--\[WPCOMMENT([\s\S]*?)WPCOMMENT\]--(\&gt\;|>)/gi, "$2");
	
	return html;
}
// escapes template open/close tags within HTML tags
function wproPlugin_templateFilter_escapeServerTags (html, tagOpen, tagClose, editor) {
	
	// prevent the contents of style and script tags from getting mucked up
	html = WPro.escapeTags(html, 'style');
	html = WPro.escapeTags(html, 'script');
	
	// escape within a tag
	var re_m = new RegExp('(<[a-z0-9][^>]*)(('+WPro.quoteMeta(tagOpen)+')[\\s\\S]*?('+WPro.quoteMeta(tagClose)+'))', 'gi')
	var re_b = new RegExp('(^<[a-z0-9][^>]*)'+WPro.quoteMeta(tagOpen)+'[\\s\\S]*', 'i');
	var re_a = new RegExp('^<[a-z0-9][^>]*'+WPro.quoteMeta(tagOpen)+'', 'i');
	while (html.match(re_m) ) {
		html = html.replace(re_m, function(x){
			var b = x.replace(re_b, '$1');
			var a = x.replace(re_a, tagOpen);
			return b + '[WPTFCODE'+escape(a.replace(/\//g, 'wproslash').replace(/([A-Z])/g, "_wproup_$1"))+'WPTFCODE]';
		});
	}
	html = html.replace(/<([a-z0-9]+)\[WPTFCODE/gi, "<$1 [WPTFCODE_goback1_");
	//html = html.replace(/WPTFCODE\]([a-z0-9]+=)/gi, "_goforward1_WPTFCODE] $1");	
	
	// escape within a head tag
	if (!editor.snippet) {
		html = WPro.escapeTags(html, 'title');
		while (eval('html.match(/(<head[^>]*[\\s\\S]*>[^<]*)(('+WPro.quoteMeta(tagOpen)+')[\\s\\S]*?('+WPro.quoteMeta(tagClose)+'))([\\s\\S]*<\\/head[^>]*>)/gi)') ) {
			html = eval('html.replace(/(<head[^>]*[\\s\\S]*>[^<]*)('+WPro.quoteMeta(tagOpen)+'[\\s\\S]*?'+WPro.quoteMeta(tagClose)+')([\\s\\S]*<\\/head[^>]*>)/gi, "$1<!--[WPCOMMENT$2WPCOMMENT]-->$3")');
		}
		html = WPro.unescapeTags(html, 'title');
	}
	
	html = WPro.unescapeTags(html, 'script');
	html = WPro.unescapeTags(html, 'style');
	
	return html;
}
