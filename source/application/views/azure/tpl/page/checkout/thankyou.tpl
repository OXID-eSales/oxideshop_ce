[{capture append="oxidBlock_content"}]

    [{* ordering steps *}]
    [{include file="page/checkout/inc/steps.tpl" active=5 }]

    [{block name="checkout_thankyou_main"}]
        [{assign var="order" value=$oView->getOrder()}]
        [{assign var="basket" value=$oView->getBasket()}]

        <div id="thankyouPage">
            <h3 class="blockHead">[{ oxmultilang ident="THANK_YOU" }]</h3>

            [{block name="checkout_thankyou_info"}]
                [{ oxmultilang ident="THANK_YOU_FOR_ORDER" }] [{ $oxcmp_shop->oxshops__oxname->value }]. <br>
                [{ oxmultilang ident="REGISTERED_YOUR_ORDER" args=$order->oxorder__oxordernr->value}] <br>
                [{if !$oView->getMailError() }]
                    [{ oxmultilang ident="MESSAGE_YOU_RECEIVED_ORDER_CONFIRM" }]<br>
                [{else}]<br>
                    [{ oxmultilang ident="MESSAGE_CONFIRMATION_NOT_SUCCEED" }]<br>
                [{/if}]
                <br>
                [{ oxmultilang ident="MESSAGE_WE_WILL_INFORM_YOU" }]<br><br>
            [{/block}]

            [{block name="checkout_thankyou_proceed"}]
                [{ oxmultilang ident="YOU_CAN_GO" }]
                <a id="backToShop" rel="nofollow" href="[{oxgetseourl ident=$oViewConf->getHomeLink()}]" class="link">[{ oxmultilang ident="BACK_TO_START_PAGE" }]</a>
                [{if $oxcmp_user->oxuser__oxpassword->value }]
                    [{ oxmultilang ident="OR" }]
                    <a id="orderHistory" rel="nofollow" href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_order"}]" class="link">[{ oxmultilang ident="CHECK_YOUR_ORDER_HISTORY" }]</a>.
                [{/if}]
            [{/block}]

            [{block name="checkout_thankyou_ts"}]
                [{if $oViewConf->showTs("THANKYOU") && $oViewConf->getTsId() }]
                    [{assign var="sTSRatingImg" value="https://www.trustedshops.com/bewertung/widget/img/bewerten_"|cat:$oView->getActiveLangAbbr()|cat:".gif"}]
                    <h3 class="blockHead">[{ oxmultilang ident="TRUSTED_SHOPS_CUSTOMER_RATINGS" }]</h3>
                    [{ oxmultilang ident="RATE_OUR_SHOP" }]
                    <div class="etrustTsRatingButton">
                        <a href="[{$oViewConf->getTsRatingUrl()}]" target="_blank" title="[{oxmultilang ident="TRUSTED_SHOPS_RATINGS"}]">
                            <img src="[{$sTSRatingImg}]" border="0" alt="[{oxmultilang ident="WRITE_REVIEW_2"}]" align="middle">
                        </a>
                    </div>
                [{/if}]
            [{/block}]

            [{block name="checkout_thankyou_partners"}]
                [{if ( $oView->getTrustedShopId()) }]
                    <h3 class="blockHead">[{ oxmultilang ident="TRUSTED_SHOP_BUYER_PROTECTION" }]</h3>
                    [{if $oView->getTrustedShopId()}]
                        <div class="etrustlogocol">
                            <a href="https://www.trustedshops.com/shop/certificate.php?shop_id=[{$oView->getTrustedShopId()}]" target="_blank">
                                <img src="[{$oViewConf->getImageUrl('trustedshops_m.gif')}]" title="[{oxmultilang ident="TRUSTED_SHOPS_IMGTITLE"}]">
                            </a>
                        </div>
                        <form id="formTsShops" name="formTShops" method="post" action="https://www.trustedshops.com/shop/protection.php" target="_blank">
                          <div>
                              <input type="hidden" name="_charset_">
                              <input name="shop_id" type=hidden value="[{$oView->getTrustedShopId()}]">
                              <input name="email" type="hidden" value="[{ $oxcmp_user->oxuser__oxusername->value }]">
                              <input name="amount" type=hidden value="[{ $order->getTotalOrderSum() }]">
                              <input name="curr" type=hidden value="[{ $order->oxorder__oxcurrency->value }]">
                              <input name="payment" type=hidden value="">
                              <input name="KDNR" type="hidden" value="[{ $oxcmp_user->oxuser__oxcustnr->value }]">
                              <input name="ORDERNR" type="hidden" value="[{ $order->oxorder__oxordernr->value }]">
                              [{ oxmultilang ident="TRUSTED_SHOP_REGISTRATION_MESSAGE" }]<br><br>
                              <span><input type="submit" id="btnProtect" name="btnProtect" value="[{oxmultilang ident="TRUSTED_SHOP_REGISTRATION"}]"></span>
                          </div>
                        </form>
                        <div class="clear"></div>
                    [{/if}]
                [{/if}]
            [{/block}]

            [{if $oView->getAlsoBoughtTheseProducts()}]
                <br><br>
                <h1 class="pageHead">
                     [{ oxmultilang ident="WHO_BOUGHT_ALSO_BOUGHT" suffix="COLON" }]
                </h1>
                [{include file="widget/product/list.tpl" type=$oView->getListDisplayType() listId="alsoBoughtThankyou" products=$oView->getAlsoBoughtTheseProducts() blDisableToCart=true}]
            [{/if}]
        </div>
    [{/block}]
    [{insert name="oxid_tracker" title=$template_title }]
[{/capture}]
[{include file="layout/page.tpl"}]