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
[{include file="_formparams.tpl" cl="user_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<colgroup>
    [{block name="admin_user_list_colgroup"}]
        <col width="20%">
        <col width="20%">
        <col width="19%">
        <col width="10%">
        <col width="10%">
        <col width="10%">
        <col width="10%">
        <col width="1%">
    [{/block}]
<colgroup>
<tr class="listitem">
    [{block name="admin_user_list_filter"}]
        <td valign="top" class="listfilter first" height="20">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="20" maxlength="128" name="where[oxuser][oxlname]" value="[{ $where.oxuser.oxlname }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="20" maxlength="128" name="where[oxuser][oxusername]" value="[{ $where.oxuser.oxusername }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="20" maxlength="128" name="where[oxuser][oxstreet]" value="[{ $where.oxuser.oxstreet }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="10" maxlength="128" name="where[oxuser][oxzip]" value="[{ $where.oxuser.oxzip }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="20" maxlength="128" name="where[oxuser][oxcity]" value="[{ $where.oxuser.oxcity }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="15" maxlength="128" name="where[oxuser][oxfon]" value="[{ $where.oxuser.oxfon }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter" colspan="2" nowrap>
            <div class="r1"><div class="b1">
            <div class="find"><input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]"></div>
            <input class="listedit" type="text" size="5" maxlength="128" name="where[oxuser][oxcustnr]" value="[{ $where.oxuser.oxcustnr }]">
            </div>
            </div></div>
        </td>
    [{/block}]
</tr>
<tr>
    [{block name="admin_user_list_sorting"}]
        <td class="listheader first" height="15">&nbsp;<a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxuser', 'oxlname', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_NAME" }]</a></td>
        <td class="listheader"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxuser', 'oxusername', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_EMAIL" }]</a></td>
        <td class="listheader"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxuser', 'oxstreet', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_STREET" }]</a></td>
        <td class="listheader"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxuser', 'oxzip', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="USER_LIST_ZIP" }]</a></td>
        <td class="listheader"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxuser', 'oxcity', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="USER_LIST_PLACE" }]</a></td>
        <td class="listheader"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxuser', 'oxfon', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_TELEPHONE" }]</a></td>
        <td class="listheader" colspan="2"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxuser', 'oxcustnr', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="USER_LIST_CUSTOMERNUM" }]</a></td>
    [{/block}]
</tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">
    [{block name="admin_user_list_item"}]
        [{ if $listitem->blacklist == 1}]
            [{assign var="listclass" value=listitem3 }]
        [{ else}]
            [{assign var="listclass" value=listitem$blWhite }]
        [{ /if}]
        [{ if $listitem->getId() == $oxid }]
            [{assign var="listclass" value=listitem4 }]
        [{ /if}]
        <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxuser__oxid->value}]');" class="[{ $listclass}]">[{ if !$listitem->oxuser__oxlname->value }]-kein Name-[{else}][{ $listitem->oxuser__oxlname->value }][{/if}] [{ $listitem->oxuser__oxfname->value }]</a></div></td>
        <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxuser__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->oxuser__oxusername->value|oxtruncate:21:"...":true }]</a></div></td>
        <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxuser__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->oxuser__oxstreet->value }]</a></div></td>
        <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxuser__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->oxuser__oxzip->value }]</a></div></td>
        <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxuser__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->oxuser__oxcity->value }]</a></div></td>
        <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxuser__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->oxuser__oxfon->value }]</a></div></td>
        <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxuser__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->oxuser__oxcustnr->value }]</a></div></td>

        <td class="[{ $listclass}]">
            [{ if !$listitem->isOx() && !$readonly  && !$listitem->blPreventDelete}]
            <a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->oxuser__oxid->value }]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a>
            [{ /if }]
        </td>
    [{/block}]
</tr>
[{if $blWhite == "2"}]
[{assign var="blWhite" value=""}]
[{else}]
[{assign var="blWhite" value="2"}]
[{/if}]
[{/foreach}]
[{include file="pagenavisnippet.tpl" colspan="8"}]
</table>
</form>
</div>

[{include file="pagetabsnippet.tpl"}]

<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="USER_LIST_MENNUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="USER_LIST_MENNUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
