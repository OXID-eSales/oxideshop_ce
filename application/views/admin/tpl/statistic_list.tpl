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
[{include file="_formparams.tpl" cl="statistic_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<colgroup>
    [{block name="admin_statistic_list_colgroup"}]
        <col width="98%"><col width="2%">
    [{/block}]
</colgroup>
<tr class="listitem">
    [{block name="admin_statistic_list_filter"}]
        <td valign="top" class="listfilter first" height="20" colspan="2">
        <div class="r1"><div class="b1">
        <div class="find"><input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]"></div>
        <input class="listedit" type="text" size="60" maxlength="128" name="where[oxstatistics][oxtitle]" value="[{ $where.oxstatistics.oxtitle }]">
        </div></div>
        </td>
    [{/block}]
</tr>
<tr>
    [{block name="admin_statistic_list_sorting"}]
        <td class="listheader first" height="15" colspan="2"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxstatistics', 'oxtitle', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_TITLE" }]</a></td>
    [{/block}]
</tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">
    [{block name="admin_statistic_list_item"}]
        [{ if $listitem->blacklist == 1}]
            [{assign var="listclass" value=listitem3 }]
        [{ else}]
            [{assign var="listclass" value=listitem$blWhite }]
        [{ /if}]
        [{ if $listitem->getId() == $oxid }]
            [{assign var="listclass" value=listitem4 }]
        [{ /if}]
        <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxstatistics__oxid->value}]');" class="[{ $listclass}]">[{ if !$listitem->oxstatistics__oxtitle->value }]-[{ oxmultilang ident="GENERAL_NONAME" }]-[{else}][{ $listitem->oxstatistics__oxtitle->value }][{/if}]</a></div></td>
        <td class="[{ $listclass}]"><a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->oxstatistics__oxid->value }]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a></td>
    [{/block}]
</tr>
[{if $blWhite == "2"}]
[{assign var="blWhite" value=""}]
[{else}]
[{assign var="blWhite" value="2"}]
[{/if}]
[{/foreach}]
[{include file="pagenavisnippet.tpl" colspan="2"}]
</table>
</form>
</div>

[{include file="pagetabsnippet.tpl"}]

<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="STASTISTIC_LIST_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="STASTISTIC_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
