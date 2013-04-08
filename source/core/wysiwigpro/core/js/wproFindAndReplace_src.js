function wproFindAndReplace() {
	this.replaceCount = 0;
	this.storedRange = null;
}
wproFindAndReplace.prototype.newSearch = function () {
	this.storedRange = null;
	if ( window.find ) {
		//currentEditor.editWindow.document.execCommand('selectall', false, null);
		//currentEditor.callFormatting('selectall');
		//var range = currentEditor.editWindow.getSelection().getRangeAt(0);
		//range.setStart(currentEditor.editWindow.document.body, 0);
		//range.collapse(true);
		//range.select();
		
		var rngCaret = dialog.editor.editDocument.createRange()
		rngCaret.setStart(currentEditor.editWindow.document.body, 0);
		rngCaret.collapse(true)
		sel = dialog.editor.editWindow.getSelection()
		sel.removeAllRanges()
		sel.addRange(rngCaret)
		
		//sel;
	} else {
		var theRange = currentEditor.editDocument.body.createTextRange();
		theRange.collapse(true);
		theRange.select();
	}
	//currentEditor.editWindow.document.execCommand('selectall', false, null);
	//var range = currentEditor.selAPI.getRange();
	//range.collapse(true);
	//range.select();
	
	//currentEditor.selAPI.removeAllRanges();
	
	//currentEditor.selAPI.removeAllRanges();
}
/* replaces the current selection with the given value */
wproFindAndReplace.prototype.replaceWith = function (strSearch, strReplace, matchCase, wholeWords) {
	if (!matchCase) matchCase = false;
	if (!wholeWords) wholeWords = false;
	
	var selectedText = dialog.editor.getSelectedText();
	
	if ((matchCase && selectedText != strSearch || !matchCase && selectedText.toLowerCase() != strSearch.toLowerCase()) || selectedText.length == 0) {
		return this.findNext(strSearch, matchCase, wholeWords);
	} else if (selectedText.length >= 1) {
		if (strReplace.length >= 1) {
			currentEditor.insertAtSelection(wproHtmlSpecialChars(strReplace));
		} else {
			//currentEditor.editDocument.execCommand('delete', false, null);
			currentEditor.callFormatting('delete');
		}
		/*
		// to prevent an enless loop make certain that this is a different range selected.
		if (WPro.isIE) {
			if (dialog.editor.editDocument.selection.type != 'Control') {
				var range = currentEditor.editDocument.selection.createRange();
				if (this.storedRange) {
					if (range.compareEndPoints('StartToStart',this.storedRange)==0 && range.compareEndPoints('EndToEnd',this.storedRange)==0) {
						this.replaceCount --;
						return false;
					}
				}
				this.storedRange = range.duplicate();
			}
		} else {
			var range = dialog.editor.editDocument.getSelection().getRangeAt(0);
			if (this.storedRange) {
				if (range.compareBoundaryPoints(START_TO_START,this.storedRange) && range.compareBoundaryPoints(END_TO_END,this.storedRange)) {
					this.replaceCount --;
					return false;
				}
			}
			this.storedRange = range.cloneRange();
		}*/
		
	}

	return this.findNext(strSearch, matchCase, wholeWords, false);
	
}
wproFindAndReplace.prototype.findNext = function (strSearch, matchCase, wholeWords, allowNewSearches) {
	if (allowNewSearches==undefined) {
		allowNewSearches = true;
	}
	
	//var strReplace = form.strReplace.value;
	if (!matchCase) matchCase = false;
	if (!wholeWords) wholeWords = false;
	if (dialog.editor.getSelectedText().length == 0 && allowNewSearches) {
		this.newSearch();
	}
	if ( window.find ) {
		//window.find('search query', caseSensitive, searchBackwards, wrapAround, wholeWord, searchInFrames, showDialog)
		//If some text is selected already (previous search or if they have selected it)
		//make that the text range. Then move to the end of it to search beyond it
		
		/*if (dialog.editor.getSelectedText().length == 0 && allowNewSearches) {
			this.newSearch();
		}*/
		var found = dialog.editor.editWindow.find( strSearch, matchCase, false, false, wholeWords, false, false);
		
	} else {
		/*
		if( dialog.editor.editDocument.selection && dialog.editor.editDocument.selection.type != 'None' && dialog.editor.editDocument.selection.type != 'Control' ) {
			//If some text is selected already (previous search or if they have selected it)
			//make that the text range. Then move to the end of it to search beyond it
			var theRange = currentEditor.editDocument.selection.createRange();
			theRange.collapse( false );
		} else {
			//If no text is selected, start from the start of the document
			var theRange = currentEditor.editDocument.body.createTextRange();
		}
		/*if (dialog.editor.getSelectedText().length == 0 && allowNewSearches) {
			this.newSearch();
		}*/
		var theRange = currentEditor.editDocument.selection.createRange();
		theRange.collapse( false );
		
		//find the next occurrence of the chosen string
		var flags = (matchCase ? 4 : 0) + (wholeWords ? 2 : 0)
		var found = theRange.findText(strSearch, 1000000000, flags);
		if (found) theRange.select();
	}
	if (found) {
		return true;
	} else {
		return false;
	}
}
wproFindAndReplace.prototype.replaceAll = function (strSearch, strReplace, matchCase, wholeWords, init) {
	if (!matchCase) matchCase = false;
	if (!wholeWords) wholeWords = false;
	if (init!=false) {
		this.replaceCount = 0;
		this.storedRange = null;
	}
	if (!this.replaceWith(strSearch, strReplace, matchCase, wholeWords)) {
		return false;
	} else {
		this.replaceCount ++;
		return this.replaceAll (strSearch, strReplace, matchCase, wholeWords, false);
	}
}