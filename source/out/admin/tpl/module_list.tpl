[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list"}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    top.reloadEditFrame();
    [{if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
    [{/if}]
}

//-->
</script>


<div id="liste">

<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="module_list">
    <input type="hidden" name="lstrt" value="[{ $lstrt }]">
    <input type="hidden" name="sort" value="[{ $sort }]">
    <input type="hidden" name="actedit" value="[{ $actedit }]">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="language" value="[{ $actlang }]">
    <input type="hidden" name="editlanguage" value="[{ $actlang }]">

<table cellspacing="0" cellpadding="0" border="0" width="100%">
<colgroup>
    <col width="3%">
    <col width="98%">
</colgroup>
<tr class="listitem">
    <td valign="top" class="listfilter first" height="20">
        <div class="r1"><div class="b1">&nbsp;</div></div>
    </td>
    <td valign="top" class="listfilter" height="20">
        <div class="r1"><div class="b1">&nbsp;</div></div>
    </td>
</tr>
<tr>
    <td class="listheader first" height="15">
        <b><a href="Javascript:document.search.sort.value='oxtitle';document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_ACTIVE" }]</a></b>
    </td>
    <td class="listheader" height="15">
        <b><a href="Javascript:document.search.sort.value='oxtitle';document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_NAME" }]</a></b>
    </td>
</tr>
[{foreach from=$mylist item=listitem}]
<tr id="row.[{counter}]">
    [{cycle values="listitem,listitem2" assign="zebra"}]
    [{ if $listitem->getInfo('id') == $oxid }]
        [{assign var="zebra" value=listitem4 }]
    [{ /if}]
    <td valign="top" class="[{$zebra}][{ if $listitem->isActive()}] active[{/if}]" height="15">
        <div class="listitemfloating">
            <a href="Javascript:top.oxid.admin.editThis('[{ $listitem->getId() }]');">&nbsp;</a></div></td>
        </div>
    </td>
    <td valign="top" class="[{$zebra}]" height="15">
        <div class="listitemfloating">
            <a href="Javascript:top.oxid.admin.editThis('[{ $listitem->getId() }]');" [{if $listitem->isFile() }]class="infoList"[{/if}]>[{ $listitem->getTitle() }]</a>
        </div>
    </td>
</tr>
[{/foreach}]
[{include file="pagenavisnippet.tpl" colspan="5"}]
</table>
</form>
</div>

<script language="javascript">
    sModuleId = '[{ $oxid }]';
    if (sModuleId == '-1' ) {
        sActClass = "module_main";
        sDefClass = "module_main";
        var oSearch = document.getElementById( "search" );
        oSearch.actedit.value = 1;
    }
</script>

[{include file="pagetabsnippet.tpl"}]

<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="ACTIONS_LIST_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="ACTIONS_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>

