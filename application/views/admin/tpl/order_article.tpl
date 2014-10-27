[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function editThis(sID)
{
    var oSearch = document.getElementById("search");
    oSearch.oxid.value = sID;
    oSearch.cl.value = 'article_main';
    oSearch.submit();

    var oSearch = parent.list.document.getElementById("search");
    oSearch.sort.value = '';
    oSearch.fnc.value = '';
    oSearch.lstrt.value = '';
    oSearch.oxid.value = sID;
    oSearch.cl.value = 'article_list';
    oSearch.actedit.value = 1;
    oSearch.submit();
}
function EditVoucher( sID)
{
    var oSearch = document.getElementById("search");
    oSearch.oxid.value=sID;
    oSearch.cl.value='voucher_main';
    oSearch.submit();

    var oSearch = parent.list.document.getElementById("search");
    oSearch.sort.value = '';
    oSearch.cl.value='voucher_list';
    oSearch.actedit.value=1;
    oSearch.submit();
}
function DeleteThisArticle( sID)
{
    blCheck = confirm("[{ oxmultilang ident="ORDER_ARTICLE_YOUWANTTODELETE" }]");
    if( blCheck == true)
    {
        var oDeleteThisArticle = document.getElementById("DeleteThisArticle");
        oDeleteThisArticle.sArtID.value=sID;
        oDeleteThisArticle.submit();
    }
}

function StornoThisArticle( sID)
{
    blCheck = confirm("[{ oxmultilang ident="ORDER_ARTICLE_YOUWANTTOSTORNO" }]");
    if( blCheck == true)
    {
        var oDeleteThisArticle = document.getElementById("DeleteThisArticle");
        oDeleteThisArticle.sArtID.value=sID;
        oDeleteThisArticle.fnc.value='storno';
        oDeleteThisArticle.submit();
    }
}

//-->
</script>

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{assign var="oCurr" value=$edit->getOrderCurrency() }]

<form name="DeleteThisArticle" id="DeleteThisArticle" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cur" value="[{ $oCurr->id }]">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="sArtID" value="">
    <input type="hidden" name="cl" value="order_article">
    <input type="hidden" name="fnc" value="DeleteThisArticle">
</form>

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cur" value="[{ $oCurr->id }]">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="order_article">
</form>


<table cellspacing="0" cellpadding="0" border="0" width="98%">
<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cur" value="[{ $oCurr->id }]">
    <input type="hidden" name="cl" value="order_article">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="fnc" value="updateOrder">
<tr>
    [{block name="admin_order_article_header"}]
        <td class="listheader first">[{ oxmultilang ident="GENERAL_SUM" }]</td>
        <td class="listheader" height="15">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_ITEMNR" }]</td>
        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_TITLE" }]</td>
        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_TYPE" }]</td>
        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="ORDER_ARTICLE_PARAMS" }]</td>
        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_SHORTDESC" }]</td>
        [{if $edit->isNettoMode() }]
            <td class="listheader">[{ oxmultilang ident="ORDER_ARTICLE_ENETTO" }]</td>
        [{else}]
            <td class="listheader">[{ oxmultilang ident="ORDER_ARTICLE_EBRUTTO" }]</td>
        [{/if}]
        <td class="listheader">[{ oxmultilang ident="GENERAL_ATALL" }]</td>
        <td class="listheader" colspan="3">[{ oxmultilang ident="ORDER_ARTICLE_MWST" }]</td>
    [{/block}]
