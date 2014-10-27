[{assign var="aRegParams" value=$oView->getRegParams()}]
[{oxscript include="js/widgets/oxinputvalidator.js" priority=10 }]
[{oxscript add="$('form.js-oxValidate').oxInputValidator();"}]
<form class="js-oxValidate" action="[{ $oViewConf->getSslSelfLink() }]" method="post">
    <div>
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="fnc" value="send">
        <input type="hidden" name="cl" value="newsletter">
        <input type="hidden" name="editval[oxuser__oxcountryid]" value="[{$oView->getHomeCountryId()}]">
    </div>
    <ul class="form">
        <li>
            <label>[{ oxmultilang ident="TITLE" }]</label>
            [{ include file="form/fieldset/salutation.tpl" name="editval[oxuser__oxsal]" value=$aRegParams.oxuser__oxsal }]
        </li>
        <li>
            <label>[{ oxmultilang ident="FIRST_NAME" suffix="COLON" }]</label>
            <input id="newsletterFname" type="text" name="editval[oxuser__oxfname]" size=40 maxlength=40 value="[{if $aRegParams.oxuser__oxfname}][{$aRegParams.oxuser__oxfname}][{/if}]">
        </li>
        <li>
            <label>[{ oxmultilang ident="LAST_NAME" }]</label>
            <input id="newsletterLname" type="text" name="editval[oxuser__oxlname]" size=40 maxlength=40 value="[{if $aRegParams.oxuser__oxlname}][{$aRegParams.oxuser__oxlname}][{/if}]">
        </li>
        <li [{if $aErrors}]class="oxInValid"[{/if}]>
            <label class="req">[{ oxmultilang ident="EMAIL" }]</label>
            <input id="newsletterUserName" type="text" class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_email" name="editval[oxuser__oxusername]" size=40 maxlength=40 value="[{if $aRegParams.oxuser__oxusername}][{$aRegParams.oxuser__oxusername}][{/if}]">
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                <span class="js-oxError_email">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOVALIDEMAIL" }]</span>
            </p>
        </li>
        <li class="checkFields">
            <input id="newsletterSubscribeOn" class="radiobox" type="radio" name="subscribeStatus" value="1" checked><label for="newsletterSubscribeOn">[{ oxmultilang ident="SUBSCRIBE" }]</label>
            <input id="newsletterSubscribeOff" class="radiobox" type="radio" name="subscribeStatus" value="0"><label for="newsletterSubscribeOff">[{ oxmultilang ident="UNSUBSCRIBE" }]</label>
        </li>
        <li class="formNote">[{ oxmultilang ident="COMPLETE_MARKED_FIELDS" }]</li>
        <li class="formSubmit">
            <button id="newsLetterSubmit" class="submitButton largeButton" type="submit">[{ oxmultilang ident="SUBMIT" }]</button>
        </li>
    </ul>
</form>