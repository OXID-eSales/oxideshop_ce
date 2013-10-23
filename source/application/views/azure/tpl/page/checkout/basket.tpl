[{capture append="oxidBlock_content"}]

    [{* ordering steps *}]
    [{include file="page/checkout/inc/steps.tpl" active=1 }]

    [{block name="checkout_basket_main"}]
        [{assign var="currency" value=$oView->getActCurrency() }]
        [{if !$oxcmp_basket->getProductsCount()  }]
            [{block name="checkout_basket_emptyshippingcart"}]
                <div class="status corners error">[{ oxmultilang ident="BASKET_EMPTY" }]</div>
            [{/block}]
        [{else }]
            <div class="lineBox clear">
                [{if $oView->showBackToShop()}]
                    [{block name="checkout_basket_backtoshop_top"}]
                        <div class="backtoshop">
                            <form action="[{$oViewConf->getSslSelfLink()}]" method="post">
                                [{ $oViewConf->getHiddenSid() }]
                                <input type="hidden" name="cl" value="basket">
                                <input type="hidden" name="fnc" value="backtoshop">
                                <button type="submit" class="submitButton largeButton">[{ oxmultilang ident="CONTINUE_SHOPPING" }]</button>
                            </form>
                        </div>
                    [{/block}]
                [{/if}]

                [{if $oView->isLowOrderPrice() }]
                    [{block name="checkout_basket_loworderprice_top"}]
                        <div>[{ oxmultilang ident="MIN_ORDER_PRICE" }] [{ $oView->getMinOrderPrice() }] [{ $currency->sign }]</div>
                    [{/block}]
                [{else}]
                    [{block name="basket_btn_next_top"}]
                        <form action="[{$oViewConf->getSslSelfLink()}]" method="post">
                            [{ $oViewConf->getHiddenSid() }]
                            <input type="hidden" name="cl" value="user">
                            <button type="submit" class="submitButton largeButton nextStep">[{ oxmultilang ident="CONTINUE_TO_NEXT_STEP" }]</button>
                        </form>
                    [{/block}]
                [{/if}]
            </div>

            <div class="lineBox">
                [{include file="page/checkout/inc/basketcontents.tpl" editable=true}]

                [{if $oViewConf->getShowVouchers()}]
                    [{block name="checkout_basket_vouchers"}]
                        [{oxscript include="js/widgets/oxinputvalidator.js" priority=10 }]
                        [{oxscript add="$('form.js-oxValidate').oxInputValidator();"}]
                        <div id="basketVoucher">
                            <form name="voucher" action="[{$oViewConf->getSelfActionLink()}]" method="post" class="js-oxValidate">
                                <div class="couponBox" id="coupon">
                                    [{foreach from=$Errors.basket item=oEr key=key}]
                                        [{if $oEr->getErrorClassType() == 'oxVoucherException'}]
                                            <div class="inlineError">
                                                [{ oxmultilang ident="COUPON_NOT_ACCEPTED" args=$oEr->getValue('voucherNr') }]
                                                <strong>[{ oxmultilang ident="REASON" suffix="COLON" }]</strong>
                                                [{ $oEr->getOxMessage() }]
                                            </div>
                                        [{/if}]
                                    [{/foreach}]
                                    <label>[{ oxmultilang ident="ENTER_COUPON_NUMBER" suffix="COLON" }]</label>
                                    [{ $oViewConf->getHiddenSid() }]
                                    <input type="hidden" name="cl" value="basket">
                                    <input type="hidden" name="fnc" value="addVoucher">
                                    <input type="text" size="20" name="voucherNr" class="textbox js-oxValidate js-oxValidate_notEmpty">
                                    <button type="submit" class="submitButton">[{ oxmultilang ident="SUBMIT_COUPON" }]</button>
                                    <p class="oxValidateError">
                                        <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                                    </p>
                                    <input type="hidden" name="CustomError" value='basket'>
                                </div>
                            </form>
                        </div>
                    [{/block}]
                [{/if}]
            </div>


            <div class="lineBox clear">
                [{if $oView->showBackToShop()}]
                    [{block name="checkout_basket_backtoshop_bottom"}]
                        <form action="[{$oViewConf->getSslSelfLink()}]" method="post">
                            <div class="backtoshop">
                                [{ $oViewConf->getHiddenSid() }]
                                <input type="hidden" name="cl" value="basket">
                                <input type="hidden" name="fnc" value="backtoshop">
                                <button type="submit" class="submitButton largeButton">[{ oxmultilang ident="CONTINUE_SHOPPING" }]</button>
                            </div>
                        </form>
                    [{/block}]
                [{/if}]

                [{if $oView->isLowOrderPrice() }]
                    [{block name="checkout_basket_loworderprice_bottom"}]
                        <div>[{ oxmultilang ident="MIN_ORDER_PRICE" }] [{ $oView->getMinOrderPrice() }] [{ $currency->sign }]</div>
                    [{/block}]
                [{else}]
                    [{block name="basket_btn_next_bottom"}]
                        <form action="[{$oViewConf->getSslSelfLink()}]" method="post">
                            [{ $oViewConf->getHiddenSid() }]
                            <input type="hidden" name="cl" value="user">
                            <button type="submit" class="submitButton largeButton nextStep">[{ oxmultilang ident="CONTINUE_TO_NEXT_STEP" }]</button>
                        </form>
                    [{/block}]
                [{/if}]
            </div>
        [{/if}]
        [{if $oView->isWrapping() }]
           [{include file="page/checkout/inc/wrapping.tpl"}]
        [{/if}]
    [{/block}]
    [{insert name="oxid_tracker" title=$template_title }]
[{/capture}]

[{include file="layout/page.tpl"}]