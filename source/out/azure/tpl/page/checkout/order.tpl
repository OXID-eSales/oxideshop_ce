[{oxscript include="js/widgets/oxagbcheck.js" priority=10 }]
[{oxscript add="$('#checkAgbTop, #checkAgbBottom').oxAGBCheck();"}]

[{capture append="oxidBlock_content"}]

    [{block name="checkout_order_errors"}]
        [{if $oView->isConfirmAGBError() == 1 }]
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
        <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
            <h3 class="section">
                <strong>[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_BASKET" }]</strong>
                [{ $oViewConf->getHiddenSid() }]
                <input type="hidden" name="cl" value="basket">
                <input type="hidden" name="fnc" value="">
                <button type="submit" class="submitButton largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_MODIFY4" }]</button>
            </h3>
        </form>
        [{block name="checkout_order_details"}]
            [{ if !$oxcmp_basket->getProductsCount()  }]
                <div>[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_BASKETEMPTY" }]</div>
            [{else}]
                [{assign var="currency" value=$oView->getActCurrency() }]

                [{if $oView->isLowOrderPrice()}]
                    [{block name="checkout_order_loworderprice_top"}]
                        <div>[{ oxmultilang ident="MIN_ORDER_PRICE" }] [{ $oView->getMinOrderPrice() }] [{ $currency->sign }]</div>
                    [{/block}]
                [{elseif $oView->showOrderButtonOnTop()}]
                    <form action="[{ $oViewConf->getSslSelfLink() }]" method="post" id="orderConfirmAgbTop">
                        [{ $oViewConf->getHiddenSid() }]
                        [{ $oViewConf->getNavFormParams() }]
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
                [{/if}]

                [{block name="order_basket"}]
                    [{include file="page/checkout/inc/basketcontents.tpl" editable=false}]
                [{/block}]

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
                            [{assign var="oDelAdress" value=$oView->getDelAddress() }]
                            [{if $oDelAdress }]
                                <dt>[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_SHIPPINGADDRESS" }]</dt>
                                <dd>
                                    [{include file="widget/address/shipping_address.tpl" delivadr=$oDelAdress}]
                                </dd>
                            [{/if}]
                        </dl>

                        [{if $oView->getOrderRemark() }]
                            <div>
                                [{ oxmultilang ident="PAGE_CHECKOUT_ORDER_WHATIWANTEDTOSAY" }] [{ $oView->getOrderRemark() }]
                            </div>
                        [{/if}]

                    </div>
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

                [{if $oView->isLowOrderPrice() }]
                    [{block name="checkout_order_loworderprice_bottom"}]
                        <div class="lineBox clear">
                            <div>[{ oxmultilang ident="MIN_ORDER_PRICE" }] [{ $oView->getMinOrderPrice() }] [{ $currency->sign }]</div>
                        </div>
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
                            [{include file="page/checkout/inc/agb.tpl"}]
                            <div class="lineBox clear">
                                <a href="[{ oxgetseourl ident=$oViewConf->getPaymentLink() }]" class="prevStep submitButton largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_BACKSTEP" }]</a>
                                <button type="submit" class="submitButton nextStep largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_SUBMITORDER" }]</button>
                            </div>
                        </form>
                    [{/block}]
                [{/if}]
            [{/if}]
        [{/block}]
    [{/block}]
    [{insert name="oxid_tracker" title=$template_title }]
[{/capture}]

[{assign var="template_title" value="PAGE_CHECKOUT_ORDER_TITLE"|oxmultilangassign}]
[{include file="layout/page.tpl" title=$template_title location=$template_title}]