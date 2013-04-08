[{if $oxcmp_user}]
    [{assign var="delivadr" value=$oxcmp_user->getSelectedAddress()}]
[{/if}]
<li>
    <label>[{ oxmultilang ident="ADDRESSES" }]</label>
    <input type="hidden" name="changeClass" value="[{$onChangeClass|default:'account_user'}]">
    [{oxscript include="js/widgets/oxusershipingaddressselect.js" priority=10 }]
    [{oxscript add="$( '#addressId' ).oxUserShipingAddressSelect();"}]
    <select id="addressId" name="oxaddressid">
        <option value="-1">[{ oxmultilang ident="NEW_ADDRESS" }]</option>
        [{if $oxcmp_user }]
            [{foreach from=$oxcmp_user->getUserAddresses() item=address }]
                <option value="[{$address->oxaddress__oxid->value}]" [{if $address->isSelected()}]SELECTED[{/if}]>[{$address}]</option>
            [{/foreach }]
        [{/if}]
    </select>
</li>
[{if $delivadr }]
    <li class="form" id="shippingAddressText">
        [{ include file="widget/address/shipping_address.tpl" delivadr=$delivadr}]
        [{oxscript add="$('#userChangeShippingAddress').click( function() { $('#shippingAddressForm').show();$('#shippingAddressText').hide(); $('#userChangeShippingAddress').hide();return false;});"}]
    </li>
