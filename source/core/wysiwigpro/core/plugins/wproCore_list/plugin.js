
function wproPlugin_wproCore_list(){}
wproPlugin_wproCore_list.prototype.init=function(EDITOR){
EDITOR.addButtonStateHandler('bulletsandnumbering',wproPlugin_wproCore_list_buttonStateHandler);
};
function wproPlugin_wproCore_list_buttonStateHandler(EDITOR,srcElement,cid,inTable,inA,range){
try {
if ((!EDITOR.editDocument.queryCommandEnabled('insertunorderedlist')&&!EDITOR.editDocument.queryCommandEnabled('insertorderedlist'))||range.type=='control') {
return "wproDisabled"
} else {
return "wproReady"
}
} catch (e) {}
}