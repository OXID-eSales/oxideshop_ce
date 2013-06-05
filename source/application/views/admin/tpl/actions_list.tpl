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
    top.reloadEditFrame();
    [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
    [{ /if}]
}
//-->
</script>


<div id="liste">

<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{include file="_formparams.tpl" cl="actions_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <colgroup>
        <col width="3%">
        <col width="30%">
        <col width="30%">
        <col width="30%">
        <col width="7%">
    </colgroup>
<tr class="listitem">
    <td valign="top" class="listfilter first" height="20"  colspan="5">
        <div class="r1">
            <div class="b1">

              <select name="displaytype" class="folderselect" onChange="document.search.submit();">
                <option value="">[{ oxmultilang ident="PROMOTION_LIST_ALL" }]</option>
                <option value="1" [{ if $displaytype == "1" }]SELECTED[{/if}]>[{ oxmultilang ident="PROMOTION_LIST_ACTIVE" }]</option>
                <option value="2" [{ if $displaytype == "2" }]SELECTED[{/if}]>[{ oxmultilang ident="PROMOTION_LIST_UPCOMING" }]</option>
                <option value="3" [{ if $displaytype == "3" }]SELECTED[{/if}]>[{ oxmultilang ident="PROMOTION_LIST_EXPIRED" }]</option>
              </select>

              <div class="find">
                <select name="changelang" class="editinput" onChange="Javascript:top.oxid.admin.changeLanguage();">
                  [{foreach from=$languages item=lang}]
                  <option value="[{ $lang->id }]" [{ if $lang->selected}]SELECTED[{/if}]>[{ $lang->name }]</option>
                  [{/foreach}]
                </select>
                <input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]">
              </div>

              <input class="listedit" type="text" size="50" maxlength="128" name="where[oxactions][oxtitle]" value="[{ $where.oxactions.oxtitle }]">
            </div>
        </div>
    </td>
</tr>
<tr>
    <td class="listheader first" height="15" width="30" align="center"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxactions', 'oxactive', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_ACTIVTITLE" }]</a></td>
    <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxactions', 'oxtitle', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_NAME" }]</a></td>
    <td class="listheader"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxactions', 'oxactivefrom', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="PROMOTION_LIST_STARTTIME" }]</a></td>
    <td class="listheader"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxactions', 'oxtype', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_TYPE" }]</a></td>
    <td class="listheader"></td>
</tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">

    [{ if $listitem->blacklist == 1}]
        [{assign var="listclass" value=listitem3 }]
    [{ else}]
        [{assign var="listclass" value=listitem$blWhite }]
    [{ /if}]
    [{ if $listitem->getId() == $oxid }]
        [{assign var="listclass" value=listitem4 }]
    [{ /if}]
    <td valign="top" class="[{ $listclass}][{ if $listitem->oxactions__oxactive->value == 1}] active[{/if}]" height="15"><div class="listitemfloating">&nbsp</a></div></td>
    <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxactions__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxactions__oxtitle->value }]</a></div></td>
    <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxactions__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxactions__oxactivefrom->value }]</a></div></td>
    <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating">
        <a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxactions__oxid->value}]');" class="[{ $listclass}]">
            [{if $listitem->oxactions__oxtype->value == 3 }]
                [{ oxmultilang ident="PROMOTIONS_MAIN_TYPE_BANNER" }]
            [{elseif $listitem->oxactions__oxtype->value == 2 }]
                [{ oxmultilang ident="PROMOTIONS_MAIN_TYPE_PROMO" }]
            [{else}]
                [{ oxmultilang ident="PROMOTIONS_MAIN_TYPE_ACTION" }]
            [{/if}]
        </a></div></td>
    <td class="[{ $listclass}]">[{ if !$listitem->isOx() && !$readonly && $listitem->oxactions__oxtype->value > 0}]<a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->oxactions__oxid->value }]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a>[{/if}]</td>
</tr>
[{if $blWhite == "2"}]
[{assign var="blWhite" value=""}]
[{else}]
[{assign var="blWhite" value="2"}]
[{/if}]
[{/foreach}]
[{include file="pagenavisnippet.tpl" colspan="5"}]
</table>
</form>
</div>

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