[{/if}]
<li>
    <ul id="shippingAddressForm" [{if $delivadr }]style="display: none;"[{/if}]>
        <li>
            <label [{if $oView->isFieldRequired(oxaddress__oxsal) }]class="req"[{/if }]>[{ oxmultilang ident="TITLE" suffix="COLON" }]</label>
              [{include file="form/fieldset/salutation.tpl" name="deladr[oxaddress__oxsal]" value=$delivadr->oxaddress__oxsal->value value2=$deladr.oxaddress__oxsal }]
        </li>
        <li [{if $aErrors.oxaddress__oxfname}]class="oxInValid"[{/if}]>
            <label [{if $oView->isFieldRequired(oxaddress__oxfname)}]class="req"[{/if}]>[{ oxmultilang ident="FIRST_NAME" suffix="COLON" }]</label>
              <input [{if $oView->isFieldRequired(oxaddress__oxfname) }]class="js-oxValidate js-oxValidate_notEmpty"[{/if }] type="text" maxlength="255" name="deladr[oxaddress__oxfname]" value="[{if isset( $deladr.oxaddress__oxfname ) }][{ $deladr.oxaddress__oxfname }][{else}][{ $delivadr->oxaddress__oxfname->value }][{/if }]">
              [{if $oView->isFieldRequired(oxaddress__oxfname)}]
              <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxaddress__oxfname}]
            </p>
              [{/if }]
        </li>
        <li [{if $aErrors.oxaddress__oxlname}]class="oxInValid"[{/if}]>
            <label [{if $oView->isFieldRequired(oxaddress__oxlname)}]class="req"[{/if}]>[{ oxmultilang ident="LAST_NAME" suffix="COLON" }]</label>
            <input [{if $oView->isFieldRequired(oxaddress__oxlname)}]class="js-oxValidate js-oxValidate_notEmpty"[{/if }] type="text" maxlength="255" name="deladr[oxaddress__oxlname]" value="[{if isset( $deladr.oxaddress__oxlname ) }][{ $deladr.oxaddress__oxlname }][{else}][{ $delivadr->oxaddress__oxlname->value }][{/if }]">
            [{if $oView->isFieldRequired(oxaddress__oxlname)}]
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxaddress__oxlname}]
            </p>
            [{/if }]
        </li>
        <li [{if $aErrors.oxaddress__oxcompany}]class="oxInValid"[{/if}]>
            <label [{if $oView->isFieldRequired(oxaddress__oxcompany) }]class="req"[{/if}]>[{ oxmultilang ident="COMPANY" suffix="COLON" }]</label>
              <input [{if $oView->isFieldRequired(oxaddress__oxcompany) }] class="js-oxValidate js-oxValidate_notEmpty" [{/if }] type="text" size="37" maxlength="255" name="deladr[oxaddress__oxcompany]" value="[{if isset( $deladr.oxaddress__oxcompany ) }][{ $deladr.oxaddress__oxcompany }][{else}][{ $delivadr->oxaddress__oxcompany->value }][{/if }]">
             [{if $oView->isFieldRequired(oxaddress__oxcompany) }]
             <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxaddress__oxcompany}]
            </p>
             [{/if }]
        </li>
        <li [{if $aErrors.oxaddress__oxaddinfo}]class="oxInValid"[{/if}]>
            [{assign var="_address_addinfo_tooltip" value="FORM_FIELDSET_USER_SHIPPING_ADDITIONALINFO2_TOOLTIP"|oxmultilangassign }]
            <label [{if $_address_addinfo_tooltip}]title="[{$_address_addinfo_tooltip}]" class="tooltip"[{/if}] [{if $oView->isFieldRequired(oxaddress__oxaddinfo) }]class="req"[{/if}]>[{ oxmultilang ident="ADDITIONAL_INFO" suffix='COLON'}]</label>
              <input [{if $oView->isFieldRequired(oxaddress__oxaddinfo) }] class="js-oxValidate js-oxValidate_notEmpty" [{/if }] type="text" size="37" maxlength="255" name="deladr[oxaddress__oxaddinfo]" value="[{if isset( $deladr.oxaddress__oxaddinfo ) }][{ $deladr.oxaddress__oxaddinfo }][{else}][{ $delivadr->oxaddress__oxaddinfo->value }][{/if }]">
              [{if $oView->isFieldRequired(oxaddress__oxaddinfo) }]
              <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxaddress__oxaddinfo}]
            </p>
              [{/if }]
        </li>
        <li [{if $aErrors.oxaddress__oxstreet}]class="oxInValid"[{/if}]>
            <label [{if $oView->isFieldRequired(oxaddress__oxstreet) || $oView->isFieldRequired(oxaddress__oxstreetnr) }]class="req"[{/if}]>[{ oxmultilang ident="STREET_AND_STREETNO" suffix="COLON" }]</label>
              <input [{if $oView->isFieldRequired(oxaddress__oxstreet) }] class="js-oxValidate js-oxValidate_notEmpty" [{/if }] type="text" data-fieldsize="pair-xsmall" maxlength="255" name="deladr[oxaddress__oxstreet]" value="[{if isset( $deladr.oxaddress__oxstreet ) }][{ $deladr.oxaddress__oxstreet }][{else}][{ $delivadr->oxaddress__oxstreet->value }][{/if }]">
              <input [{if $oView->isFieldRequired(oxaddress__oxstreetnr) }] class="js-oxValidate js-oxValidate_notEmpty" [{/if }] type="text" data-fieldsize="xsmall" maxlength="16" name="deladr[oxaddress__oxstreetnr]" value="[{if isset( $deladr.oxaddress__oxstreetnr ) }][{ $deladr.oxaddress__oxstreetnr }][{else}][{ $delivadr->oxaddress__oxstreetnr->value }][{/if }]">
              [{if $oView->isFieldRequired(oxaddress__oxstreet) || $oView->isFieldRequired(oxaddress__oxstreetnr) }]
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxaddress__oxstreet}]
            </p>
              [{/if }]
        </li>
        <li [{if $aErrors.oxaddress__oxzip || $aErrors.oxaddress__oxcity}]class="oxInValid"[{/if}]>
            <label [{if $oView->isFieldRequired(oxaddress__oxzip) || $oView->isFieldRequired(oxaddress__oxcity) }]class="req"[{/if}]>[{ oxmultilang ident="POSTAL_CODE_AND_CITY" suffix="COLON" }]</label>
             <input [{if $oView->isFieldRequired(oxaddress__oxzip) }] class="js-oxValidate js-oxValidate_notEmpty" [{/if }] type="text" data-fieldsize="small" maxlength="50" name="deladr[oxaddress__oxzip]" value="[{if isset( $deladr.oxaddress__oxzip ) }][{ $deladr.oxaddress__oxzip }][{else}][{ $delivadr->oxaddress__oxzip->value }][{/if }]">
              <input [{if $oView->isFieldRequired(oxaddress__oxcity) }] class="js-oxValidate js-oxValidate_notEmpty" [{/if }] type="text" data-fieldsize="pair-small" maxlength="255" name="deladr[oxaddress__oxcity]" value="[{if isset( $deladr.oxaddress__oxcity ) }][{ $deladr.oxaddress__oxcity }][{else}][{ $delivadr->oxaddress__oxcity->value }][{/if }]">
              [{if $oView->isFieldRequired(oxaddress__oxzip) || $oView->isFieldRequired(oxaddress__oxcity) }]
              <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxaddress__oxzip}]
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxaddress__oxcity}]
            </p>
          [{/if }]
        </li>
        [{block name="form_user_shipping_country"}]
        <li [{if $aErrors.oxaddress__oxcountryid}]class="oxInValid"[{/if}]>
            <label [{if $oView->isFieldRequired(oxaddress__oxcountryid) }]class="req"[{/if}]>[{ oxmultilang ident="COUNTRY" suffix="COLON" }]</label>
              <select [{if $oView->isFieldRequired(oxaddress__oxcountryid) }] class="js-oxValidate js-oxValidate_notEmpty" [{/if }] id="delCountrySelect" name="deladr[oxaddress__oxcountryid]">
                  <option value="">-</option>
                  [{assign var="blCountrySelected" value=false}]
                  [{foreach from=$oViewConf->getCountryList() item=country key=country_id }]
                      [{assign var="sCountrySelect" value=""}]
                      [{if !$blCountrySelected}]
                          [{if (isset($deladr.oxaddress__oxcountryid) && $deladr.oxaddress__oxcountryid == $country->oxcountry__oxid->value) ||
                               (!isset($deladr.oxaddress__oxcountryid) && ($delivadr->oxaddress__oxcountry->value == $country->oxcountry__oxtitle->value or
                                $delivadr->oxaddress__oxcountry->value == $country->oxcountry__oxid->value or
                                $delivadr->oxaddress__oxcountryid->value == $country->oxcountry__oxid->value)) }]
                              [{assign var="blCountrySelected" value=true}]
                              [{assign var="sCountrySelect" value="selected"}]
                          [{/if}]
                      [{/if}]
                      <option value="[{ $country->oxcountry__oxid->value }]" [{$sCountrySelect}]>[{ $country->oxcountry__oxtitle->value }]</option>
                  [{/foreach }]
              </select>
              [{if $oView->isFieldRequired(oxaddress__oxcountryid) }]
              <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxaddress__oxcountryid}]
            </p>
          [{/if }]
        </li>
        <li class="stateBox">
              [{include file="form/fieldset/state.tpl"
                    countrySelectId="delCountrySelect"
                    stateSelectName="deladr[oxaddress__oxstateid]"
                    selectedStateIdPrim=$deladr.oxaddress__oxstateid
                    selectedStateId=$delivadr->oxaddress__oxstateid->value
            }]
        </li>
        [{/block}]
        <li [{if $aErrors.oxaddress__oxfon}]class="oxInValid"[{/if}]>
            <label [{if $oView->isFieldRequired(oxaddress__oxfon) }]class="req"[{/if}]>[{ oxmultilang ident="PHONE" suffix="COLON" }]</label>
              <input [{if $oView->isFieldRequired(oxaddress__oxfon) }] class="js-oxValidate js-oxValidate_notEmpty" [{/if }] type="text" size="37" maxlength="128" name="deladr[oxaddress__oxfon]" value="[{if isset( $deladr.oxaddress__oxfon ) }][{ $deladr.oxaddress__oxfon }][{else}][{ $delivadr->oxaddress__oxfon->value }][{/if }]">
              [{if $oView->isFieldRequired(oxaddress__oxfon) }]
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxaddress__oxfon}]
            </p>
              [{/if }]
        </li>
        <li [{if $aErrors.oxaddress__oxfax}]class="oxInValid"[{/if}]>
            <label [{if $oView->isFieldRequired(oxaddress__oxfax) }]class="req"[{/if}]>[{ oxmultilang ident="FAX" suffix="COLON"}]</label>
              <input [{if $oView->isFieldRequired(oxaddress__oxfax) }] class="js-oxValidate js-oxValidate_notEmpty" [{/if }] type="text" size="37" maxlength="128" name="deladr[oxaddress__oxfax]" value="[{if isset( $deladr.oxaddress__oxfax ) }][{ $deladr.oxaddress__oxfax }][{else}][{ $delivadr->oxaddress__oxfax->value }][{/if }]">
             [{if $oView->isFieldRequired(oxaddress__oxfax) }]
            <p class="oxValidateError">
                <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxaddress__oxfax}]
            </p>
             [{/if }]
        </li>
    </ul>
</li>
[{if !$noFormSubmit}]
    <li class="formNote">[{ oxmultilang ident="COMPLETE_MARKED_FIELDS" }]</li>
    <li class="formSubmit">
        <button id="accUserSaveBottom" type="submit" class="submitButton" name="save">[{ oxmultilang ident="SAVE" }]</button>
    </li>
[{/if}]