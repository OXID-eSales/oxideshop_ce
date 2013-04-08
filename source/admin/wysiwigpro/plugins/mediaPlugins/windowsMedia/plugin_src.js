// JavaScript Document
function wproFilePlugin_windowsMedia () {
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
		this.updateHeight(prefix);
	}
	
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
		if (form.elements[prefix+'autoplay']) {
			o['param']['autostart'] = form.elements[prefix+'autoplay'].checked?'true':'false';
			o['embed']['autostart'] = form.elements[prefix+'autoplay'].checked?'true':'false';
		}
		if (form.elements[prefix+'loop']) {
			o['param']['loop'] = form.elements[prefix+'loop'].checked?'true':'false';
			o['embed']['loop'] = form.elements[prefix+'loop'].checked?'true':'false';
		}
		if (form.elements[prefix+'controller']) {
			o['param']['showcontrols'] = form.elements[prefix+'controller'].checked?'true':'false';
			o['embed']['showcontrols'] = form.elements[prefix+'controller'].checked?'true':'false';
		}
		return o;
	}
	
	/* inserts a localy selected object */
	this.insertLocal = function(prefix, data) {
		if (!document.dialogForm.URL.value) return;
		var form = document.dialogForm;
		if (!data) data = {};
		
		var o = this._getOptions(prefix, data);
				
		o['object']['classid']="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" ;
		//o['object']['codebase']="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab";
		o['object']['type'] = "application/x-oleobject";
		
		o['param']['url'] =form.URL.value;
		
		o['embed']['src'] = form.URL.value;
		o['embed']['filename'] = form.URL.value;
		o['embed']['pluginspage'] = "http://www.microsoft.com/Windows/MediaPlayer/";
  		o['embed']['type'] = "application/x-mplayer2";

		var s = '';
		if (form.elements[prefix+'style']) {
			s = form.elements[prefix+'style'].value
		}
		
		FB.insertMedia('windowsMedia', o, s);
	}
		
	/* inserts an object from a web location */
	this.insertRemote = function (prefix) {
		var data
		if (FB.propertiesPlugin == 'windowsMedia' && FB.mediaProperties) {
			data = FB.mediaProperties;
		}
		this.insertLocal(prefix, data);
	}
	
	/* determins if this plugin can edit the selected item's properties */
	this.canPopulate = function () {
		var arr = FB.getMediaProperties();
		if (arr['object']) {
			if (arr['object']['classid']) {
				if (arr['object']['classid'].toUpperCase() == "CLSID:6BF52A52-394A-11D3-B153-00C04F79FAA6"||arr['object']['classid'].toUpperCase() == "CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95") {
					return true;	
				}
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
		if (form.elements[prefix+'scale']&&o['param']&&o['param']['scale']) {
			form.elements[prefix+'scale'].value = o['param']['scale'];
		}
		
		if (form.elements[prefix+'autoplay']&&o['param']&&o['param']['autostart']) {
			form.elements[prefix+'autoplay'].checked = o['param']['autostart']=='true'?true:false;
		}
		if (form.elements[prefix+'loop']&&o['param']&&o['param']['loop']) {
			form.elements[prefix+'loop'].checked = o['param']['loop']=='true'?true:false;
		}
		if (form.elements[prefix+'controller']&&o['param']&&o['param']['showcontrols']) {
			form.elements[prefix+'controller'].checked = o['param']['showcontrols']=='true'?true:false;
		}
		if 	(o['param']&&o['param']['url']) {
			form.URL.value = dialog.urlFormatting(o['param']['url']);
		}
	}
}