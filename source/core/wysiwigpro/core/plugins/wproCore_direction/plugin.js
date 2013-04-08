
function wproPlugin_wproCore_direction(){}
wproPlugin_wproCore_direction.prototype.init=function(EDITOR){
EDITOR.addFormattingHandler('dirrtl',wproPlugin_wproCore_direction_callF);
EDITOR.addFormattingHandler('dirltr',wproPlugin_wproCore_direction_callF);
EDITOR.addButtonStateHandler('dirrtl',wproPlugin_wproCore_direction_bsh);
EDITOR.addButtonStateHandler('dirltr',wproPlugin_wproCore_direction_bsh);
};
function wproPlugin_wproCore_direction_bsh(EDITOR,srcElement,cid,inTable,inA,range){
var ret = 'wproReady';
if (range.type == 'control') {
var node = range.nodes[0];
} else {
var node = range.getBlockContainer();
}
var dir = node.getAttribute('dir');
if (dir) {
switch(cid) {
case 'dirrtl' :
ret = (dir=='rtl')?'wproLatched':'wproReady';
break;
case 'dirltr' :
ret = (dir=='ltr')?'wproLatched':'wproReady';
break;
}
}
return ret;
}
function wproPlugin_wproCore_direction_callF(EDITOR, sFormatString, sValue) {
switch(sFormatString) {
case 'dirrtl' :
EDITOR.applyStyle('*block* dir="rtl"', false , false , true);
break;
case 'dirltr' :
EDITOR.applyStyle('*block* dir="ltr"', false , false , true);
break;
}
}