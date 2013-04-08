
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function initFileBrowser() {
FB.init();
}
function formAction() {
var form = document.dialogForm;
var pane = '';
var panes = ['site', 'email', 'web', 'doc', 'fileBrowser']
var n = panes.length;
for (var i=0; i<n; i++) {
var f
if (f=document.getElementById(panes[i])) {
if (f.style.display=='block') {
pane = panes[i]
break;
}
}
}
if (FB.chooser) {
if (parentWindow.WPRO_FB_RETURN_FUNCTION) {
if (parentWindow.WPRO_FB_RETURN_FUNCTION[dialog.sid]) {
var url = '';
if (pane=='email') {
if (form.emailAddress.value!='') {
url = 'mailto:'+form.emailAddress.value;
if (form.emailSubject.value!='') {
url+='?subject='+escape(form.emailSubject.value)
}
if (form.emailMessage.value!='') {
url+=(form.emailSubject.value!=''?'&':'?')+'body='+escape(form.emailMessage.value)
}
}
} else {
url = form.URL.value;
var bad = /^www\./i
if (bad.test(url)) {
url = 'http://'+url;
}
var bad = /^[^:@]*@[a-z0-9\._\-]*/i
if (bad.test(url)) {
url = 'mailto:'+url;
}
}
parentWindow.WPRO_FB_RETURN_FUNCTION[dialog.sid](url);
}
}
} else {
if (FB.action=='link'||FB.action=='document') {
FB.linkAction(pane);
} else {
switch (pane) {
case 'fileBrowser' :
if (FB.awaitingFileDetails) {
dialog.showLoadMessage();
FB.doActionOnFileDetails = true;
return false;
}
if (FB.currentLocalPlugin&&FB.currentLocalPrefix&&form.URL.value) {
FB.embedPlugins[FB.currentLocalPlugin].insertLocal(FB.currentLocalPrefix);
}
break;
case 'web' :
if (FB.currentRemotePlugin&&FB.currentRemotePrefix) {
FB.embedPlugins[FB.currentRemotePlugin].insertRemote(FB.currentRemotePrefix);
}
break;
}
}
}
dialog.close();
return false;
}
function parentOutlookSelect(id, c) {
var win = dialog.getFrameWindow(document.getElementById('outlookFrame'));
if (!win.outlookSelect) {
setTimeout('parentOutlookSelect("'+dialog.addSlashes(id)+'",'+c+')', 500);
return
}
win.outlookSelect(id, c)
}
var _initiated = false;
var paneValues = [];
paneValues['site'] = [];
paneValues['site']['URL'] = '';
paneValues['site']['linkText'] = '';
paneValues['email'] = [];
paneValues['email']['URL'] = '';
paneValues['email']['linkText'] = '';
paneValues['web'] = [];
paneValues['web']['URL'] = '';
paneValues['web']['linkText'] = '';
paneValues['doc'] = [];
paneValues['doc']['URL'] = '';
paneValues['doc']['linkText'] = '';
paneValues['fileBrowser'] = [];
paneValues['fileBrowser']['URL'] = '';
paneValues['fileBrowser']['linkText'] = '';
function switchPane (id) {
var panes = ['site', 'email', 'web', 'doc', 'fileBrowser']
var pane = '';
var sPane = '';
var form  = document.dialogForm;
var n = panes.length;
for (var i=0; i<n; i++) {
var f
if (f=document.getElementById(panes[i])) {
if (f.style.display=='block') {
pane = panes[i];
sPane = panes[i];
break;
}
}
}
switch (pane) {
case 'fileBrowser' :
if (form.linkText) paneValues['fileBrowser']['linkText'] = form.linkText.value;
paneValues['fileBrowser']['URL'] = form.URL.value;
form.URL.value = '';
break;
case 'email' :
if (form.linkText) paneValues['email']['linkText'] = form.linkText.value;
paneValues['email']['URL'] = form.URL.value;
break;
case 'web' :
if (form.linkText) paneValues['web']['linkText'] = form.linkText.value;
paneValues['web']['URL'] = form.URL.value;
form.URL.value = '';
if (FB.currentRemotePlugin&&FB.currentRemotePrefix) {
if (FB.embedPlugins[FB.currentRemotePlugin].onLeaveRemote) {
FB.embedPlugins[FB.currentRemotePlugin].onLeaveRemote(FB.currentRemotePrefix);
}
}
break;
case 'doc' :
if (form.linkText) paneValues['doc']['linkText'] = form.linkText.value;
paneValues['doc']['URL'] = form.URL.value;
break;
case 'site' :
if (form.linkText) paneValues['site']['linkText'] = form.linkText.value;
paneValues['site']['URL'] = form.URL.value;
break;
}
for (var i=0; i<n; i++) {
var f
if (f=document.getElementById(panes[i])) {
if (panes[i] == id) {
pane = panes[i]
f.style.display="block";
} else {
f.style.display='none';
}
}
}
document.getElementById('bottomOptions').style.display = '';
document.dialogForm.URL.onchange = function() {  };
document.getElementById('previewButton').onclick = function() { document.dialogForm.URL.onchange(); };
document.getElementById('previewButton').style.display = 'none';
switch (String(pane)) {
case 'fileBrowser' :
if (form.linkText&&sPane) form.linkText.value = paneValues['fileBrowser']['linkText'];
if (sPane) form.URL.value = paneValues['fileBrowser']['URL']
break;
case 'email' :
if (form.linkText&&sPane) form.linkText.value = paneValues['email']['linkText'];
if (sPane) form.URL.value = paneValues['email']['URL']
document.getElementById('bottomOptions').style.display = 'none';
break;
case 'web' :
if (form.linkText&&sPane) form.linkText.value = paneValues['web']['linkText'];
if (sPane) form.URL.value = paneValues['web']['URL']
if (FB.action=='link'||FB.action=='document') {
document.getElementById('previewButton').style.display = '';
document.getElementById('previewButton').onclick = function() { FB.preview('webPreview') }
}
if (FB.currentRemotePlugin&&FB.currentRemotePrefix) {
if (FB.embedPlugins[FB.currentRemotePlugin].onArriveRemote) {
FB.embedPlugins[FB.currentRemotePlugin].onArriveRemote(FB.currentRemotePrefix);
}
}
break;
case 'doc' :
if (form.linkText&&sPane) form.linkText.value = paneValues['doc']['linkText'];
if (sPane) form.URL.value = paneValues['doc']['URL']
break;
case 'site' :
if (form.linkText&&sPane) form.linkText.value = paneValues['site']['linkText'];
if (sPane) form.URL.value = paneValues['site']['URL']
if (document.getElementById('site')&&FB.linksBrowserURL) {
setTimeout("FB._sitePaneTimeout()", 1);
document.getElementById('previewButton').style.display = '';
document.getElementById('previewButton').onclick = function() { FB.preview('sitePreview') }
}
break;
}
if (FB.action!='link'&&FB.action!='document') {
if (pane == 'web' && FB.properties) {
form.ok.value = strApply;
} else {
form.ok.value = strInsert;
}
}
}
function displayMessageBox (innerHTML, width, height) {
var box = document.getElementById('messageBox');
box.innerHTML = innerHTML
var left = 0;
var top = 0;
var winDim = wproGetWindowInnerHeight();
var availHeight = winDim['height'];
var availWidth = winDim['width'];
availHeight -= 40;
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
if (document.getElementById('folderFrame'))document.getElementById('folderFrame').style.overflow = 'hidden';
if (document.getElementById('lookInSelect'))document.getElementById('lookInSelect').style.visibility = 'hidden';
}
function hideMessageBox() {
var box = document.getElementById('messageBox');
box.innerHTML = '';
box.style.display = 'none';
if (document.getElementById('folderFrame'))document.getElementById('folderFrame').style.overflow = '';
if (document.getElementById('lookInSelect'))document.getElementById('lookInSelect').style.visibility = '';
}
function getButtonHTML (b) {
var str = '<div class="buttonHolderContainer"><div class="buttonHolder">';
for (var i=0; i<b.length; i++) {
str += '<input class="button" type="'+b[i].type+'" name="'+b[i].name+'" value="'+b[i].value+'" ';
if (b[i].onclick) {
str +='onclick="'+b[i].onclick+'"';
}
str+=' />';
}
str+='</div></div>';
return str;
}
function fbObj() {
this.localEmbedPrefixes = {};
this.remoteEmbedPrefixes = {};
this.currentLocalPlugin = '';
this.currentLocalPrefix = '';
this.propertiesPlugin = '';
this.remotePluginValues = [];
this.properties = false;
this.chooser = false;
this.linkTextChanged = false;
this.setLinkTextToFilename = false;
this.inContext = false;
this.startID = false;
this.startPath = false;
this.startFile = false;
this.embedPlugins = {};
this.folderHistory = [];
}
fbObj.prototype.init = function (node) {
var t = document.getElementById('toolbar');
var buttons = t.getElementsByTagName('BUTTON');
var n = buttons.length;
for (var i=0; i<n; i++) {
var b= buttons[i];
b.onmouseover = this.mOver;
b.onmouseout = this.mOut;
if (!dialog.isOpera) b.onmousedown = this.mDown;
b.onmouseup = this.mUp;
}
var keyhandler = 'keydown';
dialog.events.addEvent(document, keyhandler, this.keyDownHandler);
dialog.events.addEvent(document, 'keyup', this.keyUpHandler);
switch (FB.action) {
case 'link': case 'document':
if (!this.chooser) {
FB.initLink();
} else {
FB.initChooser();
}
break;
default:
if (this.properties) {
dialog.reselectRange();
var range = currentEditor.selAPI.getRange();
var img = currentEditor._selectedNode ? currentEditor._selectedNode : range.nodes[0];
var form = document.dialogForm;
var i = 0;
var other_index;
var found = false;
for (var x in this.remoteEmbedPrefixes) {
if (x=='zz_other') {
other_index = i;
}
if (this.embedPlugins[x].canPopulate) {
if (this.embedPlugins[x].canPopulate(img)) {
found = true;
document.getElementById('ddUI1').selectedIndex = i;
ddUI1.manualSwapTab();
this.embedPlugins[x].populateProperties(this.remoteEmbedPrefixes[x]);
this.currentRemotePlugin = x;
this.currentRemotePrefix = this.remoteEmbedPrefixes[x];
break;
}
}
if (found) break;
i++;
}
if (!found) {
document.getElementById('ddUI1').selectedIndex = other_index;
ddUI1.manualSwapTab();
this.embedPlugins['zz_other'].populateProperties(this.remoteEmbedPrefixes['zz_other']);
this.currentRemotePlugin = 'zz_other';
this.currentRemotePrefix = this.remoteEmbedPrefixes['zz_other'];
}
this.propertiesPlugin = this.currentRemotePlugin;
if (form.elements['mediaborder']) {
if (img.style.borderTopWidth) {
form.elements['mediaborder'].value = String(img.style.borderTopWidth.toString()).replace(/[^0-9]/gi, '');
} else {
form.elements['mediaborder'].value = img.getAttribute('border');
}
}
if (form.elements['mediascreenTip']) {
form.elements['mediascreenTip'].value = (img.getAttribute('title') ? img.getAttribute('title') : img.getAttribute('alt'));
};
if (form.elements['mediaalign']) {
if (img.style.verticalAlign) {
form.elements['mediaalign'].value = img.style.verticalAlign
} else if (img.style.cssFloat) {
form.elements['mediaalign'].value = img.style.cssFloat
} else if (img.style.styleFloat) {
form.elements['mediaalign'].value = img.style.styleFloat
} else {
form.elements['mediaalign'].value = img.getAttribute('align');
}
}
if (form.elements['mediamtop']) {
form.elements['mediamtop'].value = String(img.style.marginTop).replace(/([0-9 ]+)px/gi, '$1');
form.elements['mediambottom'].value = String(img.style.marginBottom).replace(/([0-9 ]+)px/gi, '$1');
form.elements['mediamleft'].value = String(img.style.marginLeft).replace(/([0-9 ]+)px/gi, '$1');
form.elements['mediamright'].value = String(img.style.marginRight).replace(/([0-9 ]+)px/gi, '$1');
}
dialog.selectCurrentStyle(form.elements['mediastyle']);
FB.mediaPreview();
}
break;
}
dialog.events.addEvent(document.getElementById('folderFrame'), 'contextmenu', contextHandler);
dialog.PMenu.onclose = closePMenuHandler;
}
function closePMenuHandler () {
if (document.getElementById('messageBox').style.display!='block') {
if (document.getElementById('folderFrame'))document.getElementById('folderFrame').style.overflow = '';
}
FB.inContext = false;
}
fbObj.prototype.propertiesRequired = function (node) {
if (!this.chooser) {
var range = currentEditor.selAPI.getRange();
if (range.type=='control'||currentEditor._selectedNode) {
var node = currentEditor._selectedNode ? currentEditor._selectedNode : range.nodes[0];
switch (this.action) {
case 'media' :
if (node.tagName == 'IMG' && node.className.match(/wproFilePlugin/i)) {
this.properties = true;
}
break;
case 'image':
if (node.tagName == 'IMG' && !node.className.match(/wproFilePlugin/i)) {
this.properties = true;
}
break;
}
}
}
}
fbObj.prototype.MULTIPLE_SELECT_KEY_DOWN = false;
fbObj.prototype.keyDownHandler = function (evt) {
var keyCode = (evt.which || evt.charCode || evt.keyCode);
if (evt.shiftKey||keyCode==16||keyCode==17||keyCode==224) {
FB.MULTIPLE_OVERRIDE = true;
FB.MULTIPLE_SELECT_KEY_DOWN = true;
}
}
fbObj.prototype.keyUpHandler = function (evt) {
var keyCode = (evt.which || evt.charCode || evt.keyCode);
if (evt.shiftKey||keyCode==16||keyCode==17||keyCode==224) {
FB.MULTIPLE_OVERRIDE = false;
FB.MULTIPLE_SELECT_KEY_DOWN = false;
}
}
fbObj.prototype.showLoadMessage = function () {
document.getElementById('folderFrame').innerHTML = '<div class="fbLoadMessage"><img src="'+dialog.addSlashes(dialog.themeURL)+'misc/loader.gif" alt="" /> '+strPleaseWait+'</div>';
}
fbObj.prototype.showLoadMessageInNode = function (node) {
node.innerHTML = '<div class="fbLoadMessage"><img src="'+dialog.addSlashes(dialog.themeURL)+'misc/loader.gif" alt="" /></div>';
}
fbObj.prototype.toggleDetails = function (node) {
var details = document.getElementById('detailsHolder');
var folders = document.getElementById('fileBrowserCenter');
var folders2 = document.getElementById('folderFrame');
if (!details.style.display) {
details.style.display = 'none';
node.innerHTML = '&lt;&lt;';
folders.style.marginRight = '0px';
folders2.style.width = '632px';
} else {
details.style.display = '';
node.innerHTML = '&gt;&gt; Hide';
folders.style.marginRight = '';
folders2.style.width = '';
}
folders2.style.display = 'none';
setTimeout("document.getElementById('folderFrame').style.display = 'block'", 100);
}
fbObj.prototype.hideShowButtons = function (permissions, type) {
if (!permissions) {
permissions = [];
permissions['editImages'] = false;
permissions['moveFiles'] = false;
permissions['moveFolders'] = false;
permissions['copyFiles'] = false;
permissions['copyFolders'] = false;
permissions['renameFiles'] = false;
permissions['renameFolders'] = false;
permissions['deleteFiles'] = false;
permissions['deleteFolders'] = false;
permissions['upload'] = false;
permissions['overwrite'] = false;
permissions['createFolders'] = false;
}
var t = document.getElementById('toolbar');
var buttons = t.getElementsByTagName('BUTTON');
var n = buttons.length;
for (var i=0; i<n; i++) {
var b= buttons[i];
var id = b.id;
switch (id) {
case 'editImages' :
if (type == 'image') {
if (canGD) {
b.style.display = permissions['editImages'] ? '' : 'none';
} else {
b.style.display = 'none';
}
} else {
b.style.display = 'none';
}
break;
case 'move' :
b.style.display = (permissions['moveFiles'] || permissions['moveFolders']) ? '' : 'none';
if (!permissions['deleteFiles']&&!permissions['deleteFolders']&&!permissions['renameFiles']&&!permissions['renameFolders']&&!permissions['copyFiles']&&!permissions['copyFolders']&&!permissions['moveFiles']&&!permissions['moveFolders']&&!permissions['editImages']) {
document.getElementById('editSeparator').style.display = 'none';
} else {
document.getElementById('editSeparator').style.display = '';
}
break;
case 'copy' :
b.style.display = (permissions['copyFiles'] || permissions['copyFolders']) ? '' : 'none';
break;
case 'rename' :
b.style.display = (permissions['renameFiles'] || permissions['renameFolders']) ? '' : 'none';
break;
case 'delete' :
b.style.display = (permissions['deleteFiles'] || permissions['deleteFolders']) ? '' : 'none';
break;
case 'upload' :
b.style.display = permissions['upload'] ? '' : 'none';
if (permissions['upload']) b.style.width = String(b.firstChild.offsetWidth + 32) + 'px';
if (!permissions['createFolders'] && !permissions['upload']) {
document.getElementById('uploadSeparator').style.display = 'none';
} else {
document.getElementById('uploadSeparator').style.display = '';
}
break;
case 'createFolders' :
b.style.display = permissions['createFolders']==true ? '' : 'none';
break;
case 'thumbnailsView' :
b.style.display = (type=='image'&&thumbnails&&canGD) ? '' : 'none';
if (type!='image'||!thumbnails||!canGD) {
document.getElementById('viewSeparator').style.display = 'none';
} else {
document.getElementById('viewSeparator').style.display = '';
}
break;
case 'listView' :
b.style.display = (type=='image'&&thumbnails&&canGD) ? '' : 'none';
break;
}
}
}
fbObj.prototype._getBtnClass = function (e) {
var c = String(e.className);
return e.className.replace(/wproTextButton(Ready|Latched|Over|LatchedOver|Disabled|Down) /,'');
}
fbObj.prototype._setBtnClass = function (e,c) {
var oc = String(e.className);
e.className = oc.replace(/(wpro|wproTextButton)(Ready|Latched|Over|LatchedOver|Disabled|Down)($| )/g, '$1'+c.replace(/wpro/,'')+'$3');
}
fbObj.prototype.setButtonStates = function () {
var permissions = this.getPermissions();
var t = document.getElementById('toolbar');
var buttons = t.getElementsByTagName('BUTTON');
var n = buttons.length;
var forms = new wproForms();
var form = document.dialogForm
for (var i=0; i<n; i++) {
var b= buttons[i];
if (b.style.display=='none') continue;
var id = b.id;
switch (id) {
case 'back' :
b.className= (this.folderHistory.length>1) ? 'wproReady' : 'wproDisabled';
break;
case 'up' :
b.className= (document.getElementById('lookInSelect').value.match(/\//)) ? 'wproReady' : 'wproDisabled';
break;
case 'move' :
b.className = this._getFFState('move', permissions);
break;
case 'copy' :
b.className = this._getFFState('copy', permissions);
break;
case 'rename' :
b.className = this._getFFState('rename', permissions);
break;
case 'delete' :
b.className = this._getFFState('delete', permissions);
break;
case 'editImages' :
if (!form.elements['files']) {
b.className = 'wproDisabled';
} else if (forms.getSelectedCheckbox(form.elements['files']).length == 1) {
var value = forms.getSelectedCheckboxValue(form.elements['files'])[0];
var e = this.getExtension(value);
if (e=='.jpg'||e=='.jpeg'||e=='.png'||(e=='.gif'&&canGif)) {
b.className = 'wproReady';
} else {
b.className = 'wproDisabled';
}
} else {
b.className = 'wproDisabled';
}
break;
case 'thumbnailsView' :
b.className = (form.thumbnails.value == '1') ? 'wproLatched' : 'wproReady';
break;
case 'listView' :
b.className = (form.thumbnails.value == '0') ? 'wproLatched' : 'wproReady';
break;
}
}
}
fbObj.prototype._getFFState = function (b, p) {
var forms = new wproForms();
var form = document.dialogForm
var v = 'wproDisabled';
if (form.elements['files']) {
if (forms.getSelectedCheckbox(form.elements['files']).length && p[b+'Files']) {
v = 'wproReady';
}
}
if (form.elements['folders']) {
if (forms.getSelectedCheckbox(form.elements['folders']).length) {
if (p[b+'Folders']) {
v = 'wproReady';
} else {
v = 'wproDisabled';
}
}
}
return v;
}
fbObj.prototype.mOver = function () {
var elm = this;
if (elm.style.display=='none') return;
var className = FB._getBtnClass(elm);
if (className=="wproDisabled") return;
if (className=="wproLatched") {
FB._setBtnClass(elm,"wproLatchedOver");
return;
}
if (className=="wproReady") {
FB._setBtnClass(elm,"wproOver");
return;
}
}
fbObj.prototype.mOut = function () {
var elm = this;
if (elm.style.display=='none') return;
var className = FB._getBtnClass(elm);
if (className=="wproDisabled")return;
if (className=="wproLatched")return;
if (className=="wproOver") {
FB._setBtnClass(elm,"wproReady");
return;
}
if (className=="wproLatchedOver") {
FB._setBtnClass(elm,"wproLatched");
return;
}
}
fbObj.prototype.mDown = function () {
var elm = this;
if (elm.style.display=='none') return;
if (FB._getBtnClass(elm) == "wproDisabled")return;
FB._setBtnClass(elm,"wproDown");
}
fbObj.prototype.mUp = function () {
var elm = this;
if (elm.style.display=='none') return;
var style=elm.className
if (style=="wproDisabled")return;
if (style=="wproLatched")return;
FB._setBtnClass(elm,"wproOver");
elm.blur();
}
fbObj.prototype.getPermissions = function () {
var permissions = [];
permissions['deleteFiles'] = parseInt(document.dialogForm.elements['permissions[deleteFiles]'].value);
permissions['deleteFolders'] = parseInt(document.dialogForm.elements['permissions[deleteFolders]'].value);
permissions['renameFiles'] = parseInt(document.dialogForm.elements['permissions[renameFiles]'].value);
permissions['renameFolders'] = parseInt(document.dialogForm.elements['permissions[renameFolders]'].value);
permissions['upload'] = parseInt(document.dialogForm.elements['permissions[upload]'].value);
permissions['overwrite'] = parseInt(document.dialogForm.elements['permissions[overwrite]'].value);
permissions['moveFiles'] = parseInt(document.dialogForm.elements['permissions[moveFiles]'].value);
permissions['moveFolders'] = parseInt(document.dialogForm.elements['permissions[moveFolders]'].value);
permissions['copyFiles'] = parseInt(document.dialogForm.elements['permissions[copyFiles]'].value);
permissions['copyFolders'] = parseInt(document.dialogForm.elements['permissions[copyFolders]'].value);
permissions['createFolders'] = parseInt(document.dialogForm.elements['permissions[createFolders]'].value);
permissions['editImages'] = parseInt(document.dialogForm.elements['permissions[editImages]'].value);
return permissions;
}
function contextHandler(e) {
FB.showContextMenu(e);
}
fbObj.prototype.showContextMenu = function (e) {
if (dialog.hiddenMenus.firstChild) {
dialog.hiddenMenus.removeChild(dialog.hiddenMenus.firstChild);
}
var posx = e.clientX;
var posy = e.clientY;
var t = document.getElementById('toolbar');
var buttons = t.getElementsByTagName('BUTTON');
var n = buttons.length-2;
var node = document.createElement('DIV');
node.className = 'wproFloatingMenu';
var a = document.createElement('A');
a.setAttribute('href', 'javascript:void(null);');
a.style.margin = '0px';
a.style.padding = '0px';
node.appendChild(a);
var doneItems = [];
var forms = new wproForms();
var form = document.dialogForm
if (form.elements['folders']) {
var folders = forms.getSelectedCheckboxValue(form.elements['folders']);
}
if (form.elements['files']) {
var files = forms.getSelectedCheckboxValue(form.elements['files'])
}
if (files) {
if ((!folders || !folders.length) && files.length==1) {
var text = document.createTextNode(form.ok.value);
var a = document.createElement('A');
var img = document.createElement('IMG');
var t = document.createElement('SPAN');
t.appendChild(text);
if (dialog.isIE && dialog.browserVersion < 7 && !dialog.iframeDialog) {
a.setAttribute('href',document.location+'#');
} else {
a.setAttribute('href','javascript:void(null);');
}
eval('a.onclick = function () {FB.insertSelectedFile();dialog.closePMenuTimeout();}');
}
}
if (folders) {
if ((!files || !files.length) && folders.length==1) {
var a = document.createElement('A');
var img = document.createElement('IMG');
var t = document.createElement('SPAN');
t.innerHTML = strOpenFolder;
if (dialog.isIE && dialog.browserVersion < 7 && !dialog.iframeDialog) {
a.setAttribute('href',document.location+'#');
} else {
a.setAttribute('href','javascript:void(null);');
}
eval('a.onclick = function () {FB.openSelectedFolder();dialog.closePMenuTimeout();}');
}
}
if (a&&img&&t) {
a.onmouseover = wproFMenuOver;
a.onmouseout = wproFMenuOut;
a.onfocus = wproFMenuOver;
a.onblur = wproFMenuOut;
img.setAttribute('src', dialog.themeURL+'buttons/spacer.gif');
img.setAttribute('width', 22);
img.setAttribute('height', 22);
a.appendChild(img);
a.appendChild(t);
node.appendChild(a);
doneItems.push('button');
var a = document.createElement('DIV');
a.className = 'wproSeparator';
var img = document.createElement('img');
img.setAttribute('width', '1');
img.setAttribute('height', '1');
a.appendChild(img);
node.appendChild(a);
doneItems.push('separator');
}
for (var i=2; i<n; i++) {
var b= buttons[i];
var id = b.id;
var doSep = false;
if (b.nextSibling && b.nextSibling.tagName == 'IMG') doSep = true;
if (b.style.display=='none' || b.className.match(/wproDisabled/i)) continue;
var a = document.createElement('A');
var img = document.createElement('IMG');
var t = document.createElement('SPAN');
var text = document.createTextNode(b.getAttribute('title'));
t.appendChild(text);
if (dialog.isIE && dialog.browserVersion < 7 && !dialog.iframeDialog) {
a.setAttribute('href',document.location+'#');
} else {
a.setAttribute('href','javascript:void(null);');
}
eval('a.onclick = function () {document.getElementById(\''+id+'\').onclick();dialog.closePMenuTimeout();}');
a.onmouseover = wproFMenuOver;
a.onmouseout = wproFMenuOut;
a.onfocus = wproFMenuOver;
a.onblur = wproFMenuOut;
img.setAttribute('src', String(b.style.backgroundImage).replace(/url\(["']*([^'")(]*)["']*\)/gi, "$1"));
a.appendChild(img);
a.appendChild(t);
node.appendChild(a);
doneItems.push('button');
if (doSep && doneItems[doneItems.length-1] != 'separator') {
var a = document.createElement('DIV');
a.className = 'wproSeparator';
var img = document.createElement('img');
img.setAttribute('width', '1');
img.setAttribute('height', '1');
a.appendChild(img);
node.appendChild(a);
doneItems.push('separator');
}
}
if (!doneItems.length) {
dialog.events.preventDefault(e);
return false;
}
if (doneItems[doneItems.length-1]) {
if (doneItems[doneItems.length-1] == 'separator') {
node.removeChild(node.lastChild);
}
}
dialog.hiddenMenus.appendChild(node);
if (document.getElementById('folderFrame'))document.getElementById('folderFrame').style.overflow = 'hidden';
node.style.display = 'block';
node.style.visibility = 'visible'
dialog.PMenu.showPMenu(node, node.offsetWidth, node.offsetHeight, posx, posy);
dialog.events.preventDefault(e);
return false;
}
fbObj.prototype.nonce = '12345678912345678912345678912345';
fbObj.prototype.setNonce = function (token) {
this.nonce = token;
}
fbObj.prototype.loadFolder = function (id, path, page, sortBy, sortDir, view, history) {
var locationCheck = true;
var selected = [];
var exitNoError = false;
if (id==this.startID&&(!path&&path!='')) {
if (this.startPath) {
path = this.startPath;
}
if (this.startFile) {
var selected = [this.startFile];
}
this.startID = false;
this.startPath = false;
this.startFile = false;
exitNoError = true;
}
if (!path) {
path = '';
} else {
locationCheck = false;
}
if (!sortBy) {
sortBy = 'name';
} else {
locationCheck = false;
}
if (!sortDir) {
sortDir = 'asc';
} else {
locationCheck = false;
}
if (!page) {
page = '1';
} else {
locationCheck = false;
}
if (typeof(history)=='undefined') {
history = true;
}
if (typeof(view)=='undefined') {
view = 'default';
}
if (locationCheck) {
if (document.dialogForm.folderID) {
oid = document.dialogForm.folderID.value;
opath = document.dialogForm.folderPath.value;
if (oid == id && opath == path) {
return;
}
}
}
var url = dialog.editorLink( 'dialog.php?dialog=wproCore_fileBrowser&action=preview&' + dialog.sid + (dialog.phpsid ? '&' + dialog.phpsid : '') + (dialog.appendToQueryStrings ? '&' + dialog.appendToQueryStrings : '') );
dialog.changeFrameLocation(document.getElementById('filePanePreview'), url);
document.getElementById('nothingPane').style.display='block';
document.getElementById('multiplePane').style.display='none';
document.getElementById('folderPane').style.display='none';
document.getElementById('filePane').style.display='none';
this.hideShowButtons(false, false);
this.showLoadMessage();
ajax_displayFolderList(id, path, page, sortBy, sortDir, view, selected, (history?true:false), null, exitNoError);
}
fbObj.prototype.onFolderNotFound = function () {
if (!this.goToLastFolder()) {
document.getElementById('folderFrame').innerHTML = '<div class="fbLoadMessage">'+strFolderNotFound+'</div>';
}
}
fbObj.prototype.MULTIPLE_OVERRIDE = false;
fbObj.prototype.onLoadFolder = function (history) {
this.MULTIPLE_OVERRIDE = true;
var permissions = this.getPermissions();
this.hideShowButtons(permissions, document.dialogForm.folderType.value);
this.rebuildLookInSelect(document.dialogForm.folderID.value, document.dialogForm.folderPath.value);
if (history=='true'||history==true) {
this.folderHistory.push([document.dialogForm.folderID.value, document.dialogForm.folderPath.value]);
}
var forms = new wproForms();
var form = document.dialogForm

try {
var node;
if (form.elements['folders']) {
var folders = forms.getSelectedCheckbox(form.elements['folders']);
}
if (form.elements['files']) {
var files = forms.getSelectedCheckbox(form.elements['files'])
}
if (folders||files) {
var d = [];
if (folders) {
if (!form.elements['folders'].length&&folders[0]==0) {
node = form.elements['folders'];
node.parentNode.parentNode.onclick();
} else {
for (var i=0; i<folders.length; i++) {
node = form.elements['folders'][folders[i]];
node.parentNode.parentNode.onclick();
}
}
}
if (files) {
if (!form.elements['files'].length&&files[0]==0) {
node = form.elements['files'];
node.parentNode.parentNode.onclick();
} else {
for (var i=0; i<files.length; i++) {
node = form.elements['files'][files[i]];
node.parentNode.parentNode.onclick();
}
}
}
}
}catch(e){};
this.MULTIPLE_OVERRIDE = false;
this.setButtonStates();
}
fbObj.prototype.changeView = function (view) {
var forms = new wproForms();
var form = document.dialogForm
var sortBy = form.sortBy.value
var sortDir = form.sortDir.value
var page = form.page.value
var id = form.folderID.value
var path = form.folderPath.value
if (form.elements['folders']) {
var folders = forms.getSelectedCheckboxValue(form.elements['folders']);
}
if (form.elements['files']) {
var files = forms.getSelectedCheckboxValue(form.elements['files'])
}
if (folders||files) {
var d = [];
if (folders) {
for (var i=0; i<folders.length; i++) {
d.push(folders[i]);
}
}
if (files) {
for (var i=0; i<files.length; i++) {
d.push(files[i]);
}
}
}
ajax_displayFolderList(id, path, page, sortBy, sortDir, view, d, false, null);
}
fbObj.prototype.moveCopyFinished = function (overwrite, moveCopyID, d) {
if (d) {
d.close();
}
dialog.showLoadMessage();
var forms = new wproForms();
var form = document.dialogForm
var sortBy = form.sortBy.value
var sortDir = form.sortDir.value
var view = form.view.value
var page = form.page.value
var id = form.folderID.value
var path = form.folderPath.value
this.hideShowButtons(false, false);
this.showLoadMessage();
var oString = '[';
var n = overwrite.length;
for (var i=0; i < n; i++) {
oString += "'"+overwrite[i].replace(/'/gi, "\\'")+"'";
if (i<n) {
oString += ',';
}
}
oString+=']';
setTimeout("ajax_moveCopyFinished('"+dialog.addSlashes(moveCopyID)+"', "+oString+", '"+dialog.addSlashes(page)+"', '"+dialog.addSlashes(sortBy)+"', '"+dialog.addSlashes(sortDir)+"', '"+dialog.addSlashes(view)+"')", 1);
}
fbObj.prototype.uploadFinished = function (overwrite, uploadID, d) {
if (d) {
d.close();
}
dialog.showLoadMessage();
var forms = new wproForms();
var form = document.dialogForm
var sortBy = form.sortBy.value
var sortDir = form.sortDir.value
var view = form.view.value
var page = form.page.value
var id = form.folderID.value
var path = form.folderPath.value
this.hideShowButtons(false, false);
this.showLoadMessage();
var oString = '[';
var n = overwrite.length;
for (var i=0; i < n; i++) {
oString += "'"+overwrite[i].replace(/'/gi, "\\'")+"'";
if (i<n) {
oString += ',';
}
}
oString+=']';
setTimeout("ajax_uploadFinished('"+dialog.addSlashes(id)+"', '"+dialog.addSlashes(path)+"', '"+dialog.addSlashes(uploadID)+"', "+oString+", '"+dialog.addSlashes(page)+"','"+dialog.addSlashes(sortBy)+"', '"+dialog.addSlashes(sortDir)+"'), '"+dialog.addSlashes(view)+"'", 1);
}
fbObj.prototype.editImageFinished = function (editorID, action, d) {
if (d) {
d.close();
}
dialog.showLoadMessage();
var forms = new wproForms();
var form = document.dialogForm
var sortBy = form.sortBy.value
var sortDir = form.sortDir.value
var view = form.view.value
var page = form.page.value
var id = form.folderID.value
var path = form.folderPath.value
this.hideShowButtons(false, false);
this.showLoadMessage();
setTimeout("ajax_editImage('"+dialog.addSlashes(id)+"', '"+dialog.addSlashes(path)+"', '"+dialog.addSlashes(editorID)+"', '"+dialog.addSlashes(action)+"', {'page':'"+dialog.addSlashes(page)+"', 'sortBy':'"+dialog.addSlashes(sortBy)+"', 'sortDir':'"+dialog.addSlashes(sortDir)+"', 'view':'"+dialog.addSlashes(view)+"'})", 1);
}
fbObj.prototype.refreshFolders = function (d) {
if (d) {
subDialog = d;
}
var forms = new wproForms();
var form = document.dialogForm
var sortBy = form.sortBy.value
var sortDir = form.sortDir.value
var id = form.folderID.value
var path = form.folderPath.value
var view = form.view.value
var page = form.page.value
if (form.elements['folders']) {
var folders = forms.getSelectedCheckboxValue(form.elements['folders']);
}
if (form.elements['files']) {
var files = forms.getSelectedCheckboxValue(form.elements['files'])
}
if (folders||files) {
var d = [];
if (folders) {
for (var i=0; i<folders.length; i++) {
d.push(folders[i]);
}
}
if (files) {
for (var i=0; i<files.length; i++) {
d.push(files[i]);
}
}
}
this.hideShowButtons(false, false);
this.showLoadMessage();
ajax_displayFolderList(id, path, page, sortBy, sortDir, view, d, false, null);
}
fbObj.prototype.deleteFiles = function (b) {
if (b.className == 'wproDisabled') return;
var forms = new wproForms();
var form = document.dialogForm
var folders = false;
var files = false;
if (form.elements['folders']) {
folders = forms.getSelectedCheckboxValue(form.elements['folders']);
}
if (form.elements['files']) {
files = forms.getSelectedCheckboxValue(form.elements['files'])
}
if (folders||files) {
if (confirm(strDeleteWarning)) {
var d = [];
if (folders) {
for (var i=0; i<folders.length; i++) {
d.push(folders[i]);
}
}
if (files) {
for (var i=0; i<files.length; i++) {
d.push(files[i]);
}
}
var sortBy = form.sortBy.value
var sortDir = form.sortDir.value
var view = form.view.value
var page = form.page.value
var id = form.folderID.value
var path = form.folderPath.value
this.hideShowButtons(false, false);
document.getElementById('nothingPane').style.display='block';
document.getElementById('multiplePane').style.display='none';
document.getElementById('folderPane').style.display='none';
document.getElementById('filePane').style.display='none';
var url = dialog.editorLink( 'dialog.php?dialog=wproCore_fileBrowser&action=preview&' + dialog.sid + (dialog.phpsid ? '&' + dialog.phpsid : '') + (dialog.appendToQueryStrings ? '&' + dialog.appendToQueryStrings : '') );
setTimeout("dialog.changeFrameLocation(document.getElementById('filePanePreview'), '"+dialog.addSlashes(url)+"')", 1);
dialog.showLoadMessage();
this.showLoadMessage();
ajax_delete(id, path, d, page, sortBy, sortDir, view, this.nonce);
}
}
}
fbObj.prototype.showUpload = function (b) {
if (b.className == 'wproDisabled') return;
var form = document.dialogForm;
var id = form.folderID.value
var path = form.folderPath.value
dialog.openDialogPlugin('wproCore_fileBrowser&action=upload&folderID='+escape(id)+'&folderPath='+escape(Base64.encode(path)), 450, 400);
}
fbObj.prototype.showMove = function (b) {
if (b.className == 'wproDisabled') return;
var form = document.dialogForm;
var id = form.folderID.value
var path = form.folderPath.value
var fp = '';
var op = '';
var forms = new wproForms();
var folders = false;
var files = false;
if (form.elements['folders']) {
folders = forms.getSelectedCheckboxValue(form.elements['folders']);
}
if (form.elements['files']) {
files = forms.getSelectedCheckboxValue(form.elements['files'])
}
if (folders||files) {
var d = [];
if (folders.length) {
fp+= 'moveFolders';
}
if (files.length) {
op+='moveFiles';
}
}
dialog.openDialogPlugin('wproCore_fileBrowser&action=move&srcFolderID='+escape(id)+'&srcFolderPath='+escape(Base64.encode(path))+'&requiredPermissions='+escape(fp+','+op), 450, 400);
}
fbObj.prototype.showCopy = function (b) {
if (b.className == 'wproDisabled') return;
var form = document.dialogForm;
var id = form.folderID.value
var path = form.folderPath.value
var fp = '';
var op = '';
var forms = new wproForms();
var folders = false;
var files = false;
if (form.elements['folders']) {
folders = forms.getSelectedCheckboxValue(form.elements['folders']);
}
if (form.elements['files']) {
files = forms.getSelectedCheckboxValue(form.elements['files'])
}
if (folders||files) {
var d = [];
if (folders.length) {
fp+= 'copyFolders';
}
if (files.length) {
op+='copyFiles';
}
}
dialog.openDialogPlugin('wproCore_fileBrowser&action=copy&srcFolderID='+escape(id)+'&srcFolderPath='+escape(Base64.encode(path))+'&requiredPermissions='+fp+','+op, 450, 400);
}
fbObj.prototype.showImageEditor = function (b) {
if (b.className == 'wproDisabled') return;
var form = document.dialogForm;
var id = form.folderID.value
var path = form.folderPath.value
var forms = new wproForms();
var image = forms.getSelectedCheckboxValue(document.dialogForm.files);
dialog.openDialogPlugin('wproCore_fileBrowser&action=imageEditor&folderID='+escape(id)+'&folderPath='+escape(Base64.encode(path))+'&image='+escape(image), 760, 480);
}
fbObj.prototype.showNewFolder = function (b) {
if (b.className == 'wproDisabled') return;
var str = '<div class="bodyHolder"><div>'+strEnterNewFolderName+'<br /><br /><input size="40" type="text" name="newFolderName" value="" /></div></div>';
str+=getButtonHTML([{'onclick':'return FB.newFolderAction()', 'type':'submit','name':'ok','value':strOK},{'onclick' : 'hideMessageBox()','type':'button','name':'cancel','value':strCancel}]);
displayMessageBox(str, 400, 120);
document.dialogForm.newFolderName.focus();
}
fbObj.prototype.newFolderAction = function () {
var form = document.dialogForm
var name = form.newFolderName.value
if (name.length > 0) {
dialog.showLoadMessage();
var sortBy = form.sortBy.value
var sortDir = form.sortDir.value
var view = form.view.value
var page = form.page.value
var id = form.folderID.value
var path = form.folderPath.value
this.hideShowButtons(false, false);
this.showLoadMessage();
ajax_newFolder(id, path, name, page, sortBy, sortDir, view, this.nonce);
}
return false;
}
fbObj.prototype.getExtension = function (str) {
var f, l;
str = String(str);
if (f = str.lastIndexOf('/')) {
str = str.substr(f+1);
}
if (l = str.lastIndexOf('.')) {
str = str.substr(l);
} else {
str = '';
}
if (str.substr(0,1)!='.') str = '';
return str.toLowerCase();
}
fbObj.prototype.getName = function (str) {
var l = this.getExtension (str);
return str.substr(0, str.length-l.length);
}
fbObj.prototype.showRename = function () {
var forms = new wproForms();
var form = document.dialogForm
var str = '<div class="bodyHolder"><div id="renameScroll" class="inset">';
var folders = false;
var files = false;
if (form.elements['folders']) {
folders = forms.getSelectedCheckboxValue(form.elements['folders']);
}
if (form.elements['files']) {
files = forms.getSelectedCheckboxValue(form.elements['files'])
}
if (folders) {
for (var i=0; i<folders.length; i++) {
str += '<div>'+(strEnterNewName.replace("##oldname##", folders[i]))+'<br /><br /><input size="40" type="text" name="renameFiles['+folders[i]+']" value="'+folders[i]+'" /></div><hr />';
}
}
if (files) {
for (var i=0; i<files.length; i++) {
var extension = this.getExtension(files[i]);
var name = files[i].substr(0, files[i].length - extension.length);
str += '<div>'+(strEnterNewName.replace("##oldname##", files[i]))+'<br /><br /><input size="40" type="text" name="renameFiles['+files[i]+']" value="'+this.getName(files[i])+'" />'+extension+'</div><hr />';
}
}
str += '</div></div>';
str+=getButtonHTML([{'onclick':'return FB.renameAction()', 'type':'submit','name':'ok','value':strOK},{'onclick' : 'hideMessageBox()','type':'button','name':'cancel','value':strCancel}]);
displayMessageBox(str, 400, 285);
document.getElementById('renameScroll').getElementsByTagName('INPUT').item(0).focus();
}
fbObj.prototype.renameAction = function () {
dialog.showLoadMessage();
var forms = new wproForms();
var form = document.dialogForm;
var folders = false;
var files = false;
if (form.elements['folders']) {
folders = forms.getSelectedCheckboxValue(form.elements['folders']);
}
if (form.elements['files']) {
files = forms.getSelectedCheckboxValue(form.elements['files'])
}
var f = [];
var n = folders.length;
for (var i=0; i<n; i++) {
f[folders[i]] = form.elements['renameFiles['+folders[i]+']'].value;
}
var n = files.length;
for (var i=0; i<n; i++) {
f[files[i]] = form.elements['renameFiles['+files[i]+']'].value;
}
var sortBy = form.sortBy.value
var sortDir = form.sortDir.value
var page = form.page.value
var id = form.folderID.value
var path = form.folderPath.value
var view = form.view.value
this.hideShowButtons(false, false);
this.showLoadMessage();
ajax_rename(id, path, f, page, sortBy, sortDir, view, this.nonce);
return false;
}
fbObj.prototype.goPage = function (p) {
this.loadFolder(document.dialogForm.folderID.value, document.dialogForm.folderPath.value, p, document.dialogForm.sortBy.value, document.dialogForm.sortDir.value, document.dialogForm.view.value)
}
fbObj.prototype.upOneLevel = function (b) {
if (b.className == 'wproDisabled') return;
var f = document.dialogForm.folderPath.value
if (f == '') return;
f = f.replace(/(^|\/)[^\/]*\/$/gi, '/');
this.loadFolder(document.dialogForm.folderID.value, f, '1', document.dialogForm.sortBy.value, document.dialogForm.sortDir.value, document.dialogForm.view.value)
}
fbObj.prototype.sortFilesBy = function (str) {
if (document.dialogForm.sortBy.value==str) {
if (document.dialogForm.sortDir.value=='asc') {
document.dialogForm.sortDir.value='desc'
} else {
document.dialogForm.sortDir.value='asc'
}
} else {
document.dialogForm.sortBy.value=str;
document.dialogForm.sortDir.value='asc'
}
this.loadFolder(document.dialogForm.folderID.value, document.dialogForm.folderPath.value, document.dialogForm.page.value, document.dialogForm.sortBy.value, document.dialogForm.sortDir.value, document.dialogForm.view.value)
}
fbObj.prototype.folderArray = [];
fbObj.prototype.buildLookInSelect = function (arr, currentKey, folderpath) {
if (arr==undefined) {
return;
}
var node = document.getElementById('lookInSelect');
var cn = node.childNodes;
while (cn.length > 0) {
node.removeChild(cn[0]);
}
for (var key in arr) {
var o = document.createElement('OPTION');
o.setAttribute('value', key+'|');
var l = arr[key];
o.setAttribute('label', l);
var t = document.createTextNode(l);
o.appendChild(t);
node.appendChild(o);
if (key==currentKey&&folderpath) {
farr = folderpath.split('/');
var n = farr.length;
var str='';
for (var i=0; i<n; i++) {
if (farr[i].length == 0) continue;
farr[i].trim();
var o = document.createElement('OPTION');
str += '/'+farr[i];
o.setAttribute('value', key+'|'+str);
var l = arr[key];
o.setAttribute('label', arr[key]+str);
var t = document.createTextNode(arr[key]+str);
o.appendChild(t);
node.appendChild(o);
}
}
if (key==currentKey) {
o.selected = true;
}
}
if (!this.folderArray.length) {
this.folderArray = arr
}
}
fbObj.prototype.rebuildLookInSelect = function (currentKey, folderpath) {
parentOutlookSelect(currentKey);
this.buildLookInSelect(this.folderArray, currentKey, folderpath)
}
fbObj.prototype.lookInSelectChange = function (node) {
var v = node.options[node.selectedIndex].value;
v = v.split("|");
var id = v[0];
if (v[1]) {
var fp = v[1]
} else {
var fp=false;
}
hideMessageBox();
this.loadFolder(id, fp, '1', document.dialogForm.sortBy.value, document.dialogForm.sortDir.value, document.dialogForm.view.value)
}
fbObj.prototype.goBack = function(b) {
if (b) {
if (b.className == 'wproDisabled') return;
}
if (this.folderHistory.length>1) {
var id = this.folderHistory[this.folderHistory.length-2][0];
var path = this.folderHistory[this.folderHistory.length-2][1];
var sortBy = 'name';
var sortDir = 'asc';
var view = 'default';
if (document.dialogForm.sortBy) {
sortBy = document.dialogForm.sortBy.value
}
if (document.dialogForm.sortDir) {
sortDir = document.dialogForm.sortDir.value
}
if (document.dialogForm.view) {
view = document.dialogForm.view.value
}
this.loadFolder(id, path, '1', sortBy, sortDir, view, false);
this.folderHistory.pop();
this.folderHistory.pop();
return true;
}
return false;
}
fbObj.prototype.goToLastFolder = function() {
if (this.folderHistory.length>0) {
var id = this.folderHistory[this.folderHistory.length-1][0];
var path = this.folderHistory[this.folderHistory.length-1][1];
var sortBy = 'name';
var sortDir = 'asc';
var view = 'default';
if (document.dialogForm.sortBy) {
sortBy = document.dialogForm.sortBy.value
}
if (document.dialogForm.sortDir) {
sortDir = document.dialogForm.sortDir.value
}
if (document.dialogForm.view) {
view = document.dialogForm.view.value
}
this.loadFolder(id, path, '1', sortBy, sortDir, view, false);
this.folderHistory.pop();
this.folderHistory.pop();
return true;
}
return false;
}
fbObj.prototype.TEMP_MULTIPLE_OVERRIDE = false;
fbObj.prototype.checkSelect = function (node) {
this.MULTIPLE_OVERRIDE = true
this.TEMP_MULTIPLE_OVERRIDE = true
if (node.checked) {
node.checked = false;
} else {
node.checked = true;
}
}
fbObj.prototype.selectFile = function (node, details, multiple, scroll) {
if (this.MULTIPLE_OVERRIDE==true) {
multiple = true;
}
if (!multiple) this.deselectAll();
var i = node.getElementsByTagName('INPUT').item(0)
var name = details.name;
var type = details.type;
var forms = new wproForms();
var form = document.dialogForm
var fileLen = 0;
var foldLen = 0
document.getElementById('nothingPane').style.display='none';
if (scroll || this.MULTIPLE_OVERRIDE) {
i.focus();
var parent = i.parentNode.parentNode;
var frame = document.getElementById('folderFrame');
var top = parent.offsetTop;
var height = parent.offsetHeight + 18;
var scrollTop = frame.scrollTop;
if (top + height > frame.scrollTop+frame.offsetHeight) {
frame.scrollTop = (top + height)-frame.offsetHeight
}
}
if (multiple && i.checked==true && (this.MULTIPLE_SELECT_KEY_DOWN||this.TEMP_MULTIPLE_OVERRIDE) && !this.inContext) {
document.getElementById('selectAll').checked=false
if (this.TEMP_MULTIPLE_OVERRIDE) {
this.MULTIPLE_OVERRIDE = false;
this.TEMP_MULTIPLE_OVERRIDE = false;
}
this.deselectFile(node);
this.setButtonStates();
return;
} else {
i.checked=true;
node.className = 'selected';
}
if (this.TEMP_MULTIPLE_OVERRIDE) {
this.MULTIPLE_OVERRIDE = false;
this.TEMP_MULTIPLE_OVERRIDE = false;
}
if (form.elements['files']) {
fileLen += forms.getSelectedCheckbox(form.elements['files']).length;
}
if (form.elements['folders']) {
foldLen += forms.getSelectedCheckbox(form.elements['folders']).length
}
if (fileLen + foldLen==0) {
document.getElementById('nothingPane').style.display='block';
document.getElementById('multiplePane').style.display='none';
document.getElementById('folderPane').style.display='none';
document.getElementById('filePane').style.display='none';
} else if (fileLen + foldLen > 1) {
document.getElementById('multiplePane').style.display='block';
document.getElementById('folderPane').style.display='none';
document.getElementById('filePane').style.display='none';
} else if (type=='folder') {
document.getElementById('multiplePane').style.display='none';
document.getElementById('filePane').style.display='none';
document.getElementById('folderPane').style.display='block';
if (details.name) {
if (form.elements['linkText']) {
if (this.setLinkTextToFilename) {
form.elements['linkText'].value = details.name
}
}
var t = document.createTextNode(' '+details.name);
document.getElementById('displayFolderName').innerHTML = '';
document.getElementById('displayFolderName').appendChild(t);
this.showLoadMessageInNode(document.getElementById('displayFolderSize'));
ajax_displayFolderDetails(form.folderID.value,form.folderPath.value,details.name);
form.URL.value = dialog.urlFormatting(form.folderURL.value + details.name);
}
if (details.mod) {
var t = document.createTextNode(' '+details.name);
document.getElementById('displayFolderName').innerHTML = '';
document.getElementById('displayFolderName').appendChild(t);
}
if (details.mod) {
var t = document.createTextNode(' '+details.mod);
} else {
var t = document.createTextNode(' --');
}
var url = dialog.editorLink( 'dialog.php?dialog=wproCore_fileBrowser&action=preview&' + dialog.sid + (dialog.phpsid ? '&' + dialog.phpsid : '') + (dialog.appendToQueryStrings ? '&' + dialog.appendToQueryStrings : '') );
dialog.changeFrameLocation(document.getElementById('filePanePreview'), url);
} else {
if (details.name) {
if (form.elements['linkText']) {
if (this.setLinkTextToFilename) {
form.elements['linkText'].value = details.name
}
}
var folderURL = form.folderURL.value;
if (details.prev) {
if (document.getElementById('detailsHolder').style.display!='none')
setTimeout("dialog.changeFrameLocation(document.getElementById('filePanePreview'), '"+dialog.addSlashes(folderURL + details.name)+"')", 1);
document.getElementById('loadPreview').style.display = 'none';
} else {
document.getElementById('loadPreview').style.display = '';
var url = dialog.editorLink( 'dialog.php?dialog=wproCore_fileBrowser&action=nopreview&' + dialog.sid + (dialog.phpsid ? '&' + dialog.phpsid : '') + (dialog.appendToQueryStrings ? '&' + dialog.appendToQueryStrings : '') );
if (document.getElementById('detailsHolder').style.display!='none')
setTimeout("dialog.changeFrameLocation(document.getElementById('filePanePreview'), '"+dialog.addSlashes(url)+"')", 1);
}
var t = document.createTextNode(' '+details.name);
document.getElementById('displayName').innerHTML = '';
document.getElementById('displayName').appendChild(t);
form.URL.value = dialog.urlFormatting(folderURL + details.name);
}
if (details.type) {
var t = document.createTextNode(' '+details.type);
document.getElementById('displayType').innerHTML = '';
document.getElementById('displayType').appendChild(t);
}
if (details.size) {
var t = document.createTextNode(' '+details.size);
document.getElementById('displaySize').innerHTML = '';
document.getElementById('displaySize').appendChild(t);
}
if (details.mod) {
var t = document.createTextNode(' '+details.mod);
document.getElementById('displayModified').innerHTML = '';
document.getElementById('displayModified').appendChild(t);
}
document.getElementById('multiplePane').style.display='none';
document.getElementById('filePane').style.display='block';
document.getElementById('folderPane').style.display='none';
if (this.action!='link'&&this.action!='document') this.awaitingFileDetails = true;
if (!this.chooser) this.showLoadMessageInNode(document.getElementById('displayExtra'));
if (!this.chooser) this.showLoadMessageInNode(document.getElementById('optionsLoadMessage'));
if (!this.chooser) ajax_displayFileDetails(document.dialogForm.folderID.value, document.dialogForm.folderPath.value, details.name, (this.action=='link'||this.action=='document'||this.chooser)?false:true);
}
this.setButtonStates()
}
fbObj.prototype.awaitingFileDetails = false;
fbObj.prototype.doActionOnFileDetails = false;
fbObj.prototype.displayExtraDetails = function (data) {
var c = document.getElementById('displayExtra');
document.getElementById('optionsLoadMessage').innerHTML = '';
c.innerHTML = '';
if (data) {
for (var x in data) {
var d = document.createElement('DIV');
d.innerHTML = '<div><strong>'+x+'</strong> '+data[x]+'</div>';
c.appendChild(d);
}
}
this.awaitingFileDetails = false;
if (this.doActionOnFileDetails) {
this.doActionOnFileDetails = false;
setTimeout('formAction()', 1000);
}
}
fbObj.prototype.getProportionalSize = function (width, height, mwidth, mheight) {
if (!isNaN(width) && !isNaN(height) && width && height) {
width = parseInt(width);
height = parseInt(height);
var ratioY = width/height;
var ratioX = height/width;
if (!isNaN(mwidth)) {
mwidth = parseInt(mwidth);
} else {
mwidth = 0;
}
if (!isNaN(mheight)) {
mheight = parseInt(mheight);
} else {
mheight = 0;
}
if (mwidth) {
if (width > mwidth) {
width = mwidth;
height = Math.round(width * ratioX);
}
}
if (mheight) {
if (height > mheight) {
height = mheight;
width = Math.round(height * ratioY);
}
}
}
return {'width':width,'height':height};
}
fbObj.prototype.populateLocalOptions = function (plugin, data) {
var p=this.localEmbedPrefixes[plugin];
for (var x in this.localEmbedPrefixes) {
if (this.localEmbedPrefixes[x]!=p) {
document.getElementById(this.localEmbedPrefixes[x]).style.display='none';
}
}
if (p) {
document.getElementById(p).style.display='block';
if (typeof(data['width']) != 'undefined' && typeof(data['height']) != 'undefined') {
if (FB.action == 'image') {
var mwidth = this.maxImageDisplayWidth;
var mheight = this.maxImageDisplayHeight;
} else {
var mwidth = this.maxMediaDisplayWidth;
var mheight = this.maxMediaDisplayHeight;
}
var arr = this.getProportionalSize(data['width'], data['height'], mwidth, mheight);
data['width'] = arr.width;
data['height'] = arr.height;
}
this.embedPlugins[plugin].populateLocalOptions(data, p);
this.currentLocalPrefix = p;
this.currentLocalPlugin = plugin;
}
}
fbObj.prototype.onRemotePluginChange = function (value) {
var num = value.replace(/^sPane_[^_]*_/i, '');
var plugin = '';
var prefix = '';
for (var x in FB.remoteEmbedPrefixes) {
if (FB.remoteEmbedPrefixes[x] == 'remoteEmbed'+num) {
plugin = x;
prefix = FB.remoteEmbedPrefixes[x];
break;
}
}
if (FB.embedPlugins[FB.currentRemotePlugin].onLeaveRemote) {
FB.embedPlugins[FB.currentRemotePlugin].onLeaveRemote(prefix);
}
FB.remotePluginValues[FB.currentRemotePlugin] = document.dialogForm.URL.value;
document.dialogForm.URL.value = '';
FB.currentRemotePlugin = plugin;
FB.currentRemotePrefix = FB.remoteEmbedPrefixes[plugin];
if (FB.embedPlugins[plugin].onArriveRemote) {
FB.embedPlugins[plugin].onArriveRemote(prefix);
}
if (FB.remotePluginValues[plugin]) {
document.dialogForm.URL.value = FB.remotePluginValues[plugin];
}
}
fbObj.prototype.loadEmbedPlugin = function (pluginName, params) {
if (!this.embedPlugins[pluginName]) {
if (eval('try {wproFilePlugin_'+pluginName+'}catch(e){}')) {
var p = eval('new wproFilePlugin_'+pluginName+'()');
this.embedPlugins[pluginName] = p;
}
}
}
fbObj.prototype.preview = function (frame, url) {
if (!frame) frame = 'filePanePreview';
if (!url) url = document.dialogForm.URL.value;
if (url=='') return;
if (typeof(this.embedPlugins[this.currentLocalPlugin]) != 'undefined' && typeof(this.embedPlugins[this.currentLocalPlugin].getPreviewURL) == 'function') {
url = this.embedPlugins[this.currentLocalPlugin].getPreviewURL(dialog.appendBaseToURL(url));
}
url= dialog.appendBaseToURL(url);
dialog.changeFrameLocation(document.getElementById(frame), url);
}
fbObj.prototype.previewInNewWindow = function () {
var url = document.dialogForm.URL.value;
if (url=='') return;
if (typeof(this.embedPlugins[this.currentLocalPlugin]) != 'undefined' && typeof(this.embedPlugins[this.currentLocalPlugin].getPreviewURL) == 'function') {
url = this.embedPlugins[this.currentLocalPlugin].getPreviewURL(dialog.appendBaseToURL(url));
}
url= dialog.appendBaseToURL(url);
var win = window.open(url, 'wproFilePreview');
win.focus();
}
fbObj.prototype.deselectFile = function (node, foo) {
var i = node.getElementsByTagName('INPUT').item(0)
i.checked=false;
node.className = '';
var forms = new wproForms();
var form = document.dialogForm
var fileLen = 0;
var foldLen = 0
if (form.elements['files']) {
fileLen += forms.getSelectedCheckbox(form.elements['files']).length;
}
if (form.elements['folders']) {
foldLen += forms.getSelectedCheckbox(form.elements['folders']).length
}
if (fileLen + foldLen == 0) {
document.getElementById('nothingPane').style.display='block';
document.getElementById('multiplePane').style.display='none';
document.getElementById('folderPane').style.display='none';
document.getElementById('filePane').style.display='none';
} else if (fileLen + foldLen == 1 &&!foo) {
var mo = this.MULTIPLE_OVERRIDE;
if (this.MULTIPLE_OVERRIDE==true) {
this.MULTIPLE_OVERRIDE=false;
}
if (fileLen) {
if (form.elements['files'].length) {
if (forms.getSelectedCheckbox(form.elements['files']).length) {
node = form.elements['files'][forms.getSelectedCheckbox(form.elements['files'])[0]];
node.parentNode.parentNode.onclick();
}
} else {
node = form.elements['files'];
node.parentNode.parentNode.onclick();
}
} else if (foldLen) {
if (form.elements['folders'].length) {
if (forms.getSelectedCheckbox(form.elements['folders']).length) {
node = form.elements['folders'][forms.getSelectedCheckbox(form.elements['folders'])[0]];
node.parentNode.parentNode.onclick();
}
} else {
node = form.elements['folders'];
node.parentNode.parentNode.onclick();
}
}
this.MULTIPLE_OVERRIDE = mo;
}
}
fbObj.prototype.deselectAll = function (multiple) {
var inputs = document.getElementById('fileTable').getElementsByTagName('INPUT');
var n = inputs.length
for (var i=0; i<n; i++) {
this.deselectFile(inputs[i].parentNode.parentNode, true);
}
if (multiple) {
this.setButtonStates()
}
}
fbObj.prototype.selectAll = function () {
var inputs = document.getElementById('fileTable').getElementsByTagName('INPUT');
var n = inputs.length
for (var i=0; i<n; i++) {
this.selectFile(inputs[i].parentNode.parentNode, {}, true);
}
this.setButtonStates()
}
fbObj.prototype.toggleSelectAll = function (node) {
if (node.checked) {
this.selectAll();
} else {
this.deselectAll(true);
}
}
fbObj.prototype.goToFolder = function (node) {
var i = node.getElementsByTagName('INPUT').item(0);
var fp = document.dialogForm.folderPath
var path = fp.value + i.value + '/'
this.loadFolder(document.dialogForm.folderID.value, path, '1', document.dialogForm.sortBy.value, document.dialogForm.sortDir.value, document.dialogForm.view.value);
}
fbObj.prototype.openSelectedFolder = function () {
var forms = new wproForms();
var form = document.dialogForm
if (form.elements['folders']) {
if (form.elements['folders'].length) {
if (forms.getSelectedCheckbox(form.elements['folders']).length) {
node = form.elements['folders'][forms.getSelectedCheckbox(form.elements['folders'])[0]];
this.goToFolder(node.parentNode.parentNode);
}
} else {
node = form.elements['folders'];
this.goToFolder(node.parentNode.parentNode);
}
}
}
fbObj.prototype.insertSelectedFile = function () {
var forms = new wproForms();
var form = document.dialogForm
if (form.elements['files']) {
if (form.elements['files'].length) {
if (forms.getSelectedCheckbox(form.elements['files']).length) {
node = form.elements['files'][forms.getSelectedCheckbox(form.elements['files'])[0]];
node.parentNode.parentNode.ondblclick();
}
} else {
node = form.elements['files'];
node.parentNode.parentNode.ondblclick();
}
}
}
var FB = new fbObj();