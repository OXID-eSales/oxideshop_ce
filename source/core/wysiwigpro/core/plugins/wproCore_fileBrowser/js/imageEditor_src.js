
var changesMade = false;

var saveDone = true;

function displayMessageBox (innerHTML, width, height) {
	var box = document.getElementById('messageBox');
	box.innerHTML = innerHTML;
	var left = 0;
	var top = 0;
	
	var winDim = wproGetWindowInnerHeight();
	var availHeight = winDim['height'];
	var availWidth = winDim['width'];
	
	availHeight -= 40;
	
	
	if (width < availWidth) {
		left = (availWidth/2)-(width/2);
			
	};
	
	if (height < availHeight) {
		
		top = (availHeight/2)-(height/2);
		
	};
	
	box.style.width = width+'px';
	box.style.height = height+'px';
	
	box.style.top = top+'px';
	box.style.left = left+'px';

	box.style.display = 'block';
}
function hideMessageBox() {
	var box = document.getElementById('messageBox');
	box.innerHTML = '';
	box.style.display = 'none';
}
function getButtonHTML(b) {
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
function initSaveAs() {
	dialog.showLoadMessage();
	ajax_editImage(folderID, folderPath, editorID, 'initSaveAs');
}
function showSaveAs(suggest, extension) {
	
	dialog.hideLoadMessage();
	
	var str = '<div class="bodyHolder"><div>'+strSaveAs+'<br /><br /><input size="40" type="text" id="saveAsName" name="saveAsName" value="'+suggest+'" />'+extension+'</div></div>';
	
	str+=getButtonHTML([{'onclick':'return saveAsAction()', 'type':'submit','name':'ok','value':strOK},{'onclick' : 'hideMessageBox()','type':'button','name':'cancel','value':strCancel}]);
	
	displayMessageBox(str, 400, 120);
	
	document.dialogForm.newFolderName.focus();
}
function saveAsAction() {
	
	dialog.showLoadMessage();
	ajax_editImage(folderID, folderPath, editorID, 'saveAs', document.dialogForm.saveAsName.value);
	return false;
}

function sizeChanged(value) {
	if (value!='custom') {
		document.getElementById('hiddenResize').style.display = 'none';
		var arr = value.split('x');
		var form = document.dialogForm;
		form.maxWidth.value=arr[0];
		form.maxHeight.value=arr[1];
	} else {
		document.getElementById('hiddenResize').style.display = 'inline';
	}
}

function imageResize() {
	var form = document.dialogForm;
	if (form.maxWidth && form.maxHeight) {
		var maxWidth = form.maxWidth.value;
		var maxHeight = form.maxHeight.value;
		if (isNaN(maxWidth)&&maxWidth.length>0) {
			dialog.alertWrongFormat();
			dialog.focus();
			form.maxWidth.value='';
			form.maxWidth.focus();
			return false;
		}
		if (isNaN(maxHeight)&&maxHeight.length>0) {
			dialog.alertWrongFormat();
			dialog.focus();
			form.maxHeight.value='';
			form.maxHeight.focus();
			return false;
		}
	}
	dialog.showLoadMessage();
	document.getElementById('theImage').src = 'core/images/spacer.gif';
	ajax_editImage(folderID, folderPath, editorID, 'resize', {'width':maxWidth, 'height':maxHeight});
}
function rotateRight() {
	var doit = true;
	var fname = document.getElementById('theImage').src.toString();
	if (fname.match(/\.jpg/gi) || fname.match(/\.jpeg/gi)) {
		doit = confirm(strRotateWarning);
	}	
	if (doit) {
		dialog.showLoadMessage();
		document.getElementById('theImage').src = 'core/images/spacer.gif';
		ajax_editImage(folderID, folderPath, editorID, 'rotate', 90);
	}
}
function rotateLeft() {
	var doit = true;
	var fname = document.getElementById('theImage').src.toString();
	if (fname.match(/\.jpg/i) || fname.match(/\.jpeg/i)) {
		doit = confirm(strRotateWarning);
	}	
	if (doit) {
		dialog.showLoadMessage();
		document.getElementById('theImage').src = 'core/images/spacer.gif';
		ajax_editImage(folderID, folderPath, editorID, 'rotate', 270);
	}
}

function editFinished(file, temp, w, h, changes) {
	
	dialog.hideLoadMessage();
		
	width = w;
	height = h;
	image = file;
	tempFile = temp;
	changesMade = changes;
	saveDone = false;
	
	document.getElementById('imageName').innerHTML = image;
	document.getElementById('imageWidth').innerHTML = width;
	document.getElementById('imageHeight').innerHTML = height;
			
	document.getElementById('theImage').src = baseURL + (temp?temp:file) + '?' + Math.random();
	document.getElementById('theImage').width = width;
	document.getElementById('theImage').height = height;
	
	if ((375 - height)/2 > 0) {
		var margin = ((375 - height)/2);
	} else {
		var margin = 0;
	}
	
	document.getElementById('theImage').style.marginTop = margin + 'px';
	
	if (document.dialogForm.save) {	
		if (changes) {
			document.dialogForm.save.disabled = false;
			//document.dialogForm.saveAs.disabled = false;
		} else {
			document.dialogForm.save.disabled = true;
			//document.dialogForm.saveAs.disabled = false;
		}
	}
	
}
function confirmClose(d) {
	exitDone = true;
	if (changesMade && !saveDone) {
		if (confirm(strSaveChanges)) {
			dialog.showLoadMessage();
			parentWindow.FB.editImageFinished(editorID, 'saveAndExit', (d ? dialog : false));
		} else {
			dialog.showLoadMessage();
			parentWindow.FB.editImageFinished(editorID, 'exitWithoutSaving', (d ? dialog : false));
		}
	} else {
		dialog.showLoadMessage();
		parentWindow.FB.editImageFinished(editorID, 'exitWithoutSaving', (d ? dialog : false));
	}
}

function nextImage () {
	if (changesMade && !saveDone) {
		if (confirm(strSaveChanges)) {
			dialog.showLoadMessage();
			document.getElementById('theImage').src = 'core/images/spacer.gif';
			ajax_editImage(folderID, folderPath, editorID, 'saveThenNext');
		} else {
			dialog.showLoadMessage();
			document.getElementById('theImage').src = 'core/images/spacer.gif';
			ajax_editImage(folderID, folderPath, editorID, 'next');
		}
	} else {
		dialog.showLoadMessage();
		document.getElementById('theImage').src = 'core/images/spacer.gif';
		ajax_editImage(folderID, folderPath, editorID, 'next');
	}
}
function previousImage () {
	if (changesMade && !saveDone) {
		if (confirm(strSaveChanges)) {
			dialog.showLoadMessage();
			document.getElementById('theImage').src = 'core/images/spacer.gif';
			ajax_editImage(folderID, folderPath, editorID, 'saveThenPrevious');
		} else {
			dialog.showLoadMessage();
			document.getElementById('theImage').src = 'core/images/spacer.gif';
			ajax_editImage(folderID, folderPath, editorID, 'previous');
		}
	} else {
		dialog.showLoadMessage();
		document.getElementById('theImage').src = 'core/images/spacer.gif';
		ajax_editImage(folderID, folderPath, editorID, 'previous');
	}
}
function unloadDialog () {
	if (exitDone) return;
	confirmClose(false);
}
var exitDone = false;
function saveFinished() {
	dialog.hideLoadMessage();
	changesMade = false;
	saveDone = true;
	document.dialogForm.save.disabled = true;
	//document.dialogForm.saveAs.disabled = true;
}
function formAction () {
	ajax_editImage(folderID, folderPath, editorID, 'save');
	return false;
}