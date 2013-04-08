[{assign var="template_title" value="REGISTER_MYACCOUNT"|oxmultilangassign}]
[{if $oView->isActive('PsLogin') }]
    [{include file="_header_plain.tpl" title=$template_title location=$template_title cssclass="body"}]
    <div class="psLoginPlainBox">
    [{include file="inc/error.tpl" Errorlist=$Errors.default}]
[{else}]
    [{include file="_header.tpl" title=$template_title location=$template_title}]
[{/if}]
[{assign var="invadr" value=$oView->getInvoiceAddress()}]

<form action="[{ $oViewConf->getSslSelfLink() }]" name="order" method="post">

    <strong id="test_openAccHeader" class="boxhead">[{ oxmultilang ident="REGISTER_OPENACCOUNT" }]</strong>

    <div class="box info">
        [{ $oViewConf->getHiddenSid() }]
        [{ $oViewConf->getNavFormParams() }]
        <input type="hidden" name="fnc" value="registeruser">
        <input type="hidden" name="cl" value="register">
        <input type="hidden" name="lgn_cook" value="0">
        <input type="hidden" id="reloadAddress" name="reloadaddress" value="">
        <input type="hidden" name="option" value="3">
        <table class="form" width="100%">
            <colgroup>
                <col width="35%">
                <col width="65%">
            </colgroup>
            <tr class="th_sep">
                <th colspan="2">[{ oxmultilang ident="REGISTER_ACCOUNTINFO" }] <small>[{ oxmultilang ident="REGISTER_COMPLETEMARKEDFIELDS" }]</small></th>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_EMAIL" }]</label></td>
                <td><input id="test_lgn_usr" type="text" name="lgn_usr" value="[{ $oView->getActiveUsername() }]" size="37" > <span class="req">*</span></td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_PWD" }]</label></td>
                <td><input id="userPassword" type="password" name="lgn_pwd" value="[{$lgn_pwd}]" size="37"> <span class="req">*</span></td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_CONFIRMPWD" }]</label></td>
                <td><input id="userPasswordConfirm" type="password" name="lgn_pwd2" value="[{$lgn_pwd2}]" size="37"> <span class="req">*</span></td>
            </tr>
            <tr class="td_sep">
                <td><label>[{ oxmultilang ident="REGISTER_NEWSLETTER" }]</label></td>
                <td>
                    <input type="hidden" name="blnewssubscribed" value="0">
                    <input type="checkbox"  name="blnewssubscribed" value="1" [{if $oView->isNewsSubscribed() }]checked[{/if}]>
                    <span class="fs10">[{ oxmultilang ident="REGISTER_NEWSLETTER_MESSAGE" }]</span>
                </td>
            </tr>
            [{if $oView->isActive('PsLogin') }]
            <tr class="td_sep">
                <td>[{oxifcontent ident="oxagb" object="oCont"}]
                                  [{oxmultilang ident="ORDER_IAGREETOTERMS1" }] <a id="test_OrderOpenAGBBottom" rel="nofollow" href="[{ $oCont->getLink() }]" onclick="window.open('[{ $oCont->getLink()|oxaddparams:"plain=1"}]', 'agb_popup', 'resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400');return false;" class="fontunderline">[{ oxmultilang ident="ORDER_IAGREETOTERMS2" }]</a> [{ oxmultilang ident="ORDER_IAGREETOTERMS3" }],&nbsp;
                                [{/oxifcontent}]
                                [{oxifcontent ident="oxrightofwithdrawal" object="oCont"}]
                                  [{oxmultilang ident="ORDER_IAGREETORIGHTOFWITHDRAWAL1" }] <a id="test_OrderOpenWithdrawalBottom" rel="nofollow" href="[{ $oCont->getLink() }]" onclick="window.open('[{ $oCont->getLink()|oxaddparams:"plain=1"}]', 'rightofwithdrawal_popup', 'resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400');return false;">[{ $oCont->oxcontents__oxtitle->value }]</a> [{ oxmultilang ident="ORDER_IAGREETORIGHTOFWITHDRAWAL3" }]
                                [{/oxifcontent}]
                </td>
                <td>
                    <input type="hidden" name="ord_agb" value="0">
                    <input id="test_OrderConfirmAGBBottom" type="checkbox" class="chk" name="ord_agb" value="1">
                </td>
            </tr>
            [{/if}]
            <tr class="th_sep">
                <th colspan="2" class="mid">[{ oxmultilang ident="REGISTER_BILLINGADDRESS" }] <small>[{ oxmultilang ident="REGISTER_COMPLETEMARKEDFIELDS2" }]</small></th>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_TITLE" }]</label></td>
                <td>
                    [{include file="inc/salutation.tpl" name="invadr[oxuser__oxsal]" value=$oxcmp_user->oxuser__oxsal->value value2=$invadr.oxuser__oxsal}]
                    [{if $oView->isFieldRequired(oxuser__oxsal) }]<span class="req">*</span>[{/if}]
                </td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_FIRSTNAME" }]</label></td>
                <td><input type="text" size="37" maxlength="255" name="invadr[oxuser__oxfname]" value="[{if isset( $invadr.oxuser__oxfname ) }][{$invadr.oxuser__oxfname }][{else}][{$oxcmp_user->oxuser__oxfname->value }][{/if}]"> [{if $oView->isFieldRequired(oxuser__oxfname) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_LASTNAME" }]</label></td>
                <td><input type="text" size="37" maxlength="255" name="invadr[oxuser__oxlname]" value="[{if isset( $invadr.oxuser__oxlname ) }][{$invadr.oxuser__oxlname }][{else}][{$oxcmp_user->oxuser__oxlname->value }][{/if}]"> [{if $oView->isFieldRequired(oxuser__oxlname) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_COMPANY" }]</label></td>
                <td>
                <input type="text" size="37" maxlength="255" name="invadr[oxuser__oxcompany]" value="[{if isset( $invadr.oxuser__oxcompany ) }][{$invadr.oxuser__oxcompany }][{else}][{$oxcmp_user->oxuser__oxcompany->value }][{/if}]">
                [{if $oView->isFieldRequired(oxuser__oxcompany) }]<span class="req">*</span>[{/if}]
              </td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_STREET" }]</label></td>
                <td>
                    <input type="text" size="28" maxlength="255" name="invadr[oxuser__oxstreet]" value="[{if isset( $invadr.oxuser__oxstreet ) }][{$invadr.oxuser__oxstreet }][{else}][{$oxcmp_user->oxuser__oxstreet->value }][{/if}]">
                    <input type="text" size="5" maxlength="16" name="invadr[oxuser__oxstreetnr]" value="[{if isset( $invadr.oxuser__oxstreetnr ) }][{$invadr.oxuser__oxstreetnr }][{else}][{$oxcmp_user->oxuser__oxstreetnr->value }][{/if}]">
                    [{if $oView->isFieldRequired(oxuser__oxstreet) || $oView->isFieldRequired(oxuser__oxstreetnr) }]<span class="req">*</span>[{/if}]
                </td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_PZLCITY" }]</label></td>
                <td>
                    <input type="text" size="5" maxlength="16" name="invadr[oxuser__oxzip]" value="[{if isset( $invadr.oxuser__oxzip ) }][{$invadr.oxuser__oxzip }][{else}][{$oxcmp_user->oxuser__oxzip->value }][{/if}]">
                    <input type="text" size="28" maxlength="255" name="invadr[oxuser__oxcity]" value="[{if isset( $invadr.oxuser__oxcity ) }][{$invadr.oxuser__oxcity }][{else}][{$oxcmp_user->oxuser__oxcity->value }][{/if}]">
                    [{if $oView->isFieldRequired(oxuser__oxzip) || $oView->isFieldRequired(oxuser__oxcity) }]<span class="req">*</span>[{/if}]
                </td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_VATID" }]</label></td>
                <td><input type="text" size="37" maxlength="255" name="invadr[oxuser__oxustid]" value="[{if isset( $invadr.oxuser__oxustid ) }][{$invadr.oxuser__oxustid }][{else}][{$oxcmp_user->oxuser__oxustid->value }][{/if}]"> [{if $oView->isFieldRequired(oxuser__oxustid) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_ADDITIONALINFO" }]</label></td>
                <td><input type="text" size="37" maxlength="255" name="invadr[oxuser__oxaddinfo]" value="[{if isset( $invadr.oxuser__oxaddinfo ) }][{$invadr.oxuser__oxaddinfo }][{else}][{$oxcmp_user->oxuser__oxaddinfo->value }][{/if}]"> [{if $oView->isFieldRequired(oxuser__oxaddinfo) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_COUNTRY" }]</label></td>
                <td>
                    <select id="invCountrySelect" name="invadr[oxuser__oxcountryid]" >
                        <option value="">-</option>
                        [{assign var="blCountrySelected" value=false}]
                        [{foreach from=$oViewConf->getCountryList() item=country key=country_id}]
                            [{assign var="sCountrySelect" value=""}]
                            [{if !$blCountrySelected}]
                                [{if (isset($invadr.oxuser__oxcountryid) && $invadr.oxuser__oxcountryid == $country->oxcountry__oxid->value) ||
                                     (!isset($invadr.oxuser__oxcountryid) && $oxcmp_user->oxuser__oxcountryid->value == $country->oxcountry__oxid->value) }]
                                    [{assign var="blCountrySelected" value=true}]
                                    [{assign var="sCountrySelect" value="selected"}]
                                [{/if}]
                            [{/if}]
                            <option value="[{$country->oxcountry__oxid->value}]" [{$sCountrySelect}]>[{$country->oxcountry__oxtitle->value}]</option>
                        [{/foreach}]
                    </select>
                    [{if $oView->isFieldRequired(oxuser__oxcountryid) }]<span class="req">*</span>[{/if}]
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
                <td><label>[{ oxmultilang ident="REGISTER_PHONE" }]</label></td>
                <td><input type="text" size="37" maxlength="128" name="invadr[oxuser__oxfon]" value="[{if isset( $invadr.oxuser__oxfon ) }][{$invadr.oxuser__oxfon }][{else}][{$oxcmp_user->oxuser__oxfon->value }][{/if}]"> [{if $oView->isFieldRequired(oxuser__oxfon) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_FAX" }]</label></td>
                <td><input type="text" size="37" maxlength="128" name="invadr[oxuser__oxfax]" value="[{if isset( $invadr.oxuser__oxfax ) }][{$invadr.oxuser__oxfax }][{else}][{$oxcmp_user->oxuser__oxfax->value }][{/if}]"> [{if $oView->isFieldRequired(oxuser__oxfax) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_MOBIL" }]</label></td>
                <td><input type="text" size="37" maxlength="128" name="invadr[oxuser__oxmobfon]" value="[{if isset( $invadr.oxuser__oxmobfon ) }][{$invadr.oxuser__oxmobfon }][{else}][{$oxcmp_user->oxuser__oxmobfon->value }][{/if}]"> [{if $oView->isFieldRequired(oxuser__oxmobfon) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            <tr [{if !$oViewConf->showBirthdayFields() }]class="td_sep"[{/if}]>
                <td><label>[{ oxmultilang ident="REGISTER_PRIVATPHONE" }]</label></td>
                <td><input type="text" size="37" maxlength="128" name="invadr[oxuser__oxprivfon]" value="[{if isset( $invadr.oxuser__oxprivfon ) }][{$invadr.oxuser__oxprivfon }][{else}][{$oxcmp_user->oxuser__oxprivfon->value }][{/if}]"> [{if $oView->isFieldRequired(oxuser__oxprivfon) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            [{if $oViewConf->showBirthdayFields() }]
            <tr class="td_sep" nowrap>
                <td><label>[{ oxmultilang ident="REGISTER_BIRTHDATE" }]</label></td>
                <td valign="top">
                    <input type="text" size="3" maxlength="2" name="invadr[oxuser__oxbirthdate][day]" value="[{if isset( $invadr.oxuser__oxbirthdate.day ) }][{$invadr.oxuser__oxbirthdate.day }][{elseif $oxcmp_user->oxuser__oxbirthdate->value && $oxcmp_user->oxuser__oxbirthdate->value != "0000-00-00"}][{$oxcmp_user->oxuser__oxbirthdate->value|date_format:"%d" }][{/if}]">&nbsp;&nbsp;
                    <input type="text" size="3" maxlength="2" name="invadr[oxuser__oxbirthdate][month]" value="[{if isset( $invadr.oxuser__oxbirthdate.month ) }][{$invadr.oxuser__oxbirthdate.month }][{elseif $oxcmp_user->oxuser__oxbirthdate->value && $oxcmp_user->oxuser__oxbirthdate->value != "0000-00-00" }][{$oxcmp_user->oxuser__oxbirthdate->value|date_format:"%m" }][{/if}]">&nbsp;&nbsp;
                    <input type="text" size="8" maxlength="4" name="invadr[oxuser__oxbirthdate][year]" value="[{if isset( $invadr.oxuser__oxbirthdate.year ) }][{$invadr.oxuser__oxbirthdate.year }][{elseif $oxcmp_user->oxuser__oxbirthdate->value && $oxcmp_user->oxuser__oxbirthdate->value != "0000-00-00" }][{$oxcmp_user->oxuser__oxbirthdate->value|date_format:"%Y" }][{/if}]">
                [{if $oView->isFieldRequired(oxuser__oxbirthdate) }]<span class="req">*</span>[{/if}]
            </td>
            </tr>
            [{/if}]
            <tr class="th_sep">
                <th class="mid" colspan="2">[{ oxmultilang ident="REGISTER_SHIPPINGADDRESS" }]</th>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="showHideShippAddr">
                        [{if !$oView->showShipAddress()}]
                        <span class="btn"><input type="submit" class="btn" name="blshowshipaddress" value="[{ oxmultilang ident="REGISTER_DIFFERENTSHIPPINGADDRESS" }]"></span>
                        [{else}]
                        <span class="btn"><input type="submit" class="btn" name="blhideshipaddress" value="[{ oxmultilang ident="REGISTER_DISABLESHIPPINGADDRESS" }]"></span>
                        [{/if}]
                        <br><br>
                        <span class="note">[{ oxmultilang ident="REGISTER_NOTE" }]</span> [{ oxmultilang ident="REGISTER_DIFFERENTDELIVERYADDRESS" }]
                    </div>
                </td>
            </tr>
            [{if $oView->showShipAddress()}]
            [{assign var="deladr" value=$oView->getDeliveryAddress()}]
            [{if $oxcmp_user}]
                [{assign var="delivadr" value=$oxcmp_user->getSelectedAddress()}]
            [{/if}]
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_ADDRESSES" }]</label></td>
                <td>
                    <select name="oxaddressid" onchange="oxid.form.set('reloadAddress', this.value === '-1' ? 1 : 2);oxid.form.reload(this.value === '-1','order','user','');oxid.form.clear(this.value !== '-1','order',/oxaddress__/);">
                        <option value="-1" SELECTED>[{ oxmultilang ident="REGISTER_NEWADDRESSES" }]</option>
                        [{if $oxcmp_user }]
                            [{foreach from=$oxcmp_user->getUserAddresses() item=address}]
                            <option value="[{ $address->oxaddress__oxid->value }]" [{ if $address->selected}]SELECTED[{/if}]>[{ $address }]</option>
                            [{/foreach}]
                        [{/if}]
                    </select>
                    <noscript>
                      <span class="btn"><input id="test_accUserReloadAddress" class="btn" type="submit" name="reloadaddress" value="[{ oxmultilang ident="REGISTER_ADDRESSES_SELECT" }]"></span>
                    </noscript>
                </td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_TITLE2" }]</label></td>
                <td>
                    [{include file="inc/salutation.tpl" name="deladr[oxaddress__oxsal]" value=$delivadr->oxaddress__oxsal->value value2=$deladr.oxaddress__oxsal}]
                    [{if $oView->isFieldRequired(oxaddress__oxsal) }]<span class="req">*</span>[{/if}]
                </td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_FIRSTNAME2" }]</label></td>
                <td><input type="text" size="37" maxlength="255" name="deladr[oxaddress__oxfname]" value="[{if isset( $deladr.oxaddress__oxfname ) }][{$deladr.oxaddress__oxfname}][{else}][{$delivadr->oxaddress__oxfname->value }][{/if}]"> [{if $oView->isFieldRequired(oxaddress__oxfname) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_LASTNAME2" }]</label></td>
                <td><input type="text" size="37" maxlength="255" name="deladr[oxaddress__oxlname]" value="[{if isset( $deladr.oxaddress__oxlname ) }][{$deladr.oxaddress__oxlname}][{else}][{$delivadr->oxaddress__oxlname->value }][{/if}]"> [{if $oView->isFieldRequired(oxaddress__oxlname) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_COMPANY2" }]</label></td>
                <td><input type="text" size="37" maxlength="255" name="deladr[oxaddress__oxcompany]" value="[{if isset( $deladr.oxaddress__oxcompany ) }][{$deladr.oxaddress__oxcompany}][{else}][{$delivadr->oxaddress__oxcompany->value }][{/if}]"> [{if $oView->isFieldRequired(oxaddress__oxcompany) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_STREET2" }]</label></td>
                <td>
                    <input type="text" size="28" maxlength="255" name="deladr[oxaddress__oxstreet]" value="[{if isset( $deladr.oxaddress__oxstreet ) }][{$deladr.oxaddress__oxstreet}][{else}][{$delivadr->oxaddress__oxstreet->value}][{/if}]">
                    <input type="text" size="5" maxlength="16" name="deladr[oxaddress__oxstreetnr]" value="[{if isset( $deladr.oxaddress__oxstreetnr ) }][{$deladr.oxaddress__oxstreetnr}][{else}][{$delivadr->oxaddress__oxstreetnr->value }][{/if}]">
                    [{if $oView->isFieldRequired(oxaddress__oxstreet) || $oView->isFieldRequired(oxaddress__oxstreetnr) }]<span class="req">*</span>[{/if}]
                </td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_PZLCITY2" }]</label></td>
                <td>
                    <input type="text" size="5" maxlength="16" name="deladr[oxaddress__oxzip]" value="[{if isset( $deladr.oxaddress__oxzip ) }][{$deladr.oxaddress__oxzip}][{else}][{$delivadr->oxaddress__oxzip->value }][{/if}]">
                    <input type="text" size="28" maxlength="255" name="deladr[oxaddress__oxcity]" value="[{if isset( $deladr.oxaddress__oxcity ) }][{$deladr.oxaddress__oxcity}][{else}][{$delivadr->oxaddress__oxcity->value }][{/if}]">
                    [{if $oView->isFieldRequired(oxaddress__oxzip) || $oView->isFieldRequired(oxaddress__oxcity) }]<span class="req">*</span>[{/if}]
                </td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_ADDITIONALINFO2" }]</label></td>
                <td><input type="text" size="37" maxlength="255" name="deladr[oxaddress__oxaddinfo]" value="[{if isset( $deladr.oxaddress__oxaddinfo ) }][{$deladr.oxaddress__oxaddinfo}][{else}][{$delivadr->oxaddress__oxaddinfo->value }][{/if}]"> [{if $oView->isFieldRequired(oxaddress__oxaddinfo) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_COUNTRY2" }]</label></td>
                <td>
                    <select id="dev_country_select" name="deladr[oxaddress__oxcountryid]" >
                        <option value="">-</option>
                        [{assign var="blCountrySelected" value=false}]
                        [{assign var="sCountrySelect" value=""}]
                        [{foreach from=$oViewConf->getCountryList() item=country key=country_id}]
                            [{assign var="sCountrySelect" value=""}]
                            [{if !$blCountrySelected}]
                                [{if (isset( $deladr.oxaddress__oxcountryid ) && $deladr.oxaddress__oxcountryid == $country->oxcountry__oxid->value) ||
                                     ($delivadr->oxaddress__oxcountry->value == $country->oxcountry__oxtitle->value) }]
                                    [{assign var="blCountrySelected" value=true}]
                                    [{assign var="sCountrySelect" value="selected"}]
                                [{/if}]
                            [{/if}]
                            <option value="[{$country->oxcountry__oxid->value}]" [{$sCountrySelect}]>[{$country->oxcountry__oxtitle->value}]</option>
                        [{/foreach}]
                    </select>
                    [{if $oView->isFieldRequired(oxaddress__oxcountryid) }]<span class="req">*</span>[{/if}]
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    [{include file="inc/state_selector.snippet.tpl"
                        countrySelectId="dev_country_select"
                        stateSelectName="deladr[oxaddress__oxstateid]"
                        selectedStateIdPrim=$deladr.oxaddress__oxstateid
                        selectedStateId=$delivadr->oxaddress__oxstateid->value
                     }]
                </td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_PHONE2" }]</label></td>
                <td><input type="text" size="37" maxlength="128" name="deladr[oxaddress__oxfon]" value="[{if isset( $deladr.oxaddress__oxfon ) }][{$deladr.oxaddress__oxfon}][{else}][{$delivadr->oxaddress__oxfon->value }][{/if}]"> [{if $oView->isFieldRequired(oxaddress__oxfon) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            <tr>
                <td><label>[{ oxmultilang ident="REGISTER_FAX2" }]</label></td>
                <td><input type="text" size="37" maxlength="128" name="deladr[oxaddress__oxfax]" value="[{if isset( $deladr.oxaddress__oxfax ) }][{$deladr.oxaddress__oxfax}][{else}][{$delivadr->oxaddress__oxfax->value }][{/if}]"> [{if $oView->isFieldRequired(oxaddress__oxfax) }]<span class="req">*</span>[{/if}]</td>
            </tr>
            [{/if}]
        </table>
    </div>
    <div class="bar prevnext">
        <div class="right"><input type="submit" value="[{ oxmultilang ident="REGISTER_SEND" }]"></div>
    </div>
</form>
[{if $oView->isActive('PsLogin') }]
    </div>
    [{include file="_footer_plain.tpl" }]
[{else}]
    [{ insert name="oxid_tracker" title=$template_title }]
    [{include file="_footer.tpl" }]
[{/if}]