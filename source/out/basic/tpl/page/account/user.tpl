[{assign var="template_title" value="ACCOUNT_USERTITLE"|oxmultilangassign }]
[{include file="_header.tpl" title=$template_title location="ACCOUNT_USER_LOCATION"|oxmultilangassign|cat:$template_title}]
[{assign var="invadr" value=$oView->getInvoiceAddress()}]

[{include file="inc/account_header.tpl" active_link=3 }]<br>
<strong id="test_addressSettingsHeader" class="boxhead">[{ $template_title }]</strong>
<div class="box info">
  [{include file="inc/error.tpl" Errorlist=$Errors.user errdisplay="inbox"}]
    <form action="[{ $oViewConf->getSelfActionLink() }]" name="order" method="post">
      <div class="account">
          [{ $oViewConf->getHiddenSid() }]
          [{ $oViewConf->getNavFormParams() }]
          <input type="hidden" name="fnc" value="changeuser_testvalues">
          <input type="hidden" name="cl" value="account_user">
          <input type="hidden" name="CustomError" value='user'>
          <input type="hidden" id="reloadAddress" name="reloadaddress" value="">
          <table class="form" width="90%">
          <tr class="th_sep">
              <th colspan="2">[{ oxmultilang ident="ACCOUNT_USER_BILLINGADDRESS" }] <small>[{ oxmultilang ident="ACCOUNT_USER_COMPLETEMARKEDFIELDS" }]</small></th>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_EMAIL" }]</label></td>
            <td>
              <input type="text" name="invadr[oxuser__oxusername]" value="[{if isset( $invadr.oxuser__oxusername ) }][{ $invadr.oxuser__oxusername }][{else }][{ $oxcmp_user->oxuser__oxusername->value }][{/if }]" size="37" onKeyUp="oxid.showhide( 'user_passwd', this.value != '[{if $invadr.oxuser__oxusername }][{ $invadr.oxuser__oxusername }][{else }][{ $oxcmp_user->oxuser__oxusername->value }][{/if }]');" onChange="oxid.showhide( 'user_passwd', this.value != '[{if $invadr.oxuser__oxusername }][{ $invadr.oxuser__oxusername }][{else }][{ $oxcmp_user->oxuser__oxusername->value }][{/if }]' );">
              <span class="req">*</span>
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_TITLE" }]</label></td>
            <td>
              [{include file="inc/salutation.tpl" name="invadr[oxuser__oxsal]" value=$oxcmp_user->oxuser__oxsal->value }] &nbsp;
              [{if $oView->isFieldRequired(oxuser__oxsal) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_FIRSTNAME" }]</label></td>
            <td>
              <input type="text" size="37" maxlength="255" name="invadr[oxuser__oxfname]" value="[{if isset( $invadr.oxuser__oxfname ) }][{ $invadr.oxuser__oxfname }][{else }][{ $oxcmp_user->oxuser__oxfname->value }][{/if }]">
              [{if $oView->isFieldRequired(oxuser__oxfname) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_LASTNAME" }]</label></td>
            <td>
              <input type="text" size="37" maxlength="255" name="invadr[oxuser__oxlname]" value="[{if isset( $invadr.oxuser__oxlname ) }][{ $invadr.oxuser__oxlname }][{else }][{ $oxcmp_user->oxuser__oxlname->value }][{/if }]">
              [{if $oView->isFieldRequired(oxuser__oxlname) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_COMPANY" }]</label></td>
            <td>
              <input type="text" size="37" maxlength="255" name="invadr[oxuser__oxcompany]" value="[{if isset( $invadr.oxuser__oxcompany ) }][{ $invadr.oxuser__oxcompany }][{else }][{ $oxcmp_user->oxuser__oxcompany->value }][{/if }]">
              [{if $oView->isFieldRequired(oxuser__oxcompany) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_STREETANDSTREETNO" }]</label></td>
            <td>
              <input type="text" size="28" maxlength="255" name="invadr[oxuser__oxstreet]" value="[{if isset( $invadr.oxuser__oxstreet ) }][{ $invadr.oxuser__oxstreet }][{else }][{ $oxcmp_user->oxuser__oxstreet->value }][{/if }]">
              <input type="text" size="5" maxlength="16" name="invadr[oxuser__oxstreetnr]" value="[{if isset( $invadr.oxuser__oxstreetnr ) }][{ $invadr.oxuser__oxstreetnr }][{else }][{ $oxcmp_user->oxuser__oxstreetnr->value }][{/if }]">
              [{if $oView->isFieldRequired(oxuser__oxstreet) || $oView->isFieldRequired(oxuser__oxstreetnr) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_POSTALCODEANDCITY" }]</label></td>
            <td>
              <input type="text" size="5" maxlength="16" name="invadr[oxuser__oxzip]" value="[{if isset( $invadr.oxuser__oxzip ) }][{ $invadr.oxuser__oxzip }][{else }][{ $oxcmp_user->oxuser__oxzip->value }][{/if }]">
              <input type="text" size="28" maxlength="255" name="invadr[oxuser__oxcity]" value="[{if isset( $invadr.oxuser__oxcity ) }][{ $invadr.oxuser__oxcity }][{else }][{ $oxcmp_user->oxuser__oxcity->value }][{/if }]">
              [{if $oView->isFieldRequired(oxuser__oxzip) || $oView->isFieldRequired(oxuser__oxcity) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_VATIDNO" }]</label></td>
            <td>
              <input type="text" size="37" maxlength="255" name="invadr[oxuser__oxustid]" value="[{if isset( $invadr.oxuser__oxustid ) }][{ $invadr.oxuser__oxustid }][{else}][{ $oxcmp_user->oxuser__oxustid->value }][{/if }]">
              [{if $oView->isFieldRequired(oxuser__oxustid) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_ADDITIONALINFO" }]</label></td>
            <td>
              <input type="text" size="37" maxlength="255" name="invadr[oxuser__oxaddinfo]" value="[{if isset( $invadr.oxuser__oxaddinfo ) }][{ $invadr.oxuser__oxaddinfo }][{else }][{ $oxcmp_user->oxuser__oxaddinfo->value }][{/if }]">
              [{if $oView->isFieldRequired(oxuser__oxaddinfo) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_COUNTRY" }]</label></td>
            <td>
              <select id="invCountrySelect" name="invadr[oxuser__oxcountryid]">
                <option value="">-</option>
                [{foreach from=$oViewConf->getCountryList() item=country key=country_id }]
                  <option value="[{ $country->oxcountry__oxid->value }]"  [{if $oxcmp_user->oxuser__oxcountryid->value == $country->oxcountry__oxid->value }]selected[{/if }]>[{ $country->oxcountry__oxtitle->value }]</option>
                [{/foreach }]
              </select>
              [{if $oView->isFieldRequired(oxuser__oxcountryid) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td></td>
            <td>
              [{include file="inc/state_selector.snippet.tpl"
                        countrySelectId="invCountrySelect"
                        stateSelectName="invadr[oxuser__oxstateid]"
                        selectedStateIdPrim=$invadr.oxuser__oxstateid
                        selectedStateId=$oxcmp_user->oxuser__oxstateid->value
                     }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_PHONE" }]</label></td>
            <td>
              <input type="text" size="37" maxlength="128" name="invadr[oxuser__oxfon]" value="[{if isset( $invadr.oxuser__oxfon ) }][{ $invadr.oxuser__oxfon }][{else }][{ $oxcmp_user->oxuser__oxfon->value }][{/if }]">
              [{if $oView->isFieldRequired(oxuser__oxfon) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_FAX" }]</label></td>
            <td>
              <input type="text" size="37" maxlength="128" name="invadr[oxuser__oxfax]" value="[{if isset( $invadr.oxuser__oxfax ) }][{ $invadr.oxuser__oxfax }][{else }][{ $oxcmp_user->oxuser__oxfax->value }][{/if }]">
              [{if $oView->isFieldRequired(oxuser__oxfax) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_CELLUARPHONE" }]</label></td>
            <td>
              <input type="text" size="37" maxlength="64" name="invadr[oxuser__oxmobfon]" value="[{if isset( $invadr.oxuser__oxmobfon ) }][{$invadr.oxuser__oxmobfon }][{else}][{$oxcmp_user->oxuser__oxmobfon->value }][{/if}]">
              [{if $oView->isFieldRequired(oxuser__oxmobfon) }]<span class="req">*</span>[{/if}]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_EVENINGPHONE" }]</label></td>
            <td>
              <input type="text" size="37" maxlength="64" name="invadr[oxuser__oxprivfon]" value="[{if isset( $invadr.oxuser__oxprivfon ) }][{$invadr.oxuser__oxprivfon }][{else}][{$oxcmp_user->oxuser__oxprivfon->value }][{/if}]">
              [{if $oView->isFieldRequired(oxuser__oxprivfon) }]<span class="req">*</span>[{/if}]
            </td>
          </tr>
          [{if $oViewConf->showBirthdayFields() }]
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_BIRTHDATE" }]</label></td>
            <td valign="top" nowrap>
              <input type="text" size="3" maxlength="2" name="invadr[oxuser__oxbirthdate][day]" value="[{if isset( $invadr.oxuser__oxbirthdate.day ) }][{$invadr.oxuser__oxbirthdate.day }][{elseif $oxcmp_user->oxuser__oxbirthdate->value && $oxcmp_user->oxuser__oxbirthdate->value != "0000-00-00"}][{$oxcmp_user->oxuser__oxbirthdate->value|regex_replace:"/^([0-9]{4})[-]([0-9]{1,2})[-]/":"" }][{/if}]">&nbsp;&nbsp;
              <input type="text" size="3" maxlength="2" name="invadr[oxuser__oxbirthdate][month]" value="[{if isset( $invadr.oxuser__oxbirthdate.month ) }][{$invadr.oxuser__oxbirthdate.month }][{elseif $oxcmp_user->oxuser__oxbirthdate->value && $oxcmp_user->oxuser__oxbirthdate->value != "0000-00-00" }][{$oxcmp_user->oxuser__oxbirthdate->value|regex_replace:"/^([0-9]{4})[-]/":""|regex_replace:"/[-]([0-9]{1,2})$/":"" }][{/if}]">&nbsp;&nbsp;
                 <input type="text" size="8" maxlength="4" name="invadr[oxuser__oxbirthdate][year]" value="[{if isset( $invadr.oxuser__oxbirthdate.year ) }][{$invadr.oxuser__oxbirthdate.year }][{elseif $oxcmp_user->oxuser__oxbirthdate->value && $oxcmp_user->oxuser__oxbirthdate->value != "0000-00-00" }][{$oxcmp_user->oxuser__oxbirthdate->value|regex_replace:"/[-]([0-9]{1,2})[-]([0-9]{1,2})$/":"" }][{/if}]">
              [{if $oView->isFieldRequired(oxuser__oxbirthdate) }]<span class="req">*</span>[{/if}]
            </td>
          </tr>
          [{/if}]
          <tr id="user_passwd" [{if !$invadr.oxuser__oxusername || $invadr.oxuser__oxusername == $oxcmp_user->oxuser__oxusername->value }]style="display:none;"[{/if}]>
            <td><label>[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_PWD" }]</label></td>
            <td>
              <input type="password" size="37" name="user_password">
              <span class="req">*</span>
            </td>
          </tr>

          <tr class="td_sep">
            <td colspan="2">
              <div>&nbsp;</div>
            </td>
          </tr>
          <tr class="td_sep">
            <td colspan="2" align="right">
              <span class="btn"><input id="test_accUserSaveTop" type="submit" name="save" class="btn" value="[{ oxmultilang ident="ACCOUNT_USER_SAVE" }]"></span>
            </td>
          </tr>
          [{assign var="delivadr" value=$oxcmp_user->getSelectedAddress()}]
          [{assign var="deladr" value=$oView->getDeliveryAddress()}]
          <tr class="th_sep">
            <th class="mid" colspan="2">
              [{ oxmultilang ident="ACCOUNT_USER_SHIPPINGADDRESSES" }]
            </th>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_ADDRESSES" }]</label></td>
            <td>
              <select name="oxaddressid" onchange="oxid.form.set('reloadAddress', this.value === '-1' ? 1 : 2);oxid.form.reload(this.value === '-1','order','account_user','');oxid.form.clear(this.value !== '-1','order',/oxaddress__/);">
                <option value="-1">[{ oxmultilang ident="ACCOUNT_USER_NEWADDRESS" }]</option>
                [{foreach from=$oxcmp_user->getUserAddresses() item=address }]
                  <option value="[{ $address->oxaddress__oxid->value }]" [{if $address->isSelected()}]SELECTED[{/if }]>[{$address}]</option>
                [{/foreach }]
              </select>
              <noscript>
                <span class="btn"><input id="test_accUserReloadAddress" class="btn" type="submit" name="reloadaddress" value="[{ oxmultilang ident="ACCOUNT_USER_ADDRESSES_SELECT" }]"></span>
              </noscript>
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_TITLE2" }]</label></td>
            <td>
              [{include file="inc/salutation.tpl" name="deladr[oxaddress__oxsal]" value=$delivadr->oxaddress__oxsal->value value2=$deladr.oxaddress__oxsal }]
              [{if $oView->isFieldRequired(oxaddress__oxsal) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_FIRSTLASTNAME" }]</label></td>
            <td>
              <input type="text" size="10" maxlength="255" name="deladr[oxaddress__oxfname]" value="[{if isset( $deladr.oxaddress__oxfname ) }][{ $deladr.oxaddress__oxfname }][{else}][{ $delivadr->oxaddress__oxfname->value }][{/if }]">
              <input type="text" size="23" maxlength="255" name="deladr[oxaddress__oxlname]" value="[{if isset( $deladr.oxaddress__oxlname ) }][{ $deladr.oxaddress__oxlname }][{else}][{ $delivadr->oxaddress__oxlname->value }][{/if }]">
              [{if $oView->isFieldRequired(oxaddress__oxfname) || $oView->isFieldRequired(oxaddress__oxlname) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_COMPANY2" }]</label></td>
            <td>
              <input type="text" size="37" maxlength="255" name="deladr[oxaddress__oxcompany]" value="[{if isset( $deladr.oxaddress__oxcompany ) }][{ $deladr.oxaddress__oxcompany }][{else}][{ $delivadr->oxaddress__oxcompany->value }][{/if }]">
              [{if $oView->isFieldRequired(oxaddress__oxcompany) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_STREETANDSTREETNO2" }]</label></td>
            <td>
              <input type="text" size="28" maxlength="255" name="deladr[oxaddress__oxstreet]" value="[{if isset( $deladr.oxaddress__oxstreet ) }][{ $deladr.oxaddress__oxstreet }][{else}][{ $delivadr->oxaddress__oxstreet->value }][{/if }]">
              <input type="text" size="5" maxlength="16" name="deladr[oxaddress__oxstreetnr]" value="[{if isset( $deladr.oxaddress__oxstreetnr ) }][{ $deladr.oxaddress__oxstreetnr }][{else}][{ $delivadr->oxaddress__oxstreetnr->value }][{/if }]">
              [{if $oView->isFieldRequired(oxaddress__oxstreet) || $oView->isFieldRequired(oxaddress__oxstreetnr) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_POSTALCODEANDCITY2" }]</label></td>
            <td>
              <input type="text" size="5" maxlength="50" name="deladr[oxaddress__oxzip]" value="[{if isset( $deladr.oxaddress__oxzip ) }][{ $deladr.oxaddress__oxzip }][{else}][{ $delivadr->oxaddress__oxzip->value }][{/if }]">
              <input type="text" size="28" maxlength="255" name="deladr[oxaddress__oxcity]" value="[{if isset( $deladr.oxaddress__oxcity ) }][{ $deladr.oxaddress__oxcity }][{else}][{ $delivadr->oxaddress__oxcity->value }][{/if }]">
              [{if $oView->isFieldRequired(oxaddress__oxzip) || $oView->isFieldRequired(oxaddress__oxcity) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_ADDITIONALINFO2" }]</label></td>
            <td>
              <input type="text" size="37" maxlength="255" name="deladr[oxaddress__oxaddinfo]" value="[{if isset( $deladr.oxaddress__oxaddinfo ) }][{ $deladr.oxaddress__oxaddinfo }][{else}][{ $delivadr->oxaddress__oxaddinfo->value }][{/if }]">
              [{if $oView->isFieldRequired(oxaddress__oxaddinfo) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_COUNTRY2" }]</label></td>
            <td>
              <select id="delCountrySelect" name="deladr[oxaddress__oxcountryid]">
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
              [{if $oView->isFieldRequired(oxaddress__oxcountryid) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td></td>
            <td>
              [{include file="inc/state_selector.snippet.tpl"
                        countrySelectId="delCountrySelect"
                        stateSelectName="deladr[oxaddress__oxstateid]"
                        selectedStateIdPrim=$deladr.oxaddress__oxstateid
                        selectedStateId=$delivadr->oxaddress__oxstateid->value
                     }]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_PHONE2" }]</label></td>
            <td>
              <input type="text" size="37" maxlength="128" name="deladr[oxaddress__oxfon]" value="[{if isset( $deladr.oxaddress__oxfon ) }][{ $deladr.oxaddress__oxfon }][{else}][{ $delivadr->oxaddress__oxfon->value }][{/if }]">
              [{if $oView->isFieldRequired(oxaddress__oxfon) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr class="td_sep">
            <td><label>[{ oxmultilang ident="ACCOUNT_USER_FAX2" }]</label></td>
            <td>
              <input type="text" size="37" maxlength="128" name="deladr[oxaddress__oxfax]" value="[{if isset( $deladr.oxaddress__oxfax ) }][{ $deladr.oxaddress__oxfax }][{else}][{ $delivadr->oxaddress__oxfax->value }][{/if }]">
              [{if $oView->isFieldRequired(oxaddress__oxfax) }]<span class="req">*</span>[{/if }]
            </td>
          </tr>
          <tr>
            <td colspan="2" align="right">
              <input type="hidden" name="blshowshipaddress" value="1">
              <span class="btn"><input id="test_accUserSaveBottom" type="submit" class="btn" name="save" value="[{ oxmultilang ident="ACCOUNT_USER_SAVE2" }]"></span>
            </td>
          </tr>
        </table>
      </div>
    </form>
</div>

<div class="bar prevnext">
    <form action="[{ $oViewConf->getSelfActionLink() }]" name="account_user_back" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="start">
          <div class="right">
              <input id="test_BackToShop" type="submit" value="[{ oxmultilang ident="ACCOUNT_USER_BACKTOSHOP" }]">
          </div>
      </div>
    </form>
</div>


[{insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl" }]
