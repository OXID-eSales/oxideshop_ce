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
[{include file="_formparams.tpl" cl="voucherserie_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <colgroup>
        [{block name="admin_voucherserie_list_colgroup"}]
            <col width="39%">
            <col width="15%">
            <col width="15%">
            <col width="15%">
            <col width="15%">
            <col width="1%">
        [{/block}]
    </colgroup>
    <tr class="listitem">
    [{block name="admin_voucherserie_list_filter"}]
        <td valign="top" class="listfilter first" height="20">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="30" maxlength="128" name="where[oxvoucherseries][oxserienr]" value="[{ $where.oxvoucherseries.oxserienr }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="15" maxlength="128" name="where[oxvoucherseries][oxdiscount]" value="[{ $where.oxvoucherseries.oxdiscount }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="15" maxlength="128" name="where[oxvoucherseries][oxbegindate]" value="[{ $where.oxvoucherseries.oxbegindate }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="15" maxlength="128" name="where[oxvoucherseries][oxenddate]" value="[{ $where.oxvoucherseries.oxenddate }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter" colspan="2">
            <div class="r1"><div class="b1">
            <div class="find"><input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]"></div>
            <input class="listedit" type="text" size="15" maxlength="128" name="where[oxvoucherseries][oxminimumvalue]" value="[{ $where.oxvoucherseries.oxminimumvalue }]">
            </div></div>
        </td>
    [{/block}]
</tr>

<tr>
    [{block name="admin_voucherserie_list_sorting"}]
        <td class="listheader first" height="15">&nbsp;<a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxvoucherseries', 'oxserienr', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="VOUCHERSERIE_LIST_SERIALNUM" }]</a></td>
        <td class="listheader"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxvoucherseries', 'oxdiscount', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_DISCOUNT" }]</a></td>
        <td class="listheader"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxvoucherseries', 'oxbegindate', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_BEGINDATE" }]</a></td>
        <td class="listheader"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxvoucherseries', 'oxenddate', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_ENDDATE" }]</a></td>
        <td class="listheader" colspan="2"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxvoucherseries', 'oxminimumvalue', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="VOUCHERSERIE_LIST_MINVALUE" }]</a></td>
    [{/block}]
</tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">
    [{block name="admin_voucherserie_list_item"}]
        [{ if $listitem->blacklist == 1}]
            [{assign var="listclass" value=listitem3 }]
        [{ else}]
            [{assign var="listclass" value=listitem$blWhite }]
        [{ /if}]
        [{ if $listitem->oxvoucherseries__oxid->value == $oxid }]
            [{assign var="listclass" value=listitem4 }]
        [{ /if}]
        <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxvoucherseries__oxid->value}]');" class="[{ $listclass}]">[{ if !$listitem->oxvoucherseries__oxserienr->value }]-[{ oxmultilang ident="GENERAL_NONAME" }]-[{else}][{ $listitem->oxvoucherseries__oxserienr->value }][{/if}]</a></div></td>
        <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxvoucherseries__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->oxvoucherseries__oxdiscount->value }][{if $listitem->oxvoucherseries__oxdiscounttype->value == "percent"}] %[{/if}]</a></div></td>
        <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxvoucherseries__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->oxvoucherseries__oxbegindate->value }]</a></div></td>
        <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxvoucherseries__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->oxvoucherseries__oxenddate->value }]</a></div></td>
        <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxvoucherseries__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->oxvoucherseries__oxminimumvalue->value }]</a></div></td>
        <td class="[{ $listclass}]">
          [{if !$readonly}]
              <a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->oxvoucherseries__oxid->value }]');" class="delete" id="del.[{$_cnt}]" title="" [{include file="help.tpl" helpid=item_delete}]></a>
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
[{include file="pagenavisnippet.tpl" colspan="6"}]
</table>
</form>
</div>

[{include file="pagetabsnippet.tpl"}]


<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="VOUCHERSERIE_LIST_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="VOUCHERSERIE_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
