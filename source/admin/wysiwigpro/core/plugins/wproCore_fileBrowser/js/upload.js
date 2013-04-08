
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

var RESIZE_SELECT = null;
function initUpload () {
dialog.hideLoadMessage();
}
function amountChanged(value) {
if (!value) value = 1;
value = parseInt(value);
if (value == 1) {
document.getElementById('combinedMessage').style.display = 'none';
} else {
document.getElementById('combinedMessage').style.display = 'block';
}
var f = document.getElementById('files');
var current = f.getElementsByTagName('INPUT');
var n = current.length;
if (n < value) {
while (n < value) {
var i = f.firstChild.cloneNode(false);
i.value = '';
f.appendChild(i);
n++;
}
} else {
while (n > value) {
f.removeChild(f.lastChild);
n--;
}
}
}
function sizeChanged(value) {
if (value!='custom') {
document.getElementById('hiddenResize').style.display = 'none';
var arr = value.split('x');
document.getElementById('maxWidth').value=arr[0];
document.getElementById('maxHeight').value=arr[1];
} else {
document.getElementById('hiddenResize').style.display = 'inline';
}
}
function showUploadMessage() {
var box = document.getElementById("uploadMessage");
var width = 263;
var height = 80;
var left = 0;
var top = 0;
var winDim = wproGetWindowInnerHeight();
var availHeight = winDim['height'];
var availWidth = winDim['width'];
if (width < availWidth) {
left = (availWidth/2)-(width/2);
}
if (height < availHeight) {
top = (availHeight/2)-(height/2);
}
box.style.width = width+'px';
box.style.height = height+'px';
box.style.top = top+'px';
box.style.left = left+'px';
box.style.display = 'block';
}
function doUpload() {
if (formAction ()) {
document.uploadForm.submit();
}
}
function formAction () {
var form = document.uploadForm;
var mw = document.getElementById('maxWidth');
var mh = document.getElementById('maxHeight');
if (mw && mh) {
var maxWidth = mw.value;
var maxHeight = mh.value;
if (isNaN(maxWidth)&&maxWidth.length>0) {
dialog.alertWrongFormat();
dialog.focus();
mw.value='';
mw.focus();
return false;
}
if (isNaN(maxHeight)&&maxHeight.length>0) {
dialog.alertWrongFormat();
dialog.focus();
mh.value='';
mh.focus();
return false;
}
}
var c = document.getElementById('files')
var f = c.getElementsByTagName('INPUT');
var n = f.length;
var ok = false;
for (var i=0; i<n; i++) {
if (f[i].type == 'file') {
if (f[i].value.length>0) {
ok = true;
break;
}
}
}
if (ok) {
var a = String(document.location).replace(/\&(maxWidth|maxHeight|overwrite|uploadID)=[^&#]*/, '');
a = a + '&uploadID='+document.getElementById('uploadID').value;
if (mw && mh) {
a = a + '&maxWidth='+maxWidth+'&maxHeight='+maxHeight;
}
if (document.getElementById('overwrite') && document.getElementById('overwrite').checked == true) {
a = a + '&overwrite=true';
}
form.action = a;
showUploadMessage();
return true;
} else {
f[0].focus();
return false;
}
}