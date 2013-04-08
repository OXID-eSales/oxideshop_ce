function spellcheckerObj () {
	this.classNames = /^(wproSpellcheckerError|wproSpellcheckerFixed|wproSpellcheckerCurrent|wproSpellcheckerInput)$/i;
	
	this.learntWords = '';
	this.url = dialog.editorLink('core/plugins/wproCore_spellchecker/checkSpelling.php');
	
	this.init = function () {
		this.substitutions = 0;
		this.resultsElements = [];
		this.current = -1;
		this.form = document.dialogForm;
		this.suggestions = this.form.suggestions;
		this.changeTo = this.form.changeTo;
		this.foundMessage = document.getElementById('foundMessage');
		this.resultsFrame = document.getElementById('spellcheckerResultsFrame_'+RAND);
		if (this.resultsFrame.contentWindow) {
			this.resultsWindow = this.resultsFrame.contentWindow;
		} else {
			this.resultsWindow= window.frames['spellcheckerResultsFrame_'+RAND];
		}
		this.resultsDocument = this.resultsWindow.document;
		
		dialog.editor.hideGuidelines();
		var content = dialog.editor.editDocument.body.innerHTML;
		if (dialog.editor._guidelines) dialog.editor.showGuidelines();
		
		// escape characters
		//content = content.replace(/\[WPSCODE([\s\S]*?)WPSCODE(\]=""|=""|\]|)/gi, function (x) {
			//c =  x.replace(/\[WPSCODE([\s\S]*?)WPSCODE[\s\S]*/i, '$1');
			//return x.replace(/(\[WPSCODE)[\s\S]*?(WPSCODE[\s\S]*)/i, '$1'+(escape(c))+'$2');
		//});
		//content = WPro.escapeScriptTags(content);
		//content = WPro.escapeCharacters(content);
		//content = WPro.unescapeScriptTags(content);
		
		this.form.target = 'spellcheckerResultsFrame_'+RAND;
		this.form.onsubmit = '';
		this.form.method = 'post';
		this.form.action = this.url;
		this.form.bodyHTML.value = content;
		//this.form.headHTML.value = dialog.editor.getStyles();
		this.form.elements['referrer'].value = document.location.toString();
		//this.form.dictionary.value = 'en_GB';
		this.disableButtons();
		this.form.submit();
	}
	
	this.initResults = function () {
		this.form.target = '_self';
		this.form.onsubmit = 'return dialog.doFormSubmit();';
		this.form.method = 'post';
		this.form.action = ''
		
		if (this.resultsWindow.dictionaries) {
			/*var select = this.form.dictionary;
			var n = select.length
			for (var i = n; --i >= 0;) {
				select.remove(i);
			}
			var dicts = this.resultsWindow.dictionaries;
			var curDict = this.resultsWindow.current_dict;
			var n = dicts.length-1;
			for (var i = 0; i < n; ++i) {
				var option = document.createElement("option");
				option.value = dicts[i];
				if (dicts[i] == curDict) {
					option.selected = true;
				}
				option.appendChild(document.createTextNode(dicts[i]));
				select.appendChild(option);
			}*/
			
			var select = this.form.dictionary;
			var n = select.length
			for (var i = n; --i >= 0;) {
				select.remove(i);
			}
			var dicts = this.resultsWindow.dictionaries;
			var curDict = this.resultsWindow.current_dict;
			var n = dicts.length-1;
			//for (var i = 0; i < n; ++i) {
			var x;
			for (x in dicts) {
				var option = document.createElement("option");
				option.value = x;
				if (x == curDict) {
					option.selected = true;
				}
				option.appendChild(document.createTextNode(dicts[x]));
				select.appendChild(option);
			}
			
		}
		
		var spans = this.resultsWindow.document.getElementsByTagName('SPAN');
		var n = spans.length
		j = 0;
		for (var i = 0; i < n; ++i) {
			if (this.classNames.test(spans[i].className)) {
				spans[i].onclick=wordClicked;
				spans[i].__wpsp_index = j;
				//spans[i].__wpsp_original = spans[i].firstChild.data;
				this.resultsElements[j] = spans[i];
				j++;
			}
		}
		if (j == 0) {
			this.noErrors();
			return;
		}
		this.findNext();
	}
	
	this.disableButtons = function (enable) {
		if (enable === false) {
			enable = false;
		} else {
			enable = true;
		}
		this.form.ignore.disabled = enable;
		this.form.ignoreAll.disabled = enable;
		this.form.learn.disabled = enable;
		this.form.replace.disabled = enable;
		this.form.replaceAll.disabled = enable;
	}
	
	this.finished = function() {
		alert(strComplete);
		this.disableButtons();
		dialog.doFormSubmit();
	}
	
	this.noErrors = function () {
		this.disableButtons();
		alert(strNoneFound);
	}
	
	this.buildHTML = function () {
		var n = this.resultsElements.length;
		for (var i = 0; i < n; ++i) {
			var elm = this.resultsElements[i];
			elm.parentNode.removeChild(elm.nextSibling);
			elm.parentNode.insertBefore(elm.firstChild, elm);
			elm.parentNode.removeChild(elm);
		}
		
		var html = this.resultsWindow.document.body.innerHTML;
		
		var tempregexp = new RegExp("<[^>]+>", "g")
		var results = html.match(tempregexp);
		if (results) {
			var rl = results.length;
			for (var i=0; i < rl; i++) {
				var original = results[i];
				results[i]=results[i].replace(/ wproDefanged_(on[a-z]+|data|href|src|action|longdesc|profile|usemap|background|cite|classid|codebase)/gi, " $1");
				html = html.replace(original, results[i]);
			}
		}
		html = html.replace(/<!--\[WPDEFANGED/gi, '').replace(/WPDEFANGED\]-->/gi, '').replace(/WPDEFANGED\]--&gt;/gi, '').replace(/--WPDEFANGED/gi, '-->');
		
		return html;
	}
	
	this.findNext = function(dontIncrement) {
		if (!dontIncrement) {
			this.current++;
		}
		if (this.current >= this.resultsElements.length) {
			this.finished();
		}
		if (this.resultsElements[this.current]) {
			var elm = this.resultsElements[this.current];
			if (elm.className == 'wproSpellcheckerError') {
				this.wordClicked(elm, true);
			} else {
				this.findNext();
			}
		}
	}
	
	this.wordClicked = function (elm, doScroll) {
		this.disableButtons(false);
		if (this.resultsElements[this.current]) {
			if (this.resultsElements[this.current].className == 'wproSpellcheckerCurrent') {
				this.resultsElements[this.current].className = 'wproSpellcheckerError';
			}
		}
		var word = elm.firstChild.data;
		var select = spellchecker.suggestions
		var n = select.length
		for (var i = n; --i >= 0;) {
			select.remove(i);
		}
		var sval = elm.nextSibling.value
		if (sval) {
			if (sval.length > 0) {
				var suggestions = sval.split(/, /);
				var n = suggestions.length
				for (var i = 0; i < n; ++i) {
					var option = document.createElement("option");
					option.value = suggestions[i];
					option.appendChild(document.createTextNode(suggestions[i]));
					select.appendChild(option);
				}
				var changeTo = suggestions[0];
			} 
		} else {
			var option = document.createElement("option");
			option.value = '';
			//var span = document.createElement('SPAN')
			//span.innerHTML = strNoSuggestions
			//option.appendChild(span);
			option.innerHTML = strNoSuggestions
			select.appendChild(option);
			var changeTo = word;
		}
		this.changeTo.value = changeTo;
		this.current = elm.__wpsp_index;
		elm.className = 'wproSpellcheckerCurrent';
		this.foundMessage.firstChild.data = word;
		this.scrollToElement (elm);
	}
	
	this.scrollToElement = function (elm) {
		var scrollTop = this.resultsWindow.document.body.scrollTop + this.resultsWindow.document.documentElement.scrollTop
		var scrollLeft = this.resultsWindow.document.body.scrollLeft + this.resultsWindow.document.documentElement.scrollLeft
		var frameHeight = this.resultsFrame.offsetHeight
		//scrollBottom = scrollBottom.replace(/px/i, '')
		//var frameHeight = scrollBottom
		scrollBottom = scrollTop + frameHeight
		
		var pos = WPro.getElementPosition(elm)
		if (pos['top'] > scrollBottom - 100) {
			this.resultsWindow.scrollTo(pos['left'] - 100, pos['top'] - frameHeight + 100)
		} else {
			this.resultsWindow.scrollBy(pos['left'] - scrollLeft - 100, 0)
		}
	}
	
	
	this.replace = function () {
		if (this.resultsElements[this.current]) {
			this.resultsElements[this.current].firstChild.data = this.changeTo.value
			this.resultsElements[this.current].className = 'wproSpellcheckerFixed';
			this.substitutions ++;
		}
		this.findNext();
		document.dialogForm.ok.disabled = false;
	}
	
	this.replaceAll = function () {
		var n = this.resultsElements.length;
		for (var i = 0; i < n; ++i) {
			if (this.resultsElements[i].className == 'wproSpellcheckerError' && this.resultsElements[i].firstChild.data == this.resultsElements[this.current].firstChild.data) {
				this.resultsElements[i].firstChild.data = this.changeTo.value
				this.resultsElements[i].className = 'wproSpellcheckerFixed';
				this.substitutions ++;
			}
		}
		this.replace();
		document.dialogForm.ok.disabled = false;		
	}
	
	this.ignore = function () {
		if (this.resultsElements[this.current]) {
			this.resultsElements[this.current].className = 'wproSpellcheckerFixed';
			this.findNext();
		}
	}
	
	this.ignoreAll = function () {
		var n = this.resultsElements.length;
		for (var i = 0; i < n; ++i) {
			if (this.resultsElements[i].className == 'wproSpellcheckerError' && this.resultsElements[i].firstChild.data == this.resultsElements[this.current].firstChild.data) {
				this.resultsElements[i].className = 'wproSpellcheckerFixed';
			}
		}
		this.ignore();
	}
	
	this.learn = function () {
		if (this.resultsElements[this.current]) {
			var word = this.resultsElements[this.current].firstChild.data;
			this.learntWords += WPro.quoteMeta(word)+',';
			this.ignoreAll();
		}
	}
}
spellchecker = new spellcheckerObj();

