[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list"}]
[{assign var="where" value=$oView->getListFilter()}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    [{if $updatenav}]
    var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
    oTransfer.updatenav.value = 1;
    oTransfer.cl.value = '[{$default_edit}]';
    [{/if}]
    top.reloadEditFrame();
}
//-->
</script>

<form name="search" id="search" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{include file="_formparams.tpl" cl="shop_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang delshopid="" updatenav=""}]
    [{include file="pagetabsnippet.tpl"}]
</form>

<script type="text/javascript">
if (parent.parent != null && parent.parent.setTitle )
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{oxmultilang ident="SHOP_LIST_MENUITEM"}]";
    parent.parent.sMenuSubItem = "[{oxmultilang ident="SHOP_LIST_MENUSUBITEM"}]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
