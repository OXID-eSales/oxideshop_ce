/*****
A drop in replacement for XMLHttpRequest object

Usage:
	Add to between the <head> tags,
	<!--[if lt IE 7]><script type="text/javascript" src="activex_off.js"></script><![endif]-->

*****/
var useBackupXMLObject = false;

xajax.oldGetRequestObject = xajax.getRequestObject;
xajax.getRequestObject = function() {
	var req = null;
	if (useBackupXMLObject == false)
	{
		this.temp = this.DebugMessage;
		this.DebugMessage = function() {};
		req = this.oldGetRequestObject();
		this.DebugMessage = this.temp;
		this.temp = null;
		if (req)
			return req;
		useBackupXMLObject = true;
	}
	try {
		req = new BackupXMLObject();
	} catch (e) {}
	return req;
}

/*
Chris Bolt
Removed some fluff that was causing problems and didn't seem to do anything
*/

function BackupXMLObject() {
	this.uri = null;
	this.async = null;
	this.status = null;
	this.readyState = 0;
	this.responseText = '';
	this.statusText = null;
	this.requestType = null;
	this.responseXML = null;
	this.onreadystatechange = function(){};
	this.xmlisland = null;

	this.open = function(requestType, uri, async)
	{
		this.requestType = requestType.toLowerCase();
		this.uri = uri;
		this.async = async;
	}

	this.setRequestHeader = function(headerKey, headerValue)
	{
		if (this.requestType == 'post')
			throw new Error();
	}

	this.send = function(postData)
	{
		xmlislandId = 'xml'+(new Date().getTime());
		var xmlisland = document.createElement("xml");
		xmlisland.id = xmlislandId;
		xmlisland.maker = this;
		xmlisland.src = this.uri;
		document.body.appendChild(xmlisland);
		xmlisland.XMLDocument.onreadystatechange = function() {
			xmlisland.maker.readyState = xmlisland.XMLDocument.readyState;
			xmlisland.maker.responseXML = xmlisland.XMLDocument;
			xmlisland.maker.status = 200;
			if (xmlisland.XMLDocument && xmlisland.XMLDocument.documentElement)
				xmlisland.maker.responseText = xmlisland.XMLDocument.documentElement.xml;
			xmlisland.maker.onreadystatechange();
			if (xmlisland.XMLDocument.readyState == 4)
			{
				document.body.removeChild(xmlisland);
				delete xmlisland;
				xmlisland = null;
			}
		}
	}

}
