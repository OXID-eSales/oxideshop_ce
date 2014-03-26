[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="order_main">
</form>


<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="order_address">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[oxorder__oxid]" value="[{ $oxid }]">

<table cellspacing="0" cellpadding="0" border="0"  width="98%">
<tr>
    <td valign="top" class="edittext">
        <b>[{ oxmultilang ident="GENERAL_BILLADDRESS" }]</b><br>
        <br>
        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_order_address_billing"}]
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_BILLSAL" }]
            </td>
            <td class="edittext">
            <select name="editval[oxorder__oxbillsal]" class="editinput" [{ $readonly }]>
                <option value="MR"  [{if $edit->oxorder__oxbillsal->value|lower  == "mr"  }]SELECTED[{/if}]>[{ oxmultilang ident="MR"  }]</option>
                <option value="MRS" [{if $edit->oxorder__oxbillsal->value|lower  == "mrs" }]SELECTED[{/if}]>[{ oxmultilang ident="MRS" }]</option>
            </select>
            [{ oxinputhelp ident="HELP_GENERAL_BILLSAL" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_NAME" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="10" maxlength="[{$edit->oxorder__oxbillfname->fldmax_length}]" name="editval[oxorder__oxbillfname]" value="[{$edit->oxorder__oxbillfname->value }]" [{ $readonly }]>
            <input type="text" class="editinput" size="20" maxlength="[{$edit->oxorder__oxbilllname->fldmax_length}]" name="editval[oxorder__oxbilllname]" value="[{$edit->oxorder__oxbilllname->value }]" [{ $readonly }]>
            [{ oxinputhelp ident="HELP_GENERAL_NAME" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_EMAIL" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="37" maxlength="[{$edit->oxorder__oxbillemail->fldmax_length}]" name="editval[oxorder__oxbillemail]" value="[{$edit->oxorder__oxbillemail->value }]" [{ $readonly }]>
            [{ oxinputhelp ident="HELP_GENERAL_EMAIL" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_COMPANY" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="37" maxlength="[{$edit->oxorder__oxbillcompany->fldmax_length}]" name="editval[oxorder__oxbillcompany]" value="[{$edit->oxorder__oxbillcompany->value }]" [{ $readonly }]>
            [{ oxinputhelp ident="HELP_GENERAL_COMPANY" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_STREETNUM" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="28" maxlength="[{$edit->oxorder__oxbillstreet->fldmax_length}]" name="editval[oxorder__oxbillstreet]" value="[{$edit->oxorder__oxbillstreet->value }]" [{ $readonly }]> <input type="text" class="editinput" size="5" maxlength="[{$edit->oxorder__oxbillstreetnr->fldmax_length}]" name="editval[oxorder__oxbillstreetnr]" value="[{$edit->oxorder__oxbillstreetnr->value }]" [{ $readonly }]>
            [{ oxinputhelp ident="HELP_GENERAL_STREETNUM" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_ZIPCITY" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="5" maxlength="[{$edit->oxorder__oxbillzip->fldmax_length}]" name="editval[oxorder__oxbillzip]" value="[{$edit->oxorder__oxbillzip->value }]" [{ $readonly }]>
            <input type="text" class="editinput" size="25" maxlength="[{$edit->oxorder__oxbillcity->fldmax_length}]" name="editval[oxorder__oxbillcity]" value="[{$edit->oxorder__oxbillcity->value }]" [{ $readonly }]>
            [{ oxinputhelp ident="HELP_GENERAL_ZIPCITY" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_USTID" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="15" maxlength="[{$edit->oxorder__oxbillustid->fldmax_length}]" name="editval[oxorder__oxbillustid]" value="[{$edit->oxorder__oxbillustid->value }]" [{ $readonly }]>
            [{ oxinputhelp ident="HELP_GENERAL_USTID" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_EXTRAINFO" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="37" maxlength="[{$edit->oxorder__oxbilladdinfo->fldmax_length}]" name="editval[oxorder__oxbilladdinfo]" value="[{$edit->oxorder__oxbilladdinfo->value }]" [{ $readonly }]>
            [{ oxinputhelp ident="HELP_GENERAL_EXTRAINFO" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_STATE" }]
            </td>
            <td class="edittext">
                <script type="text/javascript"><!--
                    var allStates = new Array();
                    var allStateIds = new Array();
                    var allCountryIds = new Object();
                    var cCount = 0;
                    [{foreach from=$countrylist item=country key=country_id }]
                        var states = new Array();
                        var ids = new Array();
                        var i = 0;
                        [{assign var=countryStates value=$country->getStates()}]
                        [{foreach from=$countryStates item=state key=state_id}]
                            states[i] = '[{$state->oxstates__oxtitle->value}]';
                            ids[i] = '[{$state->oxstates__oxid->value}]';
                            i++;
                        [{/foreach}]
                        allStates[++cCount] = states;
                        allStateIds[cCount]  = ids;
                        allCountryIds['[{$country->getId()}]']  = cCount;
                    [{/foreach}]

                    function getCountryStates(countries_select, states_select)
                    {
                        var oCountries = document.getElementById(countries_select);
                        var oStates = document.getElementById(states_select);
                        if (allStates[allCountryIds[oCountries.options[oCountries.selectedIndex].value]].length > 0) {
                            oStates.options.length = 0;
                            for (var i in allStates[allCountryIds[oCountries.options[oCountries.selectedIndex].value]])
                            {
                                var oOption = document.createElement('option');
                                oOption.text = allStates[allCountryIds[oCountries.options[oCountries.selectedIndex].value]][i];
                                oOption.value = allStateIds[allCountryIds[oCountries.options[oCountries.selectedIndex].value]][i];
                                var oOptionOld = oStates.options[0];
                                try {
                                    oStates.add(oOption, oOptionOld); /* standards compliant does not work in IE */
                                }
                                catch(ex) {
                                    oStates.add(oOption, oStates.selectedIndex); /* IE only */
                                }
                            }
                        }
                        else {
                            oStates.options.length = 0;
                            var oOption = document.createElement('option');
                            oOption.text = '---';
                            oOption.value = '';
                            var oOptionOld = oStates.options[0];
                            try {
                                oStates.add(oOption, oOptionOld); /* standards compliant does not work in IE */
                            }
                            catch(ex) {
                                oStates.add(oOption, oStates.selectedIndex); /* IE only */
                            }
                        }
                    }
                    //-->
                </script>

                <select id="bill_state_select" class="editinput" name="editval[oxorder__oxbillstateid]" [{ $readonly }]>
                [{if $edit->oxorder__oxbillstateid->value}]
                    [{foreach from=$countrylist item=country key=country_id }]
                        [{if $country_id == $edit->oxorder__oxbillcountryid->value}]
                            [{assign var=countryStates value=$country->getStates()}]
                            [{foreach from=$countryStates item=state key=state_id}]
                                <option value='[{$state->oxstates__oxid->value}]' [{if $edit->oxorder__oxbillstateid->value == $state->oxstates__oxid->value}]selected[{/if}]>[{$state->oxstates__oxtitle->value}]</option>
                            [{/foreach}]
                        [{/if}]
                    [{/foreach}]
                [{else}]
                    <option value=''>---</option>
                [{/if}]
                </select>
            [{ oxinputhelp ident="HELP_GENERAL_STATE" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_COUNTRY" }]
            </td>
            <td class="edittext">
            <select onchange="getCountryStates('bill_country_select', 'bill_state_select');" id="bill_country_select" class="editinput" name="editval[oxorder__oxbillcountryid]" [{ $readonly }]>
               <option value=''>---</option>
               [{ foreach from=$countrylist item=oCountry}]
               <option value="[{$oCountry->oxcountry__oxid->value}]" [{if $oCountry->oxcountry__oxid->value == $edit->oxorder__oxbillcountryid->value}]selected[{/if}]>[{$oCountry->oxcountry__oxtitle->value}]</option>
               [{/foreach}]
            </select>
            [{ oxinputhelp ident="HELP_GENERAL_COUNTRY" }]
            [{if !$edit->oxorder__oxbillcountryid->value && $edit->oxorder__oxbillcountry->value}]
               &nbsp;([{$edit->oxorder__oxbillcountry->value}])
            [{/if}]
            </td>
        </tr>

            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_FON" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="12" maxlength="[{$edit->oxorder__oxbillfon->fldmax_length}]" name="editval[oxorder__oxbillfon]" value="[{$edit->oxorder__oxbillfon->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_FON" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_FAX" }]<br><br><br>
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="12" maxlength="[{$edit->oxorder__oxbillfax->fldmax_length}]" name="editval[oxorder__oxbillfax]" value="[{$edit->oxorder__oxbillfax->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_FAX" }]
                <br><br><br></td>
            </tr>
        [{/block}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
            <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly }]>
            </td>
        </tr>
        </table>

    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="50%">

        <b>[{ oxmultilang ident="GENERAL_DELIVERYADDRESS" }]:</b><br>
        <br>

        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_order_address_delivery"}]
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_BILLSAL" }]
                </td>
                <td class="edittext">
                    <select name="editval[oxorder__oxdelsal]" class="editinput" [{ $readonly }]>
                        <option value="MR"  [{if $edit->oxorder__oxdelsal->value|lower  == "mr"  }]SELECTED[{/if}]>[{ oxmultilang ident="MR"  }]</option>
                        <option value="MRS" [{if $edit->oxorder__oxdelsal->value|lower  == "mrs" }]SELECTED[{/if}]>[{ oxmultilang ident="MRS" }]</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_NAME" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="10" maxlength="[{$edit->oxorder__oxdelfname->fldmax_length}]" name="editval[oxorder__oxdelfname]" value="[{$edit->oxorder__oxdelfname->value }]" [{ $readonly }]>
                <input type="text" class="editinput" size="20" maxlength="[{$edit->oxorder__oxdellname->fldmax_length}]" name="editval[oxorder__oxdellname]" value="[{$edit->oxorder__oxdellname->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_NAME" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_COMPANY" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="37" maxlength="[{$edit->oxorder__oxdelcompany->fldmax_length}]" name="editval[oxorder__oxdelcompany]" value="[{$edit->oxorder__oxdelcompany->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_COMPANY" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_STREETNUM" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="28" maxlength="[{$edit->oxorder__oxdelstreet->fldmax_length}]" name="editval[oxorder__oxdelstreet]" value="[{$edit->oxorder__oxdelstreet->value }]" [{ $readonly }]> <input type="text" class="editinput" size="5" maxlength="[{$edit->oxorder__oxdelstreetnr->fldmax_length}]" name="editval[oxorder__oxdelstreetnr]" value="[{$edit->oxorder__oxdelstreetnr->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_STREETNUM" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_ZIPCITY" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="5" maxlength="[{$edit->oxorder__oxdelzip->fldmax_length}]" name="editval[oxorder__oxdelzip]" value="[{$edit->oxorder__oxdelzip->value }]" [{ $readonly }]>
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxorder__oxdelcity->fldmax_length}]" name="editval[oxorder__oxdelcity]" value="[{$edit->oxorder__oxdelcity->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_ZIPCITY" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_EXTRAINFO" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="37" maxlength="[{$edit->oxorder__oxdeladdinfo->fldmax_length}]" name="editval[oxorder__oxdeladdinfo]" value="[{$edit->oxorder__oxdeladdinfo->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_EXTRAINFO" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_STATE" }]
                </td>
                <td class="edittext">
                <select id="del_state_select" class="editinput" name="editval[oxorder__oxdelstateid]" [{ $readonly }]>
                [{if $edit->oxorder__oxdelstateid->value}]
                    [{foreach from=$countrylist item=country key=country_id }]
                        [{if $country_id == $edit->oxorder__oxdelcountryid->value}]
                            [{assign var=countryStates value=$country->getStates()}]
                            [{foreach from=$countryStates item=state key=state_id}]
                                <option value='[{$state->oxstates__oxid->value}]' [{if $edit->oxorder__oxdelstateid->value == $state->oxstates__oxid->value}]selected[{/if}]>[{$state->oxstates__oxtitle->value}]</option>
                            [{/foreach}]
                        [{/if}]
                    [{/foreach}]
                [{else}]
                    <option value=''>---</option>
                [{/if}]
                </select>
                [{ oxinputhelp ident="HELP_GENERAL_STATE" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_COUNTRY" }]
                </td>
                <td class="edittext">
            <select onchange="getCountryStates('del_country_select', 'del_state_select');" id="del_country_select" class="editinput" name="editval[oxorder__oxdelcountryid]" [{ $readonly }]>
                   <option value=''>---</option>
                   [{ foreach from=$countrylist item=oCountry}]
                   <option value="[{$oCountry->oxcountry__oxid->value}]" [{if $oCountry->oxcountry__oxid->value == $edit->oxorder__oxdelcountryid->value}]selected[{/if}]>[{$oCountry->oxcountry__oxtitle->value}]</option>
                   [{/foreach}]
                </select>
            [{ oxinputhelp ident="HELP_GENERAL_COUNTRY" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_FON" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="12" maxlength="[{$edit->oxorder__oxdelfon->fldmax_length}]" name="editval[oxorder__oxdelfon]" value="[{$edit->oxorder__oxdelfon->value }]" [{ $readonly }]>
            [{ oxinputhelp ident="HELP_GENERAL_FON" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_FAX" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="12" maxlength="[{$edit->oxorder__oxdelfax->fldmax_length}]" name="editval[oxorder__oxdelfax]" value="[{$edit->oxorder__oxdelfax->value }]" [{ $readonly }]>
            [{ oxinputhelp ident="HELP_GENERAL_FAX" }]
            </td>
        </tr>
        [{/block}]
        </table>

    </td>


    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
