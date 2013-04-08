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
            <label>[{ oxmultilang ident="FORM_CONTACT_TITLE" }]</label>
            [{include file="form/fieldset/salutation.tpl" name="editval[oxuser__oxsal]" value=$editval.oxuser__oxsal }]
        </li>
        <li [{if $aErrors.oxuser__oxfname}]class="oxInValid"[{/if}]>
            <label class="req">[{ oxmultilang ident="FORM_CONTACT_FIRSTNAME" }]</label>
            <input type="text" name="editval[oxuser__oxfname]" size="70" maxlength="40" value="[{$editval.oxuser__oxfname}]" class="js-oxValidate js-oxValidate_notEmpty">
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxfname}]
            </p>
        </li>
        <li [{if $aErrors.oxuser__oxlname}]class="oxInValid"[{/if}]>
            <label class="req">[{ oxmultilang ident="FORM_CONTACT_LASTNAME" }]</label>
            <input type="text" name="editval[oxuser__oxlname]" size=70 maxlength=40 value="[{$editval.oxuser__oxlname}]" class="js-oxValidate js-oxValidate_notEmpty">
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
            </p>
        </li>
        <li [{if $aErrors.oxuser__oxusername}]class="oxInValid"[{/if}]>
            <label class="req">[{ oxmultilang ident="FORM_CONTACT_EMAIL2" }]</label>
            <input id="contactEmail" type="text" name="editval[oxuser__oxusername]"  size=70 maxlength=40 value="[{$editval.oxuser__oxusername}]" class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_email">
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
                <span class="js-oxError_email">[{ oxmultilang ident="EXCEPTION_INPUT_NOVALIDEMAIL" }]</span>
            </p>
        </li>
        <li [{if $aErrors && !$oView->getContactSubject()}]class="oxInValid"[{/if}]>
            <label class="req">[{ oxmultilang ident="FORM_CONTACT_SUBJECT" }]</label>
            <input type="text" name="c_subject" size="70" maxlength=80 value="[{$oView->getContactSubject()}]" class="js-oxValidate js-oxValidate_notEmpty">
                    <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
            </p>
        </li>
        <li>
            <label>[{ oxmultilang ident="FORM_CONTACT_MESSAGE" }]</label>
            <textarea rows="15" cols="70" name="c_message" class="areabox">[{$oView->getContactMessage()}]</textarea>
        </li>
        <li class="verify">
            <label class="req">[{ oxmultilang ident="FORM_CONTACT_VERIFICATIONCODE" }]</label>
            [{assign var="oCaptcha" value=$oView->getCaptcha() }]
            [{if $oCaptcha->isImageVisible()}]
                <img src="[{$oCaptcha->getImageUrl()}]" alt="">
            [{else}]
                <span class="verificationCode" id="verifyTextCode">[{$oCaptcha->getText()}]</span>
            [{/if}]
            <input type="text" data-fieldsize="verify" name="c_mac" value="" class="js-oxValidate js-oxValidate_notEmpty">
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
            </p>
        </li>
        <li class="formNote">
            [{ oxmultilang ident="FORM_CONTACT_COMPLETEMARKEDFIELDS2" }]
        </li>
        <li>
            <button class="submitButton largeButton" type="submit">[{ oxmultilang ident="FORM_CONTACT_SEND" }]</button>
        </li>
    </ul>
</form>
