var CURRENT_COLOR = null;
var okColor = /^#[0-9abcdef]+$/i;
function initColorPicker () {
	if (WPro) {
		if (WPro.currentColorPicker) {
			currentColorPicker = WPro.currentColorPicker;
		}
	}
	var b = document.getElementsByTagName('A');
	var n = b.length;
	for (var i=0; i<n; i++) {
		if (b[i].parentNode.className == 'colorTable') {
			b[i].style.backgroundColor = b[i].getAttribute('title');
				if (b[i].getAttribute('title')==document.dialogForm.selectedColor.value) {
					if (!CURRENT_COLOR) CURRENT_COLOR = b[i];
					b[i].style.borderColor = '#ffffff';
				}
			//b[i].onmouseout = function () {
				//document.getElementById('mOverColorText').innerHTML = '&nbsp;';
				//document.getElementById('mOverColorDisplay').style.backgroundColor = '';
			//}
			//var c = t[i].getElementsByTagName('TD');
			//var nc = c.length
			//for (var j=0;j<nc;j++) {
				b[i].onclick = selectColor;
				b[i].onmouseover = function () {
					this.style.borderColor = '#ffffff';
					//var c = 
					if (this.getAttribute('title')) {
						document.getElementById('mOverColorText').firstChild.data = this.getAttribute('title');
					} else {
						document.getElementById('mOverColorText').innerHTML = '&nbsp;';
					}
					document.getElementById('mOverColorDisplay').style.backgroundColor= this.style.backgroundColor;
				}
				b[i].onfocus = b[i].onmouseover
				b[i].onmouseout = function () {
					if (CURRENT_COLOR) {
						if (CURRENT_COLOR != this) {
							this.style.borderColor = '#000000';
						}
					} else {
						this.style.borderColor = '#000000';
					}
					document.getElementById('mOverColorText').innerHTML = '&nbsp;';
					document.getElementById('mOverColorDisplay').style.backgroundColor= '';
				}
				b[i].onblur = b[i].onmouseout
			//}
		}
	}
	
	//WPro.addEvent('keypress', );
	
	dialog.hideLoadMessage();

}
function selectColor() {
	if (CURRENT_COLOR) {
		if (CURRENT_COLOR==this) {
			return;
		}
	}
	var color = this.getAttribute('title');
	if (!okColor.test(color.toString().trim())) {
		color='';
	}
	selectColorAction (color);
	if (CURRENT_COLOR) {
		CURRENT_COLOR.style.borderColor = '#000000';
	}
	CURRENT_COLOR = this;
	this.blur();
	document.dialogForm.ok.focus();
}
function selectColorAction (color) {
	if (!okColor.test(color.toString().trim())) {
		color='';
	}
	try {
		document.getElementById('mOverColorDisplay').style.backgroundColor = color;
	}catch(e){
		document.getElementById('mOverColorDisplay').style.backgroundColor = '';
		color = '';
	}
	if (color) {
		document.getElementById('mOverColorText').innerHTML = color;
	} else {
		document.getElementById('mOverColorText').innerHTML = '&nbsp;';
	}
	document.dialogForm.selectedColor.value = color;
	document.getElementById('selectedColorDisplay').style.backgroundColor = color;
}
function formAction () {
	var v = document.dialogForm.selectedColor.value
	if (!okColor.test(v)) {
		v = '';
	}
	currentColorPicker.setColor(v);
	if (v.length > 1) {
		var c = new wproCookies();
		var val = c.readCookie('wproRecentlyUsedColors');
		v2 = v.toString();
		val = v2+'|'+val;
		c.writeCookie('wproRecentlyUsedColors', val, null, '/');
	}
	dialog.close();
	return false;
}