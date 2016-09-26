[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function ChangeDiscountType(oObj)
{   var oHObj = document.getElementById("itmart");
    var oDObj = document.getElementById("editval[oxdiscount__oxaddsum]");
    if ( oDObj != null && oHObj != null && oObj != null)
    {   if ( oObj.value == "itm")
        {   oHObj.style.display = "";
            oDObj.style.display = "none";
        }
        else
        {   oHObj.style.display = "none";
            oDObj.style.display = "";
        }
    }
}
window.onload = function ()
{
    [{if $updatelist == 1}]
    top.oxid.admin.updateList('[{$oxid}]');
    [{/if}]
    var oField = top.oxid.admin.getLockTarget();
    top.oxid.admin.unlockSave();
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
    <input type="hidden" name="cl" value="discount_main">
    <input type="hidden" name="language" value="[{$actlang}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="discount_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxdiscount__oxid]" value="[{$oxid}]">
<input type="hidden" name="language" value="[{$actlang}]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
            [{block name="admin_discount_main_form"}]
                <tr>
                    <td class="edittext" width="120">
                    [{oxmultilang ident="GENERAL_NAME"}]
                    </td>
                    <td class="edittext" width="250">
                    <input type="text" class="editinput" size="50" maxlength="[{$edit->oxdiscount__oxtitle->fldmax_length}]" name="editval[oxdiscount__oxtitle]" value="[{if $oxid == "-1"}][{$discount_title}][{else}][{$edit->oxdiscount__oxtitle->value}][{/if}]" [{$readonly}]>
                    [{oxinputhelp ident="HELP_GENERAL_NAME"}]
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                        [{oxmultilang ident="DISCOUNT_MAIN_SORT"}]
                    </td>
                    <td class="edittext" colspan="2">
                        <input type="text" class="editinput" size="25" maxlength="[{$edit->oxdiscount__oxsort->fldmax_length}]" id="oLockTarget" name="editval[oxdiscount__oxsort]" value="[{if $oxid == "-1"}][{$oView->getNextOxsort()}][{else}][{$edit->oxdiscount__oxsort->value}][{/if}]" [{$readonly}]>
                        [{oxinputhelp ident="HELP_DISCOUNT_MAIN_SORT"}]
                    </td>
                </tr>
                [{if $oxid != "-1"}]
                <tr>
                    <td class="edittext" width="120">
                    [{oxmultilang ident="GENERAL_ALWAYS_ACTIVE"}]
                    </td>
                    <td class="edittext">
                    <input class="edittext" type="checkbox" name="editval[oxdiscount__oxactive]" value='1' [{if $edit->oxdiscount__oxactive->value == 1}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_GENERAL_ACTIVE"}]
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                    [{oxmultilang ident="GENERAL_ACTIVFROMTILL"}]
                    </td>
                    <td class="edittext">
                    <input type="text" class="editinput" size="27" name="editval[oxdiscount__oxactivefrom]" value="[{$edit->oxdiscount__oxactivefrom|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{$readonly}]>[{oxmultilang ident="DISCOUNT_MAIN_AFROM"}]<br>
                    <input type="text" class="editinput" size="27" name="editval[oxdiscount__oxactiveto]" value="[{$edit->oxdiscount__oxactiveto|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{$readonly}]>[{oxmultilang ident="DISCOUNT_MAIN_ATILL"}]
                    [{oxinputhelp ident="HELP_GENERAL_ACTIVFROMTILL"}]
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                    [{oxmultilang ident="DISCOUNT_MAIN_AMOUNT"}]
                    </td>
                    <td class="edittext">
                    [{oxmultilang ident="GENERAL_FROM"}] <input type="text" class="editinput" size="10" maxlength="[{$edit->oxdiscount__oxamount->fldmax_length}]" name="editval[oxdiscount__oxamount]" value="[{$edit->oxdiscount__oxamount->value}]" [{$readonly}]>
                    [{oxmultilang ident="GENERAL_TILL"}] <input type="text" class="editinput" size="10" maxlength="[{$edit->oxdiscount__oxamountto->fldmax_length}]" name="editval[oxdiscount__oxamountto]" value="[{$edit->oxdiscount__oxamountto->value}]" [{$readonly}]>
                    [{oxinputhelp ident="HELP_DISCOUNT_MAIN_AMOUNT"}]
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                    [{oxmultilang ident="DISCOUNT_MAIN_PRICE"}] ([{$oActCur->sign}])
                    </td>
                    <td class="edittext">
                    [{oxmultilang ident="GENERAL_FROM"}] <input type="text" class="editinput" size="10" maxlength="[{$edit->oxdiscount__oxprice->fldmax_length}]" name="editval[oxdiscount__oxprice]" value="[{$edit->oxdiscount__oxprice->value}]" [{$readonly}]>
                    [{oxmultilang ident="GENERAL_TILL"}] <input type="text" class="editinput" size="10" maxlength="[{$edit->oxdiscount__oxpriceto->fldmax_length}]" name="editval[oxdiscount__oxpriceto]" value="[{$edit->oxdiscount__oxpriceto->value}]" [{$readonly}]>
                    [{oxinputhelp ident="HELP_DISCOUNT_MAIN_PRICE"}]
                    </td>
                </tr>
                <tr>
                    <td class="edittext" height="30">
                    [{oxmultilang ident="DISCOUNT_MAIN_REBATE"}]
                    </td>
                    <td class="edittext">
                    <input type="text" class="editinput" size="15" maxlength="[{$edit->oxdiscount__oxaddsum->fldmax_length}]" name="editval[oxdiscount__oxaddsum]" id="editval[oxdiscount__oxaddsum]" value="[{$edit->oxdiscount__oxaddsum->value}]" [{if $edit->oxdiscount__oxaddsumtype->value == "itm"}] style="display:none;"[{/if}][{$readonly}]>
                        <select name="editval[oxdiscount__oxaddsumtype]" class="editinput" onChange="ChangeDiscountType(this);" [{$readonly}]>
                        [{foreach from=$sumtype item=sum}]
                        <option value="[{$sum}]" [{if $sum == $edit->oxdiscount__oxaddsumtype->value}]SELECTED[{/if}]>[{$sum}]</option>
                        [{/foreach}]
                        </select>
                        [{oxinputhelp ident="HELP_DISCOUNT_MAIN_REBATE"}]
                    </td>
                </tr>
                <tr id="itmart"[{if $edit->oxdiscount__oxaddsumtype->value != "itm"}] style="display:none;"[{/if}]>
                  <td class="edittext">
                    [{oxmultilang ident="DISCOUNT_MAIN_EXTRA"}]
                  </td>
                  <td class="edittext">
                    <table>
                        [{block name="admin_discount_main_form_itm"}]
                          <tr>
                            <td>[{$oView->getItemDiscountProductTitle()}]</td>
                            <td>
                              <input [{$readonly}] type="button" value="[{oxmultilang ident="GENERAL_CHANGEPRODUCT"}]" class="edittext" onclick="showDialog('&cl=discount_main&aoc=2&oxid=[{$oxid}]');">
                              [{oxinputhelp ident="HELP_DISCOUNT_MAIN_EXTRA"}]
                            </td>
                          </tr>
                          <tr>
                            <td>[{oxmultilang ident="DISCOUNT_MAIN_MULTIPLY_DISCOUNT_AMOUNT"}]</td>
                            <td><input type="text" class="editinput" size="5" maxlength="[{$edit->oxdiscount__oxitmamount->fldmax_length}]" name="editval[oxdiscount__oxitmamount]" value="[{$edit->oxdiscount__oxitmamount->value}]" [{$readonly}]></td>
                          </tr>
                          <tr>
                            <td>[{oxmultilang ident="DISCOUNT_MAIN_MULTIPLY_DISCOUNT_ARTICLES"}]</td>
                            <td>
                              <input type="hidden" name="editval[oxdiscount__oxitmmultiple]" value="0">
                              <input class="edittext" type="checkbox" name="editval[oxdiscount__oxitmmultiple]" value='1' [{if $edit->oxdiscount__oxitmmultiple->value == 1}]checked[{/if}] [{$readonly}]>
                            </td>
                          </tr>
                        [{/block}]
                    </table>
                  </td>
                </tr>
            [{/block}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
                [{include file="language_edit.tpl"}]
            </td>
        </tr>
        [{/if}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
                <input type="submit" class="edittext" id="oLockButton" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="document.myedit.fnc.value='save'"" [{$readonly}] [{if !$edit->oxdiscount__oxsort->value}]disabled[{/if}]><br>
            </td>
        </tr>
        </table>
    </td>
    <td valign="top" width="50%">
        [{block name="admin_discount_main_assign_countries"}]
            [{if $oxid != "-1"}]
                <input [{$readonly}] type="button" value="[{oxmultilang ident="GENERAL_ASSIGNCOUNTRIES"}]" class="edittext" onclick="showDialog('&cl=discount_main&aoc=1&oxid=[{$oxid}]');">
            [{/if}]
        [{/block}]
    </td>
    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
