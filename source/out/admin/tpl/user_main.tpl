[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function chkInsert()
{
    if( document.myedit.elements["editval[oxuser__oxusername]"].value == "")
     {    alert("Bitte eMail Adresse eingeben!");
           document.myedit.elements["editval[oxuser__oxusername]"].focus();
           return false;
    }
    return true;

}

//-->
</script>

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="user_main">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post" onSubmit="return chkInsert()">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="user_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[oxuser__oxid]" value="[{ $oxid }]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_user_main_form"}]
            [{if $sSaveError}]
                <tr>
                    <td></td>
                    <td class="errorbox">[{oxmultilang ident=$sSaveError}]</td>
                </tr>
            [{/if}]
            <tr>
                <td class="edittext" width="90">
                [{ oxmultilang ident="GENERAL_ACTIVE" }]
                </td>
                <td class="edittext">
                <input class="edittext" type="checkbox" name="editval[oxuser__oxactive]" value='1' [{if $edit->oxuser__oxactive->value == 1}]checked[{/if}] [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_ACTIVE" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="USER_MAIN_RIGHTS" }]
                </td>
                <td class="edittext">
                    <select name="editval[oxuser__oxrights]" class="editinput" [{ $readonly }]>
                    [{foreach from=$rights item=shopitem}]
                    <option value="[{ $shopitem->id }]" [{ if $shopitem->selected}]SELECTED[{/if}]>[{ $shopitem->name }]</option>
                    [{/foreach}]
                    </select>
                    [{ oxinputhelp ident="HELP_USER_MAIN_RIGHTS" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="USER_MAIN_EMAILLOGIN" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxuser__oxusername->fldmax_length}]" name="editval[oxuser__oxusername]" value="[{$edit->oxuser__oxusername->value}]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_USER_MAIN_EMAILLOGIN" }]
                </td>
            </tr>

            <tr>
                <td class="edittext">
                [{ oxmultilang ident="USER_MAIN_CUSTOMERSNR" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="15" maxlength="[{$edit->oxuser__oxcustnr->fldmax_length}]" name="editval[oxuser__oxcustnr]" value="[{$edit->oxuser__oxcustnr->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_USER_MAIN_CUSTOMERSNR" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_BILLSAL" }]
                </td>
                <td class="edittext">
                  <select name="editval[oxuser__oxsal]" class="editinput" [{ $readonly }]>
                    <option value="MR"  [{if $edit->oxuser__oxsal->value|lower  == "mr"  }]SELECTED[{/if}]>[{ oxmultilang ident="MR"  }]</option>
                    <option value="MRS" [{if $edit->oxuser__oxsal->value|lower  == "mrs" }]SELECTED[{/if}]>[{ oxmultilang ident="MRS" }]</option>
                  </select>
                [{ oxinputhelp ident="HELP_GENERAL_BILLSAL" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="USER_MAIN_NAME" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="10" maxlength="[{$edit->oxuser__oxfname->fldmax_length}]" name="editval[oxuser__oxfname]" value="[{$edit->oxuser__oxfname->value }]" [{ $readonly }]>
                <input type="text" class="editinput" size="20" maxlength="[{$edit->oxuser__oxlname->fldmax_length}]" name="editval[oxuser__oxlname]" value="[{$edit->oxuser__oxlname->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_USER_MAIN_NAME" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_COMPANY" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="37" maxlength="[{$edit->oxuser__oxcompany->fldmax_length}]" name="editval[oxuser__oxcompany]" value="[{$edit->oxuser__oxcompany->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_COMPANY" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="USER_MAIN_STRNR" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="28" maxlength="[{$edit->oxuser__oxstreet->fldmax_length}]" name="editval[oxuser__oxstreet]" value="[{$edit->oxuser__oxstreet->value }]" [{ $readonly }]> <input type="text" class="editinput" size="5" maxlength="[{$edit->oxuser__oxstreetnr->fldmax_length}]" name="editval[oxuser__oxstreetnr]" value="[{$edit->oxuser__oxstreetnr->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_USER_MAIN_STRNR" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_ZIPCITY" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="5" maxlength="[{$edit->oxuser__oxzip->fldmax_length}]" name="editval[oxuser__oxzip]" value="[{$edit->oxuser__oxzip->value }]" [{ $readonly }]>
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxuser__oxcity->fldmax_length}]" name="editval[oxuser__oxcity]" value="[{$edit->oxuser__oxcity->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_ZIPCITY" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_USTID" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="15" maxlength="[{$edit->oxuser__oxustid->fldmax_length}]" name="editval[oxuser__oxustid]" value="[{$edit->oxuser__oxustid->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_USTID" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_EXTRAINFO" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="37" maxlength="[{$edit->oxuser__oxaddinfo->fldmax_length}]" name="editval[oxuser__oxaddinfo]" value="[{$edit->oxuser__oxaddinfo->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_EXTRAINFO" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_STATE" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="15" maxlength="[{$edit->oxuser__oxstateid->fldmax_length}]" name="editval[oxuser__oxstateid]" value="[{$edit->oxuser__oxstateid->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_STATE" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_COUNTRY" }]
                </td>
                <td class="edittext">
                 <select class="editinput" name="editval[oxuser__oxcountryid]" [{ $readonly }]>
                   [{ foreach from=$countrylist item=oCountry}]
                   <option value="[{$oCountry->oxcountry__oxid->value}]" [{if $oCountry->oxcountry__oxid->value == $edit->oxuser__oxcountryid->value}]selected[{/if}]>[{$oCountry->oxcountry__oxtitle->value}]</option>
                   [{/foreach}]
                 </select>
                 [{ oxinputhelp ident="HELP_GENERAL_COUNTRY" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_TELEPHONE" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="20" maxlength="[{$edit->oxuser__oxfon->fldmax_length}]" name="editval[oxuser__oxfon]" value="[{$edit->oxuser__oxfon->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_TELEPHONE" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_FAX" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="20" maxlength="[{$edit->oxuser__oxfax->fldmax_length}]" name="editval[oxuser__oxfax]" value="[{$edit->oxuser__oxfax->value }]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_FAX" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_BIRTHDATE" }]
                </td>
                <td class="edittext">
                  <input type="text" class="editinput" size="3" maxlength="2" name="editval[oxuser__oxbirthdate][day]" value="[{$edit->oxuser__oxbirthdate->value|regex_replace:"/^([0-9]{4})[-]([0-9]{1,2})[-]/":"" }]" [{ $readonly }]>
                  <input type="text" class="editinput" size="3" maxlength="2" name="editval[oxuser__oxbirthdate][month]" value="[{$edit->oxuser__oxbirthdate->value|regex_replace:"/^([0-9]{4})[-]/":""|regex_replace:"/[-]([0-9]{1,2})$/":"" }]" [{ $readonly }]>
                  <input type="text" class="editinput" size="8" maxlength="4" name="editval[oxuser__oxbirthdate][year]" value="[{$edit->oxuser__oxbirthdate->value|regex_replace:"/[-]([0-9]{1,2})[-]([0-9]{1,2})$/":"" }]" [{ $readonly }]>
                  [{ oxinputhelp ident="HELP_GENERAL_BIRTHDATE" }]
                </td>
            </tr>
            [{ if $oxid != "-1"}]
            <tr>
                <td class="edittext"><br>
                [{ oxmultilang ident="USER_MAIN_HASPASSWORD" }]
                </td>
                <td class="edittext"><br>
                [{if $edit->oxuser__oxpassword->value}][{ oxmultilang ident="GENERAL_YES" }][{else}][{ oxmultilang ident="GENERAL_NO" }][{/if}]
                [{ oxinputhelp ident="HELP_USER_MAIN_HASPASSWORD" }]
                </td>
            </tr>
            [{/if}]
            <tr>
                <td class="edittext"><br>
                [{ oxmultilang ident="USER_MAIN_NEWPASSWORD" }]
                </td>
                <td class="edittext"><br>
                <input type="password" class="editinput" size="15" name="newPassword" value="" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_USER_MAIN_NEWPASSWORD" }]
                </td>
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
    <td valign="top" class="edittext vr" align="left" width="50%">
    [{ if $oxid != "-1"}]
       <input [{ $readonly }] type="button" value="[{ oxmultilang ident="GENERAL_ASSIGNGROUPS" }]" class="edittext" onclick="JavaScript:showDialog('&cl=user_main&aoc=1&oxid=[{ $oxid }]');">
    [{ /if}]
    </td>
    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
