[{ oxmultilang ident="PAGE_ACCOUNT_FORGOTPWD_FORGOTPWD" }]<br>
[{ oxmultilang ident="PAGE_ACCOUNT_FORGOTPWD_WEWILLSENDITTOYOU" }]<br><br>
[{oxscript include="js/widgets/oxinputvalidator.js" priority=10 }]
[{oxscript add="$('form.js-oxValidate').oxInputValidator();"}]
<form class="js-oxValidate" action="[{ $oViewConf->getSelfActionLink() }]" name="forgotpwd" method="post">
  [{ $oViewConf->getHiddenSid() }]
  [{ $oViewConf->getNavFormParams() }]
  <input type="hidden" name="fnc" value="forgotpassword">
  <input type="hidden" name="cl" value="forgotpwd">
  <input type="hidden" name="actcontrol" value="forgotpwd">
  <ul class="form clear">
    <li>
        <label>[{ oxmultilang ident="PAGE_ACCOUNT_FORGOTPWD_YOUREMAIL" }]</label>
        <input id="forgotPasswordUserLoginName[{$idPrefix}]" type="text" name="lgn_usr" value="[{$oView->getActiveUsername()}]" class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_email">
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
            <span class="js-oxError_email">[{ oxmultilang ident="EXCEPTION_INPUT_NOVALIDEMAIL" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxusername}]
        </p>
    </li>
    <li class="formSubmit">
        <button class="submitButton" type="submit">[{ oxmultilang ident="PAGE_ACCOUNT_FORGOTPWD_REQUESTPWD"}]</button>
    </li>
  </ul>
</form>
[{ oxmultilang ident="PAGE_ACCOUNT_FORGOTPWD_AFTERCLICK" }]<br><br>
[{oxifcontent ident="oxforgotpwd" object="oCont"}]
    [{ $oCont->oxcontents__oxcontent->value }]
[{/oxifcontent}]