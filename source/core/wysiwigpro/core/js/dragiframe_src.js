/*===================================================================
 Author: Matt Kruse
 
 View documentation, examples, and source code at:
     http://www.JavascriptToolbox.com/

 NOTICE: You may use this code for any purpose, commercial or
 private, without any further permission from the author. You may
 remove this notice from your final code if you wish, however it is
 appreciated by the author if at least the web site address is kept.

 This code may NOT be distributed for download from script sites, 
 open source CDs or sites, or any other distribution method. If you
 wish you share this code with others, please direct them to the 
 web site above.
 
 Pleae do not link directly to the .js files on the server above. Copy
 the files to your own server for use with your site or webapp.
 ===================================================================*/
// Variables used for "Draggable IFrame" (WPRO_DIF) functions
var WPRO_DIF_dragging=false;
var WPRO_DIF_iframeBeingDragged="";
var WPRO_DIF_iframeObjects=new Object();
var WPRO_DIF_iframeWindows=new Object();
var WPRO_DIF_iframeMouseDownLeft = new Object();
var WPRO_DIF_iframeMouseDownTop = new Object();
var WPRO_DIF_pageMouseDownLeft = new Object();
var WPRO_DIF_pageMouseDownTop = new Object();
var WPRO_DIF_handles = new Object();
var WPRO_DIF_highestZIndex=99;
var WPRO_DIF_raiseSelectedIframe=false;
var WPRO_DIF_allowDragOffScreen=false;

// Set to true to always raise the dragged iframe to top zIndex
//function bringSelectedIframeToTop(val) {
 // WPRO_DIF_raiseSelectedIframe = val;
  //}
  
// Set to try to allow iframes to be dragged off the top/left of the document
//function allowDragOffScreen(val) {
 // WPRO_DIF_allowDragOffScreen=val;
  //}

// Method to be used by iframe content document to specify what object can be draggable in the window
function WPRO_DIF_addHandle(o, win) {
  if (arguments.length==2 && win==window) {
    // JS is included in the iframe who has a handle, search up the chain to find a parent window that this one is dragged in
    var p = win;
    while (p=p.parent) {
      if (p.WPRO_DIF_addHandle) { p.WPRO_DIF_addHandle(o,win,true); return; }
      if (p==win.top) { return; } // Already reached the top, stop looking
      }
    return; // If it reaches here, there is no parent with the WPRO_DIF_addHandle function defined, so this frame can't be dragged!
    }
  var topRef=win;
  var topRefStr = "window";
  while (topRef.parent && topRef.parent!=window) {
    topRef = topRef.parent;
    topRefStr = topRefStr + ".parent";
    }
  // Add handlers to child window
  if (typeof(win.WPRO_DIF_mainHandlersAdded)=="undefined" || !win.WPRO_DIF_mainHandlersAdded) {
    // This is done in a funky way to make Netscape happy
    with (win) { 
      eval("function OnMouseDownHandler(evt) { if(typeof(evt)=='undefined'){evt=event;}"+topRefStr+".parent.WPRO_DIF_begindrag(evt, "+topRefStr+") }");
      eval("document.onmousedown = OnMouseDownHandler;");
      eval("function OnMouseUpHandler(evt) { if(typeof(evt)=='undefined'){evt=event;}"+topRefStr+".parent.WPRO_DIF_enddrag(evt, "+topRefStr+") }");
      eval("document.onmouseup = OnMouseUpHandler;");
      eval("function OnMouseMoveHandler(evt) { if(typeof(evt)=='undefined'){evt=event;}"+topRefStr+".parent.WPRO_DIF_iframemove(evt, "+topRefStr+") }");
      eval("document.onmousemove = OnMouseMoveHandler;");
      win.WPRO_DIF_handlersAdded = true;
      win.WPRO_DIF_mainHandlersAdded = true;
      }
    }
  // Add handler to this window
  if (typeof(window.WPRO_DIF_handlersAdded)!="undefined" || !window.WPRO_DIF_handlersAdded) {
    eval("function OnMouseMoveHandler(evt) { if(typeof(evt)=='undefined'){evt=event;}WPRO_DIF_mouseMove(evt, window) }");
    eval("document.onmousemove = OnMouseMoveHandler;");
    eval("function OnMouseUpHandler(evt) { if(typeof(evt)=='undefined'){evt=event;}WPRO_DIF_enddrag(evt, window) }");
    eval("document.onmouseup = OnMouseUpHandler;");
    window.WPRO_DIF_handlersAdded=true;
    }
  //o.style.cursor="default";
  var name = WPRO_DIF_getIframeId(topRef);
  if (WPRO_DIF_handles[name]==null) {
    // Initialize relative positions for mouse down events
    WPRO_DIF_handles[name] = new Array();
    WPRO_DIF_iframeMouseDownLeft[name] = 0;
    WPRO_DIF_iframeMouseDownTop[name] = 0;
    WPRO_DIF_pageMouseDownLeft[name] = 0;
    WPRO_DIF_pageMouseDownTop[name] = 0;
    }
  WPRO_DIF_handles[name][WPRO_DIF_handles[name].length] = o;
  }

