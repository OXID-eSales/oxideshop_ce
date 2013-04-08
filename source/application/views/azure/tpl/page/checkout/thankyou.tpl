[{capture append="oxidBlock_content"}]

    [{if $oView->showFinalStep()}]
        [{* ordering steps *}]
        [{include file="page/checkout/inc/steps.tpl" active=5 }]
    [{/if}]

    [{block name="checkout_thankyou_main"}]
        [{assign var="order" value=$oView->getOrder()}]
        [{assign var="basket" value=$oView->getBasket()}]

        <div id="thankyouPage">
            <h3 class="blockHead">[{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_TITLE" }]</h3>

            [{block name="checkout_thankyou_info"}]
                [{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_THANKYOU1" }] [{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_THANKYOU2" }] [{ $oxcmp_shop->oxshops__oxname->value }]. <br>
                [{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_REGISTEREDYOUORDERNO1" }] [{ $order->oxorder__oxordernr->value }] [{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_REGISTEREDYOUORDERNO2" }]<br>
                [{if !$oView->getMailError() }]
                    [{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_YOURECEIVEDORDERCONFIRM" }]<br>
                [{else}]<br>
                    [{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_CONFIRMATIONNOTSUCCEED" }]<br>
                [{/if}]
                <br>
                [{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_WEWILLINFORMYOU" }]<br><br>
            [{/block}]

            [{block name="checkout_thankyou_proceed"}]
                [{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_YOUCANGO" }]
                <a id="backToShop" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getHomeLink() }]" class="link">[{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_BACKTOSHOP" }]</a>
                [{if $oxcmp_user->oxuser__oxpassword->value }]
                    [{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_OR" }]
                    <a id="orderHistory" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_order" }]" class="link">[{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_ORDERHISTORY" }]</a>.
                [{/if}]
            [{/block}]

            [{block name="checkout_thankyou_ts"}]
                [{if $oViewConf->showTs("THANKYOU") && $oViewConf->getTsId() }]
                    [{assign var="sTSRatingImg" value="https://www.trustedshops.com/bewertung/widget/img/bewerten_"|cat:$oView->getActiveLangAbbr()|cat:".gif"}]
                    <h3 class="blockHead">[{ oxmultilang ident="TS_RATINGS" }]</h3>
                    [{ oxmultilang ident="TS_RATINGS_RATEUS" }]
                    <div class="etrustTsRatingButton">
                        <a href="[{ $oViewConf->getTsRatingUrl() }]" target="_blank" title="[{ oxmultilang ident="TS_RATINGS_URL_TITLE" }]">
                            <img src="[{$sTSRatingImg}]" border="0" alt="[{ oxmultilang ident="TS_RATINGS_BUTTON_ALT" }]" align="middle">
                        </a>
                    </div>
                [{/if}]
            [{/block}]

            [{block name="checkout_thankyou_partners"}]
                [{if ( $oView->getTrustedShopId()) || $iswebmiles || $oxcmp_shop->oxshops__oxadbutlerid->value ||
                       $oxcmp_shop->oxshops__oxaffilinetid->value || $oxcmp_shop->oxshops__oxsuperclicksid->value ||
                       $oxcmp_shop->oxshops__oxaffiliweltid->value || $oxcmp_shop->oxshops__oxaffili24id->value }]

                    <h3 class="blockHead">[{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_PARTNERFROM" }]</h3>
                    [{if $oView->getTrustedShopId()}]
                        <div class="etrustlogocol">
                            <a href="https://www.trustedshops.com/shop/certificate.php?shop_id=[{$oView->getTrustedShopId()}]" target="_blank">
                                <img src="[{$oViewConf->getImageUrl('trustedshops_m.gif')}]" title="[{ oxmultilang ident="INC_TRUSTEDSHOPS_ITEM_IMGTITLE" }]">
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
                              [{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_TRUSTEDSHOPMESSAGE" }]<br><br>
                              <span><input type="submit" id="btnProtect" name="btnProtect" value="[{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_LOGGIN" }]"></span>
                          </div>
                        </form>
                        <div class="clear"></div>
                    [{/if}]

                    <!-- Anfang Tracking-Code fuer Partnerprogramme -->

                    [{ if $oxcmp_shop->oxshops__oxadbutlerid->value }]
                        <!--Adbutler-->
                        [{assign var="discountnetprice" value=$basket->getDiscountedNettoPrice()}]
                        [{assign var="currencycovindex" value=$oView->getCurrencyCovIndex()}]
                        <img src="https://www1.belboon.de/adtracking/sale/[{$oxcmp_shop->oxshops__oxadbutlerid->value }].gif/oc=[{$order->oxorder__oxordernr->value }]&sale=[{ $discountnetprice * $currencycovindex|string_format:"%.2f"}]&belboon=[{$oView->getBelboonParam()}]" WIDTH="1" HEIGHT="1">
                        <object type="application/x-shockwave-flash" data="http://www1.belboon.de/tracking/flash.swf" width="1" height="1" >
                            <param name="flashvars" value="pgmid=[{$oxcmp_shop->oxshops__oxadbutlerid->value }]&etype=sale&tparam=sale&evalue=[{ $discountnetprice * $currencycovindex|string_format:"%.2f"}]&oc=[{$order->oxorder__oxordernr->value }]">
                            <param name="movie" value="http://www1.belboon.de/tracking/flash.swf" />
                        </object>
                        <!--Adbutler ende-->
                    [{/if}]

                    [{ if $oxcmp_shop->oxshops__oxaffilinetid->value }]
                        <!--Affilinet-->
                        [{assign var="discountnetprice" value=$basket->getDiscountedNettoPrice()}]
                        [{assign var="currencycovindex" value=$oView->getCurrencyCovIndex()}]
                        <img src="https://partners.webmasterplan.com/registersale.asp?site=[{$oxcmp_shop->oxshops__oxaffilinetid->value }]&amp;order=[{$order->oxorder__oxordernr->value }]&amp;curr=[{$order->oxorder__oxcurrency->value}]&amp;price=[{$discountnetprice * $currencycovindex|string_format:"%.2f"}]" WIDTH="1" HEIGHT="1">
                        <!--Affilinet Ende-->
                    [{/if}]

                    [{ if $oxcmp_shop->oxshops__oxsuperclicksid->value }]
                        <!--Superclix-Code-->
                        [{assign var="discountnetprice" value=$basket->getDiscountedNettoPrice()}]
                        [{assign var="currencycovindex" value=$oView->getCurrencyCovIndex()}]
                        <img src="https://clix.superclix.de/cgi-bin/code.cgi?pp=[{$oxcmp_shop->oxshops__oxsuperclicksid->value }]&amp;cashflow=[{$discountnetprice * $currencycovindex|string_format:"%.2f"}]&amp;tax=1.00&amp;goods=[{$order->oxorder__oxordernr->value }]" width="1" height="1">
                        <!--Superclix Ende-->
                    [{/if}]

                    [{ if $oxcmp_shop->oxshops__oxaffiliweltid->value }]
                        <!--Affiliwelt-Code-->
                        <!--img src="https://www.affiliwelt.net/partner/sregistering.php3?ID=[{$oxcmp_shop->oxshops__oxaffiliweltid->value }]&track=[{$order->oxorder__oxordernr->value }]&wert=[{ $basket->getDiscountedNettoPrice()}]&mone=EUR" width="1" height="1" border="0"-->
                        [{assign var="discountnetprice" value=$basket->getDiscountedNettoPrice()}]
                        [{assign var="currencycovindex" value=$oView->getCurrencyCovIndex()}]
                        <img src="https://www.affiliwelt.net/tracking.php?prid=[{$oxcmp_shop->oxshops__oxaffiliweltid->value }]&amp;bestid=[{$order->oxorder__oxordernr->value }]&amp;beschreibung=OXID&preis=[{ $discountnetprice * $currencycovindex|string_format:"%.2f"}]" width="1" height="1">
                        <!--Affiliwelt Ende-->
                    [{/if}]

                    [{ if $oxcmp_shop->oxshops__oxaffili24id->value }]
                        <!--Affili24.com-->
                        [{assign var="discountnetprice" value=$basket->getDiscountedNettoPrice()}]
                        [{assign var="currencycovindex" value=$oView->getCurrencyCovIndex()}]
                        <img src="https://partners.affili24.com/registering.php?ID=[{$oxcmp_shop->oxshops__oxaffili24id->value }]&amp;track=[{$order->oxorder__oxordernr->value }]&amp;wert=[{ $discountnetprice * $currencycovindex|string_format:"%.2f"}]" width="1" height="1">
                        <!--Affili24 Ende-->
                    [{/if}]

                    <!-- Ende Tracking-Code fuer Partnerprogramme -->
                [{/if}]
            [{/block}]

            [{if $oView->showFinalStep()}]
                [{if $oView->getAlsoBoughtTheseProducts()}]
                    <br><br>
                    <h1 class="pageHead">
                         [{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_ALSOBOUGHT" }]
                    </h1>
                    [{include file="widget/product/list.tpl" type=$oView->getListDisplayType() listId="alsoBoughtThankyou" products=$oView->getAlsoBoughtTheseProducts() blDisableToCart=true}]
                [{/if}]
            [{/if}]
        </div>
    [{/block}]
    [{insert name="oxid_tracker" title=$template_title }]
[{/capture}]
[{include file="layout/page.tpl"}]