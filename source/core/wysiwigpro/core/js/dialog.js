
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproDialog () {
this.hideLoadMessageOnLoad = true;
this.timer = new wproTimer();
this.events = new wproEvents();
this.embedded = false;
this.width = '';
this.height = '';
this.doFormSubmit = function () {
this.reselectRange();
this.reselect = false;
if (typeof(formAction) != 'undefined') {
return formAction ();
} else {
return false;
}
}
this.editorLink = function(url) {
return wproEditorLink(url, this.URL, this.route);
}
this.openFileBrowser = function (type, returnFunction, getFunction) {
if (!type) type = 'link';
if (typeof(WPRO_FB_RETURN_FUNCTION)=='undefined') {
WPRO_FB_RETURN_FUNCTION = {};
}
if (typeof(WPRO_FB_GET_FUNCTION)=='undefined') {
WPRO_FB_GET_FUNCTION = {};
}
WPRO_FB_RETURN_FUNCTION[this.sid] = returnFunction
WPRO_FB_GET_FUNCTION[this.sid] = getFunction
this.openDialogPlugin('wproCore_fileBrowser&action=link&chooser=true&dirs='+type, 760, 480-55, '', 'modal', 600);
}
this.openDialogPlugin = function (url, width, height, features, modal, openerID) {
if (this.iframeDialog) {
if (!openerID) openerID = frameID;
this.editor.openDialogPlugin(url, width, height, features, modal, true, openerID);
} else {
var url = this.editorLink('dialog.php?dialog=' + url + '&' + this.sid + (this.phpsid ? '&' + this.phpsid : '') + (this.appendToQueryStrings ? '&' + this.appendToQueryStrings : ''));
this.openDialog(url, modal, width, height, features);
}
}
this.openDialog = function (url, modal, width, height, features, iframe) {
wp_openDialog(url, modal, width, height, features, iframe, true);
}
this.hideLoadMessage = function () {
var node
if (node = document.getElementById('dialog_loadMessage')) {
node.style.display = 'none';
}
}
this.showLoadMessage = function () {
var node
if (node = document.getElementById('dialog_loadMessage')) {
node.style.display = 'block';
}
}
this.reselectRange = function () {
if (this.reselect && this.rangeToReselect && this.isIE) {
try{this.rangeToReselect.select();}catch(e){}
}
}
this.inClose = false;
this.close = function () {
if (this.inClose) return;
this.inClose = true;
if (!this.iframeDialog) {
top.close();
} else {
this.reselectRange();
this._unload();
if (frameID == 0) {
if (this.editor) {
this.editor.allowInteraction();
var iframe = editorWindow.document.getElementById(this.editor._internalId + '_dialogFrame');
iframe.style.display = 'none';
iframe.contentWindow.document.location.replace(this.URL + 'core/html/iframeSecurity.htm');
}
} else {
if (this.editor) {
var iframe = editorWindow.document.getElementById(this.editor._internalId + '_dialogFrame_'+frameID);
if (iframe) {
iframe.style.display = 'none';
iframe.parentNode.removeChild(iframe);
}
}
}
}
}
this.focus = function () {
if (this.iframeDialog) {
window.focus();
} else {
top.focus();
}
try {
document.dialogForm.elements[0].focus();
} catch(e) {}
}
this.resizeTo = function(width, height) {
window.resizeTo(width, height)
}
this.writeFrame = function (node, string) {
if (string.search(/<body/gi) == -1) {
var bodya = '';
var heada = '';
var htmla = '';
if (currentEditor._inDesign) {
bodya = this.getNodeAttributesString(currentEditor.editDocument.getElementsByTagName('BODY')[0],false);
heada = this.getNodeAttributesString(currentEditor.editDocument.getElementsByTagName('HEAD')[0],false);
htmla = this.getNodeAttributesString(currentEditor.editDocument.getElementsByTagName('HTML')[0],false);
}
string = currentEditor.doctype+'<ht'+'ml'+htmla+'><he'+'ad'+heada+'><title>Preview</title>'+(currentEditor.getStyles())+'</he'+'ad><body'+bodya+'>'+string+'</bo'+'dy></ht'+'ml>';
}
var doc = this.getFrameDocument(node);
doc.open('text/html','replace');
doc.write(string);
doc.close();
}
this.changeFrameLocation = function (node, loc) {
try{this.getFrameWindow(node).location.replace(loc);}catch(e){}
}
this.getFrameWindow = function (node) {
var win
if (node.contentWindow) {
win = node.contentWindow
} else if (window.frames) {
var id = node.id;
win = window.frames[id]
}
return win
}
this.getFrameDocument = function (node) {
return this.getFrameWindow(node).document
}
this.urlFormatting = function (url, full) {
url = String(url);
if (url.match(/^javascript:/gi)) {
return url;
}
if ( /^www\./i.test(url)) {
url = 'http://'+url;
}
var locationRegex = new RegExp('^'+this.quoteMeta(this.location).replace(/[\\]&/gi,'(\\&|\\&amp\\;)')+'#', 'gi');
url = url.replace(locationRegex, '#');
var locationRegex2 = new RegExp('^'+this.quoteMeta(this.domain + this.URL + 'core/html/')+'(iframeSecurity.htm|blank.htm)', 'gi');
url = url.replace(locationRegex2, '');
var location = this.location.replace(/^([\s\S]*\/)[^\/]*$/i, "$1");
if (this.urlFormat=='absolute'||full) {
if (this._baseDomain) {
url = url.replace(/^\//gi, this._baseDomain+'/');
url = url.replace(/^([^#][^:"]*)$/gi, this.baseURL+'$1');
} else {
url = url.replace(/^\//gi, this.domain+'/');
url = url.replace(/^([^#][^:"]*)$/gi, location+'$1');
}
while(url.match(/([^:][^\/])\/[^\/]*\/\.\.\//i)) {
url = url.replace(/([^:][^\/])\/[^\/]*\/\.\.\//i, '$1/');
}
url = url.replace(/\/\.\.\//gi, '/');
} else if (this.urlFormat=='nodomain'||this.urlFormat=='relative') {
if (this._baseDomain) {
var loc = this._baseDomain;
} else {
var loc = this.domain;
}
if (loc.match(/^http(s|)\:\/\/www\./i)) {
var domainRegex = new RegExp('^'+( this.quoteMeta(loc).replace(/^(http(s|)\\\:\\\/\\\/)www\\\./i, '$1(www\\.|)') ) +'($|/)', 'gi');
} else {
var domainRegex = new RegExp('^'+( this.quoteMeta(loc).replace(/^(http(s|)\\\:\\\/\\\/)/i, '$1(www\\.|)') ) +'($|/)', 'gi');
}
url = url.replace(domainRegex, '$2');
if (this.urlFormat=='relative'&&this.baseURL) {
var b = this.baseURL.replace(domainRegex, '$2');
var r = new RegExp('^'+this.quoteMeta(b), 'gi');
url = url.replace(r, '');
if (url.substr(0,1)=='/') {
url = url.substr(1);
var c = b.match(/\//g);
for (var i=0;i<c.length;i++) {
url = '../'+url;
}
}
}
}
url = url.replace(/^[^#]*#[\s\S]+$/g, function (x){s=x.split('#');return s[0]+'#'+unescape(s[1]);});
if (this.encodeURLs) {
url = url.replace(/^[^"#]*/g, function (x){return x.replace(/\s/g, '%20');});
} else {
url = unescape(url);
}
return url;
}
this.appendBaseToURL = function (url) {
return this.urlFormatting(url, true);
}
this.applyFilters = function (trigger, str) {
if (this.editor && this.editor.triggerHTMLFilter) {
str = this.editor.triggerHTMLFilter(trigger, str);
}
return str;
}
this.selectCurrentStyle = function (elm) {
if (!elm.nodeType) return;
if (currentEditor) {
var range = currentEditor.selAPI.getRange();
var n = elm.options.length
for (var i=0; i<n; i++) {
style = elm.options[i].value;
if (style!='') {
var tagName = style.replace(/^([a-z0-9*:\-_]+)[^>]*$/gi, "$1", elm);
var tag = false;
if (currentEditor._selectedNode && 	currentEditor._selectedNode.tagName == tagName.toUpperCase()) {
tag = currentEditor._selectedNode;
} else if (range.nodes[0] && range.nodes[0].tagName == tagName.toUpperCase()) {
tag = range.nodes[0];
} else {
tag = range.getContainerByTagName(tagName);
}
if (tag) {
var attrs = style.match(/ [a-z]+="[^"]*"/gi);
if (attrs) {
var matches = false;
for (j=0;j<attrs.length;j++) {
var nodeName = attrs[j].replace(/ ([a-z]+)="[^"]*"/gi, "$1");
var nodeValue = attrs[j].replace(/ [a-z]+="([^"]*)"/gi, "$1").replace(/&quot;/, '"').replace(/&lt;/, '<').replace(/&gt;/, '>').replace(/&amp;/, '&');
var value
if (nodeName == 'class') {
value = String(tag.className).replace(/\s*wproGuide\s*/i,'').replace(/\s*wproFilePlugin\s*/i,'');
if (value.toLowerCase().trim() != nodeValue.toLowerCase().trim()) {
break;
}
} else if (nodeName == 'style') {
value = this.styleFormatting(tag.style.cssText);
nodeValue = this.styleFormatting(nodeValue);
var styles1 = value.match(/([A-Za-z\-]*:[^;]*)/gi);
var styles2 = nodeValue.match(/([A-Za-z\-]*:[^;]*)/gi);
if (styles1) {
var m = true
for (k=0;k<styles2.length;k++) {
m = false
for (l=0;l<styles1.length;l++) {
if (styles2[k] == styles1[l]) {
m = true
break;
}
}
}
if (!m) {
break;
}
} else {
break;
}
} else {
var value = tag.getAttribute(nodeName);
if (value.toLowerCase().trim() != nodeValue.toLowerCase().trim()) {
break;
}
}
if (j == attrs.length-1) {
matches = true;
}
}
if (matches) {
elm.value = style;
break;
}
} else if (this.getNodeAttributesString(tag) == '') {
elm.value = style;
break;
}
}
}
}
}
}
this.makeAttraValueOK=function(str) {
return String(str).replace(/&/gi, '&amp;').replace(/"/gi, '&quot;').replace(/</gi, '&lt;').replace(/>/gi, '&gt;');
}
this.alertWrongFormat=function() {
alert(strWrongFormat);
}
this.alertWrongSize=function(lower,upper) {
alert(strWrongSize.replace(/##lower##/gi, lower).replace(/##upper##/gi, upper));
}
this._sessTimeout = function () {
if (this.isIE&&!this.iframeDialog) {
if (WPro&&currentEditor) {
WPro._updateAll('_createSessTag');
}
if (currentEditor) {
this.timer.addTimer('dialog._sessTimeout()', currentEditor.sessRefresh*1000);
}
}
}
this.getNodeAttributesString = wproGetNodeAttributesString
this.quoteMeta = wproQuoteMeta
this.rgbToHex=wproRgbToHex
this.toHex=wproToHex
this.hexToR = wproHexToR
this.hexToG = wproHexToG
this.hexToB = wproHexToB
this.cutHex = wproCutHex
this.hexToRGB = wproHexToRGB
this.styleFormatting = wproStyleFormatting
this._shorthandStyles = wp_shorthandStyles
this._compressBoxStyles = wp_compressBoxStyles
this._setBrowserTypeStrings = wp_setBrowserTypeStrings;
this.urlEncode = wproUrlEncode;
this.urlDecode = wproUrlDecode;
this.addSlashes = wproAddSlashes
this.htmlSpecialChars = wproHtmlSpecialChars
this.htmlSpecialCharsDecode =  wproHtmlSpecialCharsDecode;
this.unloadFunctions = [];
this.addUnloadFunction = function (f) {
this.unloadFunctions.push(f);
}
this.init = function () {
if (this.width&&this.height) {
if (top.dialogArguments) {
if (parseInt(navigator.appVersion.replace(/[\s\S]*?MSIE ([0-9\.]*)[\s\S]*?/gi, "$1") ) < 7) {
var nWidth = this.width + 12;
var nHeight = this.height + 56;
} else {
var nWidth = this.width;
var nHeight = this.height;
}
window.dialogWidth = nWidth+'px';
window.dialogHeight = nHeight+'px';
} else if (window.innerHeight) {
if (this.isSafari) {
this.height+=10;
}
var hd = parseInt(window.outerHeight) - parseInt(window.innerHeight)
var wd = parseInt(window.outerWidth) - parseInt(window.innerWidth)
window.resizeTo(this.width+wd, this.height+hd);
}
}
if (wproEmbedded == true) {
this.embedded = true;
}
if (wproIframeDialogs==true) {
this.iframeDialog = true;
var pw = parent;
while (pw.wproIframeDialogs) {
pw = pw.parent
}
editorWindow = pw;
if (editorWindow.WPro) {
WPro = editorWindow.WPro;
obj = WPro.currentEditor;
currentEditor = WPro.currentEditor;
} else {
WPro = null;
parentWindow = null;
obj = null;
currentEditor = null;
}
if (openerID == null) {
parentWindow = pw;
} else {
var iframe;
if (openerID == 0||openerID==600) {
iframe = editorWindow.document.getElementById(currentEditor._internalId + '_dialogFrame');
} else {
iframe = editorWindow.document.getElementById(currentEditor._internalId + '_dialogFrame_'+openerID);
}
if (iframe) {
if (iframe.contentWindow) {
parentWindow = iframe.contentWindow
} else {
parentWindow = editorWindow.frames[iframe.id]
}
} else {
parentWindow = pw;
}
}
} else {
this.iframeDialog = false;
this.reselect = false;
if (top.dialogArguments) {
WPro = (typeof(top.dialogArguments.WPro)=='undefined')?null:top.dialogArguments.WPro;
parentWindow = top.dialogArguments;
obj = (typeof(top.dialogArguments.WPro)=='undefined'||top.dialogArguments.WPro==null)?null:WPro.currentEditor;
currentEditor = obj;
} else if (top.opener) {
WPro = (typeof(top.opener.WPro)=='undefined')?null:top.opener.WPro;
parentWindow = top.opener;
obj = (WPro==null)?null:WPro.currentEditor;
currentEditor = obj;
} else {
WPro = null;
parentWindow = null;
obj = null;
currentEditor = null;
editorWindow = null;
}
if (parentWindow) {
pw = parentWindow;
while (pw.parentWindow) {
pw = pw.parentWindow
}
editorWindow = pw;
}
}
this.WPro = WPro;
this.editor = currentEditor;
this.parentWindow = parentWindow;
this.editorWindow = editorWindow;
if (this.iframeDialog) {
this.reselect = true;
if (WPro) {
if (this.isIE) {
this.rangeToReselect = this.editor.editDocument.selection.createRange();
}
}
}
this.location = String(this.editorWindow.document.location);
this.domain = this.location.replace(/(^(http|https):\/\/[^\/]*)[\s\S]*/i, '$1');
if (this.baseURL) {
var loc = this.baseURL;
loc = loc.replace(/(^(http|https):\/\/[^\/]*)[\s\S]*/i, "$1");
this._baseDomain = loc;
}
this._sessTimeout();
this.events.addEvent(window, 'unload', wp_unload);
if (this.editor) { if (this.editor.closePMenu) this.editor.closePMenu(); }
if (typeof(wproPMenu)!='undefined') this.PMenu = new wproPMenu();
}
this.closePMenuTimeout = function () { setTimeout("dialog.PMenu.closePMenu();",100); }
this._unload = function () {
try{
this.showLoadMessage();
this.timer.clearAllTimers();
this.events.removeAllEvents();
if (!this.embedded) {
if (this.iframeDialog) {
parentWindow.wproCloseOpenDialogs(frameID);
} else {
wproCloseOpenDialogs();
}
}
if (typeof(unloadDialog)!='undefined') {
unloadDialog();
}
var n = this.unloadFunctions.length
for (var i=0; i<n; i++) {
this.unloadFunctions[i]();
}
}catch(e){};
}
}
function wp_load() {
dialog.hiddenMenus = document.getElementById('hiddenMenus');
if (dialog.isIE && dialog.browserVersion < 7 && !dialog.iframeDialog) {
var links = document.getElementsByTagName('A')
var n = links.length
for (var i=0; i<n; i++) {
if (links[i].getAttribute('href').toLowerCase()=='javascript:undefined'
||links[i].getAttribute('href').toLowerCase()=='javascript:undefined;') {
links[i].href = document.location+'#';
}
}
}
if (dialog.iframeDialog && document.getElementById('dialogTitleBar')) {
WPRO_DIF_addHandle(document.getElementById('dialogTitleBar'), window);
}
if (dialog.hideLoadMessageOnLoad) {
dialog.hideLoadMessage();
}
document.body.style.display = 'none';
document.body.style.display = '';
}
function wproHideLoadMessage () {
dialog.hideLoadMessage();
}
function wp_unload() {
dialog._unload();
}
function wproSetLoadMessageHeight () {
var winDim = wproGetWindowInnerHeight();
var height = winDim['height'];
if (height==0)height=200;
var minus = 0;
if (wproIframeDialogs==true) {
minus = 30;
document.getElementById('dialog_loadMessage').style.top = '25px';
}
document.getElementById('dialog_loadMessage').style.height = height-minus + 'px';
document.getElementById('dialog_loadMessage').firstChild.style.marginTop=(height/2-40)+'px';
}
var DIALOG = new wproDialog();
var dialog = DIALOG;