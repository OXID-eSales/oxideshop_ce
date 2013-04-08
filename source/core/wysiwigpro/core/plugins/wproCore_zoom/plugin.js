
function wproPlugin_wproCore_zoom(){}
wproPlugin_wproCore_zoom.prototype.init=function(EDITOR){
this.editor = EDITOR.name;
EDITOR.addHTMLFilter('source', wproPlugin_wproCore_zoom_unzoom);
};
wproPlugin_wproCore_zoom.prototype.zoom = function (value) {
var editor = WPro.editors[this.editor];
if (editor._inDesign) {
editor.editDocument.body.style.zoom = value;
} else if (editor._inPreview) {
editor.previewWindow.document.body.style.zoom = value;
}
}
function wproPlugin_wproCore_zoom_unzoom (EDITOR, html) {
return html.replace(/(<body [\s\S]*?style="[\s\S]*?)zoom: [0-9]+%(|; )([\s\S]*?")/gi, '$1$3').replace(/(<body[\s\S]*?) style=""/gi, '$1');
}