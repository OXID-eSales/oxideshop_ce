[{oxscript include="js/widgets/oxinputvalidator.js" priority=10 }]
[{oxscript add="$('form.js-oxValidate').oxInputValidator();"}]
<form class="js-oxValidate" name="login" action="[{ $oViewConf->getSslSelfLink() }]" method="post">
[{assign var="aErrors" value=$oView->getFieldValidationErrors()}]
    <ul class="form">
        <li [{if $aErrors}]class="oxInValid"[{/if}]>
            [{ $oViewConf->getHiddenSid() }]
            [{ $oViewConf->getNavFormParams() }]
            <input type="hidden" name="fnc" value="login_noredirect">
            <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
            <label class="short">[{ oxmultilang ident="EMAIL_ADDRESS" }]</label>
            <input type="text" name="lgn_usr" class="textbox js-oxValidate js-oxValidate_notEmpty" data-fieldsize="pair-xsmall">
            <p class="underInput short oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            </p>
        </li>
        <li [{if $aErrors}]class="oxInValid"[{/if}]>
            <label class="short">[{ oxmultilang ident="PASSWORD" }]</label>
            <input type="password" name="lgn_pwd" class="js-oxValidate js-oxValidate_notEmpty textbox stepsPasswordbox" data-fieldsize="pair-xsmall">
            &nbsp;<strong><a class="forgotPasswordOpener" id="step2PswdOpener" href="#" title="[{ oxmultilang ident="FORGOT_PASSWORD" }]">?</a></strong>
            <p class="underInput short oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            </p>
        </li>
        <li><button type="submit" class="submitButton">[{ oxmultilang ident="LOGIN" }]</button></li>
    </ul>
</form>