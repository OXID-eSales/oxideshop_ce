[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function onSelect_aField(){
    var oSelect = document.getElementById('aFields');
    var oBtnMod = document.getElementById('submit_modify');
    var oBtnDel = document.getElementById('submit_delete');
    var oEditAddName = document.getElementById('EditAddName');
    var oEditAddPrice = document.getElementById('EditAddPrice');
    var oEditAddPriceUnit = document.getElementById('EditAddPriceUnit');
    var oEditAddPos = document.getElementById('EditAddPos');
    if (oSelect.selectedIndex == -1)
    {
        oEditAddName.value = '';
        oEditAddPrice.value = '';
        oEditAddPos.value = '';
        oEditAddPriceUnit.selectedIndex = 0;
        oBtnMod.disabled = true;
        oBtnDel.disabled = true;
    }
    else
    {
        var aItem = oSelect.item(oSelect.selectedIndex).value.split('__@@');

        oEditAddName.value = aItem[1];
        var iPerc = (aItem[2]).indexOf('%');
        if (iPerc == -1)
        {
            oEditAddPriceUnit.selectedIndex = 0;
            oEditAddPrice.value = aItem[2];
        }else
        {
            oEditAddPriceUnit.selectedIndex = 1;
            oEditAddPrice.value = aItem[2].substr(0, iPerc);
        }

        oEditAddPos.value = aItem[0];
        oBtnMod.disabled = false;
        oBtnDel.disabled = false;
    }
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
    <input type="hidden" name="cl" value="selectlist_main">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="selectlist_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxselectlist__oxid]" value="[{$oxid}]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_selectlist_main_form"}]
            <tr>
                <td class="edittext" width="100">
                [{oxmultilang ident="GENERAL_TITLE"}]
                </td>
                <td class="edittext">
                <input [{$readonly}] type="text" class="editinput" size="25" maxlength="[{$edit->oxselectlist__oxtitle->fldmax_length}]" name="editval[oxselectlist__oxtitle]" value="[{$edit->oxselectlist__oxtitle->value}]" style="width: 150px;">
                [{oxinputhelp ident="HELP_GENERAL_TITLE"}]
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="edittext" width="100">
                [{oxmultilang ident="SELECTLIST_MAIN_TITLEIDENT"}]
                </td>
                <td class="edittext">
                <input [{$readonly}] type="text" class="editinput" size="25" maxlength="[{$edit->oxselectlist__oxident->fldmax_length}]" name="editval[oxselectlist__oxident]" value="[{$edit->oxselectlist__oxident->value}]" style="width: 150px;">
                [{oxinputhelp ident="HELP_SELECTLIST_MAIN_TITLEIDENT"}]
                </td>
                <td>&nbsp;
                [{if $iErrorCode == 1}]
                [{elseif $iErrorCode == -1}]
                    <span class="errorbox">[{oxmultilang ident="GENERAL_REQUIRED_MISS"}]</span>
                [{elseif $iErrorCode == -2}]
                    <span class="errorbox">[{oxmultilang ident="SELECTLIST_MAIN_ADDFIELD_POS"}] [{oxmultilang ident="GENERAL_OUTOFBOUNDS"}]</span>
                [{/if}]
                </td>
            </tr>
        [{/block}]

        <tr valign="top">
            [{block name="admin_selectlist_main_fields"}]
                <td class="edittext">
                [{oxmultilang ident="SELECTLIST_MAIN_FIELDS"}]
                </td>
                <td class="edittext">
                <select name="aFields[]" id="aFields" size="12" multiple class="editinput" style="width: 150px;" onchange="javascript:onSelect_aField()">
                   [{foreach from=$edit->getFieldList() item=sField name=fields}]
                    <option value="[{$smarty.foreach.fields.iteration}]__@@[{$sField->name}]__@@[{$sField->price}]">[{$smarty.foreach.fields.iteration}] - [{$sField->name}][{if $sField->price}],[{$sField->price}][{/if}]</option>
                    [{/foreach}]
                 </select>
                 [{oxinputhelp ident="HELP_SELECTLIST_MAIN_FIELDS"}]
                </td>
            [{/block}]
            <td class="edittext">
                <table cellspacing="0" cellpadding="0" border="0">
                    [{block name="admin_selectlist_main_newfield"}]
                        <tr>
                            <td class="edittext" width="10">&nbsp;</td>
                            <td class="edittext" width="100">
                                [{oxmultilang ident="SELECTLIST_MAIN_ADDFIELD_NAME"}]&nbsp;<span style="color:red;">*</span>
                            </td>
                            <td class="edittext" align="right">
                                <input [{$readonly}] type="text" class="edittext" id="EditAddName" name="sAddField" value="" size="15" style="width: 100px;">
                                [{oxinputhelp ident="HELP_SELECTLIST_MAIN_ADDFIELD_NAME"}]
                            </td>
                        </tr>
                        <tr>
                            <td class="edittext" width="10">&nbsp;</td>
                            <td class="edittext" width="100">
                                [{oxmultilang ident="SELECTLIST_MAIN_ADDFIELD_PREIS"}] ([{$oActCur->sign}])
                            </td>
                            <td class="edittext" align="right">
                                <input [{$readonly}] type="text" class="edittext" id="EditAddPrice" name="sAddFieldPriceMod" value="" size="10" style="width: 50px;">
                                <select [{$readonly}] id="EditAddPriceUnit" name="sAddFieldPriceModUnit" class="editinput" style="width: 46px;">
                                    <option value="" >abs</option>
                                    <option value="%" >%</option>
                                </select>
                                [{oxinputhelp ident="HELP_SELECTLIST_MAIN_ADDFIELD_PREIS"}]
                            </td>
                        </tr>
                        <tr>
                            <td class="edittext" width="10">&nbsp;</td>
                            <td class="edittext" width="100">
                                [{oxmultilang ident="SELECTLIST_MAIN_ADDFIELD_POS"}]
                            </td>
                            <td class="edittext" align="right">
                                <input [{$readonly}] type="text" class="edittext" id="EditAddPos" name="sAddFieldPos" value="" size="15" style="width: 100px;">
                                [{oxinputhelp ident="HELP_SELECTLIST_MAIN_ADDFIELD_POS"}]
                            </td>
                        </tr>
                    [{/block}]
                    <tr height="5">
                        <td class="edittext" width="10">&nbsp;</td>
                        <td class="edittext">&nbsp;</td>
                        <td class="edittext">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="edittext" width="10">&nbsp;</td>
                        <td class="edittext" colspan="2" align="right">
                         <input [{$readonly}] type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_FIELDS_ADD"}]" onClick="Javascript:document.myedit.fnc.value='addfield'" [{$readonly}] style="width: 100px;">
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext" width="10">&nbsp;</td>
                        <td class="edittext" colspan="2" align="right">
                         <input [{$readonly}] type="submit" id="submit_modify" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_FIELDS_MODIFY"}]" onClick="Javascript:document.myedit.fnc.value='changefield'" [{$readonly}] style="width: 100px;" DISABLED>
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext" width="10">&nbsp;</td>
                        <td class="edittext" colspan="2" align="right">
                         <br /><input [{$readonly}] type="submit" id="submit_delete" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_FIELDS_DELETE"}]" onClick="Javascript:document.myedit.fnc.value='delfields'" [{$readonly}] style="width: 150px;" DISABLED>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="edittext" colspan="2">
                <br>
                <input [{$readonly}] type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'">
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="edittext" colspan="2">
                <br>
                [{include file="language_edit.tpl"}]
            </td>
            <td>&nbsp;</td>
        </tr>
        </table>
    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="50%">
        [{if $oxid != "-1"}]
        <input [{$readonly}] type="button" value="[{oxmultilang ident="GENERAL_ASSIGNARTICLES"}]" class="edittext" onclick="JavaScript:showDialog('&cl=selectlist_main&aoc=1&oxid=[{$oxid}]');">
        [{/if}]
    </td>
    </tr>
</table>
</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
