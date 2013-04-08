
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproUIColorPicker () {
this.onChange = null;
this.hexColor = /^#[0-9abcdef]+$/i;
this.rgbColor = /^rgb\([0-9, ]+\)$/i;
}
wproUIColorPicker.prototype.init = function (UID) {
this.id = UID;
this.buttonNode = document.getElementById('UIColorPicker_'+UID);
this.inputNode = this.buttonNode.previousSibling;
this.inputNode.isColorPicker = true;
eval ('this.inputNode.setColor = function (color) {'+UID+'.setColor(color)}')
this.colorDisplay = this.buttonNode.getElementsByTagName('DIV')[0];
}
wproUIColorPicker.prototype.setColor = function (color, doOnChange) {
if (doOnChange==undefined) {
doOnChange = true;
}
if (!this.hexColor.test(color)) {
if (this.rgbColor.test(color)) {
color = '#'+eval('this.rgbToHex'+color.replace(/rgb/gi,''));
} else {
color = '';
}
}
try {
this.colorDisplay.style.backgroundColor = color;
}catch(e){
this.colorDisplay.style.backgroundColor = '';
color='';
}
this.inputNode.value=color;
if (this.onChange && doOnChange) {
this.onChange();
}
if (this.inputNode.type=='text'&&doOnChange) {
}
}
wproUIColorPicker.prototype.onClick = function () {
if (WPro) { WPro.currentColorPicker = this; }
var color = this.inputNode.value;
if (!this.hexColor.test(color)) {
if (this.rgbColor.test(color)) {
color = '#'+eval('this.rgbToHex'+color.replace(/rgb/gi,''));
} else {
color = '';
}
}
dialog.openDialogPlugin('wproCore_colorPicker&selectedColor='+escape(color), 324, 400);
return false;
}
wproUIColorPicker.prototype.rgbToHex=function(R,G,B) {return this.toHex(R)+this.toHex(G)+this.toHex(B)}
wproUIColorPicker.prototype.toHex=function(N) {
if (N==null) return "00";
N=parseInt(N); if (N==0 || isNaN(N)) return "00";
N=Math.max(0,N); N=Math.min(N,255); N=Math.round(N);
return "0123456789abcdef".charAt((N-N%16)/16)
+ "0123456789abcdef".charAt(N%16);
}