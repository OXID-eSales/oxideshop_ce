
var CURRENT_EMOTICON = null;
function initEmoticons () {
var d = document.getElementById('emoticons');
var b = d.getElementsByTagName('A');
var n = b.length;
for (var i=0; i<n; i++) {
addActions(b[i]);
}
dialog.hideLoadMessage();
}
function addActions(node) {
node.onclick = selectEmoticon;
node.onmouseover = function () {
this.className = 'selected';
}
node.onfocus = node.onmouseover
node.onmouseout = function () {
if (CURRENT_EMOTICON) {
if (CURRENT_EMOTICON != this) {
this.className = 'el';
}
} else {
this.className = 'el';
}
}
node.onblur = node.onmouseout
}
function selectEmoticon() {
if (CURRENT_EMOTICON) {
CURRENT_EMOTICON.className = 'el';
}
CURRENT_EMOTICON = this;
document.dialogForm.ok.disabled=false;
document.dialogForm.ok.focus();
}
function formAction () {
if (CURRENT_EMOTICON) {
var i = CURRENT_EMOTICON.firstChild;
var attrs = {width:i.width, height:i.height, border:'0', alt:''};
currentEditor.insertImage(i.src, attrs);
}
dialog.close();
return false;
}