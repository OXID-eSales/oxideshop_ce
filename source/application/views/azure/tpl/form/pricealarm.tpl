[{assign var="currency" value=$oView->getActCurrency()}]
<p>[{ oxmultilang ident="MESSAGE_PRICE_ALARM_PRICE_CHANGE" }]</p>
[{oxscript include="js/widgets/oxinputvalidator.js" priority=10 }]
[{oxscript add="$('form.js-oxValidate').oxInputValidator();"}]
<form class="js-oxValidate" name="pricealarm" action="[{ $oViewConf->getSelfActionLink() }]" method="post">
    <div>
        [{ $oViewConf->getHiddenSid() }]
        [{ $oViewConf->getNavFormParams() }]
        <input type="hidden" name="cl" value="[{$oViewConf->getTopActiveClassName()}]">
        [{if $oDetailsProduct}]
        <input type="hidden" name="anid" value="[{$oDetailsProduct->oxarticles__oxid->value}]">
        [{/if}]
        <input type="hidden" name="fnc" value="addme">
        [{assign var="oCaptcha" value=$oView->getCaptcha() }]
        <input type="hidden" name="c_mach" value="[{$oCaptcha->getHash()}]"/>
    </div>
    <ul class="form">
        <li>
            <label>[{ oxmultilang ident="YOUR_PRICE" }] ([{ $currency->sign}]):</label>
            <input class="js-oxValidate js-oxValidate_notEmpty" type="text" name="pa[price]" value="[{oxhasrights ident="SHOWARTICLEPRICE"}][{ if $product }][{ $product->getFPrice() }][{/if}][{/oxhasrights}]" size="20" maxlength="32">
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            </p>
        </li>
        <li>
            <label>[{ oxmultilang ident="EMAIL" suffix="COLON" }]</label>
            <input class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_email" type="text" name="pa[email]" value="[{ if $oxcmp_user }][{ $oxcmp_user->oxuser__oxusername->value }][{/if}]" size="20" maxlength="128">
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                <span class="js-oxError_email">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOVALIDEMAIL" }]</span>
            </p>
        </li>
        <li>
            <label>[{ oxmultilang ident="VERIFICATION_CODE" suffix="COLON" }]</label>
            [{if $oCaptcha->isImageVisible()}]
                <img class="verificationCode" src="[{$oCaptcha->getImageUrl()}]" alt="[{ oxmultilang ident="VERIFICATION_CODE" }]">
            [{else}]
                <span class="verificationCode" id="verifyTextCode">[{$oCaptcha->getText()}]</span>
            [{/if}]
            <input class="js-oxValidate js-oxValidate_notEmpty" type="text" data-fieldsize="verify" name="c_mac" value="">
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            </p>
        </li>
        <li class="formSubmit">
            <button class="submitButton largeButton" type="submit">[{ oxmultilang ident="SEND" }]</button>
        </li>
    </ul>
</form>