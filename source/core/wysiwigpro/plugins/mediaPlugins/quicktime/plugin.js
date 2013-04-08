
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproFilePlugin_quicktime () {
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
form.elements[prefix+'height'].value = 0;
form.elements[prefix+'heightUnits'].value = '';
}
this.updateHeight(prefix);
}
this._getOptions = function (prefix, o) {
var form = document.dialogForm;
if (!o) o = {}
if (!o['object']) o['object'] = {};
if (!o['embed']) o['embed'] = {};
if (!o['param']) o['param'] = {};
if (o['param']['href']) delete o['param']['href'];
if (form.elements[prefix+'width']) {
o['embed']['width'] = form.elements[prefix+'width'].value+form.elements[prefix+'widthUnits'].value;
o['object']['width'] = form.elements[prefix+'width'].value+form.elements[prefix+'widthUnits'].value;
}
if (form.elements[prefix+'height']) {
o['object']['height'] = form.elements[prefix+'height'].value+form.elements[prefix+'heightUnits'].value;
o['embed']['height'] = form.elements[prefix+'height'].value+form.elements[prefix+'heightUnits'].value;
}
if (form.elements[prefix+'autoplay']) {
o['param']['autoplay'] = form.elements[prefix+'autoplay'].checked?'true':'false';
o['embed']['autoplay'] = form.elements[prefix+'autoplay'].checked?'true':'false';
}
if (form.elements[prefix+'loop']) {
o['param']['loop'] = form.elements[prefix+'loop'].checked?'true':'false';
o['embed']['loop'] = form.elements[prefix+'loop'].checked?'true':'false';
}
if (form.elements[prefix+'controller']) {
o['param']['controller'] = form.elements[prefix+'controller'].checked?'true':'false';
o['embed']['controller'] = form.elements[prefix+'controller'].checked?'true':'false';
}
if (form.elements[prefix+'posterMovie']) {
if (form.elements[prefix+'posterMovie'].value!='') {
o['param']['href'] = form.elements['URL'].value;
o['embed']['href'] = form.elements['URL'].value;
o['param']['src'] = form.elements[prefix+'posterMovie'].value;
o['embed']['src'] = form.elements[prefix+'posterMovie'].value;
}
}
return o;
}
this.insertLocal = function(prefix, data) {
if (!document.dialogForm.URL.value) return;
var form = document.dialogForm;
if (!data) data = {};
var o = this._getOptions(prefix, data);
o['object']['classid']="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" ;
o['object']['codebase']="http://www.apple.com/qtactivex/qtplugin.cab";
if (!o['param']['src']) {
o['param']['src'] =form.URL.value;
o['embed']['src'] = form.URL.value;
}
o['embed']['pluginspage'] = "http://www.apple.com/quicktime/download/";
o['embed']['type'] = "video/quicktime";
var s = '';
if (form.elements[prefix+'style']) {
s = form.elements[prefix+'style'].value
}
FB.insertMedia('quicktime', o, s);
}
this.insertRemote = function (prefix) {
var data
if (FB.propertiesPlugin == 'quicktime' && FB.mediaProperties) {
data = FB.mediaProperties;
}
this.insertLocal(prefix, data);
}
this.canPopulate = function () {
var arr = FB.getMediaProperties();
if (arr['object']) {
if (arr['object']['classid']) {
if (arr['object']['classid'].toUpperCase() == "CLSID:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B") {
return true;
}
}
}
return false
}
this.updateHeight = function (prefix, show) {
var form = document.dialogForm;
if (/^[0-9]+$/.test(form.elements[prefix+'height'].value)) {
if (show||form.elements[prefix+'controller'].checked) {
var v = parseInt(form.elements[prefix+'height'].value) + parseInt(form.elements[prefix+'controllerHeight'].value)
form.elements[prefix+'height'].value = v;
} else {
var v = parseInt(form.elements[prefix+'height'].value) - parseInt(form.elements[prefix+'controllerHeight'].value)
form.elements[prefix+'height'].value = v;
}
}
}
this.populateProperties = function (prefix) {
var form = document.dialogForm;
var o = FB.getMediaProperties();
if (form.elements[prefix+'width']&&o['object']&&o['object']['width']) {
form.elements[prefix+'width'].value = String(o['object']['width']).replace(/[^0-9]/g, '');
if (String(o['object']['width']).match('%')) {
form.elements[prefix+'widthUnits'].value = '%';
} else {
form.elements[prefix+'widthUnits'].value = '';
}
}
if (form.elements[prefix+'height']&&o['object']&&o['object']['height']) {
form.elements[prefix+'height'].value = String(o['object']['height']).replace(/[^0-9]/g, '');
if (String(o['object']['height']).match('%')) {
form.elements[prefix+'heightUnits'].value = '%';
} else {
form.elements[prefix+'heightUnits'].value = '';
}
}
if (form.elements[prefix+'scale']&&o['param']&&o['param']['scale']) {
form.elements[prefix+'scale'].value = o['param']['scale'];
}
if (form.elements[prefix+'autoplay']&&o['param']&&o['param']['autoplay']) {
form.elements[prefix+'autoplay'].checked = o['param']['autoplay']=='true'?true:false;
}
if (form.elements[prefix+'loop']&&o['param']&&o['param']['loop']) {
form.elements[prefix+'loop'].checked = o['param']['loop']=='true'?true:false;
}
if (form.elements[prefix+'controller']&&o['param']&&o['param']['controller']) {
form.elements[prefix+'controller'].checked = o['param']['controller']=='true'?true:false;
}
if (o['param']&&o['param']['src']) {
if (o['param']['href']) {
form.URL.value = dialog.urlFormatting(o['param']['href']);
form.elements[prefix+'posterMovie'].value = o['param']['src'];
} else {
form.URL.value = dialog.urlFormatting(o['param']['src']);
}
}
}
}