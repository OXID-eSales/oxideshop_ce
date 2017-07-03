<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html id="nav">
<head>
    <title>[{oxmultilang ident="NAVIGATION_TITLE"}]</title>
    <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]nav.css">
    <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]colors_[{$oViewConf->getEdition()|lower}].css">
    <meta http-equiv="Content-Type" content="text/html; charset=[{$charset}]">
    <script language="javascript">

        [{if $loadbasefrm}]
        //reloading main frame
        window.onload = function ()
        {
            //
            top.header.document.getElementById( "homelink" ).href = "[{$oViewConf->getSelfLink()|replace:"&amp;":"&"}]&cl=navigation&item=home.tpl";
            if ( '[{$listview}]' != '' ) {
                top.basefrm.list.location = "[{$oViewConf->getSelfLink()|replace:"&amp;":"&"}]&cl=[{$listview}]&oxid=[{$oViewConf->getActiveShopId()}]&actedit=[{$actedit}]";
                top.basefrm.edit.location = "[{$oViewConf->getSelfLink()|replace:"&amp;":"&"}]&cl=[{$editview}]&oxid=[{$oViewConf->getActiveShopId()}]";
            } else if ( top.basefrm ) {
                top.basefrm.location = "[{$oViewConf->getSelfLink()|replace:"&amp;":"&"}]&cl=navigation&item=home.tpl";
            }
        }
        [{/if}]

        [{if $oView->isMall()}]
        // changes active shop
        function selectShop( iShopId )
        {
            var oForm = document.getElementById( "search" );

            if ( oForm.shp === undefined ) {
                // inserting new form element
                var oInputElement = document.createElement( 'input' );
                oInputElement.setAttribute( 'name', 'shp' );
                oInputElement.setAttribute( 'type', 'hidden' );
                oInputElement.setAttribute( 'value', iShopId );
                oForm.appendChild( oInputElement );
            } else {
                oForm.shp.value = iShopId;
            }
            oForm.submit();
        }
        [{/if}]
    </script>
