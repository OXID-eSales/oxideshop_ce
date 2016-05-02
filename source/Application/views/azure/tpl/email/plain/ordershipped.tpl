[{assign var="shop"      value=$oEmailView->getShop()}]
[{assign var="oViewConf" value=$oEmailView->getViewConfig()}]

[{block name="email_plain_ordershipped_sendemail"}]
[{oxcontent ident="oxordersendplainemail"}]
[{/block}]

[{block name="email_plain_ordershipped_infoheader"}]
[{oxmultilang ident="ORDER_SHIPPED_TO" suffix="COLON"}]
[{/block}]

[{block name="email_plain_ordershipped_address"}]
[{if $order->oxorder__oxdellname->value}]
[{include file="email/plain/inc/shipping_address.tpl"}]
[{else}]
[{include file="email/plain/inc/billing_address.tpl"}]
[{/if}]
[{/block}]

[{block name="email_plain_ordershipped_oxordernr"}]
[{oxmultilang ident="ORDER_NUMBER" suffix="COLON"}] [{$order->oxorder__oxordernr->value}]
[{/block}]

[{block name="email_plain_ordershipped_orderarticles"}]
[{foreach from=$order->getOrderArticles(true) item=oOrderArticle}]
[{$oOrderArticle->oxorderarticles__oxamount->value}] [{$oOrderArticle->oxorderarticles__oxtitle->getRawValue()}] [{$oOrderArticle->oxorderarticles__oxselvariant->getRawValue()}]
[{/foreach}]
[{/block}]

[{block name="email_plain_ordershipped_infofooter"}]
[{oxmultilang ident="YOUR_TEAM" args=$shop->oxshops__oxname->getRawValue()}]
[{/block}]

[{block name="email_plain_ordershipped_shipmenttrackingurl"}]
[{if $order->getShipmentTrackingUrl()}][{oxmultilang ident="SHIPMENT_TRACKING" suffix="COLON"}] [{$order->getShipmentTrackingUrl()}][{/if}]
[{/block}]

[{oxcontent ident="oxemailfooterplain"}]
