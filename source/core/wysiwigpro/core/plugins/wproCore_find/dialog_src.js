var fr = new wproFindAndReplace();
var initSearch = false;
function initFind() {
	var selectedText = dialog.editor.getSelectedText();
	if (selectedText.length > 1) {
		var form = document.dialogForm
		form.strSearch.value = selectedText;
		initSearch = true;
		dialog.events.addEvent(window, 'load', checkEnableButtons);
	}
	dialog.focus();
	dialog.hideLoadMessage();
}
function checkEnableButtons() {
	var form = document.dialogForm
	if (form.strSearch.value.length >= 1) {
		form.replaceButton.disabled = false;
		form.replaceAllButton.disabled = false;
		form.findNextButton.disabled = false;
	} else {
		form.replaceButton.disabled = true;
		form.replaceAllButton.disabled = true;
		form.findNextButton.disabled = true;
	}
}
function newSearch() {
	initSearch = false;
	fr.newSearch();
}
function findNext() {
	if (initSearch) newSearch();
	var form = document.dialogForm
	var matchCase = form.matchCase.checked ? true : false;
	var wholeWords = form.wholeWords.checked ? true : false;
	var strSearch = form.strSearch.value;
	if (!fr.findNext(strSearch,matchCase,wholeWords)) {
		//if (initSearch==true) {
			//initSearch = false;
			//newSearch();
			//findNext();
		//} else {
			alert(strFinishedSearching);
			//newSearch();
			initSearch = true;
		//}
	}
}
function replaceText() {
	if (initSearch) newSearch();
	// if no selection or selection doesn't match then move on
	var form = document.dialogForm
	var strSearch = form.strSearch.value;
	var strReplace = form.strReplace.value;
	var matchCase = form.matchCase.checked ? true : false;
	var wholeWords = form.wholeWords.checked ? true : false;
	if (!fr.replaceWith(strSearch,strReplace,matchCase,wholeWords)) {
		//if (initSearch==true) {
			//initSearch = false;
			//newSearch();
			//findNext();
		//} else {
			alert(strFinishedSearching);
			initSearch = true;
			//newSearch();
		//}
	}
}
function replaceAllText(init) {
	if (init!=false) {
		init = true;
	} else {
		init = false
	}
	if (init) newSearch();
	var form = document.dialogForm
	var strSearch = form.strSearch.value;
	var strReplace = form.strReplace.value;
	var matchCase = form.matchCase.checked ? true : false;
	var wholeWords = form.wholeWords.checked ? true : false;
	fr.replaceAll(strSearch,strReplace,matchCase,wholeWords,init);
	var matches = fr.replaceCount;
	//if (initSearch==true) {
		//initSearch = false;
		//newSearch();
		//replaceAllText(false);
	//} else {
		alert(strFinishedSearching + ' ' + strReplacements.replace(/##num##/, matches));
		initSearch = true;
		//newSearch();
	//}
}
function formAction () {
	return false;	
}