</tr>
[{assign var="blWhite" value=""}]
[{foreach from=$edit->getOrderArticles() item=listitem name=orderArticles}]
<tr id="art.[{$smarty.foreach.orderArticles.iteration}]">
    [{block name="admin_order_article_listitem"}]
        [{ if $listitem->oxorderarticles__oxstorno->value == 1 }]
            [{assign var="listclass" value=listitem3 }]
        [{else}]
            [{assign var="listclass" value=listitem$blWhite }]
        [{/if}]
        <td valign="top" class="[{ $listclass}]">[{ if $listitem->oxorderarticles__oxstorno->value != 1 && !$listitem->isBundle() }]<input type="text" name="aOrderArticles[[{$listitem->getId()}]][oxamount]" value="[{ $listitem->oxorderarticles__oxamount->value }]" class="listedit">[{else}][{ $listitem->oxorderarticles__oxamount->value }][{/if}]</td>
        <td valign="top" class="[{ $listclass}]" height="15">[{if $listitem->oxarticles__oxid->value}]<a href="Javascript:editThis('[{ $listitem->oxarticles__oxid->value}]');" class="[{ $listclass}]">[{/if}][{ $listitem->oxorderarticles__oxartnum->value }]</a></td>
        <td valign="top" class="[{ $listclass}]">[{if $listitem->oxarticles__oxid->value}]<a href="Javascript:editThis('[{ $listitem->oxarticles__oxid->value }]');" class="[{ $listclass}]">[{/if}][{ $listitem->oxorderarticles__oxtitle->value|oxtruncate:20:""|strip_tags }]</a></td>
        <td valign="top" class="[{ $listclass}]">[{ $listitem->oxorderarticles__oxselvariant->value }]</td>
        <td valign="top" class="[{ $listclass}]">
            [{if $listitem->getPersParams()}]
                [{foreach key=sVar from=$listitem->getPersParams() item=aParam name=persparams}]
                    [{if !$smarty.foreach.persparams.first}]&nbsp;&nbsp;,&nbsp;[{/if}]
                    <em>
                        [{if $smarty.foreach.persparams.first && $smarty.foreach.persparams.last}]
                            [{ oxmultilang ident="GENERAL_LABEL" }]
                        [{else}]
                            [{$sVar}] :
                        [{/if}]
                        [{$aParam}]
                    </em>
                [{/foreach}]
            [{/if}]
        </td>
        <td valign="top" class="[{ $listclass}]">[{ $listitem->oxorderarticles__oxshortdesc->value|oxtruncate:20:""|strip_tags }]</td>
        [{if $edit->isNettoMode() }]
        <td valign="top" class="[{ $listclass}]">[{ $listitem->getNetPriceFormated() }] <small>[{ $edit->oxorder__oxcurrency->value }]</small></td>
        <td valign="top" class="[{ $listclass}]">[{ $listitem->getTotalNetPriceFormated() }] <small>[{ $edit->oxorder__oxcurrency->value }]</small></td>
        [{else}]
        <td valign="top" class="[{ $listclass}]">[{ $listitem->getBrutPriceFormated() }] <small>[{ $edit->oxorder__oxcurrency->value }]</small></td>
        <td valign="top" class="[{ $listclass}]">[{ $listitem->getTotalBrutPriceFormated() }] <small>[{ $edit->oxorder__oxcurrency->value }]</small></td>
        [{/if}]
        <td valign="top" class="[{ $listclass}]">[{ $listitem->oxorderarticles__oxvat->value}]</td>
        <td valign="top" class="[{ $listclass}]">[{if !$listitem->isBundle()}]<a href="Javascript:DeleteThisArticle('[{ $listitem->oxorderarticles__oxid->value }]');" class="delete" [{if $readonly }]onclick="JavaScript:return false;"[{/if}] [{include file="help.tpl" helpid=item_delete}]></a>[{/if}]</td>
        <td valign="top" class="[{ $listclass}]">[{if !$listitem->isBundle()}]<a href="Javascript:StornoThisArticle('[{ $listitem->oxorderarticles__oxid->value }]');" class="pause" [{if $readonly }]onclick="JavaScript:return false;"[{/if}] [{include file="help.tpl" helpid=item_storno}]></a>[{/if}]</td>
    [{/block}]
</tr>
[{if $blWhite == "2"}]
[{assign var="blWhite" value=""}]
[{else}]
[{assign var="blWhite" value="2"}]
[{/if}]
[{/foreach}]
</table>

<input type="submit" value="[{ oxmultilang ident="ORDER_ARTICLE_UPDATE_STOCK" }]">

</form>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td valign="top" style="padding-left:10px;">
    <br />
    [{if $edit->oxorder__oxstorno->value}]
    <span class="orderstorno">[{ oxmultilang ident="ORDER_ARTICLE_STORNO" }]</span><br><br>
    [{/if}]
    <b>[{ oxmultilang ident="GENERAL_ATALL" }] : </b><br>
    [{block name="admin_order_article_total"}]
    <table border="0" cellspacing="0" cellpadding="0" id="order.info">
