
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproPMenu (editor) {
this.PMenu = null;
this.editor = editor;
this.inDialog = false;
this.rangeToReselect = null;
this.reselect = true;
this.onclose = null;
}
wproPMenu.prototype.reselectRange = function () {
if (this.reselect && this.rangeToReselect && WPro.isIE) {
try{this.rangeToReselect.select();}catch(e){}
}
}
wproPMenu.prototype.getLocation = function (oWidth, oHeight, posx, posy) {
var scrollLeft = document.body.scrollLeft + document.documentElement.scrollLeft
var scrollTop = document.body.scrollTop + document.documentElement.scrollTop
var winDim = wproGetWindowInnerHeight();
var availHeight = winDim['height'];
var availWidth = winDim['width'];
if (oWidth>availWidth) {
oWidth = availWidth;
oHeight +=18;
}
if (oHeight>availHeight) {
oHeight = availHeight;
oWidth +=18;
}
availHeight += scrollTop;
availWidth += scrollLeft;
availWidth-= 25
availHeight-= 25
var leftPos; var rightPos;
if (posx + oWidth > availWidth) {
leftPos = availWidth - oWidth;
} else {
leftPos = posx;
}
if (posy + oHeight > availHeight) {
topPos = availHeight - oHeight;
} else {
topPos = posy;
}
return {width:oWidth,height:oHeight,left:leftPos,top:topPos}
}
wproPMenu.prototype.showDropDown = function (popframe, head, body, width, height, posx, posy) {
wp_current_obj = WPro.editors[this.editor];
WPro.currentEditor = WPro.editors[this.editor];
this._removeEvents();
var doc = popframe.contentWindow.document;
var wproWritten = false;
try{wproWritten = doc.wproWritten}catch(e){};
if (wproWritten) {
doc.body.innerHTML = body;
} else {
doc.open();
doc.write('<html><head>'+head+'</head><body unselectable="on">'+body+'</body></html>');
doc.close();
}
popframe.style.width = width+'px';
popframe.style.height = height+'px'
this.showPMenu (popframe, width, height, posx, posy, 'dropdown');
if (WPro.isIE) {
var keyhandler = 'keydown';
} else {
var keyhandler = 'keypress';
}
if (wproWritten) {
WPro.events.addEvent(doc,keyhandler,this.dropDownKeyHandler);
} else {
try{doc.wproWritten = true;}catch(e){};
}
this.popDoc = doc;
return {width:width, height:height, posx:posx, posy:posy};
}
wproPMenu.prototype.dropDownKeyHandler = function (evt) {
var keyCode = evt.keyCode;
var dir = 'down';
if (keyCode == 38) {
dir='up'
} else if (keyCode == 40) {
dir='down'
} else if (keyCode == 13) {
dir='enter'
} else {
return;
}
var doc
if (evt.target) {
doc = evt.target
} else {
doc = evt.srcElement
}
var divs = doc.getElementsByTagName('DIV');
var n=divs.length;
var found = false;
var newNode
for (var i=0; i < n; i++) {
if (divs[i].className=='wproOn') {
divs[i].className = 'wproOff';
newNode = divs[i];
var found = true;
if (dir=='enter') {
divs[i].onclick();
return;
}
if (dir=='down') {
if (divs[i].nextSibling!=null) {
if (divs[i].nextSibling.className=='wproHeading') {
newNode = divs[i].nextSibling.nextSibling
} else {
newNode = divs[i].nextSibling
}
} else {
for (var j=0; j < n; j++) {
if (divs[j].className=='wproOff') {
newNode = divs[j]
break;
}
}
}
}
if (dir=='up') {
if (divs[i].previousSibling!=null) {
if (divs[i].previousSibling.className=='wproHeading') {
newNode = divs[i].previousSibling.previousSibling
} else {
newNode = divs[i].previousSibling
}
} else {
for (var j=n-1; j > 0; j--) {
if (divs[j].className=='wproOff') {
newNode = divs[j]
break;
}
}
}
}
break;
}
}
if (!found) {
for (var i=0; i < n; i++) {
if (divs[i].className=='wproOff') {
newNode = divs[i];
break;
}
}
}
if (newNode) {
newNode.className = 'wproOn';
var pos = WPro.getElementPosition(newNode);
var PMenu = WPro.currentEditor.PMenu.PMenu
if (PMenu.contentWindow) {
PMenu.contentWindow.scrollTo(pos['left'],pos['top']-(newNode.offsetHeight));
}
}
WPro.preventDefault(evt);
}
wproPMenu.prototype.showPMenu = function (node, width, height, posx, posy, type) {
if (this.editor) wp_current_obj = WPro.editors[this.editor];
if (this.editor) WPro.currentEditor = WPro.editors[this.editor];
if (WPro) {
if (WPro.isIE&&this.editor) {
this.rangeToReselect = WPro.currentEditor.editDocument.selection.createRange();
}
}
this.closePMenu();
var loc = this.getLocation(width, height, posx, posy);
node.style.top = loc['top']+'px';
node.style.left = loc['left']+'px';
node.style.width = loc['width']+'px';
node.style.height = loc['height']+'px';
this.PMenu = node;
this.PMenu.style.visibility = 'visible';
if (type != 'dropdown') {
if (this.editor) {
if (WPro.isIE) {
var keyhandler = 'keydown';
} else {
var keyhandler = 'keydown';
}
WPro.events.addEvent(this.PMenu,keyhandler,this.menuKeyHandler);
setTimeout("try{if(typeof(WPro.editors['"+this.editor+"'].PMenu.PMenu.firstChild)!='undefined')WPro.editors['"+this.editor+"'].PMenu.PMenu.firstChild.focus();}catch(e){}", 1);
} else {
if (dialog.isIE) {
var keyhandler = 'keydown';
} else {
var keyhandler = 'keydown';
}
dialog.events.addEvent(this.PMenu,keyhandler,this.menuKeyHandler);
setTimeout("try{if(typeof(dialog.PMenu.PMenu.firstChild)!='undefined')dialog.PMenu.PMenu.firstChild.focus();}catch(e){}", 1);
}
}
}
wproPMenu.prototype.menuKeyHandler = function (evt) {
var keyCode = evt.keyCode;
var dir = 'down';
if (keyCode == 38) {
dir='up'
} else if (keyCode == 40) {
dir='down'
} else {
return;
}
var doc
if (evt.target) {
doc = evt.target.parentNode
} else {
doc = evt.srcElement.parentNode
}
var divs = doc.getElementsByTagName('A');
var n=divs.length;
var found = false;
var newNode
for (var i=0; i < n; i++) {
if (divs[i].className == 'wproOver' || divs[i].className == 'wproOver wproLatched') {
if (divs[i].className == 'wproOver wproLatched') {
divs[i].className = 'wproLatched';
} else {
divs[i].className = '';
}
newNode = divs[i];
var found = true;
if (dir=='down') {
if (i+1 < n) {
newNode = divs[i+1]
} else {
newNode = divs[1]
}
}
if (dir=='up') {
if (i-1 >= 1) {
newNode = divs[i-1]
} else {
newNode = divs[n-1]
}
}
break;
}
}
if (!found) {
newNode = divs[1];
}
if (newNode) {
newNode.focus();
}
WPro.preventDefault(evt);
}
wproPMenu.prototype.closePMenu = function () {
if (this.PMenu) {
if (this.PMenu != null) {
this.PMenu.style.display = 'none';
this.PMenu = null;
if (this.onclose!=null) {
this.onclose();
}
}
}
this._removeEvents();
}
wproPMenu.prototype._removeEvents = function() {
}