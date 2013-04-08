
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproPlugin_wproCore_fullWindow () {}
wproPlugin_wproCore_fullWindow.prototype.init = function (EDITOR) {
EDITOR._fullWindow = false;
EDITOR.addButtonStateHandler('fullwindow',wproPlugin_wproCore_fullWindow_bsh);
this.getFullWindowButtons('design',EDITOR);
this.getFullWindowButtons('source',EDITOR);
this.getFullWindowButtons('preview',EDITOR);
EDITOR._fullWindowSetButtonStates=wproE__fullWindowSetButtonStates;
EDITOR.fullWindow=wproE_fullWindow;
EDITOR.exitFullWindow=wproE_exitFullWindow;
EDITOR._reFullScreen=wproE__reFullScreen;
WPro.eval('WPro.'+EDITOR._internalId+'._e_reFullScreen=function(evt){WPro.'+EDITOR._internalId+'._reFullScreen()}');
}
wproPlugin_wproCore_fullWindow.prototype.getFullWindowButtons = function (view, EDITOR) {
var buttons = eval('EDITOR.'+view+'Buttons');
var n = buttons.length;
for (var i = 0; i < n; i++) {
if (!buttons[i].getAttribute("_wp_cid")) { continue };
if (buttons[i].getAttribute("_wp_cid")=='fullwindow') {
eval('EDITOR._'+view+'FullWindowButton = buttons[i]');
break;
}
}
}
function wproE__fullWindowSetButtonStates () {
if (this._fullWindow) {
var c = 'wproLatched';
} else {
var c = 'wproReady';
}
if (this._designFullWindowButton)
this._designFullWindowButton.className = c;
if (this._sourceFullWindowButton)
this._sourceFullWindowButton.className = c;
if (this._previewFullWindowButton)
this._previewFullWindowButton.className = c;
}
function wproFullWindow_hideElements (s) {
var n = s.length
for (var i=0; i<n; i++) {
if (s[i].className) {
s[i].className = s[i].className + ' wproHide'
} else {
s[i].className = 'wproHide';
}
}
}
function wproFullWindow_showElements (s) {
var n = s.length
for (var i=0; i<n; i++) {
if (s[i].className) {
s[i].className = s[i].className.replace(/[\s]*wproHide[\s]*/gi,'');
}
}
}
function wproE_fullWindow () {
if (this._fullWindow) {
this.exitFullWindow();
return;
}
this._origFullWindowScrollTop = document.body.scrollTop + document.documentElement.scrollTop;
this._origFullWindowScrollLeft = document.body.scrollLeft + document.documentElement.scrollLeft;
wproFullWindow_hideElements(document.getElementsByTagName('SELECT'));
wproFullWindow_hideElements(document.getElementsByTagName('INPUT'));
wproFullWindow_hideElements(document.getElementsByTagName('TEXTAREA'));
wproFullWindow_hideElements(document.getElementsByTagName('BUTTON'));
wproFullWindow_showElements(this.container.getElementsByTagName('SELECT'));
wproFullWindow_showElements(this.container.getElementsByTagName('INPUT'));
wproFullWindow_showElements(this.container.getElementsByTagName('TEXTAREA'));
wproFullWindow_showElements(this.container.getElementsByTagName('BUTTON'));
this._fullWindow = true;
window.scrollTo(0,0);
this._dragOrigHeight = Math.abs(this.editorborder.offsetHeight);
this.container.style.position = 'absolute';
this.container.style.zIndex = 1000;
this.container.style.top = '0';
this.container.style.left = '0';
var winDim = wproGetWindowInnerHeight();
var availHeight = winDim['height'];
var availWidth = winDim['width'];
this._fullWindowParents = [];
var parent = this.container.parentNode;
while(parent) {
if (parent.tagName=='HTML') break;
var st = parent.style
if (parent.nodeType == 1 && st) {
this._fullWindowParents.push({
node : parent,
overflow : st.overflow,
position : st.position,
left : st.left,
top : st.top,
right : st.right,
bottom : st.bottom,
width : st.width,
height : st.height,
margin : st.margin,
padding : st.padding,
border : st.border
});
st.position = 'static';
st.left = st.top = st.margin = st.padding = st.border = '0';
st.width = st.height = st.right = st.bottom = 'auto';
}
parent = parent.parentNode;
}
document.documentElement.style.overflow = 'hidden';
document.body.style.overflow = 'hidden';
var aHeight = document.getElementById(this._internalId+'_displayAbove').offsetHeight;
var bHeight = document.getElementById(this._internalId+'_displayBelow').offsetHeight;
this.resizeTo(availWidth, availHeight-5-aHeight-bHeight);
WPro.events.addEvent(window, 'resize', this._e_reFullScreen);
this._fullWindowSetButtonStates();
this._reactivate();
for(var i=0;i<WPro.editors.length;i++) {
if (WPro.editors[i]&&WPro.editors[i]!=this) {
WPro.editors[i].container.style.display = 'none';
}
}
this.focus();
this.triggerEditorEvent('enterFullWindow');
}
function wproE_exitFullWindow () {
wproFullWindow_showElements(document.getElementsByTagName('SELECT'));
wproFullWindow_showElements(document.getElementsByTagName('INPUT'));
wproFullWindow_showElements(document.getElementsByTagName('TEXTAREA'));
wproFullWindow_showElements(document.getElementsByTagName('BUTTON'));
this._fullWindow = false;
this.container.style.position = 'static'
this.container.style.top = '';
this.container.style.left = '';
this.container.style.zIndex = '';
this.resizeTo(this.specifiedWidth, this._dragOrigHeight);
document.documentElement.style.overflow = '';
document.body.style.overflow = '';
for (var i=0;i<this._fullWindowParents.length;i++) {
var n = this._fullWindowParents[i];
var parent = this._fullWindowParents[i].node
parent.tagName;
var st = parent.style
st.position = n.position;
st.overflow = n.overflow;
st.left = n.left;
st.top = n.top;
st.right = n.right;
st.bottom = n.bottom;
st.width = n.width;
st.height = n.height;
st.margin = n.margin;
st.padding = n.padding;
st.border = n.border;
}
WPro.events.removeEvent(window, 'resize', this._e_reFullScreen);
this._fullWindowSetButtonStates();
for(var i=0;i<WPro.editors.length;i++) {
if (WPro.editors[i]&&WPro.editors[i]!=this) {
WPro.editors[i].container.style.display = 'block';
}
}
window.scrollTo(this._origFullWindowScrollLeft, this._origFullWindowScrollTop)
WPro._updateAll('_reactivate');
this.triggerEditorEvent('exitFullWindow');
}
function wproE__reFullScreen () {
if (this._fullWindow) {
var winDim = wproGetWindowInnerHeight();
var availHeight = winDim['height'];
var availWidth = winDim['width'];
var aHeight = document.getElementById(this._internalId+'_displayAbove').offsetHeight;
var bHeight = document.getElementById(this._internalId+'_displayBelow').offsetHeight;
this.resizeTo(availWidth, availHeight-5-aHeight-bHeight);
}
}
function wproPlugin_wproCore_fullWindow_bsh (EDITOR,srcElement,cid,inTable,inA,range) {
return EDITOR._fullWindow ? "wproLatched" : "wproReady";
}