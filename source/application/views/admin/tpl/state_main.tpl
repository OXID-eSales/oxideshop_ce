[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
    <!--
    window.onload = function ()
    {
        [{if $updatelist == 1}]
        top.oxid.admin.updateList('[{$oxid}]');
        [{/if}]
        var oField = top.oxid.admin.getLockTarget();
        oField.onchange = oField.onkeyup = oField.onmouseout = top.oxid.admin.unlockSave;
    }
    //-->
</script>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="oxidCopy" value="[{$oxid}]">
    <input type="hidden" name="cl" value="state_main">
    <input type="hidden" name="language" value="[{$actlang}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="state_main">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="voxid" value="[{$oxid}]">
    <input type="hidden" name="oxparentid" value="[{$oxparentid}]">
    <input type="hidden" name="editval[oxstates__oxid]" value="[{$oxid}]">
    <input type="hidden" name="language" value="[{$actlang}]">

    <table cellspacing="0" cellpadding="0" border="0" width="98%">
        <tr>
            <td valign="top" class="edittext">
                <table cellspacing="0" cellpadding="0" border="0">
                    [{block name="admin_state_main_form"}]
                        <tr>
                            <td class="edittext">
                                [{oxmultilang ident="GENERAL_TITLE"}]
                            </td>
                            <td class="edittext">
                                <input type="text" class="editinput" size="40" maxlength="[{$edit->oxstates__oxtitle->fldmax_length}]" id="oLockTarget" name="editval[oxstates__oxtitle]" value="[{$edit->oxstates__oxtitle->value}]" [{$readonly}]>
                                [{oxinputhelp ident="HELP_GENERAL_TITLE"}]
                            </td>
                        </tr>
                        <tr>
                            <td class="edittext">
                                [{oxmultilang ident="STATE_MAIN_ISO2"}]
                            </td>
                            <td class="edittext">
                                <input type="text" class="editinput" size="5" maxlength="[{$edit->oxstates__oxisoalpha2->fldmax_length}]" name="editval[oxstates__oxisoalpha2]" value="[{$edit->oxstates__oxisoalpha2->value}]" [{$readonly}]>
                                [{oxinputhelp ident="HELP_STATE_MAIN_ISO2"}]
                            </td>
                        </tr>
                        <tr>
                            <td class="edittext">
                                [{oxmultilang ident="STATE_MAIN_COUNTRY"}]
                            </td>
                            <td class="edittext">
                                <input class="listedit" type="hidden" name="editval[oxstates__oxcountryid]" value="">
                                [{assign var="CountryList" value=$oView->getCountryList()}]
                                <select name="editval[oxstates__oxcountryid]" class="editinput">
                                    <option value="">[{oxmultilang ident="STATE_LIST_ALLCOUNTRY"}]</option>
                                    [{foreach from=$CountryList->aList item=country}]
                                    <option value="[{$country->oxcountry__oxid->value}]" [{if $country->oxcountry__oxid->value == $edit->oxstates__oxcountryid->value}]SELECTED[{/if}]>
                                        [{$country->oxcountry__oxtitle->getRawValue()}]
                                    </option>
                                    [{/foreach}]
                                </select>
                            </td>
                        </tr>
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
                            <input type="submit" class="edittext" id="oLockButton" name="saveArticle" value="[{oxmultilang ident="GENERAL_SAVE"}]"
                                   onClick="Javascript:document.myedit.fnc.value='save'"" [{$readonly}] [{if !$edit->oxstates__oxtitle->value && !$oxparentid}]disabled[{/if}]>
                        </td>
                    </tr>
                </table>
            </td>
            <!-- Anfang rechte Seite -->
            <td valign="top" class="edittext" align="left" width="55%">
            </td>
            <!-- Ende rechte Seite -->
        </tr>
    </table>
</form>
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
