[{block name="checkout_user_options"}]
    [{oxscript include="js/widgets/oxequalizer.js" priority=10 }]
    [{oxscript add="$(function(){oxEqualizer.equalHeight($( '.checkoutOptions .option' ));});"}]
    <div class="checkoutOptions clear">
        [{block name="checkout_options_noreg"}]
            [{if $oView->getShowNoRegOption() }]
            <div class="lineBox option" id="optionNoRegistration">
                <h3>[{ oxmultilang ident="PAGE_CHECKOUT_USER_OPTION_NOREGISTRATION" }]</h3>
                [{block name="checkout_options_noreg_text"}]
                    <p>[{ oxmultilang ident="PAGE_CHECKOUT_USER_OPTION_NOREGISTRATION_DESCRIPTION" }]</p>
                    [{if $oView->isDownloadableProductWarning() }]
                        <p class="errorMsg">[{ oxmultilang ident="MESSAGE_DOWNLOADABLE_PRODUCT" }]</p>
                    [{/if}]
                [{/block}]
                <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
                    <p>
                        [{ $oViewConf->getHiddenSid() }]
                        [{ $oViewConf->getNavFormParams() }]
                        <input type="hidden" name="cl" value="user">
                        <input type="hidden" name="fnc" value="">
                        <input type="hidden" name="option" value="1">
                        <button class="submitButton nextStep" type="submit">[{ oxmultilang ident="PAGE_CHECKOUT_USER_OPTION_NEXT" }]</button>
                    </p>
                </form>
            </div>
            [{/if}]
        [{/block}]

        [{block name="checkout_options_reg"}]
            <div class="lineBox option" id="optionRegistration">
                <h3>[{ oxmultilang ident="PAGE_CHECKOUT_USER_OPTION_REGISTRATION" }]</h3>
                [{block name="checkout_options_reg_text"}]
                    <p>[{ oxmultilang ident="PAGE_CHECKOUT_USER_OPTION_REGISTRATION_DESCRIPTION" }]</p>
                [{/block}]
                <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
                    <p>
                        [{ $oViewConf->getHiddenSid() }]
                        [{ $oViewConf->getNavFormParams() }]
                        <input type="hidden" name="cl" value="user">
                        <input type="hidden" name="fnc" value="">
                        <input type="hidden" name="option" value="3">
                        <button class="submitButton nextStep" type="submit">[{ oxmultilang ident="PAGE_CHECKOUT_USER_OPTION_NEXT" }]</button>
                    </p>
                </form>
            </div>
        [{/block}]

        [{block name="checkout_options_login"}]
            <div class="lineBox option" id="optionLogin">
                <h3>[{ oxmultilang ident="PAGE_CHECKOUT_USER_OPTION_LOGIN" }]</h3>
                [{block name="checkout_options_login_text"}]
                    <p>[{ oxmultilang ident="PAGE_CHECKOUT_USER_OPTION_LOGIN_DESCRIPTION" }]</p>
                [{/block}]
                [{ include file="form/login.tpl"}]
            </div>
        [{/block}]
    </div>
[{/block}]