[{if $edit->isNettoMode() }]
    <tr>
    <td class="edittext" height="15">[{ oxmultilang ident="GENERAL_INETTO" }]</td>
    <td class="edittext" align="right"><b>[{ $edit->getFormattedTotalNetSum() }]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] &euro; [{/if}]</b></td>
    </tr>
    <tr>
    <td class="edittext" height="15">[{ oxmultilang ident="GENERAL_DISCOUNT" }]&nbsp;&nbsp;</td>
    <td class="edittext" align="right"><b>- [{ $edit->getFormattedDiscount() }]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] &euro; [{/if}]</b></td>
    </tr>
    [{foreach key=iVat from=$aProductVats item=dVatPrice}]
    <tr>
    <td class="edittext" height="15">[{ oxmultilang ident="GENERAL_IVAT" }] ([{ $iVat }]%)</td>
    <td class="edittext" align="right"><b>[{ $dVatPrice }]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{ $currency->name}] [{/if}]</b></td>
    </tr>
    [{/foreach}]
    <tr>
    <td class="edittext" height="15">[{ oxmultilang ident="GENERAL_IBRUTTO" }]</td>
    <td class="edittext" align="right"><b>[{ $edit->getFormattedTotalBrutSum() }]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] &euro; [{/if}]</b></td>
    </tr>
[{else}]
    <tr>
    <td class="edittext" height="15">[{ oxmultilang ident="GENERAL_IBRUTTO" }]</td>
    <td class="edittext" align="right"><b>[{ $edit->getFormattedTotalBrutSum() }]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] &euro; [{/if}]</b></td>
    </tr>
    <tr>
    <td class="edittext" height="15">[{ oxmultilang ident="GENERAL_DISCOUNT" }]&nbsp;&nbsp;</td>
    <td class="edittext" align="right"><b>- [{ $edit->getFormattedDiscount() }]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] &euro; [{/if}]</b></td>
    </tr>
    <tr>
    <td class="edittext" height="15">[{ oxmultilang ident="GENERAL_INETTO" }]</td>
    <td class="edittext" align="right"><b>[{ $edit->getFormattedTotalNetSum() }]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] &euro; [{/if}]</b></td>
    </tr>
    [{foreach key=iVat from=$aProductVats item=dVatPrice}]
    <tr>
    <td class="edittext" height="15">[{ oxmultilang ident="GENERAL_IVAT" }] ([{ $iVat }]%)</td>
    <td class="edittext" align="right"><b>[{ $dVatPrice }]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{ $currency->name}] [{/if}]</b></td>
    </tr>
    [{/foreach}]
[{/if}]
    [{if $edit->oxorder__oxvoucherdiscount->value}]
    <tr>
    <td class="edittext" height="15">[{ oxmultilang ident="GENERAL_VOUCHERS" }]</td>
    <td class="edittext" align="right"><b>- [{ $edit->getFormattedTotalVouchers() }]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] &euro; [{/if}]</b></td>
    </tr>
    [{/if}]
    <tr>
    <td class="edittext" height="15">[{ oxmultilang ident="GENERAL_DELIVERYCOST" }]&nbsp;&nbsp;</td>
    <td class="edittext" align="right"><b>[{ $edit->getFormattedeliveryCost() }]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] &euro; [{/if}]</b></td>
    </tr>
    <tr>
    <td class="edittext" height="15">[{ oxmultilang ident="GENERAL_PAYCOST" }]&nbsp;&nbsp;</td>
    <td class="edittext" align="right"><b>[{ $edit->getFormattedPayCost()}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] &euro; [{/if}]</b></td>
    </tr>
    [{if $edit->oxorder__oxwrapcost->value }]
    <tr>
    <td class="edittext" height="15">[{ oxmultilang ident="GENERAL_WRAPPING" }]&nbsp;&nbsp;</td>
    <td class="edittext" align="right"><b>[{ $edit->getFormattedWrapCost() }]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] &euro; [{/if}]</b></td>
    </tr>
    [{/if}]
    [{if $edit->oxorder__oxgiftcardcost->value }]
    <tr>
    <td class="edittext" height="15">[{ oxmultilang ident="GENERAL_CARD" }]&nbsp;&nbsp;</td>
    <td class="edittext" align="right"><b>[{ $edit->getFormattedGiftCardCost() }]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] &euro; [{/if}]</b></td>
    </tr>
    [{/if}]
    <tr>
    <td class="edittext" height="25">[{ oxmultilang ident="GENERAL_SUMTOTAL" }]&nbsp;&nbsp;</td>
    <td class="edittext" align="right"><b>[{ $edit->getFormattedTotalOrderSum() }]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] &euro; [{/if}]</b></td>
    </tr>
    </table>
    [{/block}]
  </td>
    <td valign="top" align="left" width="60%">
