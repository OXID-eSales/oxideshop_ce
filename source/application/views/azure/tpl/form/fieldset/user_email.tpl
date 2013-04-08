[{oxscript include="js/widgets/oxenterpassword.js" priority=10 }]
[{oxscript add="$('.oxValidate_enterPass').oxEnterPassword();"}]
<li [{if $aErrors.oxuser__oxusername}]class="oxInValid"[{/if}]>
    <label class="req">[{ oxmultilang ident="EMAIL_ADDRESS" suffix="COLON" }]</label>
    <input class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_email oxValidate_enterPass oxValidate_enterPassTarget[oxValidate_pwd] textbox" type="text" name="invadr[oxuser__oxusername]" value="[{if isset( $invadr.oxuser__oxusername ) }][{ $invadr.oxuser__oxusername }][{else }][{ $oxcmp_user->oxuser__oxusername->value }][{/if }]" size="37">
    <p class="oxValidateError">
        <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
        <span class="js-oxError_email">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOVALIDEMAIL" }]</span>
        [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxusername}]
    </p>
</li>
<li class="oxValidate_pwd[{if $aErrors.oxuser__oxpassword}] oxInValid[{/if}]" [{if !$aErrors.oxuser__oxpassword}]style="display:none;"[{/if}]>
    <label class="req">[{oxmultilang ident="PASSWORD" suffix="COLON" }]</label>
    <input class="js-oxValidate js-oxValidate_notEmpty textbox" type="password" size="37" name="user_password">
    <p class="oxValidateError">
        <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
        <span class="js-oxError_length">[{ oxmultilang ident="ERROR_MESSAGE_PASSWORD_TOO_SHORT" }]</span>
        [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxpassword}]
    </p>
</li>