function wproPlugin_wproCore_bookmark(){}
wproPlugin_wproCore_bookmark.prototype.init=function(EDITOR){
	this.editor = EDITOR.name;
	EDITOR.addButtonStateHandler('bookmarkproperties',wproPlugin_wproCore_bookmark_bsh);
	// jsBookmarkLinks
	if (EDITOR.jsBookmarkLinks) {
		EDITOR.addHTMLFilter('design', wproPlugin_wproCore_bookmark_df);
		EDITOR.addHTMLFilter('source', wproPlugin_wproCore_bookmark_sf);
	}
	EDITOR.addHTMLFilter('preview', wproPlugin_wproCore_bookmark_pf);
};
function wproPlugin_wproCore_bookmark_bsh(EDITOR,srcElement,cid,inTable,inA,range){
	return inA?(inA.getAttribute('name')?"wproReady":(inA.getAttribute('id')?"wproReady":"wproDisabled")):"wproDisabled";
}
function wproPlugin_wproCore_bookmark_sf(editor, html) {
	html = html.replace(/(<a [^>]*href=")#([^"]*)"([^>]*>)/gi, "$1javascript:document.location.replace(String(document.location).replace(/#([\\s\\S]*)/,'')+'#$2')\"$3");
	return html;
}
function wproPlugin_wproCore_bookmark_df(editor, html) {
	html = html.replace(/(<a [^>]*href=")javascript:document\.location\.replace\(String\(document\.location\)(|\.replace\(\/#\(\[\\s\\S\]\*\)\/,''\))\+'#([^']*)'\)"([^>]*>)/gi, "$1#$3\"$4");
	return html;
}
function wproPlugin_wproCore_bookmark_pf(editor, html) {
	html = wproPlugin_wproCore_bookmark_df(editor,html);
	html = html.replace(/(<a [^>]*href=")#([^"]*)"([^>]*>)/gi, "$1javascript:parent.WPro.currentEditor.plugins['wproCore_bookmark'].previewScrollBookmark('$2')\"$3");
	return html;
}
wproPlugin_wproCore_bookmark.prototype.previewScrollBookmark=function(name){
	var editor = WPro.editors[this.editor];
	var bs = editor.previewWindow.document.getElementsByTagName('A');
	var b = null;
	for (var i=0;i<bs.length;i++) {
		if ((bs[i].name && bs[i].name==name) || (bs[i].id && bs[i].id == name)) {
			b = bs[i];
			break;
		}
	}
	if (b==null) {
		editor.previewWindow.scroll(0,0);	
	} else {
		editor.previewWindow.scrollTo(b.offsetLeft, b.offsetTop);
	}
}