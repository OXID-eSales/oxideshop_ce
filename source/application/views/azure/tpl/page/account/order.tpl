[{capture append="oxidBlock_content"}]
[{assign var="template_title" value="ORDER_HISTORY"|oxmultilangassign}]
<h1 class="pageHead">[{ oxmultilang ident="ORDER_HISTORY" }]</h1>

[{assign var=oOrders value=$oView->getOrderList()}]

[{block name="account_order_history"}]
[{if count($oOrders) > 0}]
[{assign var=oArticleList value=$oView->getOrderArticleList()}]
<ul class="orderList">
    [{foreach from=$oOrders item=order}]
        <li>
            <table class="orderitems">
                <tr>
                    <td>
                        <dl>
                            <dt title="[{ oxmultilang ident="ORDER_DATE" suffix="COLON" }]">
                                <strong id="accOrderDate_[{$order->oxorder__oxordernr->value}]">[{ $order->oxorder__oxorderdate->value|date_format:"%d.%m.%Y" }]</strong>
                                <span>[{ $order->oxorder__oxorderdate->value|date_format:"%H:%M:%S" }]</span>
                            </dt>
                            <dd>
                                <strong>[{ oxmultilang ident="STATUS" suffix="COLON" }]</strong>
                                <span id="accOrderStatus_[{$order->oxorder__oxordernr->value}]">
                                    [{if $order->oxorder__oxstorno->value}]
                                        <span class="note">[{ oxmultilang ident="ORDER_IS_CANCELED" }]</span>
                                    [{elseif $order->oxorder__oxsenddate->value !="-" }]
                                        <span>[{ oxmultilang ident="SHIPPED" }]</span>
                                    [{else}]
                                        <span class="note">[{ oxmultilang ident="NOT_SHIPPED_YET" }]</span>
                                    [{/if}]
                                </span>
                            </dd>
                            <dd>
                                <strong>[{ oxmultilang ident="ORDER_NUMBER" suffix="COLON" }]</strong>
                                <span id="accOrderNo_[{$order->oxorder__oxordernr->value}]">[{ $order->oxorder__oxordernr->value }]</span>
                            </dd>
                            [{if $order->getShipmentTrackingUrl()}]
                                <dd>
                                    <strong>[{ oxmultilang ident="TRACKING_ID" suffix="COLON" }]</strong>
                                    <span id="accOrderTrack_[{$order->oxorder__oxordernr->value}]">
                                        <a href="[{$order->getShipmentTrackingUrl()}]">[{ oxmultilang ident="TRACK_SHIPMENT" }]</a>
                                    </span>
                                </dd>
                            [{/if}]
                            <dd>
                                <strong>[{ oxmultilang ident="SHIPMENT_TO" suffix="COLON" }]</strong>
                                <span id="accOrderName_[{$order->oxorder__oxordernr->value}]">
                                [{if $order->oxorder__oxdellname->value }]
                                    [{ $order->oxorder__oxdelfname->value }]
                                    [{ $order->oxorder__oxdellname->value }]
                                [{else }]
                                    [{ $order->oxorder__oxbillfname->value }]
                                    [{ $order->oxorder__oxbilllname->value }]
                                [{/if}]
                                </span>
                            </dd>
                        </dl>
                    </td>
                    <td>
                        <h3>[{ oxmultilang ident="CART" suffix="COLON" }]</h3>
                        <table class="orderhistory">
                            [{foreach from=$order->getOrderArticles(true) item=orderitem name=testOrderItem}]
                                [{assign var=sArticleId value=$orderitem->oxorderarticles__oxartid->value }]
                                [{assign var=oArticle value=$oArticleList[$sArticleId] }]
                                <tr id="accOrderAmount_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]">
                                  <td>
                                    [{if $oArticle->oxarticles__oxid->value && $oArticle->isVisible() }]
                                        <a  id="accOrderLink_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]" href="[{ $oArticle->getLink() }]">
                                    [{/if}]
                                        [{ $orderitem->oxorderarticles__oxtitle->value }] [{ $orderitem->oxorderarticles__oxselvariant->value }] <span class="amount"> - [{ $orderitem->oxorderarticles__oxamount->value }] [{oxmultilang ident="QNT"}]</span>
                                    [{if $oArticle->oxarticles__oxid->value && $oArticle->isVisible() }]</a>[{/if}]
                                    [{foreach key=sVar from=$orderitem->getPersParams() item=aParam}]
                                        [{if $aParam }]
                                        <br />[{ oxmultilang ident="DETAILS" suffix="COLON" }] [{$aParam}]
                                        [{/if}]
                                    [{/foreach}]
                                  </td>
                                  <td class="small">
                                    [{* Commented due to Trusted Shops precertification. Enable if needed *}]
                                    [{*
                                    [{oxhasrights ident="TOBASKET"}]
                                    [{if $oArticle->oxarticles__oxid->value && $oArticle->isBuyable() }]
                                        <a id="accOrderToBasket_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_order" params="fnc=tobasket&amp;aid=`$oArticle->oxarticles__oxid->value`&amp;am=1" }]" rel="nofollow">[{ oxmultilang ident="TO_CART" }]</a>
                                    [{/if}]
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
    [{/foreach}]
</ul>
        [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigation() place="bottom"}]
  [{else}]
  [{ oxmultilang ident="ORDER_EMPTY_HISTORY" }]
  [{/if}]
[{/block}]
[{insert name="oxid_tracker" title=$template_title }]
[{/capture}]


[{capture append="oxidBlock_sidebar"}]
    [{include file="page/account/inc/account_menu.tpl" active_link="orderhistory"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]