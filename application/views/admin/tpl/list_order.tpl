[{include file="headitem.tpl" title="SHOWLIST_TITLE"|oxmultilangassign box=" "}]

[{assign var="where" value=$oView->getListFilter()}]
[{assign var="whereparam" value=""}]
[{foreach from=$where item=aField key=sTable}]
  [{foreach from=$aField item=sFilter key=sField}]
    [{assign var="whereparam" value=$whereparam|cat:"where["|cat:$sTable|cat:"]["|cat:$sField|cat:"]="|cat:$sFilter|cat:"&amp;"}]
  [{/foreach}]
[{/foreach}]
[{assign var="viewListSize" value=$oView->getViewListSize()}]
[{assign var="whereparam" value=$whereparam|cat:"viewListSize="|cat:$viewListSize}]

<script type="text/javascript">
<!--
function editThis( sID )
{
    [{assign var="shMen" value=1}]

    [{foreach from=$menustructure item=menuholder }]
      [{if $shMen && $menuholder->nodeType == XML_ELEMENT_NODE && $menuholder->childNodes->length }]

        [{assign var="shMen" value=0}]
        [{assign var="mn" value=1}]

        [{foreach from=$menuholder->childNodes item=menuitem }]
          [{if $menuitem->nodeType == XML_ELEMENT_NODE && $menuitem->childNodes->length }]
            [{ if $menuitem->getAttribute('id') == 'mxorders' }]

              [{foreach from=$menuitem->childNodes item=submenuitem }]
                [{if $submenuitem->nodeType == XML_ELEMENT_NODE && $submenuitem->getAttribute('cl') == 'admin_order' }]

                    if ( top && top.navigation && top.navigation.adminnav ) {
                        var _sbli = top.navigation.adminnav.document.getElementById( 'nav-1-[{$mn}]-1' );
                        var _sba = _sbli.getElementsByTagName( 'a' );
                        top.navigation.adminnav._navAct( _sba[0] );
                    }

                [{/if}]
              [{/foreach}]

            [{ /if }]
            [{assign var="mn" value=$mn+1}]

          [{/if}]
        [{/foreach}]
      [{/if}]
    [{/foreach}]

    var oTransfer = document.getElementById("transfer");
    oTransfer.oxid.value=sID;
    oTransfer.cl.value='admin_order';
    oTransfer.submit();
}
//-->
</script>

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="list_order">
    <input type="hidden" name="updatelist" value="1">
</form>

[{ if $noresult }]
    <span class="listitem">
        <b>[{ oxmultilang ident="SHOWLIST_NORESULTS" }]</b><br><br>
    </span>
[{/if}]

<div id="liste">

<form name="showlist" id="showlist" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="list_order">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
    [{block name="admin_list_order_filter"}]
        <td class="listfilter first">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="15" maxlength="128" name="where[oxorder][oxorderdate]" value="[{ $where.oxorder.oxorderdate|oxformdate }]">
            </div></div>
        </td>
        <td class="listfilter">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="15" maxlength="128" name="where[oxorderarticles][oxartnum]" value="[{ $where.oxorderarticles.oxartnum }]">
            </div></div>
        </td>
        <td class="listfilter">
            <div class="r1"><div class="b1">&nbsp;</div></div>
        </td>
        <td class="listfilter">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="15" maxlength="128" name="where[oxorderarticles][oxtitle]" value="[{ $where.oxorderarticles.oxtitle }] [{ $where.oxorderarticles.oxselvariant }]">
            </div></div>
        </td>
        <td class="listfilter" colspan="2">
            <div class="r1">
              <div class="b1">
              <div class="find">
              <select name="viewListSize" class="editinput" onChange="JavaScript:top.oxid.admin.changeListSize()">
                <option value="50" [{ if $viewListSize == 50 }]SELECTED[{/if}]>50</option>
                <option value="100" [{ if $viewListSize == 100 }]SELECTED[{/if}]>100</option>
                <option value="200" [{ if $viewListSize == 200 }]SELECTED[{/if}]>200</option>
              </select>
              <input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]">
            </div>
            </div>
          </div>
        </td>
    [{/block}]
</tr>
<tr>
    [{block name="admin_list_order_sorting"}]
        <td class="listheader first"><a href="javascript:top.oxid.admin.setSorting( document.forms.showlist, '', 'oxorderdate', 'desc');document.forms.showlist.submit();" class="listheader">[{ oxmultilang ident="snporderlistoxorderdate" }]</a></td>
        <td class="listheader"><a href="javascript:top.oxid.admin.setSorting( document.forms.showlist, 'oxorderarticles', 'oxartnum', 'asc');document.forms.showlist.submit();" class="listheader">[{ oxmultilang ident="snporderlistoxartnum" }]</a></td>
        <td class="listheader"><a href="javascript:top.oxid.admin.setSorting( document.forms.showlist, '', 'oxorderamount', 'asc');document.forms.showlist.submit();" class="listheader">[{ oxmultilang ident="snporderlistsum" }]</a></td>
        <td class="listheader"><a href="javascript:top.oxid.admin.setSorting( document.forms.showlist, 'oxorderarticles', 'oxtitle', 'asc');document.forms.showlist.submit();" class="listheader">[{ oxmultilang ident="snporderlistoxtitle" }]</a></td>
        <td class="listheader"><a href="javascript:top.oxid.admin.setSorting( document.forms.showlist, '', 'oxprice', 'asc');document.forms.showlist.submit();" class="listheader">[{ oxmultilang ident="SHOWLIST_SUM" }]</a></td>
    [{/block}]
</tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=oOrder}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">
        [{block name="admin_list_order_item"}]
            <td class="listitem[{ $blWhite }]"><a href="Javascript:editThis( '[{ $oOrder->oxorder__oxorderid->value }]');" class="listitem[{ $blWhite }]">[{ $oOrder->oxorder__oxorderdate|oxformdate }]</a></td>
            <td class="listitem[{ $blWhite }]"><a href="Javascript:editThis( '[{ $oOrder->oxorder__oxorderid->value }]');" class="listitem[{ $blWhite }]">[{ $oOrder->oxorder__oxartnum->value }]</a></td>
            <td class="listitem[{ $blWhite }]"><a href="Javascript:editThis( '[{ $oOrder->oxorder__oxorderid->value }]');" class="listitem[{ $blWhite }]">[{ $oOrder->oxorder__oxorderamount->value }]</a></td>
            <td class="listitem[{ $blWhite }]"><a href="Javascript:editThis( '[{ $oOrder->oxorder__oxorderid->value }]');" class="listitem[{ $blWhite }]">[{ $oOrder->oxorder__oxtitle->getRawValue() }]</a></td>
            <td class="listitem[{ $blWhite }]"><a href="Javascript:editThis( '[{ $oOrder->oxorder__oxorderid->value }]');" class="listitem[{ $blWhite }]">[{ $oOrder->oxorder__oxprice->value }]</a></td>
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
[{ if $sumresult}]
<span class="listitem">
<b>[{ oxmultilang ident="SHOWLIST_SUM" }]:</b> [{ $sumresult}]<br>
</span>
[{/if}]
</div>

<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="ORDER_LIST_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="snporderlistheader" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