// Generalized function to get position of an event (like mousedown, mousemove, etc)
function WPRO_DIF_getEventPosition(evt) {
  var pos=new Object();
  pos.x=0;
  pos.y=0;
  if (!evt) {
    evt = window.event;
    }
  if (typeof(evt.pageX) == 'number') {
    pos.x = evt.pageX;
    pos.y = evt.pageY;
  } else {
    pos.x = evt.clientX;
    pos.y = evt.clientY;
    if (!top.opera) {
      if ((!window.document.compatMode) || (window.document.compatMode == 'BackCompat')) {
        pos.x += window.document.body.scrollLeft;
        pos.y += window.document.body.scrollTop;
      } else {        
	  	pos.x += window.document.documentElement.scrollLeft;
        pos.y += window.document.documentElement.scrollTop;
      }
    }
  }
  return pos;
}

// Gets the ID of a frame given a reference to a window object.
// Also stores a reference to the IFRAME object and it's window object
function WPRO_DIF_getIframeId(win) {
  // Loop through the window's IFRAME objects looking for a matching window object
  var iframes = document.getElementsByTagName("IFRAME");
  for (var i=0; i<iframes.length; i++) {
    var o = iframes.item(i);
    var w = null;
    if (o.contentWindow) {
      // For IE5.5 and IE6
      w = o.contentWindow;
      } else if (window.frames && window.frames[o.id].window) {
      w = window.frames[o.id];
      }
    if (w == win) {
      WPRO_DIF_iframeWindows[o.id] = win;
      WPRO_DIF_iframeObjects[o.id] = o;
      return o.id; 
      }
    }
  return null;
  }

// Gets the page x, y coordinates of the iframe (or any object)
function WPRO_DIF_getObjectXY(o) {
  var res = new Object();
  res.x=0; res.y=0;
  if (o != null) {
    res.x = o.style.left.substring(0,o.style.left.indexOf("px"));
    res.y = o.style.top.substring(0,o.style.top.indexOf("px"));
    }
  return res;
  }

// Function to get the src element clicked for non-IE browsers
function WPRO_DIF_getSrcElement(e) {
  var tgt = e.target;
  while (tgt.nodeType != 1) { tgt = tgt.parentNode; }
  return tgt;
  }

// Check if object clicked is a 'handle' - walk up the node tree if required
function WPRO_DIF_isHandleClicked(handle, objectClicked) {
  if (handle==objectClicked) { return true; }
  while (objectClicked.parentNode != null) {
    if (objectClicked==handle) {
      return true;
      }
    objectClicked = objectClicked.parentNode;
    }
  return false;
  }
  
