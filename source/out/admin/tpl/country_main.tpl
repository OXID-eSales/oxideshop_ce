[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
    [{ /if}]
    var oField = top.oxid.admin.getLockTarget();
    oField.onchange = oField.onkeyup = oField.onmouseout = top.oxid.admin.unlockSave;
}
//-->
</script>

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="oxidCopy" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="country_main">
    <input type="hidden" name="language" value="[{ $actlang }]">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="country_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="voxid" value="[{ $oxid }]">
<input type="hidden" name="oxparentid" value="[{ $oxparentid }]">
<input type="hidden" name="editval[oxcountry__oxid]" value="[{ $oxid }]">
<input type="hidden" name="language" value="[{ $actlang }]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>

    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
            [{block name="admin_country_main_form"}]
                <tr>
                    <td class="edittext" width="120">
                    [{ oxmultilang ident="GENERAL_ACTIVE" }]
                    </td>
                    <td class="edittext">
                    <input class="edittext" type="checkbox" name="editval[oxcountry__oxactive]" value='1' [{if $edit->oxcountry__oxactive->value == 1}]checked[{/if}] [{ $readonly }]>
                    [{ oxinputhelp ident="HELP_GENERAL_ACTIVE" }]
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                    [{ oxmultilang ident="GENERAL_TITLE" }]
                    </td>
                    <td class="edittext">
                    <input type="text" class="editinput" size="40" maxlength="[{$edit->oxcountry__oxtitle->fldmax_length}]" id="oLockTarget" name="editval[oxcountry__oxtitle]" value="[{$edit->oxcountry__oxtitle->value}]" [{ $readonly }]>
                    [{ oxinputhelp ident="HELP_GENERAL_TITLE" }]
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                    [{ oxmultilang ident="GENERAL_SHORTDESC" }]
                    </td>
                    <td class="edittext">
                    <input type="text" class="editinput" size="40" maxlength="[{$edit->oxcountry__oxshortdesc->fldmax_length}]" name="editval[oxcountry__oxshortdesc]" value="[{$edit->oxcountry__oxshortdesc->value}]" [{ $readonly }]>
                    [{ oxinputhelp ident="HELP_GENERAL_SHORTDESC" }]
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                    [{ oxmultilang ident="COUNTRY_MAIN_ISO2" }]
                    </td>
                    <td class="edittext">
                    <input type="text" class="editinput" size="5" maxlength="[{$edit->oxcountry__oxisoalpha2->fldmax_length}]" name="editval[oxcountry__oxisoalpha2]" value="[{$edit->oxcountry__oxisoalpha2->value}]" [{ $readonly }]>
                    [{ oxinputhelp ident="HELP_COUNTRY_MAIN_ISO2" }]
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                    [{ oxmultilang ident="COUNTRY_MAIN_ISO3" }]
                    </td>
                    <td class="edittext">
                    <input type="text" class="editinput" size="5" maxlength="[{$edit->oxcountry__oxisoalpha3->fldmax_length}]" name="editval[oxcountry__oxisoalpha3]" value="[{$edit->oxcountry__oxisoalpha3->value}]" [{ $readonly }]>
                    [{ oxinputhelp ident="HELP_COUNTRY_MAIN_ISO3" }]
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                    [{ oxmultilang ident="COUNTRY_MAIN_ISOUNNUM" }]
                    </td>
                    <td class="edittext">
                    <input type="text" class="editinput" size="5" maxlength="[{$edit->oxcountry__oxunnum3->fldmax_length}]" name="editval[oxcountry__oxunnum3]" value="[{$edit->oxcountry__oxunnum3->value}]" [{ $readonly }]>
                    [{ oxinputhelp ident="HELP_COUNTRY_MAIN_ISOUNNUM" }]
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                    [{ oxmultilang ident="GENERAL_SORT" }]
                    </td>
                    <td class="edittext">
                    <input type="text" class="editinput" size="5" maxlength="[{$edit->oxcountry__oxorder->fldmax_length}]" name="editval[oxcountry__oxorder]" value="[{$edit->oxcountry__oxorder->value}]" [{ $readonly }]>
                    [{ oxinputhelp ident="HELP_GENERAL_SORT" }]
                    </td>
                </tr>
                [{if $blForeignCountry}]
                <tr>
                    <td class="edittext">
                    [{ oxmultilang ident="COUNTRY_MAIN_OXVATSTATUS" }]
                    </td>
                    <td class="edittext">
                    <fieldset style="margin: 5px 0 0 0;">
                        <input type="radio" name="editval[oxcountry__oxvatstatus]" value="0" [{if $edit->oxcountry__oxvatstatus->value == 0}]checked[{/if}] [{ $readonly }]>
                        [{ oxmultilang ident="COUNTRY_MAIN_OXVATSTATUS_0" }]
                        [{ oxinputhelp ident="HELP_COUNTRY_MAIN_OXVATSTATUS_0" }]
                        <br />
                        <input type="radio" name="editval[oxcountry__oxvatstatus]" value="1" [{if $edit->oxcountry__oxvatstatus->value == 1}]checked[{/if}] [{ $readonly }]>
                        [{ oxmultilang ident="COUNTRY_MAIN_OXVATSTATUS_1" }]
                        [{ oxinputhelp ident="HELP_COUNTRY_MAIN_OXVATSTATUS_1" }]
                    </fieldset>
                    </td>
                </tr>
                [{/if}] [{* $blForeignCountry *}]
            [{/block}]
        [{if $oxid != "-1"}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
                [{include file="language_edit.tpl"}]
            </td>
        </tr>
        [{/if}]
        <tr>
            <td class="edittext"><br><br>
            </td>
            <td class="edittext"><br><br>
            <input type="submit" class="edittext" id="oLockButton" name="saveArticle" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly }] [{ if !$edit->oxcountry__oxtitle->value && !$oxparentid }]disabled[{/if}]><br>
            </td>
        </tr>


        </table>
    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="55%">
        [{block name="admin_country_main_description"}]
            [{ oxmultilang ident="COUNTRY_MAIN_OPDESCRIPTION" }]<br>
            <textarea class="editinput" style="width:250;height:100;" wrap="VIRTUAL" name="editval[oxcountry__oxlongdesc]" [{ $readonly }]>[{$edit->oxcountry__oxlongdesc->value}]</textarea>
        [{/block}]
    </td>
    <!-- Ende rechte Seite -->

    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
