
function wproPlugin_wproCore_ruler(){}
wproPlugin_wproCore_ruler.prototype.init=function(EDITOR){
EDITOR.addButtonStateHandler('rulerproperties',wproPlugin_wproCore_ruler_bsh);
EDITOR.addEditorEvent('dblClick', wproPlugin_wproCore_ruler_dblclick);
};
function wproPlugin_wproCore_ruler_bsh(EDITOR,srcElement,cid,inTable,inA,range){
return range.nodes[0]?(range.nodes[0].tagName=='HR'?"wproReady":"wproDisabled"):"wproDisabled";
}
function wproPlugin_wproCore_ruler_dblclick(EDITOR,evt){
var srcElement = evt.srcElement ? evt.srcElement : evt.target;
if (srcElement.tagName && srcElement.tagName == 'HR') {
EDITOR._selectedNode = evt.srcElement;
if (WPro.isSafari) {
var range = EDITOR.selAPI.getRange();
range.range.selectNode(srcElement);
range.select();
}
EDITOR.openDialogPlugin('wproCore_ruler',320,210);
}
}