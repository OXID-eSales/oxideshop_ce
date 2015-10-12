[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="user_address">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="user_address">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxaddress__oxid]" value="[{$oxaddressid}]">
<input type="hidden" name="editval[oxaddress__oxuserid]" value="[{$oxid}]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="50%">
    [{if $oxid != "-1"}]

        <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td class="edittext">
            [{oxmultilang ident="USER_ADDRESS_DELIVERYADDRESS"}]
            </td>
        </tr>
        <tr>
            <td class="edittext">

                <select [{$readonly}] name="oxaddressid" class="editinput" style="width:320px;" onChange="document.myedit.submit();">
                    <option value="-1">-</option>
                    [{foreach from=$edituser->getUserAddresses() item=address}]
                    <option value="[{$address->oxaddress__oxid->value}]" [{if $address->selected}]SELECTED[{/if}]>[{$address->oxaddress__oxfname->value}] [{$address->oxaddress__oxlname->value}], [{$address->oxaddress__oxstreet->value}], [{$address->oxaddress__oxcity->value}]</option>
                    [{/foreach}]
                </select>
                [{oxinputhelp ident="HELP_USER_ADDRESS_DELIVERYADDRESS"}]
            </td>
        </tr>
        </table>

    [{/if}]
    </td>

    <td valign="top" class="edittext vr">

        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_user_address_form"}]
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_BILLSAL"}]
                </td>
                <td class="edittext">
                <!--<input type="text" class="editinput" size="15" maxlength="[{$edit->oxuser__oxsal->fldmax_length}]" name="editval[oxaddress__oxsal]" value="[{$edit->oxaddress__oxsal->value}]" [{$readonly}]>-->
                <select name="editval[oxaddress__oxsal]" class="editinput" [{$readonly}]>
                    <option value="MR"  [{if $edit->oxaddress__oxsal->value|lower  == "mr"}]SELECTED[{/if}]>[{oxmultilang ident="MR"}]</option>
                    <option value="MRS" [{if $edit->oxaddress__oxsal->value|lower  == "mrs"}]SELECTED[{/if}]>[{oxmultilang ident="MRS"}]</option>
                  </select>
                [{oxinputhelp ident="HELP_GENERAL_BILLSAL"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_NAME"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="10" maxlength="[{$edit->oxaddress__oxfname->fldmax_length}]" name="editval[oxaddress__oxfname]" value="[{$edit->oxaddress__oxfname->value}]" [{$readonly}]>
                <input type="text" class="editinput" size="20" maxlength="[{$edit->oxaddress__oxlname->fldmax_length}]" name="editval[oxaddress__oxlname]" value="[{$edit->oxaddress__oxlname->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_NAME"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_COMPANY"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="37" maxlength="[{$edit->oxaddress__oxcompany->fldmax_length}]" name="editval[oxaddress__oxcompany]" value="[{$edit->oxaddress__oxcompany->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_COMPANY"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_STREETNUM"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="28" maxlength="[{$edit->oxaddress__oxstreet->fldmax_length}]" name="editval[oxaddress__oxstreet]" value="[{$edit->oxaddress__oxstreet->value}]" [{$readonly}]> <input type="text" class="editinput" size="5" maxlength="[{$edit->oxaddress__oxstreetnr->fldmax_length}]" name="editval[oxaddress__oxstreetnr]" value="[{$edit->oxaddress__oxstreetnr->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_STREETNUM"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_ZIPCITY"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="5" maxlength="[{$edit->oxaddress__oxzip->fldmax_length}]" name="editval[oxaddress__oxzip]" value="[{$edit->oxaddress__oxzip->value}]" [{$readonly}]>
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxaddress__oxcity->fldmax_length}]" name="editval[oxaddress__oxcity]" value="[{$edit->oxaddress__oxcity->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_ZIPCITY"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_EXTRAINFO"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="37" maxlength="[{$edit->oxaddress__oxaddinfo->fldmax_length}]" name="editval[oxaddress__oxaddinfo]" value="[{$edit->oxaddress__oxaddinfo->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_EXTRAINFO"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_STATE"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="15" maxlength="[{$edit->oxaddress__oxstateid->fldmax_length}]" name="editval[oxaddress__oxstateid]" value="[{$edit->oxaddress__oxstateid->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_STATE"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_COUNTRY"}]
                </td>
                <td class="edittext">
                 <select class="editinput" name="editval[oxaddress__oxcountryid]" [{$readonly}]>
                   [{foreach from=$countrylist item=oCountry}]
                   <option value="[{$oCountry->oxcountry__oxid->value}]" [{if $oCountry->oxcountry__oxid->value == $edit->oxaddress__oxcountryid->value}]selected[{/if}]>[{$oCountry->oxcountry__oxtitle->value}]</option>
                   [{/foreach}]
                 </select>
                 [{oxinputhelp ident="HELP_GENERAL_COUNTRY"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_FON"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="12" maxlength="[{$edit->oxaddress__oxfon->fldmax_length}]" name="editval[oxaddress__oxfon]" value="[{$edit->oxaddress__oxfon->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_FON"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_FAX"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="12" maxlength="[{$edit->oxaddress__oxfax->fldmax_length}]" name="editval[oxaddress__oxfax]" value="[{$edit->oxaddress__oxfax->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_FAX"}]
                </td>
            </tr>
        [{/block}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
            <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'"" [{$readonly}]>
            [{if $oxaddressid != "-1"}]
                <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_DELETE"}]" onClick="Javascript:document.myedit.fnc.value='deladdress'"" [{$readonly}]>
            [{/if}]
            </td>
        </tr>

        </table>
    </td>

</tr>
</table>
</form>
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]