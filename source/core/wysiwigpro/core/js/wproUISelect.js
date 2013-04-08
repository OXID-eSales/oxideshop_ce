
function wproUISelect () {
this.current = null;
this.id = '';
this.onchange = function(){}
}
wproUISelect.prototype.manualSwapTab = function () {
if (p = document.getElementById(this.id)) {
var o = document.getElementById(p.options[p.selectedIndex].value);
if (this.current) {
this.current.style.display = "none"
}
o.style.display = 'block';
this.current = o;
}
}
wproUISelect.prototype.swapTab = function (value) {
if (value) {
this.value = value;
}
if (p = document.getElementById(this.value)) {
var a = eval(this.id);
if (a.current) {
a.current.style.display = "none"
}
p.style.display = 'block';
a.current = p;
a.onchange(this.value);
}
}
wproUISelect.prototype.init = function (UID) {
var p;
if (document.getElementById(UID)) {
this.id = UID;
s = document.getElementById(UID);
o = s.getElementsByTagName('OPTION');
n = s.length;
s.onchange = this.swapTab
for (i=0; i<n; i++) {
if (o[i].selected == true) {
if (p = document.getElementById('sPane_'+this.id+'_'+i)) {
p.style.display='block';
this.current = p;
}
}
}
}
}