[{assign var="invadr" value=$oView->getInvoiceAddress()}]
[{assign var="blBirthdayRequired" value=$oView->isFieldRequired(oxuser__oxbirthdate)}]
[{if isset( $invadr.oxuser__oxbirthdate.month ) }]
    [{assign var="iBirthdayMonth" value=$invadr.oxuser__oxbirthdate.month }]
[{elseif $oxcmp_user->oxuser__oxbirthdate->value && $oxcmp_user->oxuser__oxbirthdate->value != "0000-00-00" }]
    [{assign var="iBirthdayMonth" value=$oxcmp_user->oxuser__oxbirthdate->value|regex_replace:"/^([0-9]{4})[-]/":""|regex_replace:"/[-]([0-9]{1,2})$/":"" }]
[{else}]
    [{assign var="iBirthdayMonth" value=0}]
[{/if}]
[{if isset( $invadr.oxuser__oxbirthdate.day ) }]
    [{assign var="iBirthdayDay" value=$invadr.oxuser__oxbirthdate.day}]
[{elseif $oxcmp_user->oxuser__oxbirthdate->value && $oxcmp_user->oxuser__oxbirthdate->value != "0000-00-00"}]
    [{assign var="iBirthdayDay" value=$oxcmp_user->oxuser__oxbirthdate->value|regex_replace:"/^([0-9]{4})[-]([0-9]{1,2})[-]/":"" }]
[{else}]
    [{assign var="iBirthdayDay" value=0}]
[{/if}]
[{if isset( $invadr.oxuser__oxbirthdate.year ) }]
    [{assign var="iBirthdayYear" value=$invadr.oxuser__oxbirthdate.year }]
[{elseif $oxcmp_user->oxuser__oxbirthdate->value && $oxcmp_user->oxuser__oxbirthdate->value != "0000-00-00" }]
    [{assign var="iBirthdayYear" value=$oxcmp_user->oxuser__oxbirthdate->value|regex_replace:"/[-]([0-9]{1,2})[-]([0-9]{1,2})$/":"" }]
[{else}]
    [{assign var="iBirthdayYear" value=0}]
