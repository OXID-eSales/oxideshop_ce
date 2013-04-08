    <li [{if $aErrors.oxuser__oxusername}]class="oxInValid"[{/if}]>
        [{block name="user_noaccount_email"}]
        <label class="req">[{ oxmultilang ident="EMAIL_ADDRESS" suffix="COLON" }]</label>
        <input id="userLoginName" class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_email" type="text" name="lgn_usr" value="[{ $oView->getActiveUsername() }]" size="37" >
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            <span class="js-oxError_email">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOVALIDEMAIL" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxusername}]
        </p>
        [{/block}]
    </li>
     <li>
        [{block name="user_noaccount_newsletter"}]
        <label for="newsSubscribed">[{ oxmultilang ident="NEWSLETTER_SUBSCRIPTION" }]</label>
        <input type="hidden" name="blnewssubscribed" value="0">
        <input id="newsSubscribed" type="checkbox" class="checkbox"  name="blnewssubscribed" value="1" [{if $oView->isNewsSubscribed() }]checked[{/if}]>
        <span class="inputNote">[{ oxmultilang ident="NEWSLETTER_SUBSCRIPTION" }]</span>
        [{/block}]
    </li>