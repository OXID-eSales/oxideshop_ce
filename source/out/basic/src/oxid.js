var oxid = {

    // Generall navigation with timeout
    nav: {

        timeout : 400,
        activem : null,
        timerid : null,
        elclass : '',
        opclass : ' open',

        open   : function(e){
            oxid.nav.stoptm();
            oxid.nav.setclass(oxid.nav.activem,false);

            oxid.nav.activem = this.id;
            oxid.nav.setclass(oxid.nav.activem,true);
        },

        close : function(){
            oxid.nav.setclass(oxid.nav.activem,false);
        },

        starttm : function(){
            oxid.nav.timerid = window.setTimeout(oxid.nav.close, oxid.nav.timeout);
        },

        stoptm  : function(){
            if(oxid.nav.timerid){
                window.clearTimeout(oxid.nav.timerid);
                oxid.nav.timerid = null;
            }
        },

        setclass : function(id,add) {
            if (!id){return;}
            var el = document.getElementById(id);
            if( el ) {
                if(add){
                    oxid.nav.elclass = el.parentNode.className;
                    el.parentNode.className += oxid.nav.opclass;
                }else{
                    el.parentNode.className = oxid.nav.elclass;
                }
            }
        }
    },

    // Category navigation init
    catnav : function(ul) {
        var mn = document.getElementById(ul);
        if(mn) {
            var nr = 0;
            for (var i=0; i<mn.childNodes.length; i++) {
                if(mn.childNodes[i].tagName && mn.childNodes[i].tagName.toUpperCase() == 'LI'){
                    var mi = mn.childNodes[i];

                    for ( var n=0; n<mi.childNodes.length; n++) {
                        if(mi.childNodes[n].tagName && mi.childNodes[n].tagName.toUpperCase() == 'A'){
                            mi.childNodes[n].onmouseover = mi.childNodes[n].onfocus = oxid.nav.open ;
                            mi.childNodes[n].onmouseout  = mi.childNodes[n].onblur  = oxid.nav.starttm;
                            nr ++;
                        }
                        if(mi.childNodes[n].tagName && mi.childNodes[n].tagName.toUpperCase() == 'UL'){
                            mi.childNodes[n].onmouseover = mi.childNodes[n].onfocus = oxid.nav.stoptm ;
                            mi.childNodes[n].onmouseout  = mi.childNodes[n].onblur  = oxid.nav.starttm;
                            /* setting all childs nodes width same as <ul> */
                            var ml = mi.childNodes[n];
                            var mlWidth = ml.offsetWidth;
                            for ( var k=0; k<ml.childNodes.length; k++) {
                                if(ml.childNodes[k].tagName && ml.childNodes[k].tagName.toUpperCase() == 'LI'){
                                    ml.childNodes[k].style.width = mlWidth;
                                }
                            }
                        }
                    }
                }
            }
            if(nr>0){ document.onclick = oxid.nav.close; }
        }
    },

    // Top navigation init
    topnav : function(dt,dd) {
        var _dt = document.getElementById(dt);
        if(_dt){
            _dt.onmouseover = _dt.onfocus = oxid.nav.open ;
            _dt.onmouseout  = _dt.onblur  = oxid.nav.starttm;
        }

        var _dd = document.getElementById(dd);
        if(_dd){
            _dd.onmouseover = _dd.onfocus = oxid.nav.stoptm ;
            _dd.onmouseout  = _dd.onblur  = oxid.nav.starttm;
        }
        document.onclick = oxid.nav.close;
    },

    // Blank
    blank : function(id) {
        var _a  = document.getElementById(id);

        if(_a) {
            _a.setAttribute('target','_blank');
        }
    },

    // Search auto submit
    search : function(form, param) {
        var _form  = document.getElementById(form);
        var _param = document.getElementById(param);

        if( _form && _param && _param.value.length ) {
            _form.submit();
        }
    },

    // Popups
    popup: {
        load : function(){ oxid.popup.setClass('wait','popup load on','on');},
        show : function(){ oxid.popup.setClass('popup','popup on','on');},
        showFbMsg : function(){ oxid.popup.setClass('popup','popup on fbMsg','on');},
        hide : function(id){ oxid.popup.setClass(id?id:'popup','popup','');},

        setClass: function (id,pcl,mcl){
            var _mk = document.getElementById('mask');
            var _el = document.getElementById(id);
            if(_mk && _el) {
                _mk.className = mcl;
                _el.className = pcl;
            }
        },

        addShim : function(){
            var _fr, _mk = document.getElementById('mask');
            if(_mk) {
                _fr = document.createElement('iframe');
                _fr.setAttribute('src','javascript:false;'); //MS Q261188
                _mk.appendChild(_fr);
            }
       },

        open : function(url,w,h,r){
            if (url !== null && url.length > 0) {
                var _cfg = "status=yes,scrollbars=no,menubar=no,width="+w+",height="+h+(r?",resizable=yes":"");
                window.open(url, "_blank", _cfg);
            }
        },

        compare : function(url){
            oxid.popup.open(url,620,400,true);
        },

        zoom    : function(){
            oxid.popup.setClass('zoom','popup zoom on','on');
        },

        addResizer : function(image_id,container_id,pw,ph){
            var _pl, _el = document.getElementById(image_id);
            if(_el) {
                _el.onload = function(e){
                    if(this.tagName.toUpperCase() == 'IMG') {
                        if(this.width && this.height){
                            oxid.popup.resize(container_id, this.width+pw, this.height+ph);
                        }else{
                            _pl = new Image();
                            _pl.src = _el.src;
                            _pl.onload = function(e) {
                                oxid.popup.resize(container_id, this.width+pw, this.height+ph);
                                _pl.onload = null;
                                _pl = null;
                            };
                        }
                    }
                };
            }
        },

        resize  : function(id, newWidth, newHeight ){

            if(newWidth === 0 && newHeight === 0){
                return;
            }

            var _el = document.getElementById(id);
            var maxWidth = newWidth;
            var maxHeight = maxHeight;
            var overflow = 'auto';

            if(_el) {
                if( typeof( window.innerWidth ) == 'number' ) {
                    maxWidth  = window.innerWidth;
                    maxHeight = window.innerHeight;
                } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
                    maxWidth  = document.documentElement.clientWidth;
                    maxHeight = document.documentElement.clientHeight;
                }

                if(newWidth < maxWidth && newHeight < maxHeight){ overflow = 'hidden';}
                if(newWidth > maxWidth){ newWidth = maxWidth;}
                if(newHeight > maxHeight){ newHeight = maxHeight;}

                _el.style.overflow = overflow;

                _el.style.width  = newWidth+'px';
                _el.style.height = newHeight+'px';

                _el.style.marginLeft = '-'+Math.round(newWidth/2)+'px';
                _el.style.marginTop  = '-'+Math.round(newHeight/2)+'px';
            }
        }

    },

    // Tags
    tags: {

        input  : null,

        select : function(e){
            oxid.tagsInput.value += ' ' + this.innerHTML;
            this.className = 'sel';
            this.removeAttribute('onclick');
        },

        addSelect: function(id,input){
            var tg = document.getElementById(id);
            var ed = document.getElementById(input);
            if(tg && ed) {
                oxid.tagsInput = ed;
                for (var i=0; i<tg.childNodes.length; i++) {
                    if(tg.childNodes[i].tagName && tg.childNodes[i].tagName.toUpperCase() == 'A'){
                       tg.childNodes[i].onclick = oxid.tags.select;
                       tg.childNodes[i].removeAttribute('href');
                    }
                }
            }
        }
    },

    // Forms
    form: {

        // send
        send: function(form) {
            var _form = document.forms[form];
            if(_form) { _form.submit(); }
        },

        // submits form + changes cl, fnc, formReload values
        reload: function(stop,form,cl,fnc) {
            if(stop) { return; }
            var _form = document.forms[form];
            if(_form) {
                _form.elements.cl.value  = cl;
                _form.elements.fnc.value = fnc;
                _form.submit();
            }
        },

        // clears form values using given regex on fileld name
        clear: function(stop,form,pattern) {
            if(stop) { return; }
            var _fields = document.forms[form].elements, i;
            if(_fields) {
                for (i=0;i<_fields.length;i++) {
                    if( pattern.test(_fields[i].name) ) {
                        if( _fields[i].tagName.toUpperCase() === 'INPUT' ){ _fields[i].value = ""; }
                        if( _fields[i].tagName.toUpperCase() === 'SELECT'){ _fields[i].item(0).selected = true;}
                    }
                }
            }
        },

        select: function(id,value) {
            var _el = document.getElementsByName(id);
            if(_el) { _el[value].checked='true'; }
        },

        set: function(id, value, blset) {
            var _el = document.getElementById(id);
            if (_el) {
                _el.value = value;
            }
        }

    },

    // Review / Rating
    review: {
        show : function(){
            oxid.showhideblock('write_review',true);
            oxid.showhideblock('write_new_review',false);
        },

        rate : function(value){
            oxid.review.show();
            var _form = document.getElementById("rating");
            if ( _form !== null) {
                if (_form.artrating) {
                    _form.artrating.value = value;
                } else if (_form.recommlistrating) {
                    _form.recommlistrating.value = value;
                }
                document.getElementById('current_rate').style.width = (value * 20) + '%';
            }
       }
    },

    // SelectList
    sellist: {
        set : function(name,value){
            //selectlist
            var _slist = document.getElementById("linkToNoticeList");
            if ( _slist !== null) {
                _slist.href = _slist.href + "&" + name + "=" + value;
            }
            //wishlist
            var _wlist = document.getElementById("linkToWishList");
            if ( _wlist !== null) {
                _wlist.href = _wlist.href + "&" + name + "=" + value;
            }
        }
    },

    // etc...
    showhide: function(id,show){
        var _el = document.getElementById(id);
        if (_el) { _el.style.display=show?'':'none';}
    },

    showhideblock: function(id,show){
        var _el = document.getElementById(id);
        if (_el) { _el.style.display=show?'block':'none';}
    },

    focus: function(id){
        var _el = document.getElementById(id);
        if (_el) { _el.focus(); }
    },

    // switch image src
    image: function(id,src){
        var _el = document.getElementById(id);
        if( _el ) { _el.src=src; }
    },

    password: function(user_el,password_el,name) {
        var _u = document.getElementById(user_el);
        var _p = document.getElementById(password_el);
        if( _u ) {
            _u.focus();
            _p.style.display=(_u.value === name)?'none':'';
        }
    },

    checkAll: function(obj,pref) {
        if(document.getElementsByTagName){
            var inputs = document.getElementsByTagName("input");
            for (var i=0;i<inputs.length; i=i+1) {
                if(inputs[i].type == 'checkbox' && inputs[i].checked != obj.checked && pref == inputs[i].name.split('[')[0]){
                    inputs[i].checked = obj.checked;
                }
            }
        }
    },

    getEventTarget: function(e) {
        var targ;
        if (!e) {
            var e = window.event;
        }
        if (e.target) {
            targ = e.target;
        } else if (e.srcElement) {
            targ = e.srcElement;
        }
        //Safari
        if (targ.nodeType == 3) {
            targ = targ.parentNode;
        }
        return targ;
    },

    mdVariants: {
        // reloading page by selected value in select list
        getMdVariantUrl: function(selId) {
            var _mdVar  = document.getElementById(selId);

            if (_mdVar) {
                _newUrl = _mdVar.options[_mdVar.selectedIndex].value;
            }

            if(_newUrl) {
                document.location.href = _newUrl;
            }
        },

        mdAttachAll: function()
        {
            if (!mdVariantSelectIds) {
                mdVariantSelectIds = Array();
            }

            if (!mdRealVariants) {
                mdRealVariants = Array();
            }

            for (var i = 0; i < mdVariantSelectIds.length; i++) {
                if (mdVariantSelectIds[i]) {
                    for (var j=0; j < mdVariantSelectIds[i].length; j++) {
                        //attach JS handlers
                        var mdSelect = document.getElementById(mdVariantSelectIds[i][j]);
                        if (mdSelect) {
                            mdSelect.onchange = oxid.mdVariants.resetMdVariantSelection;
                        }
                    }
                }
            }
        },

        resetMdVariantSelection: function(e) {
            mdSelect = oxid.getEventTarget(e);
            //hide all
            selectedValue = mdSelect.options[mdSelect.selectedIndex].value;
            level = oxid.mdVariants.getSelectLevel(mdSelect.id);
            if (level !== null) {
                oxid.mdVariants.hideAllMdSelect(level+1);
            }
            //show selection
            var showId = selectedValue;
            while (showId) {
                showSelectId = oxid.mdVariants.getMdSelectNameById(showId);
                oxid.mdVariants.showMdSelect(showSelectId);
                shownSelect = document.getElementById(showSelectId);
                if (shownSelect) {
                    showId = shownSelect.options[shownSelect.selectedIndex].value;
                } else {
                    showId = null;
                }
            }

            oxid.mdVariants.showMdRealVariant();
        },

        getMdSelectNameById: function(id)
        {
            var name = 'mdVariantSelect_' + id;
            return name;
        },

        getSelectLevel: function(name) {
            for (var i=0; i < mdVariantSelectIds.length; i++) {
                for (var j=0; j < mdVariantSelectIds[i].length; j++) {
                    if (mdVariantSelectIds[i][j] == name) {
                        return i;
                    }
                }
            }
            return null;
        },

        showMdSelect: function(id) {
            if (document.getElementById(id)) {
              document.getElementById(id).style.display = 'inline';
            }
        },

        hideAllMdSelect: function (level) {
            for (var i=level; i < mdVariantSelectIds.length; i++) {
                if (mdVariantSelectIds[i]) {
                    for (var j=0; j < mdVariantSelectIds[i].length; j++) {
                        if (document.getElementById(mdVariantSelectIds[i][j])) {
                            document.getElementById(mdVariantSelectIds[i][j]).style.display = 'none';
                        }
                    }
                }
            }
        },

        getSelectedMdRealVariant: function() {
            for (var i=0; i < mdVariantSelectIds.length; i++) {
                for (var j=0; j < mdVariantSelectIds[i].length; j++) {
                    var mdSelectId = mdVariantSelectIds[i][j];
                    var mdSelect = document.getElementById(mdSelectId);
                    if (mdSelect && mdSelect.style.display == "inline") {
                        var selectedVal = mdSelect.options[mdSelect.selectedIndex].value;
                        if (mdRealVariants[selectedVal])
                            return mdRealVariants[selectedVal];
                    }
                }
            }
        },

        showMdRealVariant: function() {
            document.getElementById('mdVariantBox').innerHTML = '';
            var selectedId = oxid.mdVariants.getSelectedMdRealVariant();
            if (selectedId && document.getElementById('mdVariant_' + selectedId)) {
                document.getElementById('mdVariantBox').innerHTML = document.getElementById('mdVariant_' + selectedId).innerHTML;
            }

        }
    },

    stateSelector: {

        fillStates: function  (countrySelectId, stateSelectId, divId, allStates, allStateIds, allCountryIds, statePromptString, selectedStateId) {

            var states = allStates[allCountryIds[document.getElementById(countrySelectId).options[document.getElementById(countrySelectId).selectedIndex].value]];
            var ids  = allStateIds[allCountryIds[document.getElementById(countrySelectId).options[document.getElementById(countrySelectId).selectedIndex].value]];

            var stateSelectObject = document.getElementById(stateSelectId);

            if(stateSelectObject == null) {
                return;
            }

            //add event handler to country select (this is important for the first time)
            document.getElementById(countrySelectId).onchange = function() {
                oxid.stateSelector.fillStates(countrySelectId, stateSelectId, divId, allStates, allStateIds, allCountryIds, statePromptString, selectedStateId);
            };

            //remove all nodes
            if ( stateSelectObject.hasChildNodes() ) {
                while ( stateSelectObject.childNodes.length >= 1 ) {
                    stateSelectObject.removeChild( stateSelectObject.firstChild );
                }
            }

            //create blank prompt option
            var option = document.createElement('option');
                option.appendChild(document.createTextNode(statePromptString));
                option.setAttribute('value', '');
                stateSelectObject.appendChild(option);

            //fill options with states
            if (states != null) {
             var cCount = 0;
             for(var x = 0; x < states.length; x++) {
                cCount++;
                var option = document.createElement('option');
                option.appendChild(document.createTextNode(states[x]));
                option.setAttribute('value', ids[x]);
                stateSelectObject.appendChild(option);
                if (selectedStateId == ids[x]) {
                  stateSelectObject.selectedIndex = x+1;
                }
             }
            }

            oxid.showhideblock(divId, states != null && states.length > 0);
        }
    }
};