<br />


    <form method="POST" name="searchForProduct" id="searchForProduct" action="[{ $oViewConf->getSelfLink() }]">
      [{ $oViewConf->getHiddenSid() }]
      <input type="hidden" name="oxid" value="[{ $oxid }]">
      <input type="hidden" name="cl" value="order_article">
      <input type="hidden" name="cur" value="[{ $oCurr->id }]">
      <input type="hidden" name="fnc" value="">

      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="edittext" height="15">
            [{ oxmultilang ident="GENERAL_ARTNUM" }]:
          </td>
          <td class="edittext">
            <input class="listedit" type="text" name="sSearchArtNum" value="[{ $oView->getSearchProductArtNr() }]" size="15" [{ $readonly }]>
          </td>
          <td class="edittext">
            <input class="listedit" type="submit" value="[{ oxmultilang ident="ORDER_ARTICLE_SEARCH" }]" name="search" [{ $readonly }]>
          </td>
        </tr>
      </table>
    </form>

    [{assign var="oSearchProd" value=$oView->getSearchProduct() }]
    [{if $oSearchProd }]
    [{assign var="oMainProd" value=$oView->getMainProduct() }]

    <form method="POST" name="AddThisArticle" id="AddThisArticle" action="[{ $oViewConf->getSelfLink() }]">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cur" value="[{ $oCurr->id }]">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="order_article">
    <input type="hidden" name="sSearchArtNum" value="[{ $oView->getSearchProductArtNr() }]">
    <input type="hidden" name="fnc" value="addThisArticle">


      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>
            <br />
            <fieldset>
              <legend>
                <select name="aid">
                  [{foreach key=iSel from=$oView->getProductList() item=oProduct }]

                  [{assign var="_disabled" value="" }]
                  [{assign var="_selected" value="" }]

                  [{if $oProduct->isNotBuyable() || $oProduct->isParentNotBuyable() }]
                    [{assign var="_disabled" value="disabled=\"disabled\"" }]
                  [{elseif $oSearchProd->getId() == $oProduct->getId() }]
                    [{assign var="_selected" value="selected=\"selected\"" }]
                  [{/if}]

                  <option value="[{ $oProduct->getId() }]" [{ $_selected }] [{ $_disabled }]>[{$oMainProd->oxarticles__oxtitle->value}] [{ $oProduct->oxarticles__oxvarselect->value }] [{ $oProduct->getFPrice() }] [{ $oCurr->name }]</option>
                  [{/foreach}]
                </select>
              </legend>
              <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="edittext">[{ oxmultilang ident="GENERAL_SUM" }]:</td>
                  <td class="edittext"><input class="listedit" type="text" name="am" value="1" size="4" [{ $readonly }]></td>
                <tr>

                [{assign var="oSearchProdSelList" value=$oSearchProd->getSelectLists() }]
                [{if $oSearchProdSelList }]
                    [{foreach key=iSel from=$oSearchProdSelList item=oList}]
                    <tr>
                      <td class="edittext">[{ $oList.name }]:</td>
                      <td class="edittext">
                        <select id="test_select_[{$product->oxarticles__oxid->value}]_[{$iSel}]" name="sel[[{$iSel}]]" class="listedit">
                        [{foreach key=iSelIdx from=$oList item=oSelItem}]
                          [{ if $oSelItem->name }]<option value="[{$iSelIdx}]">[{ $oSelItem->name }]</option>[{/if}]
                        [{/foreach}]
                        </select>
                      </td>
                      </div>
                    </tr>
                    [{/foreach}]
                [{/if}]

                <tr>
                  <td colspan="2" class="edittext"><input class="listedit" type="submit" value="[{ oxmultilang ident="ORDER_ARTICLE_ADDITEM" }]" name="add" [{ $readonly }]></td>
                </tr>
              </table>
            </fieldset>
          </td>
        </tr>
      </table>

    </form>
    [{elseif $oView->getSearchProductArtNr() }]
      <br />[{ oxmultilang ident="ORDER_ARTICLE_SEARCH_NOITEMSFOUND" }]
    [{/if}]
</td>
</tr>
</table>
[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
