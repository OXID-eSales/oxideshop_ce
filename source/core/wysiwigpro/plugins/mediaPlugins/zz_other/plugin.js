
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproFilePlugin_zz_other () {
this.onArriveRemote = function (prefix) {
document.getElementById('bottomOptions').style.display = 'none';
}
this.onLeaveRemote = function (prefix) {
document.getElementById('bottomOptions').style.display = '';
}
this.populateLocalOptions = function (data, prefix) {
var form = document.dialogForm;
if (data['width']) {
form.elements[prefix+'width'].value = data['width'];
form.elements[prefix+'widthUnits'].value = '';
} else {
form.elements[prefix+'width'].value = 320;
form.elements[prefix+'widthUnits'].value = '';
}
if (data['height']) {
form.elements[prefix+'height'].value = data['height'];
form.elements[prefix+'heightUnits'].value = '';
} else {
form.elements[prefix+'height'].value = 45;
form.elements[prefix+'heightUnits'].value = '';
}
document.getElementById(prefix+'objectparamholder').innerHTML = '';
document.getElementById(prefix+'embedparamholder').innerHTML = '';
var ef = eval(prefix+'addParam');
form.elements[prefix+'useObjectTag'].checked=true;
document.getElementById(prefix+'objectTag').style.display = '';
ef(prefix+'objectParams', prefix+'objectparamholder', 'src', form.URL.value)
form.elements[prefix+'useEmbedTag'].checked=true;
document.getElementById(prefix+'embedTag').style.display = '';
form.elements[prefix+'embedsrc'].value = form.URL.value;
}
this._getOptions = function (prefix, o) {
var form = document.dialogForm;
var forms = new wproForms();
if (!o) o = {};
if (form.elements[prefix+'useObjectTag'].checked) {
if (!o['object']) o['object'] = {};
o['param'] = {};
if (o['object']['width']) delete o['object']['width'];
if (form.elements[prefix+'width'].value) {
o['object']['width'] = form.elements[prefix+'width'].value+form.elements[prefix+'widthUnits'].value;
}
if (o['object']['height']) delete o['object']['height'];
if (form.elements[prefix+'height'].value) {
o['object']['height'] = form.elements[prefix+'height'].value+form.elements[prefix+'heightUnits'].value;
}
if (o['object']['classid']) delete o['object']['classid'];
if (form.elements[prefix+'classid'].value) {
o['object']['classid'] = form.elements[prefix+'classid'].value;
}
if (o['object']['codebase']) delete o['object']['codebase'];
if (form.elements[prefix+'codebase'].value) {
o['object']['codebase'] = form.elements[prefix+'codebase'].value;
}
if (o['object']['data']) delete o['object']['data'];
if (form.elements[prefix+'data'].value) {
o['object']['data'] = form.elements[prefix+'data'].value;
}
if (o['object']['codetype']) delete o['object']['codetype'];
if (form.elements[prefix+'codetype'].value) {
o['object']['codetype'] = form.elements[prefix+'codetype'].value;
}
if (o['object']['type']) delete o['object']['type'];
if (form.elements[prefix+'type'].value) {
o['object']['type'] = form.elements[prefix+'type'].value;
}
if (o['object']['archive']) delete o['object']['archive'];
if (form.elements[prefix+'archive'].value) {
o['object']['archive'] = form.elements[prefix+'archive'].value;
}
if (o['object']['standby']) delete o['object']['standby'];
if (form.elements[prefix+'standby'].value) {
o['object']['standby'] = form.elements[prefix+'standby'].value;
}
if (form.elements[prefix+'objectParams_name']) {
var names = forms.getElementValues(form.elements[prefix+'objectParams_name']);
var values = forms.getElementValues(form.elements[prefix+'objectParams_value']);
var n = names.length;
for (var i=0; i<n; i++) {
o['param'][names[i]] = values[i];
}
}
} else {
if (o['object']) delete o['object'];
}
if (form.elements[prefix+'useEmbedTag'].checked) {
o['embed'] = {};
if (o['embed']['width']) delete o['embed']['width'];
if (form.elements[prefix+'width'].value) {
o['embed']['width'] = form.elements[prefix+'width'].value+form.elements[prefix+'widthUnits'].value;
}
if (o['embed']['height']) delete o['embed']['height'];
if (form.elements[prefix+'height'].value) {
o['embed']['height'] = form.elements[prefix+'height'].value+form.elements[prefix+'heightUnits'].value;
}
if (o['embed']['src']) delete o['embed']['src'];
if (form.elements[prefix+'embedsrc'].value) {
o['embed']['src'] = form.elements[prefix+'embedsrc'].value;
}
if (o['embed']['pluginspage']) delete o['embed']['pluginspage'];
if (form.elements[prefix+'embedpluginspage'].value) {
o['embed']['pluginspage'] = form.elements[prefix+'embedpluginspage'].value;
}
if (o['embed']['type']) delete o['embed']['type'];
if (form.elements[prefix+'embedtype'].value) {
o['embed']['type'] = form.elements[prefix+'embedtype'].value;
}
if (form.elements[prefix+'embedParams_name']) {
var names = forms.getElementValues(form.elements[prefix+'embedParams_name']);
var values = forms.getElementValues(form.elements[prefix+'embedParams_value']);
var n = names.length;
for (var i=0; i<n; i++) {
o['embed'][names[i]] = values[i];
}
}
} else {
if (o['embed']) delete o['embed'];
}
o['content'] = form.elements[prefix+'alternateContent'].value;
return o;
}
this.insertLocal = function(prefix, data) {
var form = document.dialogForm;
if (!data) data = {};
var o = this._getOptions(prefix, data);
var s = '';
if (form.elements[prefix+'style']) {
s = form.elements[prefix+'style'].value
}
FB.insertMedia('unknown', o, s);
}
this.insertRemote = function (prefix) {
var data
if (FB.mediaProperties) {
data = FB.mediaProperties;
}
this.insertLocal(prefix, data);
}
this.canPopulate = function () {
return false
}
this.populateProperties = function (prefix) {
var form = document.dialogForm;
var o = FB.getMediaProperties();
var width=''
var height='';
if (o['object']) {
if (o['object']['width']) {
width = o['object']['width'];
}
if (o['object']['height']) {
height = o['object']['height'];
}
} else if (o['embed']) {
if (o['embed']['width']) {
width = o['embed']['width'];
}
if (o['embed']['height']) {
height = o['embed']['height'];
}
}
form.elements[prefix+'width'].value = String(width).replace(/[^0-9]/g, '');
if (String(width).match('%')) {
form.elements[prefix+'widthUnits'].value = '%';
} else {
form.elements[prefix+'widthUnits'].value = '';
}
form.elements[prefix+'height'].value = String(height).replace(/[^0-9]/g, '');
if (String(height).match('%')) {
form.elements[prefix+'heightUnits'].value = '%';
} else {
form.elements[prefix+'heightUnits'].value = '';
}
var ef = eval(prefix+'addParam');
if (o['object']) {
for (var x in o['object']) {
if (form.elements[prefix+x]) {
form.elements[prefix+x].value = o['object'][x];
}
}
if (o['param']) {
for (var x in o['param']) {
ef(prefix+'objectParams', prefix+'objectparamholder', x, o['param'][x])
}
}
} else {
form.elements[prefix+'useObjectTag'].checked = false;
document.getElementById(prefix+'objectTag').style.display = 'none';
}
if (o['embed']) {
for (var x in o['embed']) {
if (form.elements[prefix+'embed'+x]) {
form.elements[prefix+'embed'+x].value = o['embed'][x];
} else if (x!='width'&&x!='height') {
ef(prefix+'embedParams', prefix+'embedparamholder', x, o['embed'][x])
}
}
form.elements[prefix+'useEmbedTag'].checked = true;
document.getElementById(prefix+'embedTag').style.display = '';
}
if(o['content']) form.elements[prefix+'alternateContent'].value = o['content'];
}
}