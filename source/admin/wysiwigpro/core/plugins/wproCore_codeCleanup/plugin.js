
function wproPlugin_wproCore_codeCleanup(){}
wproPlugin_wproCore_codeCleanup.prototype.init=function(EDITOR){
this.editor = EDITOR.name
};
wproPlugin_wproCore_codeCleanup.prototype.open=function(){
var editor=WPro.editors[this.editor];
var width = 500;
var height = 406;
if (!editor.iframeDialogs && WPro.isIE) {
editor.focus();
wproCloseOpenDialogs();
var win = wproWinOpen(editor.getDialogPluginURL('wproCore_codeCleanup&action=paste'), 'wproPasteWin', 'width='+width+'px,height='+height+'px,left='+((screen.width/2)-(width/2))+',top='+((screen.height/2)-(height/2))+',scrollbars=no,status=yes,resizable=yes');
win.focus();
} else {
editor.openDialogPlugin('wproCore_codeCleanup&action=paste',width,height);
}
};