
var CURRENT_SNIPPET_ID = null;
function initSnippets () {
dialog.writeFrame(document.getElementById('snippetFrame'), '<p style="font-family:verdana;font-size:11px">'+pleaseSelectStr+'</p>');
dialog.focus();
dialog.hideLoadMessage();
}
function getSnippetContent (id) {
dialog.showLoadMessage();
var n
if (n = document.dialogForm.elements[id]) {
code = n.value;
showSnippet(code, id);
} else {
ajax_getSnippetContent(id);
}
}
function showSnippet(code, id) {
if (!document.dialogForm.elements[id]) {
var cache = document.getElementById('snippetCache');
var i = document.createElement('INPUT');
i.setAttribute('name', id);
i.id = id;
i.setAttribute('type', 'hidden');
i.value = code;
cache.appendChild(i);
}
CURRENT_SNIPPET_ID = id;
dialog.writeFrame(document.getElementById('snippetFrame'), currentEditor.triggerHTMLFilter('design',code));
document.dialogForm.ok.disabled=false;
dialog.hideLoadMessage();
}
function formAction () {
var code = '';
if (CURRENT_SNIPPET_ID != null) {
var n
if (n = document.dialogForm.elements[CURRENT_SNIPPET_ID]) {
code = n.value;
currentEditor.insertAtSelection(code);
}
}
dialog.focus();
document.dialogForm.close.focus();
return false;
}