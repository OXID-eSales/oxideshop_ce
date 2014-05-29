[{capture append="oxidBlock_content"}]

    [{block name="checkout_order_errors"}]
        [{ if $oView->isConfirmAGBError() == 1 }]
            [{include file="message/error.tpl" statusMessage="PAGE_CHECKOUT_ORDER_READANDCONFIRMTERMS"|oxmultilangassign }]
        [{/if}]
        [{assign var="iError" value=$oView->getAddressError() }]
        [{ if $iError == 1}]
           [{include file="message/error.tpl" statusMessage="ERROR_DELIVERY_ADDRESS_WAS_CHANGED_DURING_CHECKOUT"|oxmultilangassign }]
        [{ /if}]
    [{/block}]

    [{* ordering steps *}]
    [{include file="page/checkout/inc/steps.tpl" active=4 }]

    [{block name="checkout_order_main"}]
        [{if !$oView->showOrderButtonOnTop()}]
            <div class="lineBox clear">
                <span>&nbsp;</span>
                <span class="title">[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_TITLE2" }]</span>
            </div>
        [{/if}]

        [{block name="checkout_order_details"}]
            [{ if !$oxcmp_basket->getProductsCount()  }]
                [{block name="checkout_order_emptyshippingcart"}]
                <div class="status corners error">[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_BASKETEMPTY" }]</div>
                [{/block}]
            [{else}]
                [{assign var="currency" value=$oView->getActCurrency() }]

                [{if $oView->isLowOrderPrice()}]
                    [{block name="checkout_order_loworderprice_top"}]
                        <div>[{ oxmultilang ident="MIN_ORDER_PRICE" }] [{ $oView->getMinOrderPrice() }] [{ $currency->sign }]</div>
                    [{/block}]
                [{else}]

                    [{if $oView->showOrderButtonOnTop()}]
                        <div id="orderAgbTop">
                            <form action="[{$oViewConf->getSslSelfLink()}]" method="post" id="orderConfirmAgbTop">
                                [{$oViewConf->getHiddenSid()}]
                                [{$oViewConf->getNavFormParams()}]
                                <input type="hidden" name="cl" value="order">
                                <input type="hidden" name="fnc" value="[{$oView->getExecuteFnc()}]">
                                <input type="hidden" name="challenge" value="[{$challenge}]">
                                <input type="hidden" name="sDeliveryAddressMD5" value="[{$oView->getDeliveryAddressMD5()}]">

                                [{include file="page/checkout/inc/agb.tpl"}]

                                <div class="lineBox clear">
                                    <a href="[{ oxgetseourl ident=$oViewConf->getPaymentLink() }]" class="prevStep submitButton largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_BACKSTEP" }]</a>
                                    <button type="submit" class="submitButton nextStep largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_SUBMITORDER" }]</button>
                                </div>
                            </form>
                        </div>
                    [{/if}]
                [{/if}]

                [{block name="checkout_order_vouchers"}]
                    [{ if $oViewConf->getShowVouchers() && $oxcmp_basket->getVouchers()}]
                        [{ oxmultilang ident="PAGE_CHECKOUT_ORDER_USEDCOUPONS" }]
                        <div>
                            [{foreach from=$Errors.basket item=oEr key=key }]
                                [{if $oEr->getErrorClassType() == 'oxVoucherException'}]
                                    [{ oxmultilang ident="PAGE_CHECKOUT_ORDER_COUPONNOTACCEPTED1" }] [{ $oEr->getValue('voucherNr') }] [{ oxmultilang ident="PAGE_CHECKOUT_ORDER_COUPONNOTACCEPTED2" }]<br>
                                    [{ oxmultilang ident="PAGE_CHECKOUT_ORDER_REASON" }]
                                    [{ $oEr->getOxMessage() }]<br>
                                [{/if}]
                            [{/foreach}]
                            [{foreach from=$oxcmp_basket->getVouchers() item=sVoucher key=key name=aVouchers}]
                                [{ $sVoucher->sVoucherNr }]<br>
                            [{/foreach }]
                        </div>
                    [{/if}]
                [{/block}]

                [{block name="checkout_order_address"}]
                    <div id="orderAddress">
                        <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
                            <h3 class="section">
                            <strong>[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_ADDRESSES" }]</strong>
                            [{ $oViewConf->getHiddenSid() }]
                            <input type="hidden" name="cl" value="user">
                            <input type="hidden" name="fnc" value="">
                            <button type="submit" class="submitButton largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_MODIFYADDRESS" }]</button>
                            </h3>
                        </form>
                        <dl>
                            <dt>[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_BILLINGADDRESS" }]</dt>
                            <dd>
                                [{include file="widget/address/billing_address.tpl"}]
                            </dd>
                        </dl>
                        [{assign var="oDelAdress" value=$oView->getDelAddress() }]
                        [{if $oDelAdress }]
                        <dl class="shippingAddress">
                            <dt>[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_SHIPPINGADDRESS" }]</dt>
                            <dd>
                                [{include file="widget/address/shipping_address.tpl" delivadr=$oDelAdress}]
                            </dd>
                        </dl>
                        [{/if}]

                        [{if $oView->getOrderRemark() }]
                            <dl class="orderRemarks">
                                <dt>[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_WHATIWANTEDTOSAY" }]</dt>
                                <dd>
                                    [{ $oView->getOrderRemark() }]
                                </dd>
                            </dl>
                        [{/if}]
                    </div>
                    <div style="clear:both;"></div>
                [{/block}]

                [{block name="shippingAndPayment"}]
                    <div id="orderShipping">
                    <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
                        <h3 class="section">
                            <strong>[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_SHIPPINGCARRIER" }]</strong>
                            [{ $oViewConf->getHiddenSid() }]
                            <input type="hidden" name="cl" value="payment">
                            <input type="hidden" name="fnc" value="">
                            <button type="submit" class="submitButton largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_MODIFY2" }]</button>
                        </h3>
                    </form>
                    [{assign var="oShipSet" value=$oView->getShipSet() }]
                    [{ $oShipSet->oxdeliveryset__oxtitle->value }]
                    </div>

                    <div id="orderPayment">
                        <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
                            <h3 class="section">
                                <strong>[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_PAYMENTMETHOD" }]</strong>
                                [{ $oViewConf->getHiddenSid() }]
                                <input type="hidden" name="cl" value="payment">
                                <input type="hidden" name="fnc" value="">
                                <button type="submit" class="submitButton largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_MODIFY3" }]</button>
                            </h3>
                        </form>
                        [{assign var="payment" value=$oView->getPayment() }]
                        [{ $payment->oxpayments__oxdesc->value }]
                    </div>
                [{/block}]

                <div id="orderEditCart">
                    <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
                        <h3 class="section">
                            <strong>[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_BASKET" }]</strong>
                            [{ $oViewConf->getHiddenSid() }]
                            <input type="hidden" name="cl" value="basket">
                            <input type="hidden" name="fnc" value="">
                            <button type="submit" class="submitButton largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_MODIFY4" }]</button>
                        </h3>
                    </form>
                </div>

                <div class="lineBox clear">
                [{block name="order_basket"}]
                    [{include file="page/checkout/inc/basketcontents.tpl" editable=false}]
                [{/block}]

                [{if $oView->isLowOrderPrice() }]
                    [{block name="checkout_order_loworderprice_bottom"}]
                        <div>[{oxmultilang ident="MIN_ORDER_PRICE"}] [{$oView->getMinOrderPrice()}] [{$currency->sign}]</div>
                    [{/block}]
                [{else}]
                    [{block name="checkout_order_btn_confirm_bottom"}]
                        <form action="[{ $oViewConf->getSslSelfLink() }]" method="post" id="orderConfirmAgbBottom">
                            [{ $oViewConf->getHiddenSid() }]
                            [{ $oViewConf->getNavFormParams() }]
                            <input type="hidden" name="cl" value="order">
                            <input type="hidden" name="fnc" value="[{$oView->getExecuteFnc()}]">
                            <input type="hidden" name="challenge" value="[{$challenge}]">
                            <input type="hidden" name="sDeliveryAddressMD5" value="[{$oView->getDeliveryAddressMD5()}]">

                            [{if $oViewConf->isFunctionalityEnabled("blShowTSInternationalFeesMessage")}]
                                [{oxifcontent ident="oxtsinternationalfees" object="oTSIFContent"}]
                                    <hr/>
                                    <div class="clear">
                                        <span class="title">[{$oTSIFContent->oxcontents__oxcontent->value}]</span>
                                    </div>
                                [{/oxifcontent}]
                            [{/if}]

                            [{if $payment->oxpayments__oxid->value eq "oxidcashondel" && $oViewConf->isFunctionalityEnabled("blShowTSCODMessage")}]
                                [{oxifcontent ident="oxtscodmessage" object="oTSCODContent"}]
                                    <hr/>
                                    <div class="clear">
                                        <span class="title">[{$oTSCODContent->oxcontents__oxcontent->value}]</span>
                                    </div>
                                [{/oxifcontent}]
                            [{/if}]
                            <hr/>

                            [{if !$oView->showOrderButtonOnTop()}]
                                [{include file="page/checkout/inc/agb.tpl"}]
                                <hr/>
                            [{else}]
                                [{include file="page/checkout/inc/agb.tpl" hideButtons=true}]
                            [{/if}]

                            <a href="[{oxgetseourl ident=$oViewConf->getPaymentLink()}]" class="prevStep submitButton largeButton">[{oxmultilang ident="PAGE_CHECKOUT_ORDER_BACKSTEP"}]</a>
                            <button type="submit" class="submitButton nextStep largeButton">[{oxmultilang ident="PAGE_CHECKOUT_ORDER_SUBMITORDER"}]</button>
                        </form>
                    [{/block}]
                [{/if}]
                </div>
            [{/if}]
        [{/block}]
        [{oxscript add="$('#checkAgbTop').click(function(){ $('input[name=ord_agb]').val(parseInt($('input[name=ord_agb]').val())^1);});"}]
        [{oxscript add="$('#oxdownloadableproductsagreement').click(function(){ $('input[name=oxdownloadableproductsagreement]').val(parseInt($('input[name=oxdownloadableproductsagreement]').val())^1);});"}]
        [{oxscript add="$('#oxserviceproductsagreement').click(function(){ $('input[name=oxserviceproductsagreement]').val(parseInt($('input[name=oxserviceproductsagreement]').val())^1);});"}]
    [{/block}]

    [{insert name="oxid_tracker" title=$template_title}]
[{/capture}]

[{assign var="template_title" value="PAGE_CHECKOUT_ORDER_TITLE"|oxmultilangassign}]
[{include file="layout/page.tpl" title=$template_title location=$template_title}]