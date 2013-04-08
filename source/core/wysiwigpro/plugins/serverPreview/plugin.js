
function wproPlugin_serverPreview () {}
wproPlugin_serverPreview.prototype.init = function (EDITOR) {
EDITOR.updatePreview = wproPlugin_serverPreview_updatePreview;
};
function wproPlugin_serverPreview_updatePreview () {
if (!this._inPreview&&!this._movingToPreview) {
return;
}
var URL = this._serverPreviewURL + (this._serverPreviewURL.match(/\?/)?'&':'?') + this.sid + (WPro.phpsid ? '&' + WPro.phpsid : '') + (this.appendToQueryStrings ? '&' + this.appendToQueryStrings : '');
var previewHTML = this.getPreviewHTML();
var HTML = this.getHTML();
var str = '<html><head><title>Preview Form</title><style type="text/css">body{font-size:11px;font-family:verdana}textarea{display:none}</style></head><body onload="setTimeout(\'document.form1.submit();\',2000);"><form name="form1" method="post" action="'+WPro.htmlSpecialChars(URL)+'"><p><img src="'+WPro.htmlSpecialChars(this.themeURL)+'misc/loader.gif" alt="" /> '+this.lng['pleaseWait']+'</p><textarea name="wproPreviewHTML">'+WPro.htmlSpecialChars(previewHTML)+'</textarea><textarea name="wproHTML">'+WPro.htmlSpecialChars(HTML)+'</textarea></form></body></html>';
this.previewWindow.document.open('text/html', 'replace');
this.previewWindow.document.write( str );
this.previewWindow.document.close();
if (this._initFocus) {
this.previewWindow.focus();
}
this.triggerEditorEvent('enterPreview');
if (!this._loaded) {
this.triggerEditorEvent('load');
this._loaded = true;
}
};