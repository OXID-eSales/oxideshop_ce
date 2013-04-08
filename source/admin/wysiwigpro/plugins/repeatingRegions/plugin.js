
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproPlugin_repeatingRegions(){}
wproPlugin_repeatingRegions.prototype.init=function(EDITOR){
this.editor = EDITOR.name;
EDITOR.addHTMLFilter('source',wproPlugin_repeatingRegions_sf);
EDITOR.addHTMLFilter('design',wproPlugin_repeatingRegions_df);
EDITOR.addEditorEvent('click',wproPlugin_repeatingRegions_click);
}
wproPlugin_repeatingRegions.prototype.repeat=function(node){
var editor = WPro.editors[this.editor];
if (node.parentNode) {
var UDBeforeState = editor.history.pre();
var iNode = node.cloneNode(true);
var imgs = iNode.getElementsByTagName('IMG');
for (var i=0; i<imgs.length; i++) {
if (imgs[i].className && imgs[i].className == 'wproBlockDelHidden') {
imgs[i].className = 'wproBlockDel';
break;
}
}
var cn = iNode.childNodes;
for (var i=0; i<cn.length; i++) {
if (cn[i].nodeType == 8) {
if (i.data == 'StartBlockNoDelete') {
i.replaceData('StartBlockCanDelete');
} else if (i.data == 'EndBlockNoDelete') {
i.replaceData('EndBlockCanDelete');
}
}
}
node.parentNode.insertBefore(iNode, node.nextSibling);
editor.history.post(UDBeforeState);
}
}
wproPlugin_repeatingRegions.prototype.remove=function(node){
var editor = WPro.editors[this.editor];
if (node.parentNode) {
var UDBeforeState = editor.history.pre();
node.parentNode.removeChild(node);
editor.history.post(UDBeforeState);
}
}
function  wproPlugin_repeatingRegions_click(EDITOR,evt){
var srcElement = evt.srcElement ? evt.srcElement : evt.target;
if (srcElement.tagName && srcElement.tagName == 'IMG') {
if (srcElement.className == 'wproBlockAdd') {
var node = srcElement.parentNode.parentNode;
EDITOR.plugins['repeatingRegions'].repeat(node);
}
if (srcElement.className == 'wproBlockDel') {
var node = srcElement.parentNode.parentNode;
EDITOR.plugins['repeatingRegions'].remove(node);
}
}
}
function wproPlugin_repeatingRegions_sf (editor, html) {
html = html.replace(/<div class="wproRepeatingBlock">[\s]*?<div [^>]+>([\s]*?(\|wproSelectionStart\|)*?(\|wproSelectionEnd\|)*?[\s]*?<img [^>]*>[\s]*?(\|wproSelectionStart\|)*?(\|wproSelectionEnd\|)*?){1,2}[\s]*?<\/div>[\s]*?(<!--[#\s]*?StartBlock(Can|No)Delete[^>]+>)/gi, '$6');
html = html.replace(/(<!--[#\s]*?EndBlock(Can|No)Delete[^>]+>)[\s]*?<\/div>/gi, '$1');
return html;
}
function wproPlugin_repeatingRegions_df (editor, html) {
if (editor.themeURL.substr(0,1) == '/') {
var delbtn = WPro.domain + editor.themeURL + 'misc/delete.gif';
var addbtn = WPro.domain + editor.themeURL + 'misc/add.gif';
} else {
var delbtn = editor.themeURL + 'misc/delete.gif';
var addbtn = editor.themeURL + 'misc/add.gif';
}
html = html.replace(/(<!--[#\s]*?StartBlockCanDelete[^>]+>)/gi, '<div class="wproRepeatingBlock"><div class="wproBlockBtns" contentEditable="false" unselectable="on"><img class="wproBlockAdd" src="'+addbtn+'" /><img class="wproBlockDel" src="'+delbtn+'" /></div>$1');
html = html.replace(/(<!--[#\s]*?StartBlockNoDelete[^>]+>)/gi, '<div class="wproRepeatingBlock"><div class="wproBlockBtns" contentEditable="false" unselectable="on"><img class="wproBlockAdd" src="'+addbtn+'" /><img class="wproBlockDelHidden" src="'+delbtn+'" /></div>$1');
html = html.replace(/(<!--[#\s]*?EndBlock(Can|No)Delete[^>]+>)/gi, '$1</div>');
return html;
}