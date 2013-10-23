[{block name="checkout_user_options"}]
    [{oxscript include="js/widgets/oxequalizer.js" priority=10 }]
    [{oxscript add="$(function(){oxEqualizer.equalHeight($('.checkoutOptions .option'));});"}]
    <div class="checkoutOptions clear">
        [{block name="checkout_options_noreg"}]
            [{if $oView->getShowNoRegOption() }]
            <div class="lineBox option" id="optionNoRegistration">
                <h3>[{ oxmultilang ident="PURCHASE_WITHOUT_REGISTRATION" }]</h3>
                [{block name="checkout_options_noreg_text"}]
                    <p>[{ oxmultilang ident="DO_NOT_WANT_CREATE_ACCOUNT" }]</p>
                    [{if $oView->isDownloadableProductWarning() }]
                        <p class="errorMsg">[{ oxmultilang ident="MESSAGE_DOWNLOADABLE_PRODUCT" }]</p>
                    [{/if}]
                [{/block}]
                <form action="[{$oViewConf->getSslSelfLink()}]" method="post">
                    <p>
                        [{ $oViewConf->getHiddenSid() }]
                        [{ $oViewConf->getNavFormParams() }]
                        <input type="hidden" name="cl" value="user">
                        <input type="hidden" name="fnc" value="">
                        <input type="hidden" name="option" value="1">
                        <button class="submitButton nextStep" type="submit">[{ oxmultilang ident="NEXT" }]</button>
                    </p>
                </form>
            </div>
            [{/if}]
        [{/block}]

        [{block name="checkout_options_reg"}]
            <div class="lineBox option" id="optionRegistration">
                <h3>[{ oxmultilang ident="OPEN_PERSONAL_ACCOUNT" }]</h3>
                [{block name="checkout_options_reg_text"}]
                    [{oxifcontent ident="oxregistrationdescription" object="oCont"}]
                        [{$oCont->oxcontents__oxcontent->value}]
                    [{/oxifcontent}]
                [{/block}]
                <form action="[{$oViewConf->getSslSelfLink()}]" method="post">
                    <p>
                        [{ $oViewConf->getHiddenSid() }]
                        [{ $oViewConf->getNavFormParams() }]
                        <input type="hidden" name="cl" value="user">
                        <input type="hidden" name="fnc" value="">
                        <input type="hidden" name="option" value="3">
                        <button class="submitButton nextStep" type="submit">[{ oxmultilang ident="NEXT" }]</button>
                    </p>
                </form>
            </div>
        [{/block}]

        [{block name="checkout_options_login"}]
            <div class="lineBox option" id="optionLogin">
                <h3>[{ oxmultilang ident="ALREADY_CUSTOMER" }]</h3>
                [{block name="checkout_options_login_text"}]
                    <p>[{ oxmultilang ident="LOGIN_DESCRIPTION" }]</p>
                [{/block}]
                [{ include file="form/login.tpl"}]
            </div>
        [{/block}]
    </div>
[{/block}]