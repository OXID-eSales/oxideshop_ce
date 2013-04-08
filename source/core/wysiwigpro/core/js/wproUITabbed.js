
function wproUITabbed () {
this.currentPane = null;
this.currentTab = null;
this.id = '';
}
wproUITabbed.prototype.swapTab = function (index) {
var src= document.getElementById(this.id).getElementsByTagName('A').item(index);
if (p = document.getElementById('tPane_'+this.id+'_'+index)) {
if (this.currentPane) {
if (this.currentPane != p) {
this.currentPane.style.display = "none"
}
}
p.style.display = 'block';
this.currentPane = p;
c = src.className.toString();
nc = c.replace(/ selected/gi, '');
src.className = nc+' selected';
if (this.currentTab) {
if (this.currentTab != src) {
c = this.currentTab.className.toString();
nc = c.replace(/ selected/gi, '');
this.currentTab.className = nc;
}
}
this.currentTab = src;
}
}
wproUITabbed.prototype.init = function (UID) {
var p;
if (document.getElementById(UID)) {
this.id = UID;
s = document.getElementById(UID);
o = s.getElementsByTagName('A');
n = o.length;
for (i=0; i<n; i++) {
if ( o[i].className.search(/selected/gi) != -1 ) {
if (p = document.getElementById('tPane_'+this.id+'_'+i)) {
p.style.display='block';
this.currentPane = p;
}
this.currentTab = o[i];
}
}
}
}