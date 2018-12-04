[{if $shopid != "1" || $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="payment_main">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post" onSubmit="copyLongDesc( 'oxpayments__oxlongdesc' );">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="payment_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxpayments__oxid]" value="[{$oxid}]">
<input type="hidden" name="editval[oxpayments__oxlongdesc]" value="">

<table cellspacing="0" cellpadding="0" border="0" width="98%">

<tr>

    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_payment_main_form"}]
            <tr>
                <td class="edittext" width="70">
                [{oxmultilang ident="GENERAL_ACTIVE"}]
                </td>
                <td class="edittext">
                <input class="edittext" type="checkbox" name="editval[oxpayments__oxactive]" value='1' [{if $edit->oxpayments__oxactive->value == 1}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_ACTIVE"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" width="100">
                [{oxmultilang ident="PAYMENT_MAIN_NAME"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxpayments__oxdesc->fldmax_length}]" name="editval[oxpayments__oxdesc]" value="[{$edit->oxpayments__oxdesc->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_PAYMENT_MAIN_NAME"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="PAYMENT_MAIN_ADDPRICE"}] ([{$oActCur->sign}])
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="15" maxlength="[{$edit->oxpayments__oxaddsum->fldmax_length}]" name="editval[oxpayments__oxaddsum]" value="[{$edit->oxpayments__oxaddsum->value}]" [{$readonly}]>
                    <select name="editval[oxpayments__oxaddsumtype]" class="editinput" [{include file="help.tpl" helpid=addsumtype}] [{$readonly}]>
                    [{foreach from=$sumtype item=sum}]
                    <option value="[{$sum}]" [{if $sum == $edit->oxpayments__oxaddsumtype->value}]SELECTED[{/if}]>[{$sum}]</option>
                    [{/foreach}]
                    </select>
                [{oxinputhelp ident="HELP_PAYMENT_MAIN_ADDPRICE"}]
                </td>
            </tr>
            [{if $noticeoxaddsumrules eq 1}]
            <tr>
                <td colspan="2">
                    <div class="errorbox">[{oxmultilang ident="PAYMENT_MAIN_NOTICEDEFAULTVALUESSELECTED"}]</div>
                </td>
            </tr>
            [{/if}]
            <tr>
                <td class="edittext" valign="top">
                [{oxmultilang ident="PAYMENT_MAIN_ADDSUMRULES"}]:
                </td>
                <td class="edittext">
                  <table cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td><input type="checkbox" name="oxpayments__oxaddsumrules[]" value="1" [{if !$edit->oxpayments__oxaddsumrules->value || $edit->oxpayments__oxaddsumrules->value & 1}]checked[{/if}] [{$readonly}]> [{oxmultilang ident="PAYMENT_MAIN_ADDSUMRULES_ALLGOODS"}]</td>
                        <td rowspan="5" valign="top">[{oxinputhelp ident="HELP_PAYMENT_MAIN_ADDSUMRULES"}]</td>
                    </tr>
                    <tr><td><input type="checkbox" name="oxpayments__oxaddsumrules[]" value="2" [{if !$edit->oxpayments__oxaddsumrules->value || $edit->oxpayments__oxaddsumrules->value & 2}]checked[{/if}] [{$readonly}]> [{oxmultilang ident="PAYMENT_MAIN_ADDSUMRULES_DISCOUNTS"}]</td></tr>
                    <tr><td><input type="checkbox" name="oxpayments__oxaddsumrules[]" value="4" [{if !$edit->oxpayments__oxaddsumrules->value || $edit->oxpayments__oxaddsumrules->value & 4}]checked[{/if}] [{$readonly}]> [{oxmultilang ident="PAYMENT_MAIN_ADDSUMRULES_VOUCHERS"}]</td></tr>
                    <tr><td><input type="checkbox" name="oxpayments__oxaddsumrules[]" value="8" [{if !$edit->oxpayments__oxaddsumrules->value || $edit->oxpayments__oxaddsumrules->value & 8}]checked[{/if}] [{$readonly}]> [{oxmultilang ident="PAYMENT_MAIN_ADDSUMRULES_SHIPCOSTS"}]</td></tr>
                    <tr><td><input type="checkbox" name="oxpayments__oxaddsumrules[]" value="16" [{if $edit->oxpayments__oxaddsumrules->value & 16}]checked[{/if}] [{$readonly}]> [{oxmultilang ident="PAYMENT_MAIN_ADDSUMRULES_GIFTS"}]</td></tr>
                  </table>
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="PAYMENT_MAIN_FROMBONI"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxpayments__oxfromboni->fldmax_length}]" name="editval[oxpayments__oxfromboni]" value="[{$edit->oxpayments__oxfromboni->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_PAYMENT_MAIN_FROMBONI"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="PAYMENT_MAIN_AMOUNT"}] ([{$oActCur->sign}])
                </td>
                <td class="edittext">
                [{oxmultilang ident="PAYMENT_MAIN_FROM"}] <input type="text" class="editinput" size="5" maxlength="[{$edit->oxpayments__oxfromamount->fldmax_length}]" name="editval[oxpayments__oxfromamount]" value="[{$edit->oxpayments__oxfromamount->value}]" [{$readonly}]>  [{oxmultilang ident="PAYMENT_MAIN_TILL"}] <input type="text" class="editinput" size="5" maxlength="[{$edit->oxpayments__oxtoamount->fldmax_length}]" name="editval[oxpayments__oxtoamount]" value="[{$edit->oxpayments__oxtoamount->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_PAYMENT_MAIN_AMOUNT"}]
                </td>
            </tr>

            <tr>
                <td class="edittext">
                [{oxmultilang ident="PAYMENT_MAIN_SELECTED"}]
                </td>
                <td class="edittext">
                <input type="checkbox" name="editval[oxpayments__oxchecked]" value="1" [{if $edit->oxpayments__oxchecked->value}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_PAYMENT_MAIN_SELECTED"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_SORT"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxpayments__oxsort->fldmax_length}]" name="editval[oxpayments__oxsort]" value="[{$edit->oxpayments__oxsort->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_PAYMENT_MAIN_SORT"}]
                </td>
            </tr>
        [{/block}]
        [{block name="admin_payment_main_fields"}]
            <tr>
                <td class="edittext" valign="top">
                [{oxmultilang ident="GENERAL_FIELDS"}]
                </td>
                <td class="edittext">
                <select name="aFields[]" size="3" multiple class="editinput" style="width: 150px;" [{$readonly}]>
                   [{foreach from=$aFieldNames item=sField}]
                    <option value="[{$sField->name}]">[{$sField->name}]</option>
                    [{/foreach}]
                 </select>
                 [{oxinputhelp ident="HELP_GENERAL_FIELDS"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" valign="top">
                </td>
                <td class="edittext">
                    <input type="text" class="edittext" name="sAddField" value="" size="128" style="width: 150px;" [{$readonly}]>
                    <br>
                    <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_FIELDS_ADD"}]" onClick="Javascript:document.myedit.fnc.value='addfield'"" [{$readonly}] style="width: 125px;"><br>
                  <br>
                </td>
            </tr>
            <tr>
                <td class="edittext" valign="top">
                </td>
                <td class="edittext">
                    <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_FIELDS_DELETE"}]" onClick="Javascript:document.myedit.fnc.value='delfields'"" [{$readonly}] style="width: 150px;">
                </td>
            </tr>
        [{/block}]


        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
            <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'"" [{$readonly}] style="width: 150px;">
            </td>
        </tr>
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
                [{include file="language_edit.tpl"}]
            </td>
        </tr>

        </table>
    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="50%">
        [{block name="admin_payment_main_assign_groups"}]
            [{if $oxid != "-1"}]
                <input [{$readonly}] type="button" value="[{oxmultilang ident="GENERAL_ASSIGNGROUPS"}]" class="edittext" style="margin-bottom:20px;" onclick="JavaScript:showDialog('&cl=payment_main&aoc=1&oxid=[{$oxid}]');">
            [{/if}]
        [{/block}]

        [{block name="admin_payment_main_editor"}]
            [{oxhasrights object=$edit field='oxlongdesc' readonly=$readonly}]
                <div>
                    <p>[{oxmultilang ident="PAYMENT_MAIN_LONGDESC"}]</p>
                    [{$editor}]
                    <div class="messagebox">[{oxmultilang ident="EDITOR_PLAINTEXT_HINT"}]</div>
                </div>
            [{/oxhasrights}]
        [{/block}]
    </td>

    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
