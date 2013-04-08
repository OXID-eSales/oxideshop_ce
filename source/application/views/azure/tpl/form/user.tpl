[{oxscript include="js/widgets/oxinputvalidator.js" priority=10 }]
[{oxscript add="$('form.js-oxValidate').oxInputValidator();"}]
<form class="js-oxValidate" action="[{ $oViewConf->getSelfActionLink() }]" name="order" method="post">
[{assign var="aErrors" value=$oView->getFieldValidationErrors()}]
<div class="addressCollumns clear">
    <div class="collumn">
        <div>
            [{ $oViewConf->getHiddenSid() }]
            [{ $oViewConf->getNavFormParams() }]
            <input type="hidden" name="fnc" value="changeuser_testvalues">
            <input type="hidden" name="cl" value="account_user">
            <input type="hidden" name="CustomError" value='user'>
            <input type="hidden" name="blshowshipaddress" value="1">
        </div>
        <h3 class="blockHead">
            [{ oxmultilang ident="BILLING_ADDRESS" }]
            <button id="userChangeAddress" class="submitButton largeButton" [{if !empty($aErrors)}]style="display: none;"[{/if}] name="changeBillAddress" type="submit">[{ oxmultilang ident="CHANGE" }]</button>
        </h3>
        <ul class="form clear" [{if empty($aErrors)}]style="display: none;"[{/if}] id="addressForm">
            [{ include file="form/fieldset/user_email.tpl" }]
            [{ include file="form/fieldset/user_billing.tpl" noFormSubmit=true }]
        </ul>
        <ul class="form" id="addressText">
            <li>
                [{ include file="widget/address/billing_address.tpl"}]
            </li>
        </ul>
        [{oxscript add="$('#userChangeAddress').click( function() { $('#addressForm').show();$('#addressText').hide();return false;});"}]
    </div>
    <div class="collumn">
        <h3 id="addShippingAddress" class="blockHead">
            [{ oxmultilang ident="SHIPPING_ADDRESSES" }]
            <button id="userChangeShippingAddress" class="submitButton largeButton" name="changeShippingAddress" type="submit" [{if !$oView->showShipAddress() or !$oxcmp_user->getSelectedAddress()}] style="display: none;" [{/if}]>[{ oxmultilang ident="CHANGE" }]</button>
        </h3>
        <p><input type="checkbox" name="blshowshipaddress" id="showShipAddress" [{if !$oView->showShipAddress()}]checked[{/if}] value="0"><label for="showShipAddress">[{ oxmultilang ident="USE_BILLINGADDRESS_FOR_SHIPPINGADDRESS" }]</label></p>
        <ul id="shippingAddress" class="form clear" [{if !$oView->showShipAddress()}] style="display: none;" [{/if}]>
        [{ include file="form/fieldset/user_shipping.tpl" noFormSubmit=true}]
        </ul>
        [{oxscript add="$('#showShipAddress').change( function() { $('#userChangeShippingAddress').toggle($(this).is(':not(:checked)') &&  $('#addressId').val() != -1 ); $('#shippingAddress').toggle($(this).is(':not(:checked)')); });"}]
        [{oxscript add="$('#addressId').change(function() { $('#userChangeShippingAddress').toggle($('#addressId').val() != -1 ); }); "}]
    </div>
</div>
<div class="lineBox clear">
    <button id="accUserSaveTop" class="submitButton largeButton nextStep" name="userform" type="submit">[{ oxmultilang ident="SAVE" }]</button>
</div>
</form>

