[{assign var="template_title" value="ACCOUNT_ORDER_TITLE"|oxmultilangassign }]
[{include file="_header.tpl" title=$template_title location="ACCOUNT_ORDER_LOCATION"|oxmultilangassign|cat:$template_title}]

[{include file="inc/account_header.tpl" active_link=4 }]<br>

<!-- page locator -->
[{include file="inc/list_locator.tpl" sLocatorCaption="INC_LIST_LOCATOR_ORDERSPERPAGE"|oxmultilangassign }]

<strong class="boxhead" id="test_accOrderHistoryHeader">[{ $template_title }]</strong>
<div class="box info">
  [{if count($oView->getOrderList()) > 0 }]
  [{assign var=oArticleList value=$oView->getOrderArticleList()}]
  <table class="form" width="100%">
    <colgroup>
        <col width="50%" span="2">
    </colgroup>
  [{foreach from=$oView->getOrderList() item=order }]
    [{if $blShowLine }]
      <tr class="sep">
        <td colspan="2"></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
    [{else }]
      [{assign var="blShowLine" value=true }][{/if }]
      <tr>
        <td valign="top">
          <table class="form orderhistory" width="97%">
            <tr class="headrow">
              <th>[{ oxmultilang ident="ACCOUNT_ORDER_DATE" }]</th>
              <td id="test_accOrderDate_[{$order->oxorder__oxordernr->value}]">[{ $order->oxorder__oxorderdate->value }]</td>
            </tr>
            <tr class="sep">
              <td colspan="2"></td>
            </tr>
            <tr>
              <th><b>[{ oxmultilang ident="ACCOUNT_ORDER_STATUS" }]</b></th>
              <td id="test_accOrderStatus_[{$order->oxorder__oxordernr->value}]">
                [{if $order->oxorder__oxstorno->value}]
                  <span class="inlineSuccess">[{ oxmultilang ident="ACCOUNT_ORDER_STORNO" }]</span>
                [{elseif $order->oxorder__oxsenddate->value !="-" }]
                  <span class="done">[{ oxmultilang ident="ACCOUNT_ORDER_SHIPPED" }]</span>
                [{else }]
                  <span>[{ oxmultilang ident="ACCOUNT_ORDER_NOTSHIPPED" }]</span>
                [{/if }]              </td>
            </tr>
            <tr>
              <td colspan="2"></td>
            </tr>
            <tr>
               <th><b>[{ oxmultilang ident="ACCOUNT_ORDER_ORDERNO" }]</b></th>
              <td id="test_accOrderNo_[{$order->oxorder__oxordernr->value}]">[{ $order->oxorder__oxordernr->value }]</td>
            </tr>
             [{if $order->getShipmentTrackingUrl()}]
             <tr>
              <th><b>[{ oxmultilang ident="ACCOUNT_ORDER_TRACKINGID" }]</b></th>
              <td id="test_accOrderTrack_[{$order->oxorder__oxordernr->value}]">
                  <a href="[{$order->getShipmentTrackingUrl()}]">[{ oxmultilang ident="ACCOUNT_ORDER_TRACKSHIPMENT" }]</a>
              </td>
            </tr>
            [{/if }]
            <tr>
              <th><b>[{ oxmultilang ident="ACCOUNT_ORDER_SHIPMENTTO" }]</b></th>
              <td id="test_accOrderName_[{$order->oxorder__oxordernr->value}]">
                [{if $order->oxorder__oxdellname->value }]
                  [{ $order->oxorder__oxdelfname->value }]
                  [{ $order->oxorder__oxdellname->value }]
                [{else }]
                  [{ $order->oxorder__oxbillfname->value }]
                  [{ $order->oxorder__oxbilllname->value }]
                [{/if }]
              </td>
            </tr>
          </table>
        </td>
        <td valign="top">

          <table class="form orderhistory" width="100%">
            <colgroup>
                <col width="1%">
                <col width="98%">
                <col width="1%">
            </colgroup>
            <tr class="headrow">
              <th colspan="3">[{ oxmultilang ident="ACCOUNT_ORDER_CART" }]</th>
             </tr>
            <tr class="sep">
              <td colspan="3"></td>
            </tr>
            [{foreach from=$order->getOrderArticles(true) item=orderitem name=testOrderItem}]
            [{assign var=sArticleId value=$orderitem->oxorderarticles__oxartid->value }]
            [{assign var=oArticle value=$oArticleList[$sArticleId] }]
            <tr>
              <td class="amount" id="test_accOrderAmount_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]">[{ $orderitem->oxorderarticles__oxamount->value }]</td>
              <td>
                [{ if $oArticle->oxarticles__oxid->value && $oArticle->isVisible() }]<a id="test_accOrderLink_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]" href="[{ $oArticle->getLink() }]" class="artlink">[{/if }]
                [{ $orderitem->oxorderarticles__oxtitle->value }] [{ $orderitem->oxorderarticles__oxselvariant->value }]
                [{ if $oArticle->oxarticles__oxid->value && $oArticle->isVisible() }]</a>[{/if }]

                [{foreach key=sVar from=$orderitem->getPersParams() item=aParam}]
                    [{if $aParam }]
                    <br />[{ oxmultilang ident="ORDER_DETAILS" }]: [{$aParam}]
                    [{/if }]
                [{/foreach}]

            </td>
              <td align="right">
                [{* Commented due to Trusted Shops precertification. Enable if needed *}]
                [{*
                [{oxhasrights ident="TOBASKET"}]
                [{if $oArticle->isBuyable() }]
                  [{if $oArticle->oxarticles__oxid->value }]
                    <a  id="test_accOrderToBasket_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_order" params="fnc=tobasket&amp;aid=`$oArticle->oxarticles__oxid->value`&amp;am=1" }]" class="tocart" rel="nofollow"></a>
                  [{/if }]
                [{/if }]
                [{/oxhasrights}]
                *}]
              </td>
            </tr>

          [{/foreach }]
        </table>
      </td>
    </tr>
  [{/foreach }]
  </table>
  [{/if }]
  [{if !$blShowLine }][{ oxmultilang ident="ACCOUNT_ORDER_EMPTYHISTORY" }][{/if }]
</div>

<!-- page locator -->
[{include file="inc/list_locator.tpl" sLocatorCaption="INC_LIST_LOCATOR_ORDERSPERPAGE"|oxmultilangassign }]

<div class="bar prevnext">
    <form action="[{ $oViewConf->getSelfActionLink() }]" name="order" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="start">
          <div class="right">
              <input id="test_BackToShop" type="submit" value="[{ oxmultilang ident="ACCOUNT_ORDER_BACKTOSHOP" }]">
          </div>
      </div>
    </form>
</div>

&nbsp;


[{insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl" }]
