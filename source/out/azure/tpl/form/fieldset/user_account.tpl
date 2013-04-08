    <li [{if $aErrors.oxuser__oxusername}]class="oxInValid"[{/if}]>
        [{block name="user_account_username"}]
        <label class="req">[{ oxmultilang ident="FORM_FIELDSET_USER_ACCOUNT_EMAIL" }]</label>
        <input id="userLoginName" class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_email" type="text" name="lgn_usr" value="[{ $oView->getActiveUsername()}]" size="37" >
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
            <span class="js-oxError_email">[{ oxmultilang ident="EXCEPTION_INPUT_NOVALIDEMAIL" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxusername}]
        </p>
        [{/block}]
    </li>
    <li [{if $aErrors.oxuser__oxpassword}]class="oxInValid"[{/if}]>
        [{block name="user_account_password"}]
        <label class="req">[{ oxmultilang ident="FORM_FIELDSET_USER_ACCOUNT_PWD" }]</label>
        <input type="hidden" id="passwordLength" value="[{$oViewConf->getPasswordLength()}]">
        <input id="userPassword" class="textbox js-oxValidate js-oxValidate_notEmpty js-oxValidate_length js-oxValidate_match" type="password" name="lgn_pwd" value="[{$lgn_pwd}]" size="37">
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
            <span class="js-oxError_length">[{ oxmultilang ident="EXCEPTION_INPUT_PASSTOOSHORT" }]</span>
            <span class="js-oxError_match">[{ oxmultilang ident="EXCEPTION_USER_PWDDONTMATCH" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxpassword}]
        </p>
        [{/block}]
    </li>
    <li [{if $aErrors.oxuser__oxpassword}]class="oxInValid"[{/if}]>
        [{block name="user_account_confirmpwd"}]
        <label class="req">[{ oxmultilang ident="FORM_FIELDSET_USER_ACCOUNT_CONFIRMPWD" }]</label>
        <input id="userPasswordConfirm" class="textbox js-oxValidate js-oxValidate_notEmpty js-oxValidate_length js-oxValidate_match" type="password" name="lgn_pwd2" value="[{$lgn_pwd2}]" size="37">
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
            <span class="js-oxError_length">[{ oxmultilang ident="EXCEPTION_INPUT_PASSTOOSHORT" }]</span>
            <span class="js-oxError_match">[{ oxmultilang ident="EXCEPTION_USER_PWDDONTMATCH" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxpassword}]
        </p>
        [{/block}]
    </li>
    <li>
        [{block name="user_account_newsletter"}]
        <label>[{ oxmultilang ident="FORM_FIELDSET_USER_ACCOUNT_NEWSLETTER" }]</label>
        <input type="hidden" name="blnewssubscribed" value="0">
        <input type="checkbox" class="checkbox"  name="blnewssubscribed" value="1" [{if $oView->isNewsSubscribed() }]checked[{/if}]>
        <span class="inputNote">[{ oxmultilang ident="FORM_FIELDSET_USER_ACCOUNT_NEWSLETTER_MESSAGE" }]</span>
        [{/block}]
    </li>