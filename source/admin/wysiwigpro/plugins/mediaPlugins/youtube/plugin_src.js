// JavaScript Document
function wproFilePlugin_youtube () {
	/* populates the local options form with data gathered from the server */
	this.populateLocalOptions = function (data, prefix) {}
	
	/* internal helper function */
	this._getOptions = function (prefix, o) {
		var form = document.dialogForm;
		if (!o) o = {}
		if (!o['object']) o['object'] = {};
		if (!o['embed']) o['embed'] = {};
		if (!o['param']) o['param'] = {};
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
	
	/* inserts a localy selected object */
	this.insertLocal = function(prefix, data) {
		if (!document.dialogForm.URL.value) return;
		var form = document.dialogForm;
		if (!data) data = {};
		
		var o = this._getOptions(prefix, data);
		
		var url = this._getMovieIdFromURL(form.URL.value);
		
		if (url=='') return;
		
		url = 'http://www.youtube.com/v/'+url;
		
		o['param']['movie'] =url;
		
		o['embed']['src'] =url;
  		o['embed']['type'] = "application/x-shockwave-flash";

		var s = '';
		if (form.elements[prefix+'style']) {
			s = form.elements[prefix+'style'].value
		}
		
		FB.insertMedia('youtube', o, s);
	}
		
	/* inserts an object from a web location */
	this.insertRemote = function (prefix) {
		var data
		if (FB.propertiesPlugin == 'youtube' && FB.mediaProperties) {
			data = FB.mediaProperties;
		}
		this.insertLocal(prefix, data);
	}
	
	/* determins if this plugin can edit the selected item's properties */
	this.canPopulate = function () {
		var arr = FB.getMediaProperties();
		if (arr['param']) {
			if (arr['param']['movie']) {
				if (arr['param']['movie'].match('http://www.youtube.com')) return true;
			}
		}
		return false
	}
	
	this._getMovieIdFromURL = function(url) {
		var id = '';
		var m = url.match(/v(=|\/)[a-zA-Z0-9_\-]+/gi);
		if (m && m[0]) {
			id = m[0].replace(/v(=|\/)([a-zA-Z0-9_\-]+)/gi, "$2");
		}
		return id;
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
		if 	(o['param']&&o['param']['movie']) {
			form.URL.value = dialog.urlFormatting(o['param']['movie']);
		}
	}
}