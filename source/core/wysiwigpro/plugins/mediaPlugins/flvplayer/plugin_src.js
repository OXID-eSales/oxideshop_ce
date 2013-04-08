// JavaScript Document
function wproFilePlugin_flvplayer () {
	
	/* populates the local options form with data gathered from the server */
	this.populateLocalOptions = function (data, prefix) {
		var form = document.dialogForm;
		if (data['width']) {
			form.elements[prefix+'width'].value = data['width'];
			form.elements[prefix+'widthUnits'].value = '';
		} else {
			form.elements[prefix+'width'].value = 320;
			form.elements[prefix+'widthUnits'].value = '';
		}
		if (data['height']) {
			form.elements[prefix+'height'].value = data['height'];
			form.elements[prefix+'heightUnits'].value = '';
		} else {
			form.elements[prefix+'height'].value = 0;
			form.elements[prefix+'heightUnits'].value = '';
		}
		
		
		if (form.elements[prefix+'playlist']) {
			//form.elements[prefix+'width'].value = parseInt(form.elements[prefix+'width'].value) + parseInt(form.elements[prefix+'playlistWidth'].value)
			form.elements[prefix+'playlist'].checked = data['playlist'] ? true : false;
			if (data['playlist']) {
				this.updateWidth(prefix, true);
			} else {
				//this.updateWidth(prefix, false);
			}
		}
		
		//this.updateWidth(prefix);
		this.updateHeight(prefix);
		
	}
	
	/* internal helper function */
	this._getOptions = function (prefix, o) {
		var form = document.dialogForm;
		if (!o) o = {}
		if (!o['object']) o['object'] = {};
		if (!o['embed']) o['embed'] = {};
		if (!o['param']) o['param'] = {};
		if (!o['object']['id']) {
			o['object']['id'] = 'flvp'+(new Date().getTime());	
		}
		if (!o['object']['name']) {
			o['object']['name'] = o['object']['id'];
		}
		if (!o['embed']['id']) {
			o['embed']['id'] = o['object']['id'];
		}
		if (!o['embed']['name']) {
			o['embed']['name'] = o['object']['id'];
		}
		if (form.elements[prefix+'width']) {
			o['embed']['width'] = form.elements[prefix+'width'].value+form.elements[prefix+'widthUnits'].value;
			o['object']['width'] = form.elements[prefix+'width'].value+form.elements[prefix+'widthUnits'].value;
		}
		if (form.elements[prefix+'height']) {
			o['object']['height'] = form.elements[prefix+'height'].value+form.elements[prefix+'heightUnits'].value;
			o['embed']['height'] = form.elements[prefix+'height'].value+form.elements[prefix+'heightUnits'].value;
		}
		return o;
	}
	
	this._fixRelative = function (url) {
		var base = dialog.domain+dialog.URL+'media/';
		
		url = url.replace(/^\//gi, dialog.domain+'/');
		url = url.replace(/^([^#][^:"]*)$/gi, base+'$1');
		
		while(url.match(/([^:][^\/])\/[^\/]*\/\.\.\//i)) {
			url = url.replace(/([^:][^\/])\/[^\/]*\/\.\.\//i, '$1/');
		}
		url = url.replace(/\/\.\.\//gi, '/');
		return dialog.urlFormatting(url);
	}
	
	/* inserts a locally selected object */
	this.insertLocal = function(prefix, data) {
		if (!document.dialogForm.URL.value) return;
		var form = document.dialogForm;
		if (!data) data = {};
		
		var o = this._getOptions(prefix, data);
		
		var url = dialog.urlFormatting(dialog.domain+dialog.URL+'media/player.swf');
		
		var file = form.URL.value;
		
		// check format, if video format, .flv, .h264, .mp4
		// and links are to be relative then make relative to the player swf
		var e = FB.getExtension(file);
		if ((e=='.flv'||e=='.h264'||e=='.mp4') && dialog.urlFormat=='relative') {
			// make absolute
			file = dialog.urlFormatting(file, true);
			
			var base = dialog.domain+dialog.URL+'media/';
			var loc = dialog.domain;
			
			// make relative to base
			// allow for www and non www
			if (loc.match(/^http(s|)\:\/\/www\./i)) {
				var domainRegex = new RegExp('^'+( dialog.quoteMeta(loc).replace(/^(http(s|)\\\:\\\/\\\/)www\\\./i, '$1(www\\.|)') ) +'[^\/]*(|/)', 'gi');
			} else {
				var domainRegex = new RegExp('^'+( dialog.quoteMeta(loc).replace(/^(http(s|)\\\:\\\/\\\/)/i, '$1(www\\.|)') ) +'[^\/]*(|/)', 'gi');
			}
			file = file.replace(domainRegex, '$2');
			
			// compute base URL without the domain
			var b = base.replace(domainRegex, '$2');
			// strip base url path from URL
			var r = new RegExp('^'+dialog.quoteMeta(b), 'gi');
			file = file.replace(r, '');
			// if URL begins with a / then add the ../
			if (file.substr(0,1)=='/') {
				file = file.substr(1);
				var c = b.match(/\//g);
				for (var i=0;i<c.length;i++) {
					file = '../'+file;
				}
			}
			
		}
		
		var flashvars = 'file=' + file;
		
		if (flashvars.match(/.mp3/) ) {
			flashvars += '&showeq=true';	
		}
		if (form.elements[prefix+'autoplay']) {
			flashvars += '&autostart='+(form.elements[prefix+'autoplay'].checked?'true':'false');
		}
		if (form.elements[prefix+'loop'] && form.elements[prefix+'loop'].checked) {
			flashvars += '&repeat=always';
		}
		if (form.elements[prefix+'controller']) {
			flashvars += '&controlbar='+(form.elements[prefix+'controller'].checked?'bottom':'none');
		}
		if (form.elements[prefix+'playlist']) {
			if (form.elements[prefix+'playlist'].checked==true) {
				//flashvars += '&displaywidth='+(parseInt(o['object']['width'].replace(/[^0-9]/g,'')) - parseInt(form.elements[prefix+'playlistWidth'].value));
				flashvars += '&playlist=right&playlistsize='+form.elements[prefix+'playlistWidth'].value;
			}
		}
		
		flashvars += '&fullscreen=true';
		
		
		o['object']['classid']="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ;
		o['object']['codebase']="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0";
		
		o['param']['movie'] = url;
		o['param']['flashvars'] = flashvars;
		o['param']['allowfullscreen'] = 'true';
		o['param']['allowscriptaccess'] = 'always';
		
		o['embed']['src'] = url;
		o['embed']['flashvars'] = flashvars;
		o['embed']['allowfullscreen'] = 'true';
		o['embed']['allowscriptaccess'] = 'always';
  		o['embed']['type'] = "application/x-shockwave-flash";

		var s = '';
		if (form.elements[prefix+'style']) {
			s = form.elements[prefix+'style'].value
		}
		
		FB.insertMedia('flvplayer', o, s);
	}
		
	/* inserts an object from a web location */
	this.insertRemote = function (prefix) {
		var data
		if (FB.propertiesPlugin == 'flvplayer' && FB.mediaProperties) {
			data = FB.mediaProperties;
		}
		this.insertLocal(prefix, data);
	}
	
	/* used for local previews */
	this.getPreviewURL = function (u) {
		var url = dialog.editorLink('dialog.php?dialog=wproCore_fileBrowser&action=mediapreview&' + dialog.sid + (dialog.phpsid ? '&' + dialog.phpsid : '') + (dialog.appendToQueryStrings ? '&' + dialog.appendToQueryStrings : '') + '&plugin=flvplayer&url=' + dialog.urlEncode(u));
		return url;	
	}
	
	/* determins if this plugin can edit the selected item's properties */
	this.canPopulate = function () {
		var arr = FB.getMediaProperties();
		if (arr['param']) {
			if (arr['param']['movie']) {
				if (arr['param']['movie'].match('media/player.swf')) return true;
			}
		}
		return false
	}
	
	this.updateHeight = function (prefix, show) {
		var form = document.dialogForm;
		if (/^[0-9]+$/.test(form.elements[prefix+'height'].value)) {
			if (show||form.elements[prefix+'controller'].checked) {
				var v = parseInt(form.elements[prefix+'height'].value) + parseInt(form.elements[prefix+'controllerHeight'].value)
				form.elements[prefix+'height'].value = v;
			} else {
				var v = parseInt(form.elements[prefix+'height'].value) - parseInt(form.elements[prefix+'controllerHeight'].value)
				form.elements[prefix+'height'].value = v;
			}
		}
	}
	this.updateWidth = function (prefix, show) {
		var form = document.dialogForm;
		if (/^[0-9]+$/.test(form.elements[prefix+'width'].value)) {
			if (show||form.elements[prefix+'playlist'].checked) {
				var v = parseInt(form.elements[prefix+'width'].value) + parseInt(form.elements[prefix+'playlistWidth'].value)
				form.elements[prefix+'width'].value = v;
			} else {
				var v = parseInt(form.elements[prefix+'width'].value) - parseInt(form.elements[prefix+'playlistWidth'].value)
				form.elements[prefix+'width'].value = v;
			}
		}
	}
	
	/* populates the properties editor form */
	this.populateProperties = function (prefix) {
		var form = document.dialogForm;
		var o = FB.getMediaProperties();
		if (form.elements[prefix+'width']&&o['object']&&o['object']['width']) {
			form.elements[prefix+'width'].value = String(o['object']['width']).replace(/[^0-9]/g, '');
			if (String(o['object']['width']).match('%')) {
				form.elements[prefix+'widthUnits'].value = '%';
			} else {
				form.elements[prefix+'widthUnits'].value = '';
			}
		}
		if (form.elements[prefix+'height']&&o['object']&&o['object']['height']) {
			form.elements[prefix+'height'].value = String(o['object']['height']).replace(/[^0-9]/g, '');
			if (String(o['object']['height']).match('%')) {
				form.elements[prefix+'heightUnits'].value = '%';
			} else {
				form.elements[prefix+'heightUnits'].value = '';
			}
		}
		
		if (o['param']&&o['param']['flashvars']) {
		
			if (form.elements[prefix+'autoplay']) {
				if (o['param']['flashvars'].match(/[?&]autostart=true/gi)) { 
					form.elements[prefix+'autoplay'].checked = true;
				} else {
					form.elements[prefix+'autoplay'].checked = false;
				}
			}
			if (form.elements[prefix+'loop']) {
				if (o['param']['flashvars'].match(/[?&]repeat=(list|always)/gi)) { 
					form.elements[prefix+'loop'].checked = true;
				} else {
					form.elements[prefix+'loop'].checked = false;
				}
			}
			if (form.elements[prefix+'controller']) {
				if (!o['param']['flashvars'].match(/[?&]controlbar=none/gi)) { 
					form.elements[prefix+'controller'].checked = true;
				} else {
					form.elements[prefix+'controller'].checked = false;
				}
			}
			if (form.elements[prefix+'playlist']) {
				if (o['param']['flashvars'].match(/[?&]playlist=/gi)) { 
					form.elements[prefix+'playlist'].checked = true;
				} else {
					form.elements[prefix+'playlist'].checked = false;
				}
			}
			var m = o['param']['flashvars'].match(/([?&]|^)file=([^&]+)/gi);
			if (m) { 
				var file = m[0].replace(/[?&]*file=/gi, '');
				var e = FB.getExtension(file);
				if ((e=='.flv'||e=='.h264'||e=='.mp4') && dialog.urlFormat=='relative') {
					file = this._fixRelative(file);
				}
				form.URL.value = dialog.urlFormatting(file);
			}
		}
		
	}
}
