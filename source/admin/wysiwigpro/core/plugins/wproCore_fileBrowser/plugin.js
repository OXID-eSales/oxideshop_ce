
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproPlugin_wproCore_fileBrowser(){}
wproPlugin_wproCore_fileBrowser.prototype.init=function(EDITOR){
EDITOR.addHTMLFilter('design', wproPlugin_wproCore_fileBrowser_df);
EDITOR.addHTMLFilter('source', wproPlugin_wproCore_fileBrowser_sf);
EDITOR.addButtonStateHandler('imageproperties', wproPlugin_wproCore_fileBrowser_bsh);
EDITOR.addButtonStateHandler('mediaproperties', wproPlugin_wproCore_fileBrowser_bsh);
EDITOR.addButtonStateHandler('linkproperties', wproPlugin_wproCore_fileBrowser_bsh);
EDITOR.addButtonStateHandler('insertlink', wproPlugin_wproCore_fileBrowser_bsh);
EDITOR.addButtonStateHandler('insertdoclink', wproPlugin_wproCore_fileBrowser_bsh);
EDITOR.addEditorEvent('dblClick', wproPlugin_wproCore_fileBrowser_dblclick);
EDITOR.addFormattingHandler('unlink',wproPlugin_wproCore_fileBrowser_callF);
EDITOR.addButtonStateHandler('unlink', wproPlugin_wproCore_fileBrowser_bsh);
this.editor = EDITOR.name;
}
function wproPlugin_wproCore_fileBrowser_callF (EDITOR, sFormatString, sValue) {
var range = EDITOR.selAPI.getRange();
if (range.type == 'control') {
var node = range.nodes[0];
} else {
var node = WPro.getParent(range.getStartContainer());
}
switch(sFormatString) {
case "unlink" :
if (range.getText() == '') {
var a = WPro.getParentNodeByTagName(node, 'A');
if (a) {
WPro.removeNode(a);
}
} else {
WPro.callCommand(EDITOR.editDocument, 'unlink', false, null);
}
break;
}
}
function wproPlugin_wproCore_fileBrowser_bsh(EDITOR,srcElement,cid,inTable,inA,range){
var ret = 'wproDisabled';
switch(cid) {
case 'imageproperties':
if (range.type=='control') {
if (range.nodes[0]) {
if (range.nodes[0].tagName == 'IMG' && !range.nodes[0].className.match(/wproFilePlugin/i)) {
ret = 'wproReady';
}
}
}
break;
case 'mediaproperties' :
if (range.type=='control') {
if (range.nodes[0]) {
if (range.nodes[0].tagName == 'IMG' && range.nodes[0].className.match(/wproFilePlugin/i)) {
ret = 'wproReady';
}
}
}
break;
case 'unlink' :
if (range.type == 'control') {
var node = range.nodes[0];
} else {
var node = WPro.getParent(range.getStartContainer());
}
var a = WPro.getParentNodeByTagName(node, 'A');
ret = a ? 'wproReady' : 'wproDisabled';
break;
}
return ret;
}
function  wproPlugin_wproCore_fileBrowser_dblclick(EDITOR,evt){
var srcElement = evt.srcElement ? evt.srcElement : evt.target;
if (srcElement.tagName && srcElement.tagName == 'IMG' && !srcElement.className.match(/wproBlock/i)) {
EDITOR._selectedNode = evt.srcElement;
if (WPro.isSafari) {
var range = EDITOR.selAPI.getRange();
range.range.selectNode(srcElement);
range.select();
}
if (srcElement.className.match(/wproFilePlugin/i)) {
EDITOR.openDialogPlugin('wproCore_fileBrowser&action=media',760,480);
} else {
EDITOR.openDialogPlugin('wproCore_fileBrowser&action=image',760,480);
}
}
}
wproPlugin_wproCore_fileBrowser.prototype._getArrAttributes = function (o, decode) {
var attrs = String(o).match(/'[a-z]+'\:"[^"]*"/gi);
var arr = {};
if (attrs) {
var rl2 = attrs.length;
for (var j=0; j < rl2; j++) {
var name = attrs[j].replace(/'([a-z]+)'\:"[^"]*"/gi, "$1");
var value = attrs[j].replace(/'[a-z]+'\:"([^"]*)"/gi, "$1");
if (decode) value = WPro.htmlSpecialCharsDecode(value);
arr[name.trim()] = value.trim();
}
}
return arr;
}
wproPlugin_wproCore_fileBrowser.prototype._getAttributes = function (o, decode) {
var attrs = String(o).match(/[\s][a-z]+="[^"]*"/gi);
var arr = {};
if (attrs) {
var rl2 = attrs.length;
for (var j=0; j < rl2; j++) {
var name = attrs[j].replace(/([a-z]+)="[^"]*"/gi, "$1");
var value = attrs[j].replace(/[a-z]+="([^"]*)"/gi, "$1");
if (decode) value = WPro.htmlSpecialCharsDecode(value);
arr[name.trim()] = value.trim();
}
}
return arr;
}
wproPlugin_wproCore_fileBrowser.prototype.unserializeMedia = function (data, filter) {
if(!filter)filter='design';
var editor=WPro.editors[this.editor];
var ob = {};
var o = data.match(/'object'\:\{[\s\S]*?\}(,'param'|,'embed'|,'content'|,'end')/i);
var p = data.match(/'param'\:\{[\s\S]*?\}(,'embed'|,'content'|,'end')/i);
var e = data.match(/'embed'\:\{[\s\S]*?\}(,'content'|,'end')/i);
var c = data.match(/'content'\:"[^"]+",'end'/i);
if (o) {
ob['object'] = {};
var oAttrs = this._getArrAttributes(o[0].replace(/'object'\:\{([\s\S]*?)\}(,'param'|,'embed'|,'content'|,'end')/i, " $1"), false);
for (var x in oAttrs) {
ob['object'][x] = editor.triggerHTMLFilter(filter,WPro.htmlSpecialCharsDecode(unescape(oAttrs[x])));
}
if (p) {
ob['param'] = {};
var params = p[0].match(/'[a-z]+'\:"[\s\S]*?"/gi)
if (params) {
var rl2 = params.length;
for (var j=0; j < rl2; j++) {
var name = params[j].replace(/'([a-z]+)'\:"[\s\S]*?"/gi, "$1");
var value = params[j].replace(/'[a-z]+'\:"([\s\S]*?)"/gi, "$1");
ob['param'][name] = editor.triggerHTMLFilter(filter,WPro.htmlSpecialCharsDecode(unescape(value)));
}
}
}
}
if (e) {
ob['embed'] = {};
var oAttrs = this._getArrAttributes(e[0].replace(/'embed'\:\{([\s\S]*?)\}(,'content'|,'end')/i, " $1"));
for (var x in oAttrs) {
ob['embed'][x] = editor.triggerHTMLFilter(filter,WPro.htmlSpecialCharsDecode(unescape(oAttrs[x])));
}
}
if (c) {
ob['content'] = '';
ob['content'] = editor.triggerHTMLFilter(filter,editor.sourceFormatting(WPro.htmlSpecialCharsDecode(unescape(c[0].replace(/'content'\:"([^"]+)",'end'/i, "$1")))));
}
return ob
}
wproPlugin_wproCore_fileBrowser.prototype.unserializeMediaTag = function(data) {
var embeds = data.match(/<embed [^>]*>(|[\s\S]*?<\/embed>)/gi)
var objects = data.match(/<object [^>]*>[\s\S]*?<\/object>/gi)
var data = {};
if (objects) {
var o = objects[0].match(/<object [^>]*>/i);
var object = '';
if (o) {
data['object'] = this._getAttributes(o[0], true);
var p = objects[0].match(/<param [^>]*>/gi);
if (p) {
data['param'] = {};
var rl2 = p.length;
for (var j=0; j < rl2; j++) {
var name = p[j].replace(/^[\s\S]*name="([^"]*)"[\s\S]*$/gi, "$1").toLowerCase();
var value = p[j].replace(/^[\s\S]*value="([^"]*)"[\s\S]*$/gi, "$1");
data['param'][name] = WPro.htmlSpecialCharsDecode(value);
}
}
}
var content = objects[0].replace(/\s*<(noembed|embed|object)[^>]*>/gi, '').replace(/<\/(noembed|embed|object)>\s*/gi,'').replace(/\s*<param [^>]*>\s*/gi, '').trim();
if (content) data['content'] = content;
}
if (embeds) {
var e = embeds[0].match(/<embed [^>]*>/i);
if (e) {
data['embed'] = this._getAttributes(e[0], true);
}
var content = embeds[0].replace(/\s*<(noembed|embed|object)[^>]*>/gi, '').replace(/<\/(noembed|embed|object)>\s*/gi,'').replace(/\s*<param [^>]*>\s*/gi, '').trim();
if (content) data['content'] = content;
}
return data;
}
wproPlugin_wproCore_fileBrowser.prototype.serializeMedia = function(attrs) {
var object = '';
var param = '';
var embed = '';
var content = '';
var arr = [];
if (attrs['object']) {
for(var x in attrs['object']) {
arr.push('\''+x+'\':"'+escape(WPro.htmlSpecialChars(attrs['object'][x]))+'"');
}
object = arr.join(',');
}
if (attrs['param']) {
arr = []
for(var x in attrs['param']) {
arr.push('\''+x+'\':"'+escape(WPro.htmlSpecialChars(attrs['param'][x]))+'"');
}
param = arr.join(',');
}
if (attrs['embed']) {
arr = [];
for(var x in attrs['embed']) {
arr.push('\''+x+'\':"'+escape(WPro.htmlSpecialChars(attrs['embed'][x]))+'"');
}
embed = arr.join(',');
}
content = escape(WPro.htmlSpecialChars(attrs['content']?attrs['content']:''));
arr = [];
if (object) arr.push('\'object\':{'+object+'}');
if(param) arr.push('\'param\':{'+param+'}');
if(embed) arr.push('\'embed\':{'+embed+'}');
if(content) arr.push('\'content\':"'+content+'"');
arr.push('\'end\':"end"');
return '{'+arr.join(',')+'}';
}
wproPlugin_wproCore_fileBrowser.prototype.serializeMediaToTag = function(data) {
var editor=WPro.editors[this.editor];
var str = '';
var allowedEmpty = /^(title)$/i
if (data['object']) {
str += '<object';
for(var x in data['object']) {
if (data['object'][x]=='') if (!allowedEmpty.test(x)) continue;
str	+= ' '+x+'="'+WPro.htmlSpecialChars(data['object'][x], true)+'"';
}
str+='>';
if (data['param']) {
for(var x in data['param']) {
str+='\n<param name="'+x+'" value="'+WPro.htmlSpecialChars(data['param'][x], true)+'"';
if (editor.useXHTML) {
str+=' />';
} else {
str+='>';
}
}
}
}
if (data['embed']) {
str += '<embed';
for(var x in data['embed']) {
if (data['embed'][x]=='') if (!allowedEmpty.test(x)) continue;
str	+= ' '+x+'="'+WPro.htmlSpecialChars(data['embed'][x], true)+'"';
}
str+='>';
if (data['content']) {
str +='<noembed>'+data['content']+'</noembed>';
}
str+='</embed>';
}
if (data['content']&&!data['embed']) {
str+=data['content'];
}
if (data['object']) {
str+='</object>';
}
return str;
}
function wproPlugin_wproCore_fileBrowser_sf(editor, html) {
var objects = html.match(/<img [^>]*class="wproFilePlugin[^"]*" [^>]*>/gi)
if (objects) {
var rl = objects.length;
for (var i=0; i < rl; i++) {
var original = objects[i];
var data;
var copy = {}
var title = objects[i].match(/ _wpro_media_data="[^"]*"/i);
var reg;
var arrs = ['width','height','style','class','align','border','hspace','vspace','title'];
for (var j=0;j<arrs.length;j++) {
reg = new RegExp(' '+arrs[j]+'="[^"]*"', 'i');
copy[arrs[j]] = objects[i].match(reg);
if (copy[arrs[j]]&&arrs[j]=='class') {
copy[arrs[j]][0] = copy[arrs[j]][0].replace(/\s*wproFilePlugin\s*/,'');
if (copy[arrs[j]][0] == '') delete copy[arrs[j]];
}
}
if (title) {
data = editor.plugins['wproCore_fileBrowser'].unserializeMedia(unescape(WPro.htmlSpecialCharsDecode(title[0])), 'source');
var str = '';
if (data['object']) {
for (var x in copy) {
if (copy[x]) {
data['object'][x] = copy[x][0].replace(/^ [a-z]+="([^"]*)"/i, '$1');
}
}
}
if (data['embed']) {
for (var x in copy) {
if (!data['object']||(x=='width'||x=='height')) {
if (copy[x]) {
data['embed'][x] = copy[x][0].replace(/^ [a-z]+="([^"]*)"/i, '$1');
}
}
}
}
str = editor.plugins['wproCore_fileBrowser'].serializeMediaToTag(data);
html = html.replace(original, str);
}
}
}
return html;
}
function _wproPlugin_wproCore_fileBrowser_df(editor, object) {
object = editor.sourceFormatting(object);
var attrs = editor.plugins['wproCore_fileBrowser'].unserializeMediaTag(object);
var str = '';
var width = '';
var height = '';
var style = '';
var className = '';
var hspace = '';
var vspace = '';
var title = '';
var align = '';
var border = '';
var d
if (attrs['object']) {
d = attrs['object']
} else if (attrs['embed']) {
d = attrs['embed']
}
for(var x in d) {
if (x == 'width') {
width = d[x];
} else if (x == 'height') {
height = d[x];
} else if (x == 'style') {
style = d[x];
} else if (x == 'class') {
className = ' '+d[x];
} else if (x == 'hspace') {
hsapce = d[x];
} else if (x == 'vspace') {
vspace = d[x];
} else if (x == 'align') {
align = d[x];
} else if (x == 'border') {
border = d[x];
} else if (x == 'title') {
title = d[x];
}
}
var mediaData = editor.plugins['wproCore_fileBrowser'].serializeMedia(attrs);
var str = '<img class="wproFilePlugin'+WPro.htmlSpecialChars(className)+'" src="'+WPro.domain+WPro.URL+'core/images/placeholder.gif"';
if (width) str+=' width="'+width+'"';
if (height) str+=' height="'+height+'"';
if (style) str+=' style="'+style+'"';
if (hspace) str+=' hspace="'+hspace+'"';
if (vspace) str+=' vspace="'+vspace+'"';
if (align) str+=' align="'+align+'"';
if (border) str+=' border="'+border+'"';
if (title) str+=' title="'+title+'"';
str += ' _wpro_media_data="'+WPro.htmlSpecialChars(escape(mediaData))+'" />';
return str;
}
function wproPlugin_wproCore_fileBrowser_df(editor, html) {
var objects = html.match(/<object [^>]*>[\s\S]*?<\/object>/gi)
if (objects) {
var rl = objects.length;
for (var i=0; i < rl; i++) {
var original = objects[i];
var str = _wproPlugin_wproCore_fileBrowser_df(editor, objects[i]);
html = html.replace(original, str);
}
}
var embeds = html.match(/(<embed(>| [^>]*>)[\s\S]*?<\/embed>|<embed(\/>| [^>]*\/>))/gi)
if (embeds) {
var rl = embeds.length;
for (var i=0; i < rl; i++) {
var original = embeds[i];
var str = _wproPlugin_wproCore_fileBrowser_df(editor, embeds[i]);
html = html.replace(original, str);
}
}
return html;
}