[{/if}]
    <li>
        <label [{if $oView->isFieldRequired(oxuser__oxsal)}]class="req"[{/if}]>[{ oxmultilang ident="TITLE" suffix="COLON" }]</label>
        [{include file="form/fieldset/salutation.tpl" name="invadr[oxuser__oxsal]" value=$oxcmp_user->oxuser__oxsal->value }]
    </li>
    <li [{if $aErrors.oxuser__oxfname}]class="oxInValid"[{/if}]>
        <label [{if $oView->isFieldRequired(oxuser__oxfname) }]class="req"[{/if}]>[{ oxmultilang ident="FIRST_NAME" suffix="COLON" }]</label>
          <input [{if $oView->isFieldRequired(oxuser__oxfname) }]class="js-oxValidate js-oxValidate_notEmpty" [{/if}]type="text" size="37" maxlength="255" name="invadr[oxuser__oxfname]" value="[{if isset( $invadr.oxuser__oxfname ) }][{ $invadr.oxuser__oxfname }][{else }][{ $oxcmp_user->oxuser__oxfname->value }][{/if}]">
          [{if $oView->isFieldRequired(oxuser__oxfname)}]
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxfname}]
        </p>
          [{/if}]
    </li>
    <li [{if $aErrors.oxuser__oxlname}]class="oxInValid"[{/if}]>
        <label [{if $oView->isFieldRequired(oxuser__oxlname) }]class="req"[{/if}]>[{ oxmultilang ident="LAST_NAME" suffix="COLON" }]</label>
          <input [{if $oView->isFieldRequired(oxuser__oxlname) }]class="js-oxValidate js-oxValidate_notEmpty" [{/if}]type="text" size="37" maxlength="255" name="invadr[oxuser__oxlname]" value="[{if isset( $invadr.oxuser__oxlname ) }][{ $invadr.oxuser__oxlname }][{else }][{ $oxcmp_user->oxuser__oxlname->value }][{/if}]">
          [{if $oView->isFieldRequired(oxuser__oxlname)}]
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxlname}]
        </p>
          [{/if}]
    </li>
    <li [{if $aErrors.oxuser__oxcompany}]class="oxInValid"[{/if}]>
        <label [{if $oView->isFieldRequired(oxuser__oxcompany) }]class="req"[{/if}]>[{ oxmultilang ident="COMPANY" suffix="COLON"}]</label>
          <input [{if $oView->isFieldRequired(oxuser__oxcompany) }]class="js-oxValidate js-oxValidate_notEmpty" [{/if}]type="text" size="37" maxlength="255" name="invadr[oxuser__oxcompany]" value="[{if isset( $invadr.oxuser__oxcompany ) }][{ $invadr.oxuser__oxcompany }][{else }][{ $oxcmp_user->oxuser__oxcompany->value }][{/if}]">
          [{if $oView->isFieldRequired(oxuser__oxcompany) }]
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxcompany}]
        </p>
          [{/if}]
    </li>
    <li [{if $aErrors.oxuser__oxaddinfo}]class="oxInValid"[{/if}]>
        [{assign var="_address_addinfo_tooltip" value="FORM_FIELDSET_USER_BILLING_ADDITIONALINFO_TOOLTIP"|oxmultilangassign }]
        <label [{if $_address_addinfo_tooltip}]title="[{$_address_addinfo_tooltip}]" class="tooltip"[{/if}] [{if $oView->isFieldRequired(oxuser__oxaddinfo) }]class="req"[{/if}]>[{ oxmultilang ident="ADDITIONAL_INFO" suffix='COLON' }]</label>
          <input [{if $oView->isFieldRequired(oxuser__oxaddinfo) }]class="js-oxValidate js-oxValidate_notEmpty" [{/if}]type="text" size="37" maxlength="255" name="invadr[oxuser__oxaddinfo]" value="[{if isset( $invadr.oxuser__oxaddinfo ) }][{ $invadr.oxuser__oxaddinfo }][{else }][{ $oxcmp_user->oxuser__oxaddinfo->value }][{/if}]">
          [{if $oView->isFieldRequired(oxuser__oxaddinfo) }]
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxaddinfo}]
        </p>
          [{/if}]
    </li>
    <li [{if $aErrors.oxuser__oxstreet}]class="oxInValid"[{/if}]>
        <label [{if $oView->isFieldRequired(oxuser__oxstreet) || $oView->isFieldRequired(oxuser__oxstreetnr) }]class="req"[{/if}]>[{ oxmultilang ident="STREET_AND_STREETNO" suffix="COLON" }]</label>
          <input [{if $oView->isFieldRequired(oxuser__oxstreet) }]class="js-oxValidate js-oxValidate_notEmpty" [{/if}]type="text" data-fieldsize="pair-xsmall" maxlength="255" name="invadr[oxuser__oxstreet]" value="[{if isset( $invadr.oxuser__oxstreet ) }][{ $invadr.oxuser__oxstreet }][{else }][{ $oxcmp_user->oxuser__oxstreet->value }][{/if}]">
          <input [{if $oView->isFieldRequired(oxuser__oxstreetnr) }]class="js-oxValidate js-oxValidate_notEmpty" [{/if}]type="text" data-fieldsize="xsmall" maxlength="16" name="invadr[oxuser__oxstreetnr]" value="[{if isset( $invadr.oxuser__oxstreetnr ) }][{ $invadr.oxuser__oxstreetnr }][{else }][{ $oxcmp_user->oxuser__oxstreetnr->value }][{/if}]">
          [{if $oView->isFieldRequired(oxuser__oxstreet) || $oView->isFieldRequired(oxuser__oxstreetnr) }]
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxstreet}]
        </p>
          [{/if}]
    </li>
    <li [{if $aErrors.oxuser__oxzip}]class="oxInValid"[{/if}]>
        <label [{if $oView->isFieldRequired(oxuser__oxzip) || $oView->isFieldRequired(oxuser__oxcity) }]class="req"[{/if}]>[{ oxmultilang ident="POSTAL_CODE_AND_CITY" suffix="COLON" }]</label>
          <input [{if $oView->isFieldRequired(oxuser__oxzip) }]class="js-oxValidate js-oxValidate_notEmpty" [{/if}]type="text" data-fieldsize="small" maxlength="16" name="invadr[oxuser__oxzip]" value="[{if isset( $invadr.oxuser__oxzip ) }][{ $invadr.oxuser__oxzip }][{else }][{ $oxcmp_user->oxuser__oxzip->value }][{/if}]">
          <input [{if $oView->isFieldRequired(oxuser__oxcity) }]class="js-oxValidate js-oxValidate_notEmpty" [{/if}]type="text" data-fieldsize="pair-small" maxlength="255" name="invadr[oxuser__oxcity]" value="[{if isset( $invadr.oxuser__oxcity ) }][{ $invadr.oxuser__oxcity }][{else }][{ $oxcmp_user->oxuser__oxcity->value }][{/if}]">
          [{if $oView->isFieldRequired(oxuser__oxzip) || $oView->isFieldRequired(oxuser__oxcity) }]
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxzip}]
        </p>
          [{/if}]
    </li>
    <li [{if $aErrors.oxuser__oxustid}]class="oxInValid"[{/if}]>
        <label [{if $oView->isFieldRequired(oxuser__oxustid) }]class="req"[{/if}]>[{ oxmultilang ident="VAT_ID_NUMBER" suffix="COLON" }]</label>
         <input [{if $oView->isFieldRequired(oxuser__oxustid) }]class="js-oxValidate js-oxValidate_notEmpty" [{/if}]type="text" size="37" maxlength="255" name="invadr[oxuser__oxustid]" value="[{if isset( $invadr.oxuser__oxustid ) }][{ $invadr.oxuser__oxustid }][{else}][{ $oxcmp_user->oxuser__oxustid->value }][{/if}]">
          [{if $oView->isFieldRequired(oxuser__oxustid) }]
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxustid}]
        </p>
          [{/if}]
    </li>
    [{block name="form_user_billing_country"}]
    <li [{if $aErrors.oxuser__oxcountryid}]class="oxInValid"[{/if}]>
        <label [{if $oView->isFieldRequired(oxuser__oxcountryid) }]class="req"[{/if}]>[{ oxmultilang ident="COUNTRY" suffix="COLON" }]</label>
          <select [{if $oView->isFieldRequired(oxuser__oxcountryid) }] class="js-oxValidate js-oxValidate_notEmpty" [{/if}] id="invCountrySelect" name="invadr[oxuser__oxcountryid]" data-fieldsize="normal">
              <option value="">-</option>
              [{assign var="blCountrySelected" value=false}]
              [{foreach from=$oViewConf->getCountryList() item=country key=country_id }]
                  [{assign var="sCountrySelect" value=""}]
                  [{if !$blCountrySelected}]
                      [{if (isset($invadr.oxuser__oxcountryid) && $invadr.oxuser__oxcountryid == $country->oxcountry__oxid->value) ||
                           (!isset($invadr.oxuser__oxcountryid) && $oxcmp_user->oxuser__oxcountryid->value == $country->oxcountry__oxid->value) }]
                          [{assign var="blCountrySelected" value=true}]
                          [{assign var="sCountrySelect" value="selected"}]
                      [{/if}]
                  [{/if}]
                <option value="[{ $country->oxcountry__oxid->value }]" [{$sCountrySelect}]>[{ $country->oxcountry__oxtitle->value }]</option>
            [{/foreach }]
          </select>
          [{if $oView->isFieldRequired(oxuser__oxcountryid) }]
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxcountryid}]
        </p>
          [{/if}]
    </li>
    <li class="stateBox">
          [{include file="form/fieldset/state.tpl"
                countrySelectId="invCountrySelect"
                stateSelectName="invadr[oxuser__oxstateid]"
                selectedStateIdPrim=$invadr.oxuser__oxstateid
                selectedStateId=$oxcmp_user->oxuser__oxstateid->value
         }]
    </li>
    [{/block}]


    <li [{if $aErrors.oxuser__oxfon}]class="oxInValid"[{/if}]>
        <label [{if $oView->isFieldRequired(oxuser__oxfon) }]class="req"[{/if}]>[{ oxmultilang ident="PHONE" suffix="COLON" }]</label>
          <input [{if $oView->isFieldRequired(oxuser__oxfon) }]class="js-oxValidate js-oxValidate_notEmpty" [{/if}]type="text" size="37" maxlength="128" name="invadr[oxuser__oxfon]" value="[{if isset( $invadr.oxuser__oxfon ) }][{ $invadr.oxuser__oxfon }][{else }][{ $oxcmp_user->oxuser__oxfon->value }][{/if}]">
          [{if $oView->isFieldRequired(oxuser__oxfon) }]
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxfon}]
        </p>
          [{/if}]
    </li>
    <li [{if $aErrors.oxuser__oxfax}]class="oxInValid"[{/if}]>
        <label [{if $oView->isFieldRequired(oxuser__oxfax) }]class="req"[{/if}]>[{ oxmultilang ident="FAX" suffix="COLON" }]</label>
          <input [{if $oView->isFieldRequired(oxuser__oxfax) }] class="js-oxValidate js-oxValidate_notEmpty" [{/if}]type="text" size="37" maxlength="128" name="invadr[oxuser__oxfax]" value="[{if isset( $invadr.oxuser__oxfax ) }][{ $invadr.oxuser__oxfax }][{else }][{ $oxcmp_user->oxuser__oxfax->value }][{/if}]">
          [{if $oView->isFieldRequired(oxuser__oxfax) }]
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxfax}]
        </p>
          [{/if}]
    </li>
    <li [{if $aErrors.oxuser__oxmobfon}]class="oxInValid"[{/if}]>
        <label [{if $oView->isFieldRequired(oxuser__oxmobfon) }]class="req"[{/if}]>[{ oxmultilang ident="CELLUAR_PHONE" suffix="COLON"}]</label>
         <input [{if $oView->isFieldRequired(oxuser__oxmobfon) }] class="js-oxValidate js-oxValidate_notEmpty"[{/if}]type="text" size="37" maxlength="64" name="invadr[oxuser__oxmobfon]" value="[{if isset( $invadr.oxuser__oxmobfon ) }][{$invadr.oxuser__oxmobfon }][{else}][{$oxcmp_user->oxuser__oxmobfon->value }][{/if}]">
          [{if $oView->isFieldRequired(oxuser__oxmobfon) }]
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxmobfon}]
        </p>
          [{/if}]
    </li>
    <li [{if $aErrors.oxuser__oxprivfon}]class="oxInValid"[{/if}]>
        <label [{if $oView->isFieldRequired(oxuser__oxprivfon) }]class="req"[{/if}]>[{ oxmultilang ident="PERSONAL_PHONE" suffix="COLON" }]</label>
        <input [{if $oView->isFieldRequired(oxuser__oxprivfon) }] class="js-oxValidate js-oxValidate_notEmpty" [{/if}] type="text" size="37" maxlength="64" name="invadr[oxuser__oxprivfon]" value="[{if isset( $invadr.oxuser__oxprivfon ) }][{$invadr.oxuser__oxprivfon }][{else}][{$oxcmp_user->oxuser__oxprivfon->value }][{/if}]">
        [{if $oView->isFieldRequired(oxuser__oxprivfon) }]
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxprivfon}]
        </p>
        [{/if}]
    </li>
    [{if $oViewConf->showBirthdayFields() }]
    <li class="oxDate[{if $aErrors.oxuser__oxbirthdate}] oxInValid[{/if}]">
        <label [{if $oView->isFieldRequired(oxuser__oxbirthdate) }]class="req"[{/if}]>[{ oxmultilang ident="BIRTHDATE" suffix="COLON" }]</label>
        <select class='oxMonth js-oxValidate js-oxValidate_date [{if $oView->isFieldRequired(oxuser__oxbirthdate) }] js-oxValidate_notEmpty [{/if}]' name='invadr[oxuser__oxbirthdate][month]'>
            <option value="" >-</option>
            [{section name="month" start=1 loop=13 }]
            <option value="[{$smarty.section.month.index}]" [{if $iBirthdayMonth == $smarty.section.month.index}] selected="selected" [{/if}]>
                [{oxmultilang ident="MONTH_NAME_"|cat:$smarty.section.month.index}]
            </option>
            [{/section}]
        </select>
        <label class="innerLabel" for="oxDay">[{ oxmultilang ident="DAY" }]</label>
        <input id="oxDay" class='oxDay js-oxValidate' name='invadr[oxuser__oxbirthdate][day]' type="text" data-fieldsize="xsmall" maxlength="2" value="[{if $iBirthdayDay > 0 }][{$iBirthdayDay }][{/if}]" />
        [{oxscript include="js/widgets/oxinnerlabel.js" priority=10 }]
        [{oxscript add="$( '#oxDay' ).oxInnerLabel({sReloadElement:'#userChangeAddress'});"}]
        <label class="innerLabel" for="oxYear">[{ oxmultilang ident="YEAR" }]</label>
        <input id="oxYear" class='oxYear js-oxValidate' name='invadr[oxuser__oxbirthdate][year]' type="text" data-fieldsize="small" maxlength="4" value="[{if $iBirthdayYear }][{$iBirthdayYear }][{/if}]" />
        [{oxscript include="js/widgets/oxinnerlabel.js" priority=10 }]
        [{oxscript add="$( '#oxYear' ).oxInnerLabel({sReloadElement:'#userChangeAddress'});"}]
        <p class="oxValidateError">
            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
            <span class="js-oxError_incorrectDate">[{ oxmultilang ident="ERROR_MESSAGE_INCORRECT_DATE" }]</span>
            [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxbirthdate}]
        </p>
    </li>
    [{/if}]

    <li class="formNote">[{ oxmultilang ident="COMPLETE_MARKED_FIELDS" }]</li>
    [{if !$noFormSubmit}]
    <li class="formSubmit">
        <button id="accUserSaveTop" type="submit" name="save" class="submitButton">[{ oxmultilang ident="SAVE" }]</button>
    </li>
    [{/if}]