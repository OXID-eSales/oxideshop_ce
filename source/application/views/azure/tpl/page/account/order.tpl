[{capture append="oxidBlock_content"}]
[{assign var="template_title" value="PAGE_ACCOUNT_ORDER_TITLE"|oxmultilangassign }]
<h1 class="pageHead">[{ oxmultilang ident="PAGE_ACCOUNT_ORDER_TITLE" }]</h1>

[{assign var=oOrders value=$oView->getOrderList() }]

[{block name="account_order_history"}]
[{if count($oOrders) > 0 }]
[{assign var=oArticleList value=$oView->getOrderArticleList() }]
<ul class="orderList">
    [{foreach from=$oOrders item=order }]
        <li>
            <table class="orderitems">
                <tr>
                    <td>
                        <dl>
                            <dt title="[{ oxmultilang ident="PAGE_ACCOUNT_ORDER_DATE" }]">
                                <strong id="accOrderDate_[{$order->oxorder__oxordernr->value}]">[{ $order->oxorder__oxorderdate->value|date_format:"%d.%m.%Y" }]</strong>
                                <span>[{ $order->oxorder__oxorderdate->value|date_format:"%H:%M:%S" }]</span>
                            </dt>
                            <dd>
                                <strong>[{ oxmultilang ident="PAGE_ACCOUNT_ORDER_STATUS" }]</strong>
                                <span id="accOrderStatus_[{$order->oxorder__oxordernr->value}]">
                                    [{if $order->oxorder__oxstorno->value}]
                                        <span class="note">[{ oxmultilang ident="PAGE_ACCOUNT_ORDER_STORNO" }]</span>
                                    [{elseif $order->oxorder__oxsenddate->value !="-" }]
                                        <span>[{ oxmultilang ident="PAGE_ACCOUNT_ORDER_SHIPPED" }]</span>
                                    [{else }]
                                        <span class="note">[{ oxmultilang ident="PAGE_ACCOUNT_ORDER_NOTSHIPPED" }]</span>
                                    [{/if }]
                                </span>
                            </dd>
                            <dd>
                                <strong>[{ oxmultilang ident="PAGE_ACCOUNT_ORDER_ORDERNO" }]</strong>
                                <span id="accOrderNo_[{$order->oxorder__oxordernr->value}]">[{ $order->oxorder__oxordernr->value }]</span>
                            </dd>
                            [{if $order->getShipmentTrackingUrl()}]
                                <dd>
                                    <strong>[{ oxmultilang ident="PAGE_ACCOUNT_ORDER_TRACKINGID" }]</strong>
                                    <span id="accOrderTrack_[{$order->oxorder__oxordernr->value}]">
                                        <a href="[{$order->getShipmentTrackingUrl()}]">[{ oxmultilang ident="PAGE_ACCOUNT_ORDER_TRACKSHIPMENT" }]</a>
                                    </span>
                                </dd>
                            [{/if }]
                            <dd>
                                <strong>[{ oxmultilang ident="PAGE_ACCOUNT_ORDER_SHIPMENTTO" }]</strong>
                                <span id="accOrderName_[{$order->oxorder__oxordernr->value}]">
                                [{if $order->oxorder__oxdellname->value }]
                                    [{ $order->oxorder__oxdelfname->value }]
                                    [{ $order->oxorder__oxdellname->value }]
                                [{else }]
                                    [{ $order->oxorder__oxbillfname->value }]
                                    [{ $order->oxorder__oxbilllname->value }]
                                [{/if }]
                                </span>
                            </dd>
                        </dl>
                    </td>
                    <td>
                        <h3>[{ oxmultilang ident="PAGE_ACCOUNT_ORDER_CART" }]</h3>
                        <table class="orderhistory">
                            [{foreach from=$order->getOrderArticles(true) item=orderitem name=testOrderItem}]
                                [{assign var=sArticleId value=$orderitem->oxorderarticles__oxartid->value }]
                                [{assign var=oArticle value=$oArticleList[$sArticleId] }]
                                <tr id="accOrderAmount_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]">
                                  <td>
                                    [{ if $oArticle->oxarticles__oxid->value && $oArticle->isVisible() }]
                                        <a  id="accOrderLink_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]" href="[{ $oArticle->getLink() }]">
                                    [{/if }]
                                        [{ $orderitem->oxorderarticles__oxtitle->value }] [{ $orderitem->oxorderarticles__oxselvariant->value }] <span class="amount"> - [{ $orderitem->oxorderarticles__oxamount->value }] [{oxmultilang ident="PAGE_ACCOUNT_ORDERHISTORY_QNT"}]</span>
                                    [{ if $oArticle->oxarticles__oxid->value && $oArticle->isVisible() }]</a>[{/if }]
                                    [{foreach key=sVar from=$orderitem->getPersParams() item=aParam}]
                                        [{if $aParam }]
                                        <br />[{ oxmultilang ident="ORDER_DETAILS" }]: [{$aParam}]
                                        [{/if }]
                                    [{/foreach}]
                                  </td>
                                  <td class="small">
                                    [{* Commented due to Trusted Shops precertification. Enable if needed *}]
                                    [{*
                                    [{oxhasrights ident="TOBASKET"}]
                                    [{if $oArticle->isBuyable() }]
                                      [{if $oArticle->oxarticles__oxid->value }]
                                        <a id="accOrderToBasket_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_order" params="fnc=tobasket&amp;aid=`$oArticle->oxarticles__oxid->value`&amp;am=1" }]" rel="nofollow">[{ oxmultilang ident="ADD_TO_CART" }]</a>
                                      [{/if }]
                                    [{/if }]
                                    [{/oxhasrights}]
                                    *}]
                                  </td>
                                </tr>

                            [{/foreach}]
                        </table>
                  </td>
                </tr>
            </table>
        </li>
    [{/foreach }]
</ul>
        [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigation() place="bottom"}]
  [{else}]
  [{ oxmultilang ident="PAGE_ACCOUNT_ORDER_EMPTYHISTORY" }]
  [{/if }]
[{/block}]
[{insert name="oxid_tracker" title=$template_title }]
[{/capture}]


[{capture append="oxidBlock_sidebar"}]
    [{include file="page/account/inc/account_menu.tpl" active_link="orderhistory"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]