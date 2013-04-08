    <li [{if $aErrors.oxuser__oxusername}]class="oxInValid"[{/if}]>
        [{block name="user_account_username"}]
        <label class="req">[{ oxmultilang ident="EMAIL_ADDRESS" suffix="COLON" }]</label>
        <input id="userLoginName" class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_email" type="text" name="lgn_usr" value="[{ $oView->getActiveUsername()}]" size="37" >
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            <span class="js-oxError_email">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOVALIDEMAIL" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxusername}]
        </p>
        [{/block}]
    </li>
    <li [{if $aErrors.oxuser__oxpassword}]class="oxInValid"[{/if}]>
        [{block name="user_account_password"}]
        <label class="req">[{ oxmultilang ident="PASSWORD" suffix="COLON" }]</label>
        <input type="hidden" id="passwordLength" value="[{$oViewConf->getPasswordLength()}]">
        <input id="userPassword" class="textbox js-oxValidate js-oxValidate_notEmpty js-oxValidate_length js-oxValidate_match" type="password" name="lgn_pwd" value="[{$lgn_pwd}]" size="37">
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            <span class="js-oxError_length">[{ oxmultilang ident="ERROR_MESSAGE_PASSWORD_TOO_SHORT" }]</span>
            <span class="js-oxError_match">[{ oxmultilang ident="ERROR_MESSAGE_USER_PWDDONTMATCH" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxpassword}]
        </p>
        [{/block}]
    </li>
    <li [{if $aErrors.oxuser__oxpassword}]class="oxInValid"[{/if}]>
        [{block name="user_account_confirmpwd"}]
        <label class="req">[{ oxmultilang ident="CONFIRM_PASSWORD" suffix="COLON" }]</label>
        <input id="userPasswordConfirm" class="textbox js-oxValidate js-oxValidate_notEmpty js-oxValidate_length js-oxValidate_match" type="password" name="lgn_pwd2" value="[{$lgn_pwd2}]" size="37">
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            <span class="js-oxError_length">[{ oxmultilang ident="ERROR_MESSAGE_PASSWORD_TOO_SHORT" }]</span>
            <span class="js-oxError_match">[{ oxmultilang ident="ERROR_MESSAGE_USER_PWDDONTMATCH" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxpassword}]
        </p>
        [{/block}]
    </li>
    <li>
        [{block name="user_account_newsletter"}]
        <label for="newsSubscribed">[{ oxmultilang ident="NEWSLETTER_SUBSCRIPTION" }]</label>
        <input type="hidden" name="blnewssubscribed" value="0">
        <input id="newsSubscribed" type="checkbox" class="checkbox"  name="blnewssubscribed" value="1" [{if $oView->isNewsSubscribed() }]checked[{/if}]>
        <span class="inputNote">[{ oxmultilang ident="NEWSLETTER_SUBSCRIPTION" }]</span>
        [{/block}]
    </li>