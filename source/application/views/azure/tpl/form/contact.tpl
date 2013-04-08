[{assign var="editval" value=$oView->getUserData() }]
[{oxscript include="js/widgets/oxinputvalidator.js" priority=10 }]
[{oxscript add="$('form.js-oxValidate').oxInputValidator();"}]
<form class="js-oxValidate" action="[{ $oViewConf->getSslSelfLink() }]" method="post">
    <div>
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="fnc" value="send"/>
        <input type="hidden" name="cl" value="contact"/>
        [{assign var="oCaptcha" value=$oView->getCaptcha() }]
        <input type="hidden" name="c_mach" value="[{$oCaptcha->getHash()}]"/>
    </div>
    <ul class="form clear">
        <li>
            <label>[{ oxmultilang ident="TITLE" }]</label>
            [{if $oxcmp_user && !$editval.oxuser__oxsal}]
                [{include file="form/fieldset/salutation.tpl" name="editval[oxuser__oxsal]" value=$oxcmp_user->oxuser__oxsal->value }]
            [{else}]
                [{include file="form/fieldset/salutation.tpl" name="editval[oxuser__oxsal]" value=$editval.oxuser__oxsal }]
            [{/if}]
        </li>
        <li [{if $aErrors.oxuser__oxfname}]class="oxInValid"[{/if}]>
            <label class="req">[{ oxmultilang ident="FIRST_NAME" suffix="COLON" }]</label>
            <input type="text" name="editval[oxuser__oxfname]" size="70" maxlength="40" value="[{if $oxcmp_user && !$editval.oxuser__oxfname}][{$oxcmp_user->oxuser__oxfname->value}][{else}][{$editval.oxuser__oxfname}][{/if}]" class="js-oxValidate js-oxValidate_notEmpty">
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxfname}]
            </p>
        </li>
        <li [{if $aErrors.oxuser__oxlname}]class="oxInValid"[{/if}]>
            <label class="req">[{ oxmultilang ident="LAST_NAME" }]</label>
            <input type="text" name="editval[oxuser__oxlname]" size=70 maxlength=40 value="[{if $oxcmp_user && !$editval.oxuser__oxlname}][{$oxcmp_user->oxuser__oxlname->value}][{else}][{$editval.oxuser__oxlname}][{/if}]" class="js-oxValidate js-oxValidate_notEmpty">
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            </p>
        </li>
        <li [{if $aErrors.oxuser__oxusername}]class="oxInValid"[{/if}]>
            <label class="req">[{ oxmultilang ident="EMAIL" suffix="COLON" }]</label>
            <input id="contactEmail" type="text" name="editval[oxuser__oxusername]"  size=70 maxlength=40 value="[{if $oxcmp_user && !$editval.oxuser__oxusername}][{$oxcmp_user->oxuser__oxusername->value}][{else}][{$editval.oxuser__oxusername}][{/if}]" class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_email">
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                <span class="js-oxError_email">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOVALIDEMAIL" }]</span>
            </p>
        </li>
        <li [{if $aErrors && !$oView->getContactSubject()}]class="oxInValid"[{/if}]>
            <label class="req">[{ oxmultilang ident="SUBJECT" suffix="COLON" }]</label>
            <input type="text" name="c_subject" size="70" maxlength=80 value="[{$oView->getContactSubject()}]" class="js-oxValidate js-oxValidate_notEmpty">
                    <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            </p>
        </li>
        <li>
            <label>[{ oxmultilang ident="MESSAGE" suffix="COLON" }]</label>
            <textarea rows="15" cols="70" name="c_message" class="areabox">[{$oView->getContactMessage()}]</textarea>
        </li>
        <li class="verify">
            <label class="req">[{ oxmultilang ident="VERIFICATION_CODE" suffix="COLON" }]</label>
            [{assign var="oCaptcha" value=$oView->getCaptcha() }]
            [{if $oCaptcha->isImageVisible()}]
                <img src="[{$oCaptcha->getImageUrl()}]" alt="">
            [{else}]
                <span class="verificationCode" id="verifyTextCode">[{$oCaptcha->getText()}]</span>
            [{/if}]
            <input type="text" data-fieldsize="verify" name="c_mac" value="" class="js-oxValidate js-oxValidate_notEmpty">
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            </p>
        </li>
        <li class="formNote">
            [{ oxmultilang ident="COMPLETE_MARKED_FIELDS" }]
        </li>
        <li>
            <button class="submitButton largeButton" type="submit">[{ oxmultilang ident="SEND" }]</button>
        </li>
    </ul>
</form>
