// JavaScript Document
function initMoveCopy() {
	// add the selected files to the form...
	var forms = new wproForms();
	var form = parentWindow.document.dialogForm
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
		var str = d.join('/');
		document.dialogForm.files.value = str;
	} else {
		dialog.close();	
	}
}
function switchPane() {
	return true;	
}
function hideMessageBox() {
	return true;	
}

var FB = new Object();
FB.loadFolder = function(id) {
	if (id != document.dialogForm.destFolderID.value) {
		if (dialog.doFormSubmit()) {
			document.dialogForm.destFolderID.value = id;
			document.dialogForm.destFolderPath.value = '';
			document.dialogForm.submit();
		}
	}
	return true;	
}
FB.buildLookInSelect = function(id) {
	return true;	
}

function selectFolder(path) {
	document.dialogForm.destFolderPath.value = path;
}

function formAction () {
	
	document.dialogForm.destFolderPath.value = Base64.encode(document.dialogForm.destFolderPath.value);
	document.dialogForm.srcFolderPath.value = Base64.encode(document.dialogForm.srcFolderPath.value);
	
	return true;	
}