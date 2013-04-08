[{assign var="_oProduct" value=$oView->getProduct()}]
[{assign var="editval" value=$oView->getSuggestData()}]
[{oxscript include="js/widgets/oxinputvalidator.js" priority=10 }]
[{oxscript add="$('form.js-oxValidate').oxInputValidator();"}]
<form class="js-oxValidate" action="[{ $oViewConf->getSslSelfLink() }]" method="post">
    <div>
        [{ $oViewConf->getHiddenSid() }]
        [{ $oViewConf->getNavFormParams() }]
        <input type="hidden" name="fnc" value="send">
        <input type="hidden" name="cl" value="suggest">
        <input type="hidden" name="anid" value="[{ $_oProduct->oxarticles__oxnid->value }]">
        <input type="hidden" name="CustomError" value='suggest'>
        [{assign var="oCaptcha" value=$oView->getCaptcha() }]
        <input type="hidden" name="c_mach" value="[{$oCaptcha->getHash()}]">
        <h3 class="blockHead">[{ oxmultilang ident="CARD_TO" suffix="COLON" }]</h3>
        <ul class="form">
            <li>
                <label class="req">[{ oxmultilang ident="RECIPIENT_NAME" suffix="COLON" }]</label>
                <input class="js-oxValidate js-oxValidate_notEmpty" type="text" name="editval[rec_name]" size="73" maxlength="73" value="[{$editval->rec_name}]" >
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li>
                <label class="req">[{ oxmultilang ident="RECIPIENT_EMAIL" suffix="COLON" }]</label>
                <input class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_email" type="text" name="editval[rec_email]" size="73" maxlength="73" value="[{$editval->rec_email}]">
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                    <span class="js-oxError_email">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOVALIDEMAIL" }]</span>
                </p>
            </li>
        </ul>
        <h3 class="blockHead">[{ oxmultilang ident="FROM" suffix="COLON" }]</h3>
        <ul class="form">
            <li>
                <label class="req">[{ oxmultilang ident="SENDER_NAME" suffix="COLON" }]</label>
                <input class="js-oxValidate js-oxValidate_notEmpty" type="text" name="editval[send_name]" size=73 maxlength=73 value="[{$editval->send_name}]">
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li>
                <label class="req">[{ oxmultilang ident="SENDER_EMAIL" suffix="COLON" }]</label>
                <input class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_email" type="text" name="editval[send_email]" size=73 maxlength=73 value="[{$editval->send_email}]" >
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                    <span class="js-oxError_email">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOVALIDEMAIL" }]</span>
                </p>
            </li>
            <li>
                <label class="req">[{ oxmultilang ident="SUBJECT" suffix="COLON"}]</label>
                <input class="js-oxValidate js-oxValidate_notEmpty" type="text" name="editval[send_subject]" size=73 maxlength=73 value="[{if $editval->send_subject}][{$editval->send_subject}][{else}][{ oxmultilang ident="HAVE_A_LOOK" suffix="COLON" }] [{ $_oProduct->oxarticles__oxtitle->value|strip_tags }][{/if}]">
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li>
                <label class="req">[{ oxmultilang ident="MESSAGE" suffix="COLON" }]</label>
                <textarea cols="70" rows="8" name="editval[send_message]" class="areabox js-oxValidate js-oxValidate_notEmpty">[{if $editval->send_message}][{$editval->send_message}][{else}][{ oxmultilang ident="SHOP_SUGGEST_MESSAGE" args=$oxcmp_shop->oxshops__oxname->value}] [{/if}]</textarea>
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li class="verify">
                <label class="req">[{ oxmultilang ident="VERIFICATION_CODE" suffix="COLON" }]</label>
                [{assign var="oCaptcha" value=$oView->getCaptcha() }]
                [{if $oCaptcha->isImageVisible()}]
                    <img src="[{$oCaptcha->getImageUrl()}]" alt="">
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
    </div>
</form>