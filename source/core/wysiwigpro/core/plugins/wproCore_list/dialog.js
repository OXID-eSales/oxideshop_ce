
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function initList () {
dialog.reselectRange();
var form = document.dialogForm;
if (form.bulleted) {
var i = form.getElementsByTagName('SELECT')[0];
var e;
var range = currentEditor.selAPI.getRange();
if (e = range.getContainerByTagName('UL')) {
if (!e.style.listStyleType) {
i.selectedIndex = 0;
eval(i.id+'.manualSwapTab()');
}
} else if (e = range.getContainerByTagName('OL')) {
if (!e.style.listStyleType) {
i.selectedIndex = 1;
eval(i.id+'.manualSwapTab()');
}
}
if (e) {
var bul = form.bulleted;
var num = form.numbered;
switch (e.style.listStyleType) {
case 'disc':
case 'circle':
case 'square':
bul.setValue(e.style.listStyleType);
i.selectedIndex = 0;
eval(i.id+'.manualSwapTab()');
break;
case 'decimal':
case 'lower-roman':
case 'upper-roman':
case 'lower-alpha':
case 'upper-alpha':
num.setValue(e.style.listStyleType);
i.selectedIndex = 1;
eval(i.id+'.manualSwapTab()');
break;
}
if (form.start) {
var s
if (s = e.getAttribute('start')) {
form.start.value = s;
}
}
}
}
dialog.selectCurrentStyle(form.elements['style']);
dialog.focus();
dialog.hideLoadMessage();
}
function checkStartField() {
var form = document.dialogForm;
var v = form.start.value;
if (isNaN(v)) {
dialog.alertWrongFormat();
form.start.value = '';
dialog.focus();
form.start.focus();
return false;
} else {
return true;
}
}
function formAction () {
var form = document.dialogForm;
var style = form.elements['style'].value;
if (form.bulleted) {
var i = form.getElementsByTagName('SELECT')[0].selectedIndex;
if (i == 0) {
var tagName = 'UL';
var type = form.bulleted.value
} else {
var tagName = 'OL';
var type = form.numbered.value
}
var style1 = tagName + ' style="list-style-type:'+type+'"';
if (form.start && tagName == 'OL') {
var v = form.start.value;
if (v > 0) {
style1 += ' start="'+v+'"';
}
}
}
currentEditor.applyStyle(style1);
if (style!='') {
currentEditor.applyStyle(style);
}
dialog.close();
return false;
}