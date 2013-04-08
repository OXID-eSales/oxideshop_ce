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
[{include file="_formparams.tpl" cl="category_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <colgroup>
        [{block name="admin_country_list_colgroup"}]
            <col width="4%">
            <col width="10%">
            <col width="87%">
            <col width="1%">
        [{/block}]
    </colgroup>
    <tr class="listitem">
        [{block name="admin_country_list_filter"}]
            <td valign="top" class="listfilter first" height="20">
                <div class="r1"><div class="b1">&nbsp;</div></div>
            </td>
            <td valign="top" class="listfilter" height="20" align="center">
                <div class="r1"><div class="b1">
                <input class="listedit" type="text" size="5" maxlength="128" name="where[oxcategories][oxsort]" value="[{ $where.oxcategories.oxsort }]">
                </div></div>
            </td>
            <td valign="top" class="listfilter" height="20" colspan="2">
                <div class="r1"><div class="b1">
                <div class="find">
                    <select name="changelang" class="editinput" onChange="Javascript:top.oxid.admin.changeLanguage();">
                    [{foreach from=$languages item=lang}]
                    <option value="[{ $lang->id }]" [{ if $lang->selected}]SELECTED[{/if}]>[{ $lang->name }]</option>
                    [{/foreach}]
                    </select>
                    <input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]">
                </div>

                <select name="where[oxcategories][oxparentid]" class="editinput" onChange="Javascript:document.search.submit();">
                [{foreach from=$cattree->aList item=pcat}]
                <option value="[{ $pcat->oxcategories__oxid->value }]" [{ if $pcat->selected}]SELECTED[{/if}]>[{ $pcat->oxcategories__oxtitle->getRawValue() }]</option>
                [{/foreach}]
                </select>

                <input class="listedit" type="text" size="50" maxlength="128" name="where[oxcategories][oxtitle]" value="[{ $where.oxcategories.oxtitle}]">
                </div></div>
            </td>
        [{/block}]
    </tr>
    <tr>
        [{block name="admin_country_list_sorting"}]
            <td class="listheader first" height="15" align="center"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxcategories', 'oxactive', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_ACTIVTITLE" }]</a></td>
            <td class="listheader" height="15" align="center"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxcategories', 'oxsort', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_SORT" }]</a></td>
            <td class="listheader" height="15" colspan="2"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxcategories', 'oxtitle', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_TITLE" }]</a></td>
        [{/block}]
    </tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">

        [{block name="admin_country_list_item"}]
            [{ if $listitem->blacklist == 1}]
                [{assign var="listclass" value=listitem3 }]
            [{ else}]
                [{assign var="listclass" value=listitem$blWhite }]
            [{ /if}]
            [{ if $listitem->getId() == $oxid }]
                [{assign var="listclass" value=listitem4 }]
            [{ /if}]
            <td valign="top" class="[{ $listclass}][{ if $listitem->oxcategories__oxactive->value == 1}] active[{/if}]"><div class="listitemfloating">&nbsp;</div></td>
            <td valign="top" class="[{ $listclass}]" height="15" align="center"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxcategories__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxcategories__oxsort->value }]</a></div></td>
            <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxcategories__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxcategories__oxtitle->value }]</a></div></td>
            <td class="[{ $listclass}]">
            [{if !$readonly }]

                [{ if $listitem->oxcategories__oxleft->value + 1 == $listitem->oxcategories__oxright->value }]
                  <a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->oxcategories__oxid->value }]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a>
                [{/if}]
            [{/if}]
            </td>
        [{/block}]
    </tr>
[{if $blWhite == "2"}]
[{assign var="blWhite" value=""}]
[{else}]
[{assign var="blWhite" value="2"}]
[{/if}]
[{/foreach}]
[{include file="pagenavisnippet.tpl" colspan="4"}]
</table>
</form>
</div>


[{include file="pagetabsnippet.tpl"}]

<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="GENERAL_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="CATEGORY_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>