function wordClicked() {
	spellchecker.wordClicked(this);
}

function finishedSpellChecking() {
	spellchecker.initResults();
	dialog.hideLoadMessage();
}

var baseHeight = 0;
dialog.events.addEvent(window, 'load', wproSetBaseSize);
function wproSetBaseSize () {
	var winDim = wproGetWindowInnerHeight();
	baseHeight = winDim['height'];
}
function resizeHeight() {
	var currentHeight=0;
	if (window.innerHeight) {
		currentHeight = window.innerHeight;
	} else if (document.documentElement.clientHeight) {
		currentHeight=document.documentElement.clientHeight;
	} else {
		currentHeight=document.body.clientHeight;
	}
	var winDim = wproGetWindowInnerHeight();
	currentHeight = winDim['height'];
	
	if (currentHeight>400) {
		var amount = currentHeight-baseHeight;
		baseHeight=currentHeight;
		
		var iframe = this.resultsFrame;
		var iframeHeight = iframe.offsetHeight;
		iframeHeight = iframeHeight+amount;
		iframe.style.height=iframeHeight+'px';
		
		var rightCol = document.getElementById('rightCol');
		var rightColHeight = parseInt(rightCol.style.marginTop.toString().replace(/px/i, ''));
		var rightColHeight = rightColHeight +amount;
		rightCol.style.marginTop = rightColHeight+'px';
		
	}
}
dialog.events.addEvent(window, 'resize', resizeHeight);	

function formAction () {
	if (spellchecker.substitutions > 0) {
		var UDBeforeState = dialog.editor.history.pre();
		var str = spellchecker.buildHTML();
		WPro.setInnerHTML(dialog.editor.editDocument.body, str);
		if (dialog.editor._guidelines) dialog.editor.showGuidelines();
		dialog.editor.history.post(UDBeforeState);
	}
	var c = new wproCookies();
	var val = c.readCookie('wproLearntWords');
	val = val + spellchecker.learntWords;
	c.writeCookie('wproLearntWords', val, null, '/');	
	dialog.close();
	return false;
}