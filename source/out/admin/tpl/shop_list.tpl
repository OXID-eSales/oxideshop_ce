[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list"}]
[{assign var="where" value=$oView->getListFilter()}]

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
<!--
function editThis( sID )
{
    var oForm = top.navigation.adminnav.document.getElementById( "search" );
    if ( oForm ) {
        // passing this info about active view and tab to nav frame
        var oInputElement = document.createElement( 'input' );
        oInputElement.setAttribute( 'name', 'listview');
        oInputElement.setAttribute( 'type', 'hidden' );
        oInputElement.value = "[{$oViewConf->getActiveClassName()}]";
        oForm.appendChild( oInputElement );

        var oInputElement = document.createElement( 'input' );
        oInputElement.setAttribute( 'name', 'actedit');
        oInputElement.setAttribute( 'type', 'hidden' );
        oInputElement.value = "[{ $actedit }]";
        oForm.appendChild( oInputElement );

        var oInputElement = document.createElement( 'input' );
        oInputElement.setAttribute( 'name', 'editview');
        oInputElement.setAttribute( 'type', 'hidden' );
        oInputElement.value = top.oxid.admin.getClass( sID );
        oForm.appendChild( oInputElement );

        // selecting shop
        top.navigation.adminnav.selectShop( sID );
    }
}


window.onload = function ()
{
    [{ if $updatenav }]
    var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
    oTransfer.updatenav.value = 1;
    oTransfer.cl.value = '[{ $default_edit }]';
    [{ /if}]
    top.reloadEditFrame();
}
         //-->
</script>

<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{include file="_formparams.tpl" cl="shop_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang delshopid="" updatenav=""}]




[{include file="pagetabsnippet.tpl"}]

<script type="text/javascript">
if (parent.parent != null && parent.parent.setTitle )
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="SHOP_LIST_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="SHOP_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
