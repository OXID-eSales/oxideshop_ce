[{include file="headitem.tpl" box="list" title="DYNSCREEN_TITLE"|oxmultilangassign box="list"}]

<script type="text/javascript">
<!--
function changeEditBar( sLocation, sPos )
{
    parent.edit.location = '[{$oViewConf->getSelfLink()|replace:"&amp;":"&"}]&cl=' + sLocation;

    var oSearch = document.getElementById("search");
    oSearch.actedit.value = sPos;
    oSearch.submit();
}

function ChangeExternal( sLocation, sPos)
{
    parent.edit.location=sLocation;

    var oSearch = document.getElementById("search");
    oSearch.actedit.value=sPos;
    oSearch.submit();
}

//-->
</script>

<div class="liste">
<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="dynscreen_list">
    <input type="hidden" name="actedit" value="[{ $actedit }]">
    <input type="hidden" name="oxid" value="1">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="menu" value="[{$menu}]">
</form>


<b>[{ oxmultilang ident="GENERAL_OXIDESHOP" }]</b> - [{ oxmultilang ident="DYNSCREEN_LIST_SERVICE" }]
</div>

[{include file="pagetabsnippet.tpl" noOXIDCheck="true" sEditAction="changeEditBar" }]


<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="DYNSCREEN_LIST_SERVICE" }]";
    parent.parent.sMenuSubItem = "";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
[{include file="bottomitem.tpl"}]
