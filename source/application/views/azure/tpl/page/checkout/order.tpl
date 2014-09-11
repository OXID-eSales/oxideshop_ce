[{capture append="oxidBlock_content"}]

    [{block name="checkout_order_errors"}]
        [{if $oView->isConfirmAGBError() == 1}]
            [{include file="message/error.tpl" statusMessage="READ_AND_CONFIRM_TERMS"|oxmultilangassign}]
        [{/if}]
        [{assign var="iError" value=$oView->getAddressError()}]
        [{if $iError == 1}]
           [{include file="message/error.tpl" statusMessage="ERROR_DELIVERY_ADDRESS_WAS_CHANGED_DURING_CHECKOUT"|oxmultilangassign}]
        [{/if}]
    [{/block}]

    [{* ordering steps *}]
    [{include file="page/checkout/inc/steps.tpl" active=4}]

    [{block name="checkout_order_main"}]
        [{if !$oView->showOrderButtonOnTop()}]
            <div class="lineBox clear">
                <span>&nbsp;</span>
                <span class="title">[{oxmultilang ident="MESSAGE_SUBMIT_BOTTOM"}]</span>
            </div>
        [{/if}]

        [{block name="checkout_order_details"}]
            [{if !$oxcmp_basket->getProductsCount()}]
                [{block name="checkout_order_emptyshippingcart"}]
                    <div class="status corners error">[{oxmultilang ident="BASKET_EMPTY"}]</div>
                [{/block}]
            [{else}]
                [{assign var="currency" value=$oView->getActCurrency()}]

                [{block name="checkout_order_next_step_top"}]
                    [{if $oView->isLowOrderPrice()}]
                        [{block name="checkout_order_loworderprice_top"}]
                            <div>[{oxmultilang ident="MIN_ORDER_PRICE"}] [{$oView->getMinOrderPrice()}] [{$currency->sign}]</div>
                        [{/block}]
                    [{else}]

                        [{block name="checkout_order_btn_confirm_top"}]
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
                                            <a href="[{oxgetseourl ident=$oViewConf->getPaymentLink()}]" class="prevStep submitButton largeButton">[{oxmultilang ident="PREVIOUS_STEP"}]</a>
                                            <button type="submit" class="submitButton nextStep largeButton">[{oxmultilang ident="SUBMIT_ORDER"}]</button>
                                        </div>
                                    </form>
                                </div>
                            [{/if}]
                        [{/block}]
                    [{/if}]
                [{/block}]

                [{block name="checkout_order_vouchers"}]
                    [{if $oViewConf->getShowVouchers() && $oxcmp_basket->getVouchers()}]
                        [{oxmultilang ident="USED_COUPONS"}]
                        <div>
                            [{foreach from=$Errors.basket item=oEr key=key}]
                                [{if $oEr->getErrorClassType() == 'oxVoucherException'}]
                                    [{oxmultilang ident="COUPON_NOT_ACCEPTED" args=$oEr->getValue('voucherNr')}]<br>
                                    [{oxmultilang ident="REASON" suffix="COLON"}]
                                    [{$oEr->getOxMessage()}]<br>
                                [{/if}]
                            [{/foreach}]
                            [{foreach from=$oxcmp_basket->getVouchers() item=sVoucher key=key name=aVouchers}]
                                [{$sVoucher->sVoucherNr}]<br>
                            [{/foreach}]
                        </div>
                    [{/if}]
                [{/block}]

                [{block name="checkout_order_address"}]
                    <div id="orderAddress">
                        <form action="[{$oViewConf->getSslSelfLink()}]" method="post">
                            <h3 class="section">
                            <strong>[{oxmultilang ident="ADDRESSES"}]</strong>
                            [{$oViewConf->getHiddenSid()}]
                            <input type="hidden" name="cl" value="user">
                            <input type="hidden" name="fnc" value="">
                            <button type="submit" class="submitButton largeButton">[{oxmultilang ident="EDIT"}]</button>
                            </h3>
                        </form>
                        <dl>
                            <dt>[{oxmultilang ident="BILLING_ADDRESS"}]</dt>
                            <dd>
                                [{include file="widget/address/billing_address.tpl"}]
                            </dd>
                        </dl>
                        [{assign var="oDelAdress" value=$oView->getDelAddress()}]
                        [{if $oDelAdress}]
                        <dl class="shippingAddress">
                            <dt>[{oxmultilang ident="SHIPPING_ADDRESS"}]</dt>
                            <dd>
                                [{include file="widget/address/shipping_address.tpl" delivadr=$oDelAdress}]
                            </dd>
                        </dl>
                        [{/if}]

                        [{if $oView->getOrderRemark()}]
                            <dl class="orderRemarks">
                                <dt>[{oxmultilang ident="WHAT_I_WANTED_TO_SAY"}]</dt>
                                <dd>
                                    [{$oView->getOrderRemark()}]
                                </dd>
                            </dl>
                        [{/if}]
                    </div>
                    <div style="clear:both;"></div>
                [{/block}]

                [{block name="shippingAndPayment"}]
                    <div id="orderShipping">
                    <form action="[{$oViewConf->getSslSelfLink()}]" method="post">
                        <h3 class="section">
                            <strong>[{oxmultilang ident="SHIPPING_CARRIER"}]</strong>
                            [{$oViewConf->getHiddenSid()}]
                            <input type="hidden" name="cl" value="payment">
                            <input type="hidden" name="fnc" value="">
                            <button type="submit" class="submitButton largeButton">[{oxmultilang ident="EDIT"}]</button>
                        </h3>
                    </form>
                    [{assign var="oShipSet" value=$oView->getShipSet()}]
                    [{$oShipSet->oxdeliveryset__oxtitle->value}]
                    </div>

                    <div id="orderPayment">
                        <form action="[{$oViewConf->getSslSelfLink()}]" method="post">
                            <h3 class="section">
                                <strong>[{oxmultilang ident="PAYMENT_METHOD"}]</strong>
                                [{$oViewConf->getHiddenSid()}]
                                <input type="hidden" name="cl" value="payment">
                                <input type="hidden" name="fnc" value="">
                                <button type="submit" class="submitButton largeButton">[{oxmultilang ident="EDIT"}]</button>
                            </h3>
                        </form>
                        [{assign var="payment" value=$oView->getPayment()}]
                        [{$payment->oxpayments__oxdesc->value}]
                    </div>
                [{/block}]

                <div id="orderEditCart">
                    <form action="[{$oViewConf->getSslSelfLink()}]" method="post">
                        <h3 class="section">
                            <strong>[{oxmultilang ident="CART"}]</strong>
                            [{$oViewConf->getHiddenSid()}]
                            <input type="hidden" name="cl" value="basket">
                            <input type="hidden" name="fnc" value="">
                            <button type="submit" class="submitButton largeButton">[{oxmultilang ident="EDIT"}]</button>
                        </h3>
                    </form>
                </div>

                <div id="basketContent" class="lineBox clear">
                [{block name="order_basket"}]
                    [{include file="page/checkout/inc/basketcontents.tpl" editable=false}]
                [{/block}]

                [{block name="checkout_order_next_step_bottom"}]
                    [{if $oView->isLowOrderPrice()}]
                        [{block name="checkout_order_loworderprice_bottom"}]
                            <div>[{oxmultilang ident="MIN_ORDER_PRICE"}] [{$oView->getMinOrderPrice()}] [{$currency->sign}]</div>
                        [{/block}]
                    [{else}]
                        [{block name="checkout_order_btn_confirm_bottom"}]
                            <form action="[{$oViewConf->getSslSelfLink()}]" method="post" id="orderConfirmAgbBottom">
                                [{$oViewConf->getHiddenSid()}]
                                [{$oViewConf->getNavFormParams()}]
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

                                <a href="[{oxgetseourl ident=$oViewConf->getPaymentLink()}]" class="prevStep submitButton largeButton">[{oxmultilang ident="PREVIOUS_STEP"}]</a>
                                <button type="submit" class="submitButton nextStep largeButton">[{oxmultilang ident="SUBMIT_ORDER"}]</button>
                            </form>
                        [{/block}]
                    [{/if}]
                [{/block}]
                </div>
            [{/if}]
        [{/block}]
    [{/block}]
[{/capture}]
[{include file="layout/page.tpl"}]