// Called when user clicks an iframe that has a handle in it to begin dragging
function WPRO_DIF_begindrag(e, win) {
  // Get the IFRAME ID that was clicked on
  var iframename = WPRO_DIF_getIframeId(win);
  if (iframename==null) { return; }
  // Make sure that this IFRAME has a handle and that the handle was clicked
  if (WPRO_DIF_handles[iframename]==null || WPRO_DIF_handles[iframename].length<1) {
    return;
    }
  var isHandle = false;
  var t = e.srcElement || WPRO_DIF_getSrcElement(e);
  for (var i=0; i<WPRO_DIF_handles[iframename].length; i++) {
    if (WPRO_DIF_isHandleClicked(WPRO_DIF_handles[iframename][i],t)) {
      isHandle=true;
      break;
      }
    }
  if (!isHandle) { return false; }
  WPRO_DIF_iframeBeingDragged = iframename;
  if (WPRO_DIF_raiseSelectedIframe) {
    WPRO_DIF_iframeObjects[WPRO_DIF_iframeBeingDragged].style.zIndex=WPRO_DIF_highestZIndex++;
    }
  WPRO_DIF_dragging=true;
  var pos=WPRO_DIF_getEventPosition(e);
  WPRO_DIF_iframeMouseDownLeft[WPRO_DIF_iframeBeingDragged] = pos.x;
  WPRO_DIF_iframeMouseDownTop[WPRO_DIF_iframeBeingDragged] = pos.y;
  var o = WPRO_DIF_getObjectXY(WPRO_DIF_iframeObjects[WPRO_DIF_iframeBeingDragged]);
  WPRO_DIF_pageMouseDownLeft[WPRO_DIF_iframeBeingDragged] = o.x - 0 + pos.x;
  WPRO_DIF_pageMouseDownTop[WPRO_DIF_iframeBeingDragged] = o.y -0 + pos.y;
 
  // added by chris bolt: prevent default drag behaviour...
	if (typeof( e.cancelBubble ) == "undefined") {
		e.stopPropagation();
		e.preventDefault();
	} else {
		e.cancelBubble = true;
		e.returnValue = false;
	}
	
  }

// Called when mouse button is released after dragging an iframe
function WPRO_DIF_enddrag(e) {
  WPRO_DIF_dragging=false;
  WPRO_DIF_iframeBeingDragged="";
  }

// Called when mouse moves in the main window
function WPRO_DIF_mouseMove(e) {
  if (WPRO_DIF_dragging) {
    var pos = WPRO_DIF_getEventPosition(e);
    WPRO_DIF_drag(pos.x - WPRO_DIF_pageMouseDownLeft[WPRO_DIF_iframeBeingDragged] , pos.y - WPRO_DIF_pageMouseDownTop[WPRO_DIF_iframeBeingDragged]);
    }
  }

// Called when mouse moves in the IFRAME window
function WPRO_DIF_iframemove(e) {
  if (WPRO_DIF_dragging) {
    var pos = WPRO_DIF_getEventPosition(e);
    WPRO_DIF_drag(pos.x - WPRO_DIF_iframeMouseDownLeft[WPRO_DIF_iframeBeingDragged] , pos.y - WPRO_DIF_iframeMouseDownTop[WPRO_DIF_iframeBeingDragged]);
    }
  }

// Function which actually moves of the iframe object on the screen
function WPRO_DIF_drag(x,y) {
  var o = WPRO_DIF_getObjectXY(WPRO_DIF_iframeObjects[WPRO_DIF_iframeBeingDragged]);
  // Don't drag it off the top or left of the screen?
  var newPositionX = o.x-0+x;
  var newPositionY = o.y-0+y;
  if (!WPRO_DIF_allowDragOffScreen) {
    if (newPositionX < 0) { newPositionX=0; }
    if (newPositionY < 0) { newPositionY=0; }
    }
  WPRO_DIF_iframeObjects[WPRO_DIF_iframeBeingDragged].style.left = newPositionX + "px";
  WPRO_DIF_iframeObjects[WPRO_DIF_iframeBeingDragged].style.top  = newPositionY + "px";
  WPRO_DIF_pageMouseDownLeft[WPRO_DIF_iframeBeingDragged] += x;
  WPRO_DIF_pageMouseDownTop[WPRO_DIF_iframeBeingDragged] += y;
  }