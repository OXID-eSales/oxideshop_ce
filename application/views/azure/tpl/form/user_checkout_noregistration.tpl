[{oxscript include="js/widgets/oxinputvalidator.js" priority=10}]
[{oxscript add="$('form.js-oxValidate').oxInputValidator();"}]
[{block name="user_checkout_noregistration"}]
    <form class="js-oxValidate" action="[{$oViewConf->getSslSelfLink()}]" name="order" method="post">
        [{block name="user_checkout_noregistration_form"}]
            [{assign var="aErrors" value=$oView->getFieldValidationErrors()}]
            [{$oViewConf->getHiddenSid()}]
            [{$oViewConf->getNavFormParams()}]
            <input type="hidden" name="cl" value="user">
            <input type="hidden" name="option" value="1">
            [{if !$oxcmp_user->oxuser__oxpassword->value}]
                <input type="hidden" name="fnc" value="createuser">
            [{else}]
                <input type="hidden" name="fnc" value="changeuser">
                <input type="hidden" name="lgn_cook" value="0">
            [{/if}]
            <input type="hidden" id="reloadAddress" name="reloadaddress" value="">
            <input type="hidden" name="blshowshipaddress" value="1">
            [{block name="user_checkout_noregistration_next_step_top"}]
                <div class="lineBox clear">
                    <a href="[{oxgetseourl ident=$oViewConf->getBasketLink()}]" class="prevStep submitButton largeButton" id="userBackStepTop">[{oxmultilang ident="PREVIOUS_STEP"}]</a>
                    <button id="userNextStepTop" class="submitButton largeButton nextStep" name="userform" type="submit">[{oxmultilang ident="CONTINUE_TO_NEXT_STEP"}]</button>
                </div>
            [{/block}]
            <div class="checkoutColumns clear">
                <div class="row">
                    <h3 class="blockHead">[{oxmultilang ident="NOTREGISTERED_ACCOUNTINFO"}]</h3>
                    <ul class="form">
                        [{include file="form/fieldset/user_noaccount.tpl"}]
                    </ul>
                </div>
                <div class="column">
                    <h3 class="blockHead">[{oxmultilang ident="BILLING_ADDRESS"}]</h3>
                    <ul class="form">
                        [{include file="form/fieldset/user_billing.tpl" noFormSubmit=true blOrderRemark=true}]
                    </ul>
                </div>
                <div class="column">
                    <h3 class="blockHead">[{oxmultilang ident="SHIPPING_ADDRESS"}]</h3>

                    <p>
                        <input type="checkbox" name="blshowshipaddress" id="showShipAddress" [{if !$oView->showShipAddress()}]checked[{/if}] value="0"><label for="showShipAddress">[{oxmultilang ident="USE_BILLINGADDRESS_FOR_SHIPPINGADDRESS"}]</label>
                    </p>
                    <ul id="shippingAddress" class="form" [{if !$oView->showShipAddress()}]style="display: none;"[{/if}]>
                        [{include file="form/fieldset/user_shipping.tpl" noFormSubmit=true}]
                    </ul>
                    <ul class="form">
                        <li>
                            [{include file="form/fieldset/order_remark.tpl" blOrderRemark=true}]
                        </li>
                    </ul>
                </div>
            </div>
            [{oxscript add="$('#showShipAddress').change( function() { $('#shippingAddress').toggle($(this).is(':not(:checked)'));});"}]
            [{block name="user_checkout_noregistration_next_step_bottom"}]
                <div class="lineBox clear">
                    <a href="[{oxgetseourl ident=$oViewConf->getBasketLink()}]" class="prevStep submitButton largeButton" id="userBackStepBottom">[{oxmultilang ident="PREVIOUS_STEP"}]</a>
                    <button id="userNextStepBottom" class="submitButton largeButton nextStep" name="userform" type="submit">[{oxmultilang ident="CONTINUE_TO_NEXT_STEP"}]</button>
                </div>
            [{/block}]
        [{/block}]
    </form>
[{/block}]
