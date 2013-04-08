[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list"}]
[{assign var="where" value=$oView->getListFilter()}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
<!--
function StornoThisArticle( sID)
{
    blCheck = confirm("[{ oxmultilang ident="ORDER_LIST_YOUWANTTOSTORNO" }]");
    if( blCheck == true)
    {
        var oSearch = document.getElementById("search");
        oSearch.oxid.value=sID;
        oSearch.fnc.value='storno';

        var oTransfer = parent.edit.document.getElementById("transfer");
        oTransfer.oxid.value=sID;
        oTransfer.cl.value='[{ $default_edit }]';

       //forcing edit frame to reload after submit
       top.forceReloadingEditFrame();

       oSearch.submit();
    }
}
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
[{include file="_formparams.tpl" cl="order_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <colgroup>
        [{block name="admin_order_list_colgroup"}]
            <col width="25%">
            <col width="25%">
            <col width="10%">
            <col width="17%">
            <col width="17%">
            <col width="2%">
        [{/block}]
    </colgroup>
    <tr class="listitem">
        [{block name="admin_order_list_filter"}]
            <td valign="top" class="listfilter first" height="20">
                <div class="r1"><div class="b1">
                <select name="folder" class="folderselect" onChange="document.search.submit();">
                    <option value="-1" style="color: #000000;">[{ oxmultilang ident="ORDER_LIST_FOLDER_ALL" }]</option>
                    [{foreach from=$afolder key=field item=color}]
                    <option value="[{ $field }]" [{ if $folder == $field }]SELECTED[{/if}] style="color: [{ $color }];">[{ oxmultilang ident=$field noerror=true }]</option>
                    [{/foreach}]
                </select>
                <input class="listedit" type="text" size="15" maxlength="128" name="where[oxorder][oxorderdate]" value="[{ $where.oxorder.oxorderdate|oxformdate }]" [{include file="help.tpl" helpid=order_date}]>
                </div></div>
            </td>
            <td valign="top" class="listfilter" height="20">
                <div class="r1"><div class="b1">
                <select name="addsearchfld" class="folderselect" >
                    <option value="-1" style="color: #000000;">[{ oxmultilang ident="ORDER_LIST_PAID" }]</option>
                    [{foreach from=$asearch key=table item=desc}]
                    [{assign var="ident" value=ORDER_SEARCH_FIELD_$desc}]
                    [{assign var="ident" value=$ident|oxupper }]
                    <option value="[{ $table }]" [{ if $addsearchfld == $table }]SELECTED[{/if}]>[{ oxmultilang|oxtruncate:20:"..":true ident=$ident }]</option>
                    [{/foreach}]
                </select>
                <input class="listedit" type="text" size="15" maxlength="128" name="addsearch" value="[{ $addsearch }]">
                </div></div>
            </td>
            <td valign="top" class="listfilter" height="20">
                <div class="r1"><div class="b1">
                <input class="listedit" type="text" size="7" maxlength="128" name="where[oxorder][oxordernr]" value="[{ $where.oxorder.oxordernr }]">
                </div></div>
            </td>
            <td valign="top" class="listfilter" height="20">
                <div class="r1"><div class="b1">
                <input class="listedit" type="text" size="50" maxlength="128" name="where[oxorder][oxbillfname]" value="[{ $where.oxorder.oxbillfname }]">
                </div></div>
            </td>
            <td valign="top" class="listfilter" height="20" colspan="2" nowrap>
                <div class="r1"><div class="b1">
                <div class="find"><input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]"></div>
                <input class="listedit" type="text" size="50" maxlength="128" name="where[oxorder][oxbilllname]" value="[{ $where.oxorder.oxbilllname }]">
                </div></div>
            </td>
        [{/block}]
    </tr>
    <tr>
        [{block name="admin_order_list_sorting"}]
            <td class="listheader first" height="15">&nbsp;<a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxorder', 'oxorderdate', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="ORDER_LIST_ORDERTIME" }]</a></td>
            <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxorder', 'oxpaid', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="ORDER_LIST_PAID" }]</a></td>
            <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxorder', 'oxordernr', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_ORDERNUM" }]</a></td>
            <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxorder', 'oxbillfname', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="ORDER_LIST_CUSTOMERFNAME" }]</a></td>
            <td class="listheader" height="15" colspan="2"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxorder', 'oxbilllname', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="ORDER_LIST_CUSTOMERLNAME" }]</a></td>
        [{/block}]
    </tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">

        [{block name="admin_order_list_item"}]
            [{ if $listitem->oxorder__oxstorno->value == 1 }]
                [{assign var="listclass" value=listitem3 }]
            [{else}]
                [{ if $listitem->blacklist == 1}]
                    [{assign var="listclass" value=listitem3 }]
                [{ else}]
                    [{assign var="listclass" value=listitem$blWhite }]
                [{ /if}]
            [{/if}]
            [{ if $listitem->getId() == $oxid }]
                [{assign var="listclass" value=listitem4 }]
            [{ /if}]
            <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxorder__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxorder__oxorderdate|oxformdate:'datetime':true }]</a></div></td>
            <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxorder__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxorder__oxpaid|oxformdate }]</a></div></td>
            <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxorder__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxorder__oxordernr->value }]</a></div></td>
            <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxorder__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxorder__oxbillfname->value }]</a></div></td>
            <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxorder__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxorder__oxbilllname->value }]</a></div></td>
            <td class="[{ $listclass}]">
                [{if !$readonly}]
                    <a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->oxorder__oxid->value }]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a>
                    <a href="Javascript:StornoThisArticle('[{ $listitem->oxorder__oxid->value }]');" class="pause" id="pau.[{$_cnt}]" [{include file="help.tpl" helpid=item_storno}]></a>
                [{/if}]</td>
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
    parent.parent.sMenuItem    = "[{ oxmultilang ident="ORDER_LIST_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="ORDER_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>

