
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproPlugin_JSEmbed(){}
wproPlugin_JSEmbed.prototype.init=function(EDITOR){
this.editor = EDITOR.name;
EDITOR.removeHTMLFilter('source', wproPlugin_wproCore_fileBrowser_sf);
EDITOR.removeHTMLFilter('preview', wproPlugin_wproCore_fileBrowser_sf);
EDITOR.addHTMLFilter('design', wproPlugin_JSEmbed_df);
EDITOR.addHTMLFilter('source', wproPlugin_JSEmbed_sf);
EDITOR.addHTMLFilter('preview', wproPlugin_JSEmbed_pf);
}
wproPlugin_JSEmbed.prototype.serializeMediaToTag = function(data) {
var editor=WPro.editors[this.editor];
var arr = [];
var str = unescape(editor.plugins['wproCore_fileBrowser'].serializeMedia(data));
return '<script type="text/javascript" src="'+editor.urlFormatting(WPro.domain+WPro.URL+'js/wproObject.js')+'"></script>\n<script type="text/javascript">wproObject.write('+str+');</script>';
}
function wproPlugin_JSEmbed_sf(editor, html) {
var objects = html.match(/<img [^>]*class="wproFilePlugin[^"]*" [^>]*>/gi)
if (objects) {
var rl = objects.length;
for (var i=0; i < rl; i++) {
var original = objects[i];
var data;
var copy = {}
var title = objects[i].match(/ _wpro_media_data="[^"]*"/i);
var reg;
var arrs = ['width','height','style','class','align','border','hspace','vspace','title','alt'];
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
str = editor.plugins['JSEmbed'].serializeMediaToTag(data);
html = html.replace(original, str);
}
}
}
return html;
}
function _wproPlugin_JSEmbed_df(editor, attrs) {
var str = '';
var width = '';
var height = '';
var style = '';
var className = '';
var hspace = '';
var vspace = '';
var title = '';
var alt = '';
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
} else if (x == 'alt') {
alt = d[x];
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
if (alt) str+=' alt="'+alt+'"';
str += ' _wpro_media_data="'+WPro.htmlSpecialChars(escape(mediaData))+'" />';
return str;
}
function wproPlugin_JSEmbed_df(editor, html) {
var objects = html.match(/(|<script type="text\/javascript" src="[^"]*\/js\/wproObject\.js"><\/script>\s*)<script [^>]*>wproObject\.write\([\s\S]*?\);<\/script>/gi)
if (objects) {
var rl = objects.length;
for (var i=0; i < rl; i++) {
var original = objects[i];
data = editor.plugins['wproCore_fileBrowser'].unserializeMedia(objects[i].replace(/\\('|")/g,"$1"));
str = _wproPlugin_JSEmbed_df(editor, data);
html = html.replace(original, str);
}
}
return html;
}
function wproPlugin_JSEmbed_pf(editor, html) {
var objects = html.match(/(|<script type="text\/javascript" src="[^"]*\/js\/wproObject\.js"><\/script>\s*)<script [^>]*>wproObject\.write\([\s\S]*?\);<\/script>/gi)
if (objects) {
var rl = objects.length;
for (var i=0; i < rl; i++) {
var original = objects[i];
data = editor.plugins['wproCore_fileBrowser'].unserializeMedia(objects[i].replace(/\\('|")/g,"$1"));
str = editor.plugins['wproCore_fileBrowser'].serializeMediaToTag(data);
html = html.replace(original, str);
}
}
return html;
}