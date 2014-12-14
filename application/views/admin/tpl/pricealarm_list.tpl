[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list"}]
[{assign var="where" value=$oView->getListFilter()}]


  [{ if $shopid != "oxbaseshop" }]
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
[{include file="_formparams.tpl" cl="pricealarm_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<colgroup>
    [{block name="admin_pricealarm_list_colgroup"}]
        <col width="15%">
        <col width="15%">
        <col width="10%">
        <col width="10%">
        <col width="30%">
        <col width="10%">
        <col width="8%">
        <col width="2%">
    [{/block}]
</colgroup>
<tr class="listitem">
    [{block name="admin_pricealarm_list_filter"}]
        <td valign="top" class="listfilter first" height="20">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="20" maxlength="128" name="where[oxpricealarm][oxemail]" value="[{ $where.oxpricealarm.oxemail }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter" height="20">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="20" maxlength="128" name="where[oxuser][oxlname]" value="[{ $where.oxuser.oxlname }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter" height="20">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="20" maxlength="128" name="where[oxpricealarm][oxinsert]" value="[{ $where.oxpricealarm.oxinsert }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter" height="20">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="20" maxlength="128" name="where[oxpricealarm][oxsended]" value="[{ $where.oxpricealarm.oxsended }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter" height="20">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="20" maxlength="128" name="where[oxarticles][oxtitle]" value="[{ $where.oxarticles.oxtitle }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter" height="20">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="5" maxlength="128" name="where[oxpricealarm][oxprice]" value="[{ $where.oxpricealarm.oxprice }]">
            </div></div>
        </td>
        <td valign="top" class="listfilter" height="20" [{if count($mylist) > 0}]colspan="2"[{/if}]>
            <div class="r1"><div class="b1">
            <div class="find"><input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]"></div>
            <input class="listedit" type="text" size="5" maxlength="128" name="where[oxarticles][oxprice]" value="[{ $where.oxarticles.oxprice }]">
            </div></div>
        </td>
    [{/block}]
</tr>
<tr>
    [{block name="admin_pricealarm_list_sorting"}]
        <td class="listheader first" height="15">&nbsp;<a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxpricealarm', 'oxemail', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_EMAIL" }]</a></td>
        <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxuser', 'oxlname', 'asc');top.oxid.admin.setSorting( document.search, 'oxuser', 'oxfname', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_NAME" }]</a></td>
        <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxpricealarm', 'oxinsert', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="PRICEALARM_LIST_CONFIRMDATE" }]</a></td>
        <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxpricealarm', 'oxsended', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="PRICEALARM_LIST_SENDDATE" }]</a></td>
        <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxarticles', 'oxtitle', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_ITEM" }]</a></td>
        <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxpricealarm', 'oxprice', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="PRICEALARM_LIST_CUSTOMERSPRICE" }]</a></td>
        <td class="listheader" height="15"  [{if count($mylist) > 0}]colspan="2"[{/if}]>&nbsp;<a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxarticles', 'oxprice', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="PRICEALARM_LIST_STANDARTPRICE" }]</a></td>
    [{/block}]
</tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">
    [{block name="admin_pricealarm_list_item"}]
        [{ if $listitem->blacklist == 1}]
            [{assign var="listclass" value=listitem3 }]
        [{ else}]
            [{assign var="listclass" value=listitem$blWhite }]
        [{ /if}]
        [{ if $listitem->getId() == $oxid }]
            [{assign var="listclass" value=listitem4 }]
        [{ /if}]
        <td valign="top" class="[{$listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxpricealarm__oxid->value}]');" class="[{if $listitem->getPriceAlarmStatus()==1}]listitemred[{elseif $listitem->getPriceAlarmStatus()==2}]listitemgreen[{else}][{$listclass}][{/if}]">[{ $listitem->oxpricealarm__oxemail->value }]</a></div></td>
        <td valign="top" class="[{$listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxpricealarm__oxid->value}]');" class="[{if $listitem->getPriceAlarmStatus()==1}]listitemred[{elseif $listitem->getPriceAlarmStatus()==2}]listitemgreen[{else}][{$listclass}][{/if}]">[{ $listitem->oxpricealarm__userlname->value }] [{ $listitem->oxpricealarm__userfname->value }]</a></div></td>
        <td valign="top" class="[{$listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxpricealarm__oxid->value}]');" class="[{if $listitem->getPriceAlarmStatus()==1}]listitemred[{elseif $listitem->getPriceAlarmStatus()==2}]listitemgreen[{else}][{$listclass}][{/if}]">[{ $listitem->oxpricealarm__oxinsert|oxformdate }]</a></div></td>
        <td valign="top" class="[{$listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxpricealarm__oxid->value}]');" class="[{if $listitem->getPriceAlarmStatus()==1}]listitemred[{elseif $listitem->getPriceAlarmStatus()==2}]listitemgreen[{else}][{$listclass}][{/if}]">[{ $listitem->oxpricealarm__oxsended|oxformdate }]</a></div></td>
        <td valign="top" class="[{$listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxpricealarm__oxid->value}]');" class="[{if $listitem->getPriceAlarmStatus()==1}]listitemred[{elseif $listitem->getPriceAlarmStatus()==2}]listitemgreen[{else}][{$listclass}][{/if}]">[{ $listitem->getTitle() }]</a></div></td>
        <td valign="top" class="[{$listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxpricealarm__oxid->value}]');" class="[{if $listitem->getPriceAlarmStatus()==1}]listitemred[{elseif $listitem->getPriceAlarmStatus()==2}]listitemgreen[{else}][{$listclass}][{/if}]">[{ $listitem->getFProposedPrice() }]&nbsp;[{ $listitem->oxpricealarm__oxcurrency->value }]</a></div></td>
        <td valign="top" class="[{$listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxpricealarm__oxid->value}]');" class="[{if $listitem->getPriceAlarmStatus()==1}]listitemred[{elseif $listitem->getPriceAlarmStatus()==2}]listitemgreen[{else}][{$listclass}][{/if}]">[{ $listitem->getFPrice() }]&nbsp;[{ $listitem->oxpricealarm__oxcurrency->value }]</a></div></td>
        <td class="[{$listclass}]">
          [{ if !$listitem->isOx() }]
            [{ if $readonly == ""}]
              <a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->oxpricealarm__oxid->value }]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a>
            [{/if}]
          [{/if}]
    [{/block}]
    </td>
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
    parent.parent.sMenuItem    = "[{ oxmultilang ident="PRICEALARM_LIST_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="PRICEALARM_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
