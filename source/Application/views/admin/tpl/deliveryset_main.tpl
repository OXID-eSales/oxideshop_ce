[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="oxidCopy" value="[{$oxid}]">
    <input type="hidden" name="cl" value="deliveryset_main">
    <input type="hidden" name="language" value="[{$actlang}]">
</form>


<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="deliveryset_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxdeliveryset__oxid]" value="[{$oxid}]">
<input type="hidden" name="language" value="[{$actlang}]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>

    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_deliveryset_main_form"}]
            <tr>
                <td class="edittext" width="140">
                [{oxmultilang ident="GENERAL_NAME"}]
                </td>
                <td class="edittext" width="250">
                <input type="text" class="editinput" size="50" maxlength="[{$edit->oxdeliveryset__oxtitle->fldmax_length}]" name="editval[oxdeliveryset__oxtitle]" value="[{$edit->oxdeliveryset__oxtitle->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_NAME"}]
                </td>
            </tr>
            [{if $oxid != "-1"}]
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_ALWAYS_ACTIVE"}]
                </td>
                <td class="edittext">
                <input class="edittext" type="checkbox" name="editval[oxdeliveryset__oxactive]" value='1' [{if $edit->oxdeliveryset__oxactive->value == 1}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_ACTIVE"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_ACTIVFROMTILL"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="27" name="editval[oxdeliveryset__oxactivefrom]" value="[{$edit->oxdeliveryset__oxactivefrom|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{$readonly}]>([{oxmultilang ident="GENERAL_FROM"}])<br>
                <input type="text" class="editinput" size="27" name="editval[oxdeliveryset__oxactiveto]" value="[{$edit->oxdeliveryset__oxactiveto|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{$readonly}]>([{oxmultilang ident="GENERAL_TILL"}])
                [{oxinputhelp ident="HELP_GENERAL_ACTIVFROMTILL"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" width="140">
                [{oxmultilang ident="GENERAL_SORT"}]
                </td>
                <td class="edittext" width="250">
                <input type="text" class="editinput" size="5" maxlength="[{$edit->oxdeliveryset__oxpos->fldmax_length}]" name="editval[oxdeliveryset__oxpos]" value="[{$edit->oxdeliveryset__oxpos->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_DELIVERYSET_MAIN_POS"}]
                </td>
            </tr>
        [{/block}]
        <tr><td colspan="2">&nbsp;</td></tr>
        [{if $oxid != "-1"}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
                [{include file="language_edit.tpl"}]
            </td>
        </tr>
        [{/if}]
        [{/if}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
            <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'"" [{$readonly}]><br>
            </td>
        </tr>
        </table>
    </td>
    <td valign="top" width="50%">
        [{block name="admin_deliveryset_main_assign_delivery"}]
            [{if $oxid != "-1"}]
                <input [{$readonly}] type="button" value="[{oxmultilang ident="DELIVERYSET_MAIN_ASSIGNDELIVERY"}]" class="edittext" onclick="JavaScript:showDialog('&cl=deliveryset_main&aoc=1&oxid=[{$oxid}]');"><br>
                <input [{$readonly}] type="button" value="[{oxmultilang ident="GENERAL_ASSIGNCOUNTRIES"}]" class="edittext" onclick="JavaScript:showDialog('&cl=deliveryset_payment&aoc=2&oxid=[{$oxid}]');">
            [{/if}]
        [{/block}]
    </td>
    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
