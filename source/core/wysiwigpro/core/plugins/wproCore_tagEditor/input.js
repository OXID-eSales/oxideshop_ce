
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

var TYPE_CHANGED = false;
function inputTypeChange (val) {
var f = document.dialogForm
switch (val) {
case '' :
case 'text' :
case 'password' :
f.value.parentNode.style.display = 'block'
f.value.parentNode.previousSibling.style.display = 'block'
f.src.parentNode.style.display = 'none'
f.src.parentNode.previousSibling.style.display = 'none'
f.accept.parentNode.style.display = 'none'
f.accept.parentNode.previousSibling.style.display = 'none'
f.size.parentNode.style.display = 'block'
f.size.parentNode.previousSibling.style.display = 'block'
if (f.align) {
f.align.parentNode.style.display = 'none'
f.align.parentNode.previousSibling.style.display = 'none'
}
f.alt.parentNode.style.display = 'none'
f.alt.parentNode.previousSibling.style.display = 'none'
f.checked.parentNode.style.display = 'none'
f.checked.parentNode.previousSibling.style.display = 'none'
f.maxLength.parentNode.style.display = 'block'
f.maxLength.parentNode.previousSibling.style.display = 'block'
f.readOnly.parentNode.style.display = 'block'
f.readOnly.parentNode.previousSibling.style.display = 'block'
f.disabled.parentNode.style.display = 'block'
f.disabled.parentNode.previousSibling.style.display = 'block'
f.useMap.parentNode.style.display = 'none'
f.useMap.parentNode.previousSibling.style.display = 'none'
break;
case 'radio' :
case 'checkbox' :
f.value.parentNode.style.display = 'block'
f.value.parentNode.previousSibling.style.display = 'block'
f.src.parentNode.style.display = 'none'
f.src.parentNode.previousSibling.style.display = 'none'
f.accept.parentNode.style.display = 'none'
f.accept.parentNode.previousSibling.style.display = 'none'
f.size.parentNode.style.display = 'none'
f.size.parentNode.previousSibling.style.display = 'none'
if (f.align) {
f.align.parentNode.style.display = 'none'
f.align.parentNode.previousSibling.style.display = 'none'
}
f.alt.parentNode.style.display = 'none'
f.alt.parentNode.previousSibling.style.display = 'none'
f.checked.parentNode.style.display = 'block'
f.checked.parentNode.previousSibling.style.display = 'block'
f.maxLength.parentNode.style.display = 'none'
f.maxLength.parentNode.previousSibling.style.display = 'none'
f.readOnly.parentNode.style.display = 'none'
f.readOnly.parentNode.previousSibling.style.display = 'none'
f.disabled.parentNode.style.display = 'block'
f.disabled.parentNode.previousSibling.style.display = 'block'
f.useMap.parentNode.style.display = 'none'
f.useMap.parentNode.previousSibling.style.display = 'none'
break;
case 'image' :
f.value.parentNode.style.display = 'block'
f.value.parentNode.previousSibling.style.display = 'block'
f.src.parentNode.style.display = 'block'
f.src.parentNode.previousSibling.style.display = 'block'
f.accept.parentNode.style.display = 'none'
f.accept.parentNode.previousSibling.style.display = 'none'
f.size.parentNode.style.display = 'none'
f.size.parentNode.previousSibling.style.display = 'none'
if (f.align) {
f.align.parentNode.style.display = 'block'
f.align.parentNode.previousSibling.style.display = 'block'
}
f.alt.parentNode.style.display = 'block'
f.alt.parentNode.previousSibling.style.display = 'block'
f.checked.parentNode.style.display = 'none'
f.checked.parentNode.previousSibling.style.display = 'none'
f.maxLength.parentNode.style.display = 'none'
f.maxLength.parentNode.previousSibling.style.display = 'none'
f.readOnly.parentNode.style.display = 'none'
f.readOnly.parentNode.previousSibling.style.display = 'none'
f.disabled.parentNode.style.display = 'block'
f.disabled.parentNode.previousSibling.style.display = 'block'
f.useMap.parentNode.style.display = 'block'
f.useMap.parentNode.previousSibling.style.display = 'block'
break;
case 'file' :
f.value.parentNode.style.display = 'none'
f.value.parentNode.previousSibling.style.display = 'none'
f.src.parentNode.style.display = 'none'
f.src.parentNode.previousSibling.style.display = 'none'
f.accept.parentNode.style.display = 'none'
f.accept.parentNode.previousSibling.style.display = 'none'
f.size.parentNode.style.display = 'block'
f.size.parentNode.previousSibling.style.display = 'block'
if (f.align) {
f.align.parentNode.style.display = 'none'
f.align.parentNode.previousSibling.style.display = 'none'
}
f.alt.parentNode.style.display = 'none'
f.alt.parentNode.previousSibling.style.display = 'none'
f.checked.parentNode.style.display = 'none'
f.checked.parentNode.previousSibling.style.display = 'none'
f.maxLength.parentNode.style.display = 'none'
f.maxLength.parentNode.previousSibling.style.display = 'none'
f.readOnly.parentNode.style.display = 'none'
f.readOnly.parentNode.previousSibling.style.display = 'none'
f.disabled.parentNode.style.display = 'block'
f.disabled.parentNode.previousSibling.style.display = 'block'
f.useMap.parentNode.style.display = 'none'
f.useMap.parentNode.previousSibling.style.display = 'none'
break;
case 'hidden' :
f.value.parentNode.style.display = 'block'
f.value.parentNode.previousSibling.style.display = 'block'
f.src.parentNode.style.display = 'none'
f.src.parentNode.previousSibling.style.display = 'none'
f.accept.parentNode.style.display = 'none'
f.accept.parentNode.previousSibling.style.display = 'none'
f.size.parentNode.style.display = 'none'
f.size.parentNode.previousSibling.style.display = 'none'
if (f.align) {
f.align.parentNode.style.display = 'none'
f.align.parentNode.previousSibling.style.display = 'none'
}
f.alt.parentNode.style.display = 'none'
f.alt.parentNode.previousSibling.style.display = 'none'
f.checked.parentNode.style.display = 'none'
f.checked.parentNode.previousSibling.style.display = 'none'
f.maxLength.parentNode.style.display = 'none'
f.maxLength.parentNode.previousSibling.style.display = 'none'
f.readOnly.parentNode.style.display = 'none'
f.readOnly.parentNode.previousSibling.style.display = 'none'
f.disabled.parentNode.style.display = 'none'
f.disabled.parentNode.previousSibling.style.display = 'none'
f.useMap.parentNode.style.display = 'none'
f.useMap.parentNode.previousSibling.style.display = 'none'
break;
case 'button' :
case 'submit' :
case 'reset' :
f.value.parentNode.style.display = 'block'
f.value.parentNode.previousSibling.style.display = 'block'
f.src.parentNode.style.display = 'none'
f.src.parentNode.previousSibling.style.display = 'none'
f.accept.parentNode.style.display = 'none'
f.accept.parentNode.previousSibling.style.display = 'none'
f.size.parentNode.style.display = 'none'
f.size.parentNode.previousSibling.style.display = 'none'
if (f.align) {
f.align.parentNode.style.display = 'none'
f.align.parentNode.previousSibling.style.display = 'none'
}
f.alt.parentNode.style.display = 'none'
f.alt.parentNode.previousSibling.style.display = 'none'
f.checked.parentNode.style.display = 'none'
f.checked.parentNode.previousSibling.style.display = 'none'
f.maxLength.parentNode.style.display = 'none'
f.maxLength.parentNode.previousSibling.style.display = 'none'
f.readOnly.parentNode.style.display = 'none'
f.readOnly.parentNode.previousSibling.style.display = 'none'
f.disabled.parentNode.style.display = 'block'
f.disabled.parentNode.previousSibling.style.display = 'block'
f.useMap.parentNode.style.display = 'none'
f.useMap.parentNode.previousSibling.style.display = 'none'
break;
}
}