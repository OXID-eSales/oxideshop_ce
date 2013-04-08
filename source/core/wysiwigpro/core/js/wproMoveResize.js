
/*
 * WysiwygPro 3.2.1.20091130 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproMoveResize() {

document.onmousemove = getMouseXY;

this.mousePosX = 0;
this.mousePosY = 0;
this.layer = '';
this.origX = 0;
this.origY = 0;
this.origLayerX = 0;
this.origLayerY = 0;
this.origWidth  = 0;
this.origHeight = 0;
this.minWidth = 10;
this.minHeight = 10;
this.resize = false;

function getMouseXY(evt) {
this.mousePosX = evt.clientX; + document.body.scrollLeft + document.documentElement.scrollLeft;
this.mousePosY = evt.clientY; + document.body.scrollTop + document.documentElement.scrollTop;
}

function startLayerDrag(layerID)
{
this.layer = document.getElementById(layerID);
this.origX = this.mousePosX;
this.origY = this.mousePosY;
this.origLayerX = Math.abs(this.layer.style.left.substring(0, this.layer.style.left.length - 2));
this.origLayerY = Math.abs(this.layer.style.top.substring(0, this.layer.style.top.length - 2));
document.onmousemove = (this.resize == true ? this.onResize : this.onLayerDrag);
}
function endLayerDrag()
{
this.layer = '';
this.origX = 0;
this.origY = 0;
document.onmousemove = this.getMouseXY;
}
function onLayerDrag(e)
{
this.getMouseXY(e);
var diffX = this.mousePosX - this.origX;
var diffY = this.mousePosY - this.origY;
this.layer.style.left = this.origLayerX + diffX + 'px';
this.layer.style.top  = this.origLayerY + diffY + 'px';
}

function startResize(layerID)
{
this.layer = document.getElementById(layerID);
this.origX = this.mousePosX;
this.origY = this.mousePosY;
this.origWidth  = Math.abs(this.layer.style.width.substring(0, this.layer.style.width.length - 2));
this.origHeight = Math.abs(this.layer.style.height.substring(0, this.layer.style.height.length - 2));
this.resize = true;
}
function endResize()
{
this.layer = '';
this.origX = 0;
this.origY = 0;
this.resize = false;
document.onmousemove = this.getMouseXY;
}
function onResize(e)
{
this.getMouseXY(e);
diffX = this.mousePosX - this.origX;
diffY = this.mousePosY - this.origY;
if (this.origWidth + diffX <= this.minWidth) {
this.layer.style.width  = this.minWidth + 'px';
} else {
this.layer.style.width  = this.origWidth + diffX + 'px';
}
if (this.origHeight + diffY <= this.minHeight) {
this.layer.style.height = this.minHeight + 'px';
} else {
this.layer.style.height = this.origHeight + diffY + 'px';
}
}
</script>
<style type="text/css">
<!--
.popupAddressbookClose {
font-family: Marlett;
font-size: 10pt;
position: absolute;
right: 0;
top: 0;
z-index: 2;
margin: 1px;
background-color: #cccccc;
border: 2px white outset;
cursor: hand;
}
.popupAddressbookResize {
font-family: Marlett;
position: absolute;
right: 0;
bottom: 0;
z-index: 2;
cursor: se-resize;
color: #333333;
}
</style>
<div id="popupAddressbook"
style="visibility: visible;
cursor: move;
position: absolute;
left: 10px;
top: 10px;
z-index: 1;
width: 200;
height: 200;
background-color: transparent;
border: 1px solid #cccccc" onmousedown="startLayerDrag('popupAddressbook')" onmouseup="endLayerDrag()">
<div id="popupAddressbookClose" class="popupAddressbookClose" onclick="document.getElementById('popupAddressbook').style.visibility = 'hidden'">r</div>
<div id="popupAddressbookResize" class="popupAddressbookResize" onmousedown="return startResize('popupAddressbook')" onmouseup="endResize()">o</div>
</div>