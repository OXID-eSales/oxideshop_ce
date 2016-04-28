[{capture append="oxidBlock_content"}]

    [{* ordering steps *}]
    [{include file="page/checkout/inc/steps.tpl" active=5}]

    [{block name="checkout_thankyou_main"}]
        [{assign var="order" value=$oView->getOrder()}]
        [{assign var="basket" value=$oView->getBasket()}]

        <div id="thankyouPage">
            <h3 class="blockHead">[{oxmultilang ident="THANK_YOU"}]</h3>

            [{block name="checkout_thankyou_info"}]
                [{oxmultilang ident="THANK_YOU_FOR_ORDER"}] [{$oxcmp_shop->oxshops__oxname->value}]. <br>
                [{oxmultilang ident="REGISTERED_YOUR_ORDER" args=$order->oxorder__oxordernr->value}] <br>
                [{if !$oView->getMailError()}]
                    [{oxmultilang ident="MESSAGE_YOU_RECEIVED_ORDER_CONFIRM"}]<br>
                [{else}]<br>
                    [{oxmultilang ident="MESSAGE_CONFIRMATION_NOT_SUCCEED"}]<br>
                [{/if}]
                <br>
                [{oxmultilang ident="MESSAGE_WE_WILL_INFORM_YOU"}]<br><br>
            [{/block}]

            [{block name="checkout_thankyou_proceed"}]
                [{oxmultilang ident="YOU_CAN_GO"}]
                <a id="backToShop" rel="nofollow" href="[{oxgetseourl ident=$oViewConf->getHomeLink()}]" class="link">[{oxmultilang ident="BACK_TO_START_PAGE"}]</a>
                [{if $oxcmp_user->oxuser__oxpassword->value}]
                    [{oxmultilang ident="OR"}]
                    <a id="orderHistory" rel="nofollow" href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_order"}]" class="link">[{oxmultilang ident="CHECK_YOUR_ORDER_HISTORY"}]</a>.
                [{/if}]
            [{/block}]

            [{block name="checkout_thankyou_partners"}]
            [{/block}]

            [{if $oView->getAlsoBoughtTheseProducts()}]
                <br><br>
                <h1 class="pageHead">
                     [{oxmultilang ident="WHO_BOUGHT_ALSO_BOUGHT" suffix="COLON"}]
                </h1>
                [{include file="widget/product/list.tpl" type=$oView->getListDisplayType() listId="alsoBoughtThankyou" products=$oView->getAlsoBoughtTheseProducts() blDisableToCart=true}]
            [{/if}]
        </div>
    [{/block}]
[{/capture}]
[{include file="layout/page.tpl"}]
