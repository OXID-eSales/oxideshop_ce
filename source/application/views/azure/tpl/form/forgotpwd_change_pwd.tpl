[{oxscript include="js/widgets/oxinputvalidator.js" priority=10 }]
[{oxscript add="$('form.js-oxValidate').oxInputValidator();"}]
 <form class="js-oxValidate" action="[{ $oViewConf->getSelfActionLink() }]" name="forgotpwd" method="post">
        [{assign var="aErrors" value=$oView->getFieldValidationErrors()}]
          [{ $oViewConf->getHiddenSid() }]
          [{ $oViewConf->getNavFormParams() }]
          <input type="hidden" name="fnc" value="updatePassword">
          <input type="hidden" name="uid" value="[{ $oView->getUpdateId() }]">
          <input type="hidden" name="cl" value="forgotpwd">
          <input type="hidden" id="passwordLength" value="[{$oViewConf->getPasswordLength()}]">
      <ul class="form clear">
            <li [{if $aErrors.oxuser__oxpassword}]class="oxInValid"[{/if}]>
                <label>[{ oxmultilang ident="NEW_PASSWORD" suffix="COLON" }]</label>
                <input type="password" name="password_new" class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_length js-oxValidate_match textbox">
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                    <span class="js-oxError_length">[{ oxmultilang ident="ERROR_MESSAGE_PASSWORD_TOO_SHORT" }]</span>
                    <span class="js-oxError_match">[{ oxmultilang ident="ERROR_MESSAGE_USER_PWDDONTMATCH" }]</span>
                    [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxpassword}]
                    </p>
            </li>
            <li [{if $aErrors.oxuser__oxpassword}]class="oxInValid"[{/if}]>
                <label>[{ oxmultilang ident="CONFIRM_PASSWORD" suffix="COLON" }]</label>
                <input type="password" name="password_new_confirm" class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_length js-oxValidate_match textbox">
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                    <span class="js-oxError_length">[{ oxmultilang ident="ERROR_MESSAGE_PASSWORD_TOO_SHORT" }]</span>
                    <span class="js-oxError_match">[{ oxmultilang ident="ERROR_MESSAGE_USER_PWDDONTMATCH" }]</span>
                    [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxpassword}]
                </p>
            </li>
            <li class="formSubmit">
                <button class="submitButton" type="submit" name="save" value="[{ oxmultilang ident="CHANGE_PASSWORD" }]">[{ oxmultilang ident="CHANGE_PASSWORD" }]</button>
            </li>
      </ul>
    </form>