</head>
<body>
    <div id="shopLogo"><img src="[{$oViewConf->getImageUrl('logo.svg')}]" /></div>
    <table>
    <tr><td class="main">
    [{include file="navigation_shopselect.tpl"}]
    [{block name="admin_navigation_menustructure"}]
        [{assign var='mh' value=0}]
        [{foreach from=$menustructure item=menuholder}]
        [{if $menuholder->nodeType == XML_ELEMENT_NODE && $menuholder->childNodes->length}]
            [{assign var='mh' value=$mh+1}]
            [{assign var='mn' value=0}]
            <h2>
                [{if $menuholder->getAttribute('url')}]<a href="[{$oViewConf->getSelfLink()}]&cl=navigation&amp;fnc=exturl&amp;url=[{$menuholder->getAttribute('url')|escape:'url'}]" target="basefrm" >[{/if}]
                [{oxmultilang ident=$menuholder->getAttribute('name')|default:$menuholder->getAttribute('id') noerror=true}]
                [{if $menuholder->getAttribute('url')}]</a>[{/if}]
            </h2>
            <ul>
            [{strip}]
                [{foreach from=$menuholder->childNodes item=menuitem name=menuloop}]
                [{assign var='actClass' value=$menuitem->childNodes->length}]
                [{if $menuitem->nodeType == XML_ELEMENT_NODE}]
                    [{assign var='mn' value=$mn+1}]
                    [{assign var='sm' value=0}]
                    <li class="[{if $menuitem->getAttribute('active')}]exp[{assign var='sNavExpId' value="nav-`$mh`-`$mn`"}][{/if}]" id="nav-[{$mh}]-[{$mn}]">
                        [{if $menuitem->getAttribute('url')}]
                            <a href="[{$menuitem->getAttribute('url')}]" onclick="_navAct(this);" class="rc" target="[{if $menuitem->getAttribute('target')}][{$menuitem->getAttribute('target')}][{else}]basefrm[{/if}]"><b>[{oxmultilang ident=$menuitem->getAttribute('name')|default:$menuitem->getAttribute('id') noerror=true}]</b></a>
                        [{elseif $menuitem->getAttribute('expand') == 'none'}]
                            <a href="[{$menuitem->getAttribute('link')}]" onclick="_navAct(this);" target="basefrm" class="rc"><b>[{oxmultilang ident=$menuitem->getAttribute('name')|default:$menuitem->getAttribute('id') noerror=true}]</b></a>
                        [{else}]
                            <a href="#" onclick="_navExp(this);return false;" class="rc"><b>[{oxmultilang ident=$menuitem->getAttribute('name')|default:$menuitem->getAttribute('id') noerror=true}]</b></a>
                        [{/if}]
                        [{if $menuitem->childNodes->length}]
                        <ul>
                            [{foreach from=$menuitem->childNodes item=submenuitem}]
                            [{if $submenuitem->nodeType == XML_ELEMENT_NODE}]
                                [{assign var='sm' value=$sm+1}]
                                [{if $submenuitem->getAttribute('linkicon')}] [{assign var='linkicon' value=$submenuitem->getAttribute('linkicon')}][{/if}]
                                <li class="[{if $submenuitem->getAttribute('active')}]act[{assign var='sNavActId' value="nav-`$mh`-`$mn`-`$sm`"}][{/if}]" id="nav-[{$mh}]-[{$mn}]-[{$sm}]" name="nav_[{$submenuitem->getAttribute('cl')}]" rel="nav-[{$mh}]-[{$mn}]">
                                    <a href="[{if $submenuitem->getAttribute('url')}][{$submenuitem->getAttribute('url')}][{else}][{$submenuitem->getAttribute('link')}][{/if}]" onclick="_navAct(this);" target="basefrm" class="rc"><b>[{if $linkicon}]<span class="[{$linkicon}]">[{/if}][{oxmultilang ident=$submenuitem->getAttribute('name')|default:$submenuitem->getAttribute('id') noerror=true}][{if $linkicon}]</span>[{/if}]</b></a>
                                </li>
                                [{assign var='linkicon' value=''}]
                            [{/if}]
                            [{/foreach}]
                        </ul>
                        [{/if}]
                    </li>
                [{/if}]
                [{/foreach}]
              [{/strip}]
            </ul>
        [{/if}]
        [{/foreach}]
    [{/block}]
    </td></tr>
    <tr><td class="extra">

    <ul>
        [{block name="admin_navigation_history"}]
            [{strip}]
                [{assign var='mh' value=$mh+1}]
                [{assign var='mn' value=1}]
                [{assign var='sm' value=0}]
                <li id="nav-[{$mh}]-[{$mn}]" class="[{if $blOpenHistory}]exp[{assign var='sHistoryId' value="nav-`$mh`-`$mn`"}][{/if}]">
                    <a class="rc" name="_hist" href="[{$oViewConf->getSelfLink()}]&cl=navigation&item=navigation.tpl&openHistory=1&[{$smarty.now}]#_hist"><b>[{oxmultilang ident=NAVIGATION_HISTORY noerror=true}]</b></a>

                    <ul>
                        [{foreach from=$menuhistory item=submenuitem}]
                            [{if $submenuitem->nodeType == XML_ELEMENT_NODE}]
                                [{assign var='sm' value=$sm+1}]
                                <li id="nav-[{$mh}]-[{$mn}]-[{$sm}]" class="">
                                    <a href="[{$submenuitem->getAttribute('link')}]" onclick="_navAct(this);" target="basefrm" class="rc"><b>[{oxmultilang ident=$submenuitem->getAttribute('name')|default:$submenuitem->getAttribute('id') noerror=true}]</b></a>
                                </li>
                            [{/if}]
                        [{/foreach}]
                    </ul>
                </li>
            [{/strip}]
        [{/block}]
    </ul>

    <ul>
        [{block name="admin_navigation_favorites"}]
            [{strip}]
                [{assign var='mh' value=$mh+1}]
                [{assign var='mn' value=1}]
                [{assign var='sm' value=0}]
                <li id="nav-[{$mh}]-[{$mn}]">
                    <a class="rc" onclick="_navExp(this);return false;" href="#" ><b>[{oxmultilang ident=NAVIGATION_FAVORITES noerror=true}]</b></a>
                    <a class="ed" href="[{$oViewConf->getSelfLink()}]&cl=navigation&amp;item=favorites.tpl" target="basefrm" >[{oxmultilang ident=NAVIGATION_FAVORITES_EDIT noerror=true}]</a>
                    <ul>
                        [{foreach from=$menufavorites item=submenuitem}]
                            [{if $submenuitem->nodeType == XML_ELEMENT_NODE}]
                                [{assign var='sm' value=$sm+1}]
                                <li id="nav-[{$mh}]-[{$mn}]-[{$sm}]" class="">
                                    <a href="[{$submenuitem->getAttribute('link')}]" onclick="_navAct(this);" target="basefrm" class="rc"><b>[{oxmultilang ident=$submenuitem->getAttribute('name')|default:$submenuitem->getAttribute('id') noerror=true}]</b></a>
                                </li>
                            [{/if}]
                        [{/foreach}]
                    </ul>
                </li>
            [{/strip}]
        [{/block}]
    </ul>

    </td></tr>
    </table>

    <script type="text/javascript">
        [{block name="admin_navigation_scripts"}]
            <!--
            var _expid = [{if $blOpenHistory}]'[{$sHistoryId}]'[{elseif $sNavExpId}]'[{$sNavExpId}]'[{else}]0[{/if}];
            function _navExp(el){
                var _cur = el.parentNode,
                    _exp = document.getElementById(_expid);
                _cur.className = "exp";
                if(_expid != 0){ _exp.className = "";}
                if(_expid == _cur.id){ _expid = 0;}else{_expid = _cur.id;}
            }

            var _actid = [{if $sNavActId}]'[{$sNavActId}]'[{else}]0[{/if}];
            function _navAct(el){
                 var _cur = el.parentNode,
                     _act = document.getElementById(_actid);
                _cur.className = "act";
                if(_actid != 0 && _actid != _cur.id){ _act.className = "";}
                _actid = _cur.id;
            }

            function _navExtExpAct(mnid,sbid){
                var _mnli = document.getElementById(mnid);
                var _sbli = document.getElementById(sbid);
                if(_mnli && _sbli) {
                    var _mna = _mnli.getElementsByTagName("a");
                    var _sba = _sbli.getElementsByTagName("a");
                    if(_mna.length && _sba.length) {
                        _navExp(_mna[0]);
                        _navAct(_sba[0]);
                    }
                }
            }

            function _navExtExp(mnid){
                var _mnli = document.getElementById(mnid);
                if(_mnli) {
                    var _mna = _mnli.getElementsByTagName("a");
                    if(_mna.length) {
                        _navExp(_mna[0]);
                    }
                }
            }

            function _navExtExpActByName(sbid){
                var sbid = "nav_" + sbid;
                var _sbli = document.getElementsByName(sbid)[0];
                if(_sbli) {
                    var mnid = _sbli.getAttribute("rel");
                    var _mnli = document.getElementById(mnid);
                    if(_mnli){
                        var _mna = _mnli.getElementsByTagName("a");
                        var _sba = _sbli.getElementsByTagName("a");
                        if(_mna.length && _sba.length) {
                            _navExp(_mna[0]);
                            _navAct(_sba[0]);
                        }
                    }
                }
            }

            //-->
        [{/block}]
    </script>
</body